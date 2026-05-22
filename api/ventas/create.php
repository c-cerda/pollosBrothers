<?php
// POST /api/ventas/create.php
// Body (JSON): { items: [{id_producto, cantidad}, ...], metodo_pago, cliente? }
// Crea una venta atómica: descripcionVenta + descuento de inventario +
// movimiento_inventario. Precios se toman de la BD para evitar manipulación
// desde el cliente.

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

$rol = $_SESSION['acceso'] ?? null;
$id_empleado = $_SESSION['id_empleado'] ?? null;

if (!$id_empleado || !in_array($rol, ['cajero', 'admin'], true)) {
	fail(401, 'No autenticado.');
}

$body = json_decode(file_get_contents('php://input'), true);
if (!is_array($body)) {
	fail(400, 'Cuerpo inválido.');
}

$items   = $body['items']        ?? null;
$metodo  = $body['metodo_pago']  ?? null;
$cliente = isset($body['cliente']) ? trim((string)$body['cliente']) : null;
if ($cliente === '') $cliente = null;

if (!is_array($items) || count($items) === 0) {
	fail(400, 'El ticket está vacío.');
}
if (!in_array($metodo, ['efectivo', 'tarjeta', 'transferencia'], true)) {
	fail(400, 'Método de pago inválido.');
}

$cantidad_por_producto = [];
foreach ($items as $it) {
	$idp = isset($it['id_producto']) ? (int)$it['id_producto'] : 0;
	$qty = isset($it['cantidad'])    ? (int)$it['cantidad']    : 0;
	if ($idp <= 0 || $qty <= 0) {
		fail(400, 'Items inválidos.');
	}
	$cantidad_por_producto[$idp] = ($cantidad_por_producto[$idp] ?? 0) + $qty;
}

$db = new ConexionBD();
$conn = $db->getConexion();
if (!$conn) {
	fail(500, 'Error de conexión.');
}

try {
	$conn->beginTransaction();

	$ids = array_keys($cantidad_por_producto);
	$placeholders = implode(',', array_fill(0, count($ids), '?'));

	$stmt = $conn->prepare(
		"SELECT p.id, p.nombre, p.precio_venta, p.activo,
		        COALESCE(i.cantidad, 0) AS stock
		 FROM producto p
		 LEFT JOIN inventario i ON i.id_producto = p.id
		 WHERE p.id IN ($placeholders)
		 FOR UPDATE"
	);
	$stmt->execute($ids);
	$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

	if (count($productos) !== count($ids)) {
		throw new RuntimeException('Producto no encontrado.');
	}

	$total = 0.0;
	$detalles = [];

	foreach ($productos as $p) {
		if (!$p['activo']) {
			throw new RuntimeException("Producto '{$p['nombre']}' no está activo.");
		}
		$qty = $cantidad_por_producto[(int)$p['id']];
		if ((int)$p['stock'] < $qty) {
			throw new RuntimeException(
				"Stock insuficiente: {$p['nombre']} (disponible: {$p['stock']})."
			);
		}
		$precio = (float)$p['precio_venta'];
		$sub    = $precio * $qty;
		$total += $sub;
		$detalles[] = [
			'id_producto'     => (int)$p['id'],
			'cantidad'        => $qty,
			'precio_unitario' => $precio,
			'subtotal'        => $sub,
		];
	}

	$stmt = $conn->prepare(
		"INSERT INTO venta (id_empleado, cliente, estado, metodo_pago, total)
		 VALUES (:id_empleado, :cliente, 'pendiente', :metodo, :total)"
	);
	$stmt->execute([
		':id_empleado' => $id_empleado,
		':cliente'     => $cliente,
		':metodo'      => $metodo,
		':total'       => $total,
	]);
	$id_venta = (int)$conn->lastInsertId();

	$stmtDesc = $conn->prepare(
		"INSERT INTO descripcionVenta
		   (id_venta, id_producto, cantidad, precio_unitario, subtotal)
		 VALUES (:id_venta, :id_producto, :cantidad, :precio_unitario, :subtotal)"
	);
	$stmtInv = $conn->prepare(
		"UPDATE inventario SET cantidad = cantidad - :qty WHERE id_producto = :id"
	);
	$stmtMov = $conn->prepare(
		"INSERT INTO movimiento_inventario (id_producto, tipo, cantidad, id_venta)
		 VALUES (:id, 'venta', :qty, :id_venta)"
	);

	foreach ($detalles as $d) {
		$stmtDesc->execute([
			':id_venta'        => $id_venta,
			':id_producto'     => $d['id_producto'],
			':cantidad'        => $d['cantidad'],
			':precio_unitario' => $d['precio_unitario'],
			':subtotal'        => $d['subtotal'],
		]);
		$stmtInv->execute([':qty' => $d['cantidad'], ':id' => $d['id_producto']]);
		$stmtMov->execute([
			':id'       => $d['id_producto'],
			':qty'      => $d['cantidad'],
			':id_venta' => $id_venta,
		]);
	}

	$conn->commit();

	echo json_encode([
		'ok'    => true,
		'id'    => $id_venta,
		'total' => $total,
	]);

} catch (Throwable $e) {
	if ($conn->inTransaction()) {
		$conn->rollBack();
	}
	$msg = ($e instanceof RuntimeException) ? $e->getMessage() : 'Error al procesar la venta.';
	fail(400, $msg);
}
