<?php
// GET /api/inventario/stats.php
// Devuelve estadísticas del inventario

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

$db = new ConexionBD();
$conn = $db->getConexion();
if (!$conn) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Error de conexión a la base de datos.']);
    exit;
}

try {
    // Total de productos activos
    $stmt = $conn->query("SELECT COUNT(*) as total FROM producto WHERE activo = 1");
    $totalProductos = (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Total de categorías
    $stmt = $conn->query("SELECT COUNT(DISTINCT id_categoria) as total FROM producto WHERE activo = 1 AND id_categoria IS NOT NULL");
    $totalCategorias = (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Stock bajo (menos de 10 unidades pero mayor a 0)
    $stmt = $conn->query("
        SELECT COUNT(*) as total 
        FROM inventario i
        INNER JOIN producto p ON i.id_producto = p.id
        WHERE p.activo = 1 AND i.cantidad > 0 AND i.cantidad < 10
    ");
    $stockBajo = (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Sin stock (cantidad = 0)
    $stmt = $conn->query("
        SELECT COUNT(*) as total 
        FROM inventario i
        INNER JOIN producto p ON i.id_producto = p.id
        WHERE p.activo = 1 AND i.cantidad = 0
    ");
    $sinStock = (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    echo json_encode([
        'ok' => true,
        'stats' => [
            'total_productos' => $totalProductos,
            'total_categorias' => $totalCategorias,
            'stock_bajo' => $stockBajo,
            'sin_stock' => $sinStock
        ]
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Error al obtener estadísticas: ' . $e->getMessage()]);
}
