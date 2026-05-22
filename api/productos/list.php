<?php
// GET /api/productos/list.php
// Devuelve los productos activos para que la caja arme el ticket.

session_start();

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../util/conec.php';

if (!isset($_SESSION['id_empleado'])) {
	http_response_code(401);
	echo json_encode(['ok' => false, 'error' => 'No autenticado.']);
	exit;
}

$db = new ConexionBD();
$conn = $db->getConexion();
if (!$conn) {
	http_response_code(500);
	echo json_encode(['ok' => false, 'error' => 'Error de conexión.']);
	exit;
}

$stmt = $conn->prepare(
	"SELECT p.id, p.nombre, p.precio_venta AS precio
	 FROM producto p
	 WHERE p.activo = TRUE
	 ORDER BY p.nombre"
);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($rows as &$r) {
	$r['id']     = (int)$r['id'];
	$r['precio'] = (float)$r['precio'];
}
unset($r);

echo json_encode(['ok' => true, 'productos' => $rows]);
