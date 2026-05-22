<?php
require_once __DIR__ . '/util/conec.php';

$db = new ConexionBD();
$conn = $db->getConexion();

if (!$conn) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de conexión']);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

// Obtener ventas de los últimos 7 días agrupadas por día
$stmt = $conn->prepare("
    SELECT 
        DATE(fecha) as fecha,
        SUM(total) as total
    FROM venta
    WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
        AND estado IN ('listo', 'entregado')
    GROUP BY DATE(fecha)
    ORDER BY fecha ASC
");
$stmt->execute();
$ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Crear array con todos los días de la semana (incluso los que no tienen ventas)
$hoy = new DateTime();
$dias = [];
$totales = [];

for ($i = 6; $i >= 0; $i--) {
    $fecha = clone $hoy;
    $fecha->modify("-{$i} days");
    $fechaStr = $fecha->format('Y-m-d');
    $diaStr = $fecha->format('D'); // Mon, Tue, Wed, etc.
    $dias[] = $diaStr;
    
    $total = 0;
    foreach ($ventas as $venta) {
        if ($venta['fecha'] == $fechaStr) {
            $total = (float)$venta['total'];
            break;
        }
    }
    $totales[] = $total;
}

echo json_encode([
    'dias' => $dias,
    'totales' => $totales
]);
?>
