<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test de Conexión</title>
</head>
<body>
    <h1>Prueba de Conexión a Base de Datos</h1>
    <?php
    require_once __DIR__ . '/api/util/conec.php';
    
    try {
        $db = new ConexionBD();
        $conn = $db->getConexion();
        
        if (!$conn) {
            echo "<p style='color: red;'>❌ Error de conexión: " . $db->getMensaje() . "</p>";
            exit;
        }
        
        echo "<p style='color: green;'>✅ Conexión exitosa a la base de datos</p>";
        
        // Probar consulta de productos
        $stmt = $conn->query("SELECT COUNT(*) as total FROM producto");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<p>📦 Total de productos: <strong>" . $result['total'] . "</strong></p>";
        
        // Probar consulta de inventario
        $stmt = $conn->query("SELECT COUNT(*) as total FROM inventario");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<p>📊 Total de registros en inventario: <strong>" . $result['total'] . "</strong></p>";
        
        // Probar consulta de categorías
        $stmt = $conn->query("SELECT COUNT(*) as total FROM categorias");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<p>🏷️ Total de categorías: <strong>" . $result['total'] . "</strong></p>";
        
        echo "<hr>";
        echo "<h2>Muestra de Productos</h2>";
        $stmt = $conn->query("SELECT p.nombre, c.nombre as categoria, i.cantidad as stock FROM producto p LEFT JOIN categorias c ON p.id_categoria = c.id LEFT JOIN inventario i ON p.id = i.id_producto LIMIT 5");
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>Producto</th><th>Categoría</th><th>Stock</th></tr>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
            echo "<td>" . htmlspecialchars($row['categoria'] ?? '-') . "</td>";
            echo "<td>" . htmlspecialchars($row['stock'] ?? '0') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    }
    ?>
</body>
</html>
