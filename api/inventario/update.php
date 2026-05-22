<?php
// PUT /api/inventario/update.php
// Actualiza un producto y su inventario

session_start();

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../util/conec.php';

if (!isset($_SESSION['id_empleado'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'No autenticado.']);
    exit;
}

// Verificar que sea admin
if (!isset($_SESSION['acceso']) || $_SESSION['acceso'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'Acceso denegado.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Método no permitido.']);
    exit;
}

$db = new ConexionBD();
$conn = $db->getConexion();
if (!$conn) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Error de conexión a la base de datos.']);
    exit;
}

// Leer datos JSON
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Datos inválidos.']);
    exit;
}

// Validar campos requeridos
$id = (int)($data['id'] ?? 0);
$nombre = trim($data['nombre'] ?? '');
$id_categoria = (int)($data['id_categoria'] ?? 0);
$unidad = trim($data['unidad'] ?? 'pieza');
$precio_venta = (float)($data['precio_venta'] ?? 0);
$precio_compra = (float)($data['precio_compra'] ?? 0);
$stock = (int)($data['stock'] ?? 0);
$cantidad_min = (int)($data['cantidad_min'] ?? 5);

if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'ID de producto inválido.']);
    exit;
}

if ($nombre === '') {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'El nombre del producto es requerido.']);
    exit;
}

if ($precio_venta <= 0) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'El precio de venta debe ser mayor a 0.']);
    exit;
}

try {
    $conn->beginTransaction();

    // Verificar existencia del producto (rowCount() en UPDATE devuelve filas
    // cambiadas, no coincidentes: si no cambia ningún campo daría falso 404).
    $stmt = $conn->prepare("SELECT 1 FROM producto WHERE id = :id AND activo = 1");
    $stmt->execute([':id' => $id]);
    if (!$stmt->fetchColumn()) {
        $conn->rollBack();
        http_response_code(404);
        echo json_encode(['ok' => false, 'error' => 'Producto no encontrado.']);
        exit;
    }

    // Actualizar producto
    $stmt = $conn->prepare("
        UPDATE producto
        SET id_categoria = :id_categoria,
            nombre = :nombre,
            unidad = :unidad,
            precio_venta = :precio_venta,
            precio_compra = :precio_compra
        WHERE id = :id AND activo = 1
    ");

    $stmt->execute([
        ':id' => $id,
        ':id_categoria' => $id_categoria > 0 ? $id_categoria : null,
        ':nombre' => $nombre,
        ':unidad' => $unidad,
        ':precio_venta' => $precio_venta,
        ':precio_compra' => $precio_compra
    ]);
    
    // Upsert de inventario en una sola consulta (evita el doble-paso
    // UPDATE+INSERT que fallaba con duplicate key cuando los valores
    // de stock no cambiaban).
    $stmt = $conn->prepare("
        INSERT INTO inventario (id_producto, cantidad, cantidad_min)
        VALUES (:id_producto, :cantidad, :cantidad_min)
        ON DUPLICATE KEY UPDATE
            cantidad = VALUES(cantidad),
            cantidad_min = VALUES(cantidad_min)
    ");

    $stmt->execute([
        ':id_producto' => $id,
        ':cantidad' => $stock,
        ':cantidad_min' => $cantidad_min
    ]);
    
    $conn->commit();
    
    echo json_encode([
        'ok' => true,
        'mensaje' => 'Producto actualizado exitosamente.'
    ]);
    
} catch (PDOException $e) {
    $conn->rollBack();
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Error al actualizar producto: ' . $e->getMessage()]);
}
