<?php
// Verifica un PIN de 6 dígitos contra los hashes de cajero/cocinero.
// El PIN identifica al empleado: si exactamente un hash coincide, entra.

session_start();

require_once __DIR__ . '/util/conec.php';

header('Content-Type: text/html; charset=utf-8');

$pin = $_POST['pin'] ?? '';
if (!preg_match('/^\d{6}$/', $pin)) {
	echo 'PIN inválido.';
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
	"SELECT c.id, c.id_empleado, c.usuario, c.pin_hash, c.acceso,
	        c.locked_until
	 FROM credenciales c
	 JOIN empleados e ON e.id = c.id_empleado
	 WHERE c.acceso IN ('cajero','cocinero') AND e.activo = TRUE"
);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$matched = [];
foreach ($rows as $row) {
	if ($row['locked_until'] && strtotime($row['locked_until']) > time()) {
		continue;
	}
	if (password_verify($pin, $row['pin_hash'])) {
		$matched[] = $row;
	}
}

if (count($matched) === 0) {
	echo 'PIN inválido.';
	exit;
}

if (count($matched) > 1) {
	// Two employees share the same PIN; refuse and force admin intervention
	// so we don't silently log in the wrong person.
	echo 'PIN duplicado. Contacta al administrador.';
	exit;
}

$cred = $matched[0];

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
	'cajero'   => '/pollosBrothers/html/caja.html',
	'cocinero' => '/pollosBrothers/html/cocina.html',
};

header('HX-Redirect: ' . $destino);
