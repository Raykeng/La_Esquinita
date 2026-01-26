<?php
/**
 * SCRIPT FINAL - Crear tablas de ventas limpias
 * Ejecutar desde: http://localhost/La_Esquinita/ejecutar_ventas_limpio.php
 */

require_once __DIR__ . '/config/db.php';

echo "<!DOCTYPE html>";
echo "<html><head><meta charset='UTF-8'><title>Crear Ventas Limpio</title></head><body>";
echo "<h1>🧹 Creando Tablas de Ventas Limpias</h1>";

try {
    echo "<h2>Paso 1: Eliminando tablas existentes (si existen)</h2>";
    
    // Eliminar en orden correcto (por foreign keys)
    $tablasEliminar = ['venta_detalles', 'ventas', 'clientes'];
    
    foreach ($tablasEliminar as $tabla) {
        try {
            $pdo->exec("DROP TABLE IF EXISTS $tabla");
            echo "<p>🗑️ Tabla $tabla eliminada</p>";
        } catch (Exception $e) {
            echo "<p>⚠️ Error eliminando $tabla: " . $e->getMessage() . "</p>";
        }
    }
    
    echo "<h2>Paso 2: Creando tabla ventas</h2>";
    
    $sqlVentas = "CREATE TABLE ventas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        fecha_venta DATETIME DEFAULT CURRENT_TIMESTAMP,
        total DECIMAL(10,2) NOT NULL,
        metodo_pago ENUM('efectivo', 'tarjeta') DEFAULT 'efectivo',
        estado ENUM('completada', 'cancelada') DEFAULT 'completada',
        subtotal DECIMAL(10,2) DEFAULT 0.00,
        descuento_total DECIMAL(10,2) DEFAULT 0.00,
        monto_recibido DECIMAL(10,2) DEFAULT 0.00,
        cambio DECIMAL(10,2) DEFAULT 0.00,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sqlVentas);
    echo "<p>✅ Tabla ventas creada correctamente</p>";
    
    echo "<h2>Paso 3: Creando tabla venta_detalles</h2>";
    
    $sqlDetalles = "CREATE TABLE venta_detalles (
        id INT AUTO_INCREMENT PRIMARY KEY,
        venta_id INT NOT NULL,
        producto_id INT NOT NULL,
        cantidad INT NOT NULL,
        precio_unitario DECIMAL(10,2) NOT NULL,
        subtotal DECIMAL(10,2) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (venta_id) REFERENCES ventas(id) ON DELETE CASCADE
    )";
    
    $pdo->exec($sqlDetalles);
    echo "<p>✅ Tabla venta_detalles creada correctamente</p>";
    
    echo "<h2>Paso 4: Creando tabla clientes</h2>";
    
    $sqlClientes = "CREATE TABLE clientes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(100) NOT NULL DEFAULT 'Público',
        apellido VARCHAR(100) DEFAULT 'General',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sqlClientes);
    echo "<p>✅ Tabla clientes creada correctamente</p>";
    
    // Insertar cliente por defecto
    $pdo->exec("INSERT INTO clientes (id, nombre, apellido) VALUES (1, 'Público', 'General')");
    echo "<p>✅ Cliente por defecto insertado</p>";
    
    echo "<h2>Paso 5: Verificación final</h2>";
    
    // Verificar estructura de ventas
    $stmt = $pdo->query("DESCRIBE ventas");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>📋 Estructura tabla ventas:</h3>";
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Clave</th><th>Por defecto</th></tr>";
    
    $tieneDescuentoTotal = false;
    foreach ($columns as $col) {
        echo "<tr>";
        echo "<td>{$col['Field']}</td>";
        echo "<td>{$col['Type']}</td>";
        echo "<td>{$col['Null']}</td>";
        echo "<td>{$col['Key']}</td>";
        echo "<td>{$col['Default']}</td>";
        echo "</tr>";
        
        if ($col['Field'] === 'descuento_total') {
            $tieneDescuentoTotal = true;
        }
    }
    echo "</table>";
    
    // Verificar que las tablas existen
    $stmt = $pdo->query("SHOW TABLES");
    $tablas = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<h3>📋 Tablas en la base de datos:</h3>";
    echo "<ul>";
    foreach ($tablas as $tabla) {
        echo "<li>✅ $tabla</li>";
    }
    echo "</ul>";
    
    if ($tieneDescuentoTotal && in_array('ventas', $tablas) && in_array('venta_detalles', $tablas)) {
        echo "<h2>🎉 ¡PERFECTO! TODO LISTO</h2>";
        echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
        echo "<p style='color: #155724; font-size: 18px; margin: 0;'>✅ Todas las tablas creadas correctamente</p>";
        echo "<p style='color: #155724; font-size: 18px; margin: 5px 0 0 0;'>✅ La columna 'descuento_total' existe</p>";
        echo "<p style='color: #155724; font-size: 18px; margin: 5px 0 0 0;'>🚀 El sistema POS ya debería funcionar sin errores</p>";
        echo "</div>";
        
        echo "<p><strong><a href='index.php' style='background: #28a745; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; font-size: 18px;'>🚀 PROBAR SISTEMA POS AHORA</a></strong></p>";
    } else {
        echo "<h2>❌ Aún hay problemas</h2>";
        echo "<p style='color: red;'>Algo salió mal en la creación</p>";
    }
    
} catch (Exception $e) {
    echo "<h2>💥 Error:</h2>";
    echo "<p style='color: red; font-size: 16px;'>" . $e->getMessage() . "</p>";
    echo "<p>Detalles del error:</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "</body></html>";
?>