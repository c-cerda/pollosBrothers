<?php
require_once __DIR__ . '/util/conec.php';

$db = new ConexionBD();
$conn = $db->getConexion();

if (!$conn) {
    http_response_code(500);
    echo '<div class="cards"><p>Error de conexión</p></div>';
    exit;
}

// Pedidos Pendientes de hoy
$stmt = $conn->prepare("
    SELECT COUNT(*) as count FROM venta 
    WHERE estado = 'pendiente' AND DATE(fecha) = CURDATE()
");
$stmt->execute();
$pendientes = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

// Órdenes del Día
$stmt = $conn->prepare("
    SELECT COUNT(*) as count FROM venta 
    WHERE DATE(fecha) = CURDATE()
");
$stmt->execute();
$ordenes = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

// Ingresos del Día
$stmt = $conn->prepare("
    SELECT COALESCE(SUM(total), 0) as total FROM venta 
    WHERE DATE(fecha) = CURDATE() AND estado IN ('listo', 'entregado')
");
$stmt->execute();
$ingresos = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

// Inventario Bajo (productos bajo cantidad mínima)
$stmt = $conn->prepare("
    SELECT COUNT(*) as count FROM inventario 
    WHERE cantidad < cantidad_min
");
$stmt->execute();
$bajo = $stmt->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;

?>
<div class="cards">
	<div class="card red">
		<p>Pedidos Pendientes</p>
		<h3><?php echo $pendientes; ?></h3>
	</div>
	<div class="card orange">
		<p>Órdenes del Día</p>
		<h3><?php echo $ordenes; ?></h3>
	</div>
	<div class="card yellow">
		<p>Ingresos del Día</p>
		<h3>$<?php echo number_format($ingresos, 2); ?></h3>
	</div>
	<div class="card blue">
		<p>Inventario Bajo</p>
		<h3><?php echo $bajo; ?></h3>
	</div>
</div>

