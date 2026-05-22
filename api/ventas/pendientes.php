<?php
// GET /api/ventas/pendientes.php
// Devuelve el fragmento HTML con los pedidos activos (pendiente / en_proceso),
// pensado para el polling de cocina.html via htmx.

session_start();

header('Content-Type: text/html; charset=utf-8');

require_once __DIR__ . '/../util/conec.php';
require_once __DIR__ . '/_pedidos_render.php';

$rol = $_SESSION['acceso'] ?? null;
if (!in_array($rol, ['cocinero', 'admin'], true)) {
	http_response_code(401);
	echo '<p class="pedidos-empty">No autorizado.</p>';
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
