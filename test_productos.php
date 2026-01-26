<?php
// test_productos.php - Script para probar la conexión y productos
header('Content-Type: text/html; charset=utf-8');

echo "<h2>🔍 Diagnóstico del Sistema POS</h2>";

// 1. Probar conexión a la base de datos
echo "<h3>1. Conexión a Base de Datos</h3>";
try {
    require_once 'config/db.php';
    echo "✅ <strong>Conexión exitosa</strong> a la base de datos 'la_esquinita'<br>";
} catch (Exception $e) {
    echo "❌ <strong>Error de conexión:</strong> " . $e->getMessage() . "<br>";
    exit;
}

// 2. Verificar si existen las tablas
echo "<h3>2. Verificar Tablas</h3>";
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'productos'");
    if ($stmt->rowCount() > 0) {
        echo "✅ <strong>Tabla 'productos' existe</strong><br>";
    } else {
        echo "❌ <strong>Tabla 'productos' NO existe</strong><br>";
    }
    
    $stmt = $pdo->query("SHOW TABLES LIKE 'categorias'");
    if ($stmt->rowCount() > 0) {
        echo "✅ <strong>Tabla 'categorias' existe</strong><br>";
    } else {
        echo "❌ <strong>Tabla 'categorias' NO existe</strong><br>";
    }
} catch (Exception $e) {
    echo "❌ <strong>Error verificando tablas:</strong> " . $e->getMessage() . "<br>";
}

// 3. Contar productos
echo "<h3>3. Productos en la Base de Datos</h3>";
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM productos");
    $result = $stmt->fetch();
    $total = $result['total'];
    
    if ($total > 0) {
        echo "✅ <strong>Hay {$total} productos</strong> en la base de datos<br>";
        
        // Mostrar algunos productos
        $stmt = $pdo->query("SELECT id, nombre, precio_venta, stock_actual FROM productos LIMIT 5");
        $productos = $stmt->fetchAll();
        
        echo "<h4>Productos de ejemplo:</h4>";
        echo "<ul>";
        foreach ($productos as $producto) {
            echo "<li><strong>{$producto['nombre']}</strong> - Q{$producto['precio_venta']} (Stock: {$producto['stock_actual']})</li>";
        }
        echo "</ul>";
    } else {
        echo "❌ <strong>No hay productos</strong> en la base de datos<br>";
        echo "<p>💡 <strong>Solución:</strong> Necesitas insertar productos de prueba</p>";
    }
} catch (Exception $e) {
    echo "❌ <strong>Error consultando productos:</strong> " . $e->getMessage() . "<br>";
}

// 4. Probar la API directamente
echo "<h3>4. Probar API de Productos</h3>";
echo "<p><a href='api/productos.php' target='_blank'>🔗 Abrir API de productos</a> (debe mostrar JSON)</p>";

// 5. Insertar productos de prueba si no hay
if (isset($total) && $total == 0) {
    echo "<h3>5. Insertar Productos de Prueba</h3>";
    echo "<p>¿Quieres que inserte algunos productos de prueba?</p>";
    echo "<a href='?insertar=si' class='btn'>✅ Sí, insertar productos de prueba</a>";
    
    if (isset($_GET['insertar']) && $_GET['insertar'] == 'si') {
        try {
            // Insertar categorías primero
            $pdo->exec("INSERT IGNORE INTO categorias (id, nombre) VALUES 
                (1, 'Bebidas'), 
                (2, 'Abarrotes'), 
                (3, 'Frescos')");
            
            // Insertar productos de prueba
            $pdo->exec("INSERT IGNORE INTO productos (id, nombre, precio_venta, stock_actual, categoria_id, estado) VALUES 
                (1, 'Coca Cola 600ml', 8.00, 50, 1, 'activo'),
                (2, 'Pepsi 600ml', 7.50, 30, 1, 'activo'),
                (3, 'Agua Pura 1L', 3.00, 100, 1, 'activo'),
                (4, 'Arroz 1lb', 4.50, 25, 2, 'activo'),
                (5, 'Frijol Negro 1lb', 6.00, 20, 2, 'activo')");
            
            echo "<p style='color: green;'>✅ <strong>Productos de prueba insertados correctamente</strong></p>";
            echo "<p><a href='?'>🔄 Recargar diagnóstico</a></p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ <strong>Error insertando productos:</strong> " . $e->getMessage() . "</p>";
        }
    }
}

echo "<hr>";
echo "<p><strong>📋 Próximos pasos:</strong></p>";
echo "<ol>";
echo "<li>Si todo está ✅, recarga la página del POS</li>";
echo "<li>Si hay ❌, revisa los errores mostrados arriba</li>";
echo "<li>Elimina este archivo cuando todo funcione</li>";
echo "</ol>";
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
.btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 10px 0; }
.btn:hover { background: #0056b3; }
</style>