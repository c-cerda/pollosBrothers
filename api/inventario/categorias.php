<?php
// GET /api/inventario/categorias.php
// Devuelve la lista de categorías

session_start();

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '../util/conec.php';

if (!isset($_SESSION['id_empleado'])) {
	http_response_code(401);
	echo json_encode(['ok' => false, 'error' => 'No autenticado.']);
	exit;
}

// Verificar que sea admin
if (!isset($_SESSION['acceso']) || $_SESSION['acceso'] !== 'admin') {
	http_response_code(403);
	echo json_encode(['ok' => false, 'error' => 'Acceso denegado.']);
	exit;
}

$db = new ConexionBD();
$conn = $db->getConexion();
if (!$conn) {
	http_response_code(500);
	echo json_encode(['ok' => false, 'error' => 'Error de conexión a la base de datos.']);
	exit;
}

try {
	$stmt = $conn->query("SELECT id, nombre FROM categorias ORDER BY nombre");
	$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

	// Convertir ID a entero
	foreach ($categorias as &$c) {
		$c['id'] = (int)$c['id'];
	}
	unset($c);

	echo json_encode(['ok' => true, 'categorias' => $categorias]);
} catch (PDOException $e) {
	http_response_code(500);
	echo json_encode(['ok' => false, 'error' => 'Error al obtener categorías: ' . $e->getMessage()]);
}
