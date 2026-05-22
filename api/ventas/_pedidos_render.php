<?php
// Helper interno. Renderiza los pedidos activos (pendiente / en_proceso) como
// fragmento HTML para que las endpoints de list y marcar_listo devuelvan
// exactamente lo mismo.

function render_pedidos_pendientes(PDO $conn): string
{
	$sql = "SELECT v.id, v.fecha, v.cliente, v.estado,
	               dv.cantidad, p.nombre AS producto_nombre
	        FROM venta v
	        LEFT JOIN descripcionVenta dv ON dv.id_venta = v.id
	        LEFT JOIN producto p ON p.id = dv.id_producto
	        WHERE v.estado IN ('pendiente', 'en_proceso')
	        ORDER BY v.fecha ASC, v.id ASC, p.nombre ASC";

	$rows = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);

	$grouped = [];
	foreach ($rows as $r) {
		$vid = (int)$r['id'];
		if (!isset($grouped[$vid])) {
			$grouped[$vid] = [
				'id'      => $vid,
				'fecha'   => $r['fecha'],
				'cliente' => $r['cliente'],
				'estado'  => $r['estado'],
				'items'   => [],
			];
		}
		if ($r['producto_nombre'] !== null) {
			$grouped[$vid]['items'][] = [
				'nombre'   => $r['producto_nombre'],
				'cantidad' => (int)$r['cantidad'],
			];
		}
	}

	if (empty($grouped)) {
		return '<p class="pedidos-empty">No hay pedidos pendientes.</p>';
	}

	ob_start();
	foreach ($grouped as $v):
		$id_str = str_pad((string)$v['id'], 3, '0', STR_PAD_LEFT);
		$hora   = $v['fecha'] ? date('h:i A', strtotime($v['fecha'])) : '';
?>
		<article class="pedido-card <?= htmlspecialchars($v['estado'], ENT_QUOTES, 'UTF-8') ?>">
			<div class="pedido-head">
				<span class="pedido-id">#<?= $id_str ?></span>
				<span class="pedido-time"><?= $hora ?></span>
			</div>

			<?php if ($v['cliente']): ?>
				<div class="pedido-cliente"><?= htmlspecialchars($v['cliente'], ENT_QUOTES, 'UTF-8') ?></div>
			<?php endif ?>

			<ul class="pedido-items">
				<?php foreach ($v['items'] as $it): ?>
					<li>
						<span class="qty"><?= $it['cantidad'] ?>×</span>
						<?= htmlspecialchars($it['nombre'], ENT_QUOTES, 'UTF-8') ?>
					</li>
				<?php endforeach ?>
			</ul>

			<div class="pedido-actions">
				<button type="button"
					class="btn pedido-listo-btn"
					hx-post="../api/ventas/marcar_listo.php"
					hx-vals='{"id": <?= $v['id'] ?>}'
					hx-target="#pedidos"
					hx-swap="innerHTML">
					Marcar como listo
				</button>
			</div>
		</article>
<?php endforeach;

	return ob_get_clean();
}
