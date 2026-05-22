<?php
// POST /api/ventas/marcar_listo.php
// Body: id=<int>
// Marca la venta como 'listo' (solo si estaba pendiente o en_proceso),
// devuelve la lista actualizada de pedidos activos.

session_start();

header('Content-Type: text/html; charset=utf-8');

require_once __DIR__ . '/../util/conec.php';
require_once __DIR__ . '/_pedidos_render.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	http_response_code(405);
	echo '<p class="pedidos-empty">Método no permitido.</p>';
	exit;
}

$rol = $_SESSION['acceso'] ?? null;
if (!in_array($rol, ['cocinero', 'admin'], true)) {
	http_response_code(401);
	echo '<p class="pedidos-empty">No autorizado.</p>';
	exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id <= 0) {
	http_response_code(400);
	echo '<p class="pedidos-empty">ID inválido.</p>';
	exit;
}

$db = new ConexionBD();
$conn = $db->getConexion();
if (!$conn) {
	http_response_code(500);
	echo '<p class="pedidos-empty">Error de conexión.</p>';
	exit;
}

$stmt = $conn->prepare(
	"UPDATE venta
	 SET estado = 'listo'
	 WHERE id = :id AND estado IN ('pendiente', 'en_proceso')"
);
$stmt->execute([':id' => $id]);

echo render_pedidos_pendientes($conn);
