<?php
// GET /api/inventario/list.php
// Devuelve la lista de productos con su información de inventario

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

// Obtener parámetro de búsqueda si existe
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

try {
    $sql = "SELECT 
                p.id,
                p.nombre,
                c.nombre AS categoria,
                p.id_categoria,
                i.cantidad AS stock,
                i.cantidad_min,
                p.precio_venta,
                p.precio_compra,
                p.unidad,
                p.activo,
                CASE 
                    WHEN i.cantidad = 0 THEN 'sin_stock'
                    WHEN i.cantidad < i.cantidad_min THEN 'bajo'
                    ELSE 'disponible'
                END AS estado
            FROM producto p
            LEFT JOIN categorias c ON p.id_categoria = c.id
            LEFT JOIN inventario i ON p.id = i.id_producto
            WHERE p.activo = 1";
    
    if ($search !== '') {
        $sql .= " AND p.nombre LIKE :search";
    }
    
    $sql .= " ORDER BY p.nombre";
    
    $stmt = $conn->prepare($sql);
    
    if ($search !== '') {
        $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
    }
    
    $stmt->execute();
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Convertir tipos
    foreach ($productos as &$p) {
        $p['id'] = (int)$p['id'];
        $p['id_categoria'] = (int)$p['id_categoria'];
        $p['stock'] = (int)($p['stock'] ?? 0);
        $p['cantidad_min'] = (int)($p['cantidad_min'] ?? 0);
        $p['precio_venta'] = (float)$p['precio_venta'];
        $p['precio_compra'] = (float)$p['precio_compra'];
        $p['activo'] = (int)$p['activo'];
    }
    unset($p);
    
    echo json_encode(['ok' => true, 'productos' => $productos]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Error al obtener productos: ' . $e->getMessage()]);
}
