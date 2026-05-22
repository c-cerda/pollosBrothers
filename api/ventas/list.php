<?php
// GET /api/ventas/list.php
// Devuelve las ventas con sus items.
// Params opcionales: ?fecha=YYYY-MM-DD  ?estado=pendiente|en_proceso|listo|entregado|cancelado

session_start();

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../util/conec.php';

function fail(int $code, string $msg): void {
    http_response_code($code);
    echo json_encode(['ok' => false, 'error' => $msg]);
    exit;
}

$rol         = $_SESSION['acceso']      ?? null;
$id_empleado = $_SESSION['id_empleado'] ?? null;

if (!$id_empleado || !in_array($rol, ['cajero', 'admin'], true)) {
    fail(401, 'No autenticado.');
}

$db   = new ConexionBD();
$conn = $db->getConexion();
if (!$conn) {
    fail(500, 'Error de conexión.');
}

// ── Filtros ──────────────────────────────────────────────
$where  = ['1=1'];
$params = [];

// Filtro por fecha (día completo)
$fecha = $_GET['fecha'] ?? null;
if ($fecha && preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
    $where[]  = 'DATE(v.fecha) = :fecha';
    $params[':fecha'] = $fecha;
}

// Filtro por estado
$estados_validos = ['pendiente', 'en_proceso', 'listo', 'entregado', 'cancelado'];
$estado = $_GET['estado'] ?? null;
if ($estado && in_array($estado, $estados_validos, true)) {
    $where[]          = 'v.estado = :estado';
    $params[':estado'] = $estado;
}

$where_sql = implode(' AND ', $where);

// ── Consulta principal ───────────────────────────────────
$stmt = $conn->prepare(
    "SELECT v.id, v.cliente, v.estado, v.metodo_pago,
            v.total, v.fecha,
            CONCAT(e.nombre, ' ', e.apellido) AS cajero
     FROM venta v
     JOIN empleados e ON e.id = v.id_empleado
     WHERE $where_sql
     ORDER BY v.fecha DESC
     LIMIT 200"
);
$stmt->execute($params);
$ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$ventas) {
    echo json_encode(['ok' => true, 'ventas' => []]);
    exit;
}

// ── Items de cada venta ──────────────────────────────────
$ids          = array_column($ventas, 'id');
$placeholders = implode(',', array_fill(0, count($ids), '?'));

$stmtItems = $conn->prepare(
    "SELECT dv.id_venta, dv.cantidad, dv.precio_unitario, dv.subtotal,
            p.nombre
     FROM descripcionVenta dv
     JOIN producto p ON p.id = dv.id_producto
     WHERE dv.id_venta IN ($placeholders)"
);
$stmtItems->execute($ids);
$allItems = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

// Indexar items por id_venta
$itemsPorVenta = [];
foreach ($allItems as $it) {
    $itemsPorVenta[(int)$it['id_venta']][] = [
        'nombre'          => $it['nombre'],
        'cantidad'        => (int)$it['cantidad'],
        'precio_unitario' => (float)$it['precio_unitario'],
        'subtotal'        => (float)$it['subtotal'],
    ];
}

// ── Armar respuesta ──────────────────────────────────────
foreach ($ventas as &$v) {
    $v['id']    = (int)$v['id'];
    $v['total'] = (float)$v['total'];
    $v['items'] = $itemsPorVenta[$v['id']] ?? [];
}
unset($v);

echo json_encode(['ok' => true, 'ventas' => $ventas]);