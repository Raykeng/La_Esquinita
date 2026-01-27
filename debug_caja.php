<?php
// Script de debug para verificar el sistema de caja
require_once 'config/db.php';

echo "<h2>🔍 Debug del Sistema de Caja</h2>";

try {
    // 1. Verificar conexión
    echo "<h3>1. Conexión a Base de Datos</h3>";
    if ($pdo) {
        echo "✅ Conexión exitosa<br>";
    } else {
        echo "❌ Error de conexión<br>";
        exit;
    }
    
    // 2. Crear las tablas necesarias
    echo "<h3>2. Creando Tablas Necesarias</h3>";
    
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
    
    // 3. Verificar estructura de las tablas
    echo "<h3>3. Verificando Estructura de Tablas</h3>";
    
    // Verificar cierres_caja
    $result = $pdo->query("DESCRIBE cierres_caja");
    if ($result) {
        echo "<strong>Tabla cierres_caja:</strong><br>";
        while ($row = $result->fetch()) {
            echo "- {$row['Field']} ({$row['Type']})<br>";
        }
    }
    
    echo "<br>";
    
    // Verificar movimientos_caja
    $result = $pdo->query("DESCRIBE movimientos_caja");
    if ($result) {
        echo "<strong>Tabla movimientos_caja:</strong><br>";
        while ($row = $result->fetch()) {
            echo "- {$row['Field']} ({$row['Type']})<br>";
        }
    }
    
    // 4. Probar inserción de datos de prueba
    echo "<h3>4. Probando Inserción de Datos</h3>";
    
    // Insertar movimiento de prueba
    try {
        $sqlTest = "INSERT INTO movimientos_caja (tipo, concepto, monto, usuario) 
                   VALUES ('ingreso', 'Prueba de debug', 10.50, 'Debug')";
        
        if ($pdo->exec($sqlTest)) {
            echo "✅ Movimiento de prueba insertado correctamente<br>";
            
            // Verificar que se puede leer
            $sqlRead = "SELECT * FROM movimientos_caja WHERE concepto = 'Prueba de debug' ORDER BY id DESC LIMIT 1";
            $stmt = $pdo->prepare($sqlRead);
            $stmt->execute();
            $movimiento = $stmt->fetch();
            
            if ($movimiento) {
                echo "✅ Movimiento leído correctamente: {$movimiento['concepto']} - Q{$movimiento['monto']}<br>";
                echo "✅ Fecha del movimiento: {$movimiento['fecha']}<br>";
            }
        }
    } catch (Exception $e) {
        echo "❌ Error insertando movimiento de prueba: " . $e->getMessage() . "<br>";
    }
    
    // 5. Probar consulta con DATE()
    echo "<h3>5. Probando Consulta con DATE()</h3>";
    
    try {
        $fechaHoy = date('Y-m-d');
        $sqlDateTest = "SELECT * FROM movimientos_caja WHERE DATE(fecha) = ? ORDER BY fecha DESC";
        $stmt = $pdo->prepare($sqlDateTest);
        $stmt->execute([$fechaHoy]);
        $movimientos = $stmt->fetchAll();
        
        echo "✅ Consulta con DATE() exitosa. Encontrados: " . count($movimientos) . " movimientos de hoy<br>";
        
        foreach ($movimientos as $mov) {
            echo "- {$mov['concepto']}: Q{$mov['monto']} ({$mov['fecha']})<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Error en consulta con DATE(): " . $e->getMessage() . "<br>";
    }
    
    echo "<h3>✅ Debug Completado</h3>";
    echo "<p><strong>El sistema de caja debería funcionar correctamente ahora.</strong></p>";
    echo "<p><a href='index.php?vista=caja' class='btn btn-primary'>Probar Módulo de Caja</a></p>";
    
} catch (Exception $e) {
    echo "<h3>❌ Error en Debug</h3>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h2, h3 { color: #333; }
.btn { 
    display: inline-block; 
    padding: 10px 20px; 
    background: #007bff; 
    color: white; 
    text-decoration: none; 
    border-radius: 5px; 
}
</style>