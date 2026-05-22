<?php
// Verifies the submitted secret against the stored hash. On success, sets the
// session and tells htmx to redirect to the role's dashboard via HX-Redirect.
// On failure, returns a short error fragment for the #login-error target.

session_start();
require_once __DIR__ . '/util/conec.php';

header('Content-Type: text/html; charset=utf-8');

$id_empleado = filter_input(INPUT_POST, 'id_empleado', FILTER_VALIDATE_INT);
$password = $_POST['password'] ?? null;
$pin = $_POST['pin'] ?? null;

if (!$id_empleado) {
	http_response_code(400);
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
	"SELECT c.id, c.id_empleado, c.usuario, c.password_hash, c.pin_hash, c.acceso,
	        c.failed_attempts, c.locked_until
	 FROM credenciales c
	 JOIN empleados e ON e.id = c.id_empleado
	 WHERE c.id_empleado = :id AND e.activo = TRUE"
);
$stmt->execute([':id' => $id_empleado]);
$cred = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cred) {
	echo 'Credenciales inválidas.';
	exit;
}

if ($cred['locked_until'] && strtotime($cred['locked_until']) > time()) {
	echo 'Cuenta bloqueada temporalmente. Inténtalo más tarde.';
	exit;
}

$ok = false;
if ($cred['acceso'] === 'admin') {
	$ok = is_string($password) && password_verify($password, $cred['password_hash']);
} else {
	$ok = is_string($pin)
		&& preg_match('/^\d{6}$/', $pin) === 1
		&& password_verify($pin, $cred['pin_hash']);
}

if (!$ok) {
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

$destino = match ($cred['acceso']) {
	'admin'    => '/html/admin.html',
	'cajero'   => '/html/caja.html',
	'cocinero' => '/html/cocina.html',
};

header('HX-Redirect: ' . $destino);
