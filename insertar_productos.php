<?php
// insertar_productos.php - Insertar productos de prueba
require_once 'config/db.php';

echo "<h2>🛒 Insertando Productos de Prueba</h2>";

try {
    // 1. Primero insertar categorías si no existen
    echo "<h3>1. Insertando Categorías...</h3>";
    $categorias = [
        [1, 'Bebidas', 'Refrescos, jugos, agua'],
        [2, 'Abarrotes', 'Arroz, frijol, azúcar, aceite'],
        [3, 'Frescos', 'Frutas, verduras, perecederos'],
        [4, 'Lácteos', 'Leche, queso, crema'],
        [5, 'Limpieza', 'Jabón, cloro, detergente']
    ];
    
    foreach ($categorias as $cat) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO categorias (id, nombre, descripcion) VALUES (?, ?, ?)");
        $stmt->execute($cat);
        echo "✅ Categoría: {$cat[1]}<br>";
    }
    
    // 2. Insertar productos de prueba
    echo "<h3>2. Insertando Productos...</h3>";
    $productos = [
        // Bebidas
        [1, '7501234567890', 'Coca Cola 600ml', 1, 6.00, 8.00, 50, 10, 'pieza'],
        [2, '7501234567891', 'Pepsi 600ml', 1, 5.50, 7.50, 30, 10, 'pieza'],
        [3, '7501234567892', 'Agua Pura 1L', 1, 2.00, 3.00, 100, 20, 'pieza'],
        [4, '7501234567893', 'Jugo Jumex Mango', 1, 4.00, 6.00, 25, 5, 'pieza'],
        [5, '7501234567894', 'Gatorade Azul', 1, 8.00, 12.00, 20, 5, 'pieza'],
        
        // Abarrotes
        [6, '7501234567895', 'Arroz Blanco 1lb', 2, 3.00, 4.50, 40, 10, 'pieza'],
        [7, '7501234567896', 'Frijol Negro 1lb', 2, 4.50, 6.00, 30, 8, 'pieza'],
        [8, '7501234567897', 'Azúcar Blanca 1lb', 2, 2.50, 3.50, 25, 5, 'pieza'],
        [9, '7501234567898', 'Aceite Vegetal 1L', 2, 12.00, 15.00, 15, 3, 'pieza'],
        [10, '7501234567899', 'Sal de Mesa 1lb', 2, 1.50, 2.00, 50, 10, 'pieza'],
        
        // Frescos
        [11, '7501234567900', 'Tomate Rojo 1lb', 3, 3.00, 5.00, 20, 5, 'pieza'],
        [12, '7501234567901', 'Cebolla Blanca 1lb', 3, 2.50, 4.00, 15, 3, 'pieza'],
        [13, '7501234567902', 'Plátano Maduro 1lb', 3, 2.00, 3.50, 25, 5, 'pieza'],
        
        // Lácteos
        [14, '7501234567903', 'Leche Entera 1L', 4, 8.00, 12.00, 30, 5, 'pieza'],
        [15, '7501234567904', 'Queso Fresco 1lb', 4, 15.00, 20.00, 10, 2, 'pieza'],
        
        // Limpieza
        [16, '7501234567905', 'Jabón en Polvo 1kg', 5, 8.00, 12.00, 20, 3, 'pieza'],
        [17, '7501234567906', 'Cloro 1L', 5, 6.00, 9.00, 15, 3, 'pieza']
    ];
    
    foreach ($productos as $prod) {
        $stmt = $pdo->prepare("
            INSERT IGNORE INTO productos 
            (id, codigo_barras, nombre, categoria_id, precio_compra, precio_venta, stock_actual, stock_minimo, unidad_medida, estado) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'activo')
        ");
        $stmt->execute($prod);
        echo "✅ Producto: {$prod[2]} - Q{$prod[5]} (Stock: {$prod[6]})<br>";
    }
    
    // 3. Verificar que se insertaron
    $stmt = $pdo->query("SELECT COUNT(*) FROM productos");
    $total = $stmt->fetchColumn();
    
    echo "<h3>3. ✅ Resultado Final</h3>";
    echo "<p style='color: green; font-size: 18px;'><strong>¡Éxito! Se insertaron productos correctamente.</strong></p>";
    echo "<p><strong>Total de productos en la base de datos:</strong> $total</p>";
    
    echo "<hr>";
    echo "<h3>🎯 Próximos Pasos:</h3>";
    echo "<ol>";
    echo "<li><strong>Recarga el POS:</strong> <a href='index.php?vista=pos' target='_blank'>Abrir POS</a></li>";
    echo "<li><strong>Verifica que aparezcan los productos</strong></li>";
    echo "<li><strong>Elimina este archivo</strong> cuando todo funcione</li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'><strong>❌ Error:</strong> " . $e->getMessage() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
a { color: #007bff; text-decoration: none; padding: 5px 10px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 3px; }
a:hover { background: #007bff; color: white; }
</style>