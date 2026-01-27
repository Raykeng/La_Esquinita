<?php
// Test script para verificar funcionalidad de caja
require_once 'config/db.php';

echo "<h2>Test de Funcionalidad de Caja</h2>";

try {
    // Test 1: Verificar conexión a BD
    echo "<h3>1. Conexión a Base de Datos</h3>";
    if ($pdo) {
        echo "✅ Conexión exitosa<br>";
    } else {
        echo "❌ Error de conexión<br>";
        exit;
    }
    
    // Test 2: Verificar tablas necesarias
    echo "<h3>2. Verificación de Tablas</h3>";
    
    $tablas = ['ventas', 'cierres_caja', 'movimientos_caja'];
    foreach ($tablas as $tabla) {
        $sql = "SHOW TABLES LIKE '$tabla'";
        $result = $pdo->query($sql);
        if ($result->rowCount() > 0) {
            echo "✅ Tabla '$tabla' existe<br>";
        } else {
            echo "⚠️ Tabla '$tabla' no existe - se creará automáticamente<br>";
        }
    }
    
    // Test 3: Verificar datos de ventas del día
    echo "<h3>3. Datos de Ventas del Día</h3>";
    $fechaHoy = date('Y-m-d');
    
    $sqlVentas = "SELECT 
                    COUNT(*) as num_ventas,
                    COALESCE(SUM(total), 0) as total_ventas,
                    COALESCE(SUM(CASE WHEN metodo_pago = 'efectivo' THEN total ELSE 0 END), 0) as total_efectivo,
                    COALESCE(SUM(CASE WHEN metodo_pago = 'tarjeta' THEN total ELSE 0 END), 0) as total_tarjeta,
                    COALESCE(SUM(CASE WHEN metodo_pago = 'vales' THEN total ELSE 0 END), 0) as total_vales
                  FROM ventas 
                  WHERE DATE(fecha_venta) = ? AND estado = 'completada'";
    
    $stmt = $pdo->prepare($sqlVentas);
    $stmt->execute([$fechaHoy]);
    $resumen = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "Número de ventas: " . $resumen['num_ventas'] . "<br>";
    echo "Total ventas: Q " . number_format($resumen['total_ventas'], 2) . "<br>";
    echo "Total efectivo: Q " . number_format($resumen['total_efectivo'], 2) . "<br>";
    echo "Total tarjeta: Q " . number_format($resumen['total_tarjeta'], 2) . "<br>";
    echo "Total vales: Q " . number_format($resumen['total_vales'], 2) . "<br>";
    
    // Test 4: Crear tablas si no existen
    echo "<h3>4. Creación de Tablas Faltantes</h3>";
    
    // Crear tabla cierres_caja
    $sqlCierres = "CREATE TABLE IF NOT EXISTS cierres_caja (
        id INT PRIMARY KEY AUTO_INCREMENT,
        fecha_cierre DATE NOT NULL,
        turno ENUM('matutino', 'vespertino', 'nocturno', 'Diurno') DEFAULT 'Diurno',
        total_ventas DECIMAL(10,2) NOT NULL DEFAULT 0,
        total_efectivo DECIMAL(10,2) NOT NULL DEFAULT 0,
        total_tarjeta DECIMAL(10,2) NOT NULL DEFAULT 0,
        total_vales DECIMAL(10,2) NOT NULL DEFAULT 0,
        ingresos_adicionales DECIMAL(10,2) DEFAULT 0,
        egresos DECIMAL(10,2) DEFAULT 0,
        total_final DECIMAL(10,2) NOT NULL DEFAULT 0,
        diferencia DECIMAL(10,2) DEFAULT 0,
        usuario VARCHAR(50) NOT NULL,
        detalles JSON,
        notas TEXT,
        estado ENUM('abierto', 'cerrado') DEFAULT 'cerrado',
        fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if ($pdo->exec($sqlCierres) !== false) {
        echo "✅ Tabla 'cierres_caja' creada/verificada<br>";
    } else {
        echo "❌ Error creando tabla 'cierres_caja'<br>";
    }
    
    // Crear tabla movimientos_caja
    $sqlMovimientos = "CREATE TABLE IF NOT EXISTS movimientos_caja (
        id INT PRIMARY KEY AUTO_INCREMENT,
        tipo ENUM('ingreso', 'egreso') NOT NULL,
        concepto VARCHAR(255) NOT NULL,
        monto DECIMAL(10,2) NOT NULL,
        usuario VARCHAR(50) NOT NULL,
        fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if ($pdo->exec($sqlMovimientos) !== false) {
        echo "✅ Tabla 'movimientos_caja' creada/verificada<br>";
    } else {
        echo "❌ Error creando tabla 'movimientos_caja'<br>";
    }
    
    // Test 5: Insertar datos de prueba
    echo "<h3>5. Datos de Prueba</h3>";
    
    // Insertar movimiento de prueba
    $sqlInsertMovimiento = "INSERT INTO movimientos_caja (tipo, concepto, monto, usuario) 
                           VALUES ('ingreso', 'Venta especial de prueba', 25.50, 'Test')";
    
    if ($pdo->exec($sqlInsertMovimiento)) {
        echo "✅ Movimiento de prueba insertado<br>";
    } else {
        echo "⚠️ No se pudo insertar movimiento de prueba (puede que ya exista)<br>";
    }
    
    echo "<h3>✅ Test Completado</h3>";
    echo "<p><strong>La funcionalidad de caja está lista para usar.</strong></p>";
    echo "<p><a href='index.php?modulo=caja' class='btn btn-primary'>Ir al Módulo de Caja</a></p>";
    
} catch (Exception $e) {
    echo "<h3>❌ Error en el Test</h3>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>