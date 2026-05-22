<?php
// Verifica usuario + contraseña contra una credencial con rol admin.

session_start();
require_once __DIR__ . '/util/conec.php';

header('Content-Type: text/html; charset=utf-8');

$usuario = trim($_POST['usuario'] ?? '');
$password = $_POST['password'] ?? '';

if ($usuario === '' || $password === '') {
	echo 'Solicitud inválida.';
	exit;
}

$db = new ConexionBD();
$conn = $db->getConexion();
if (!$conn) {
	http_response_code(500);
	echo 'Error de conexión.';
	exit;
}

$stmt = $conn->prepare(
	"SELECT c.id, c.id_empleado, c.usuario, c.password_hash, c.acceso,
	        c.failed_attempts, c.locked_until
	 FROM credenciales c
	 JOIN empleados e ON e.id = c.id_empleado
	 WHERE c.usuario = :usuario
	   AND c.acceso = 'admin'
	   AND e.activo = TRUE"
);
$stmt->execute([':usuario' => $usuario]);
$cred = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cred) {
	echo 'Credenciales inválidas.';
	exit;
}

if ($cred['locked_until'] && strtotime($cred['locked_until']) > time()) {
	echo 'Cuenta bloqueada temporalmente. Inténtalo más tarde.';
	exit;
}

if (!password_verify($password, $cred['password_hash'])) {
	$upd = $conn->prepare(
		"UPDATE credenciales
		 SET failed_attempts = failed_attempts + 1,
		     locked_until = CASE
		         WHEN failed_attempts + 1 >= 5 THEN DATE_ADD(NOW(), INTERVAL 5 MINUTE)
		         ELSE locked_until
		     END
		 WHERE id = :id"
	);
	$upd->execute([':id' => $cred['id']]);
	echo 'Credenciales inválidas.';
	exit;
}

$conn->prepare(
	"UPDATE credenciales
	 SET failed_attempts = 0, locked_until = NULL, last_login_at = NOW()
	 WHERE id = :id"
)->execute([':id' => $cred['id']]);

session_regenerate_id(true);
$_SESSION['id_empleado'] = (int)$cred['id_empleado'];
$_SESSION['usuario'] = $cred['usuario'];
$_SESSION['acceso'] = $cred['acceso'];

header('HX-Redirect: /pbros/html/dashboard.html');
