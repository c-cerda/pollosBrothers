<?php
// POST /api/inventario/create.php
// Crea un nuevo producto con su inventario inicial

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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
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
$nombre = trim($data['nombre'] ?? '');
$id_categoria = (int)($data['id_categoria'] ?? 0);
$unidad = trim($data['unidad'] ?? 'pieza');
$precio_venta = (float)($data['precio_venta'] ?? 0);
$precio_compra = (float)($data['precio_compra'] ?? 0);
$stock_inicial = (int)($data['stock'] ?? 0);
$cantidad_min = (int)($data['cantidad_min'] ?? 5);

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
    
    // Insertar producto
    $stmt = $conn->prepare("
        INSERT INTO producto (id_categoria, nombre, unidad, precio_venta, precio_compra, activo)
        VALUES (:id_categoria, :nombre, :unidad, :precio_venta, :precio_compra, 1)
    ");
    
    $stmt->execute([
        ':id_categoria' => $id_categoria > 0 ? $id_categoria : null,
        ':nombre' => $nombre,
        ':unidad' => $unidad,
        ':precio_venta' => $precio_venta,
        ':precio_compra' => $precio_compra
    ]);
    
    $id_producto = (int)$conn->lastInsertId();
    
    // Insertar en inventario
    $stmt = $conn->prepare("
        INSERT INTO inventario (id_producto, cantidad, cantidad_min)
        VALUES (:id_producto, :cantidad, :cantidad_min)
    ");
    
    $stmt->execute([
        ':id_producto' => $id_producto,
        ':cantidad' => $stock_inicial,
        ':cantidad_min' => $cantidad_min
    ]);
    
    $conn->commit();
    
    echo json_encode([
        'ok' => true,
        'mensaje' => 'Producto creado exitosamente.',
        'id' => $id_producto
    ]);
    
} catch (PDOException $e) {
    $conn->rollBack();
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Error al crear producto: ' . $e->getMessage()]);
}
