<?php
// GET /api/ventas/list.php
// Devuelve los pedidos activos (pendiente / en_proceso) como fragmento HTML
// para que cocina.html los inserte vía htmx.

session_start();

header('Content-Type: text/html; charset=utf-8');

require_once __DIR__ . '/../util/conec.php';
require_once __DIR__ . '/_pedidos_render.php';

if (!isset($_SESSION['id_empleado'])) {
	http_response_code(401);
	echo '<p class="pedidos-empty">Sesión expirada.</p>';
	exit;
}

$db = new ConexionBD();
$conn = $db->getConexion();
if (!$conn) {
	http_response_code(500);
	echo '<p class="pedidos-empty">Error de conexión.</p>';
	exit;
}

echo render_pedidos_pendientes($conn);
