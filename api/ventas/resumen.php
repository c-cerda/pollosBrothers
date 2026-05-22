<?php
// GET /api/ventas/resumen.php
// Devuelve: ingresos hoy/semana/mes, promedio por venta,
//           ventas por día (últimos 7 días), top productos del mes.

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

// ── Ingresos hoy ─────────────────────────────────────────
$stmt = $conn->prepare(
    "SELECT COALESCE(SUM(total), 0) AS total
     FROM venta
     WHERE DATE(fecha) = CURDATE()
       AND estado != 'cancelado'"
);
$stmt->execute();
$ingresos_hoy = (float)$stmt->fetchColumn();

// ── Ingresos semana (lunes a hoy) ────────────────────────
$stmt = $conn->prepare(
    "SELECT COALESCE(SUM(total), 0) AS total
     FROM venta
     WHERE fecha >= DATE(DATE_SUB(NOW(), INTERVAL WEEKDAY(NOW()) DAY))
       AND estado != 'cancelado'"
);
$stmt->execute();
$ingresos_semana = (float)$stmt->fetchColumn();

// ── Ingresos mes ─────────────────────────────────────────
$stmt = $conn->prepare(
    "SELECT COALESCE(SUM(total), 0) AS total
     FROM venta
     WHERE YEAR(fecha) = YEAR(NOW())
       AND MONTH(fecha) = MONTH(NOW())
       AND estado != 'cancelado'"
);
$stmt->execute();
$ingresos_mes = (float)$stmt->fetchColumn();

// ── Promedio por venta (mes actual) ──────────────────────
$stmt = $conn->prepare(
    "SELECT COALESCE(AVG(total), 0) AS promedio
     FROM venta
     WHERE YEAR(fecha) = YEAR(NOW())
       AND MONTH(fecha) = MONTH(NOW())
       AND estado != 'cancelado'"
);
$stmt->execute();
$promedio = (float)$stmt->fetchColumn();

// ── Ventas por día (últimos 7 días) ──────────────────────
$stmt = $conn->prepare(
    "SELECT DATE(fecha) AS dia, COALESCE(SUM(total), 0) AS total
     FROM venta
     WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
       AND estado != 'cancelado'
     GROUP BY DATE(fecha)
     ORDER BY dia ASC"
);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Rellenar días sin ventas con 0
$ventas_semana = [];
for ($i = 6; $i >= 0; $i--) {
    $dia = date('Y-m-d', strtotime("-$i days"));
    $ventas_semana[$dia] = 0.0;
}
foreach ($rows as $r) {
    $ventas_semana[$r['dia']] = (float)$r['total'];
}
$grafica = [];
foreach ($ventas_semana as $dia => $total) {
    $grafica[] = [
        'dia'   => date('D', strtotime($dia)), // Mon, Tue…
        'fecha' => $dia,
        'total' => $total,
    ];
}

// ── Top 5 productos más vendidos (mes actual) ────────────
$stmt = $conn->prepare(
    "SELECT p.nombre,
            SUM(dv.cantidad)        AS unidades,
            SUM(dv.subtotal)        AS total
     FROM descripcionVenta dv
     JOIN producto p         ON p.id       = dv.id_producto
     JOIN venta v            ON v.id       = dv.id_venta
     WHERE YEAR(v.fecha)  = YEAR(NOW())
       AND MONTH(v.fecha) = MONTH(NOW())
       AND v.estado != 'cancelado'
     GROUP BY p.id, p.nombre
     ORDER BY total DESC
     LIMIT 5"
);
$stmt->execute();
$top_productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($top_productos as &$tp) {
    $tp['unidades'] = (int)$tp['unidades'];
    $tp['total']    = (float)$tp['total'];
}
unset($tp);

echo json_encode([
    'ok'             => true,
    'ingresos_hoy'   => $ingresos_hoy,
    'ingresos_semana'=> $ingresos_semana,
    'ingresos_mes'   => $ingresos_mes,
    'promedio'       => $promedio,
    'grafica'        => $grafica,
    'top_productos'  => $top_productos,
]);
