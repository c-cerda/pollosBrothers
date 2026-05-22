<?php
// POST /api/ventas/update_estado.php
// Body (JSON): { id: int, estado: string }
// Cambia el estado de una venta. Solo admin puede cancelar.

session_start();

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../util/conec.php';

function fail(int $code, string $msg): void {
    http_response_code($code);
    echo json_encode(['ok' => false, 'error' => $msg]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    fail(405, 'Método no permitido.');
}

$rol         = $_SESSION['acceso']      ?? null;
$id_empleado = $_SESSION['id_empleado'] ?? null;

if (!$id_empleado || !in_array($rol, ['cajero', 'admin'], true)) {
    fail(401, 'No autenticado.');
}

$body = json_decode(file_get_contents('php://input'), true);
if (!is_array($body)) {
    fail(400, 'Cuerpo inválido.');
}

$id_venta = isset($body['id']) ? (int)$body['id'] : 0;
$estado   = $body['estado'] ?? null;

$estados_validos = ['pendiente', 'en_proceso', 'listo', 'entregado', 'cancelado'];

if ($id_venta <= 0) {
    fail(400, 'ID de venta inválido.');
}
if (!in_array($estado, $estados_validos, true)) {
    fail(400, 'Estado inválido.');
}
// Solo admin puede cancelar
if ($estado === 'cancelado' && $rol !== 'admin') {
    fail(403, 'Solo el administrador puede cancelar pedidos.');
}

$db   = new ConexionBD();
$conn = $db->getConexion();
if (!$conn) {
    fail(500, 'Error de conexión.');
}

// Verificar que la venta existe
$stmtCheck = $conn->prepare("SELECT id, estado FROM venta WHERE id = :id");
$stmtCheck->execute([':id' => $id_venta]);
$venta = $stmtCheck->fetch(PDO::FETCH_ASSOC);

if (!$venta) {
    fail(404, 'Venta no encontrada.');
}

// No permitir reabrir una venta ya entregada o cancelada (solo admin)
$bloqueados = ['entregado', 'cancelado'];
if (in_array($venta['estado'], $bloqueados, true) && $rol !== 'admin') {
    fail(403, "No se puede modificar un pedido ya {$venta['estado']}.");
}

$stmt = $conn->prepare(
    "UPDATE venta SET estado = :estado WHERE id = :id"
);
$stmt->execute([':estado' => $estado, ':id' => $id_venta]);

echo json_encode(['ok' => true, 'id' => $id_venta, 'estado' => $estado]);
