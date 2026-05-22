<?php
require_once __DIR__ . '/util/conec.php';

$db = new ConexionBD();
$conn = $db->getConexion();

if (!$conn) {
    http_response_code(500);
    echo '<div class="table"><p>Error de conexión</p></div>';
    exit;
}

$stmt = $conn->prepare("
    SELECT 
        v.id,
        COALESCE(v.cliente, 'Sin cliente') as cliente,
        v.estado,
        v.total,
        DATE_FORMAT(v.fecha, '%h:%i %p') as hora
    FROM venta v
    ORDER BY v.fecha DESC
    LIMIT 10
");
$stmt->execute();
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

function getStatusClass($estado) {
    return match($estado) {
        'pendiente' => 'pending',
        'en_proceso' => 'processing',
        'listo' => 'ready',
        'entregado' => 'done',
        'cancelado' => 'cancelled',
        default => 'pending'
    };
}

function getStatusLabel($estado) {
    return match($estado) {
        'pendiente' => 'Pendiente',
        'en_proceso' => 'En Proceso',
        'listo' => 'Listo',
        'entregado' => 'Entregado',
        'cancelado' => 'Cancelado',
        default => 'Desconocido'
    };
}
?>
<div class="table">
	<h3>Pedidos Recientes</h3>
	<table>
		<thead>
			<tr>
				<th>Cliente</th>
				<th>Estado</th>
				<th>Total</th>
				<th>Hora</th>
			</tr>
		</thead>
		<tbody>
			<?php if (empty($pedidos)): ?>
				<tr>
					<td colspan="4">No hay pedidos</td>
				</tr>
			<?php else: ?>
				<?php foreach ($pedidos as $pedido): ?>
					<tr>
						<td><?php echo htmlspecialchars($pedido['cliente']); ?></td>
						<td><span class="status <?php echo getStatusClass($pedido['estado']); ?>"><?php echo getStatusLabel($pedido['estado']); ?></span></td>
						<td>$<?php echo number_format($pedido['total'], 2); ?></td>
						<td><?php echo htmlspecialchars($pedido['hora']); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
</div>
