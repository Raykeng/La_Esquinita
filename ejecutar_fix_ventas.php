<?php
/**
 * SCRIPT DE EMERGENCIA - Arreglar tabla ventas
 * Ejecutar desde: http://localhost/La_Esquinita/ejecutar_fix_ventas.php
 */

require_once __DIR__ . '/config/db.php';

echo "<!DOCTYPE html>";
echo "<html><head><meta charset='UTF-8'><title>Fix Ventas</title></head><body>";
echo "<h1>🔧 Arreglando Base de Datos</h1>";

try {
    echo "<h2>Paso 1: Verificando tabla ventas actual</h2>";
    
    // Verificar si la tabla existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'ventas'");
    if ($stmt->rowCount() == 0) {
        echo "<p>❌ Tabla ventas no existe. Creándola...</p>";
        
        // Crear tabla completa
        $sql = "CREATE TABLE ventas (
            id INT AUTO_INCREMENT PRIMARY KEY,
            cliente_id INT DEFAULT 1,
            fecha_venta DATETIME DEFAULT CURRENT_TIMESTAMP,
            subtotal DECIMAL(10,2) DEFAULT 0.00,
            descuento_total DECIMAL(10,2) DEFAULT 0.00,
            total DECIMAL(10,2) NOT NULL,
            metodo_pago ENUM('efectivo', 'tarjeta') DEFAULT 'efectivo',
            monto_recibido DECIMAL(10,2) DEFAULT 0.00,
            cambio DECIMAL(10,2) DEFAULT 0.00,
            estado ENUM('completada', 'cancelada') DEFAULT 'completada',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        $pdo->exec($sql);
        echo "<p>✅ Tabla ventas creada</p>";
    } else {
        echo "<p>✅ Tabla ventas existe</p>";
        
        // Verificar columnas
        $stmt = $pdo->query("DESCRIBE ventas");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        echo "<p>Columnas actuales: " . implode(', ', $columns) . "</p>";
        
        // Agregar columnas faltantes una por una
        $columnasNecesarias = [
            'subtotal' => 'DECIMAL(10,2) DEFAULT 0.00',
            'descuento_total' => 'DECIMAL(10,2) DEFAULT 0.00', 
            'monto_recibido' => 'DECIMAL(10,2) DEFAULT 0.00',
            'cambio' => 'DECIMAL(10,2) DEFAULT 0.00'
        ];
        
        foreach ($columnasNecesarias as $columna => $tipo) {
            if (!in_array($columna, $columns)) {
                echo "<p>➕ Agregando columna: $columna</p>";
                try {
                    $pdo->exec("ALTER TABLE ventas ADD COLUMN $columna $tipo");
                    echo "<p>✅ Columna $columna agregada</p>";
                } catch (Exception $e) {
                    echo "<p>⚠️ Error agregando $columna: " . $e->getMessage() . "</p>";
                }
            } else {
                echo "<p>✅ Columna $columna ya existe</p>";
            }
        }
    }
    
    echo "<h2>Paso 2: Creando tabla venta_detalles</h2>";
    
    $stmt = $pdo->query("SHOW TABLES LIKE 'venta_detalles'");
    if ($stmt->rowCount() == 0) {
        $sql = "CREATE TABLE venta_detalles (
            id INT AUTO_INCREMENT PRIMARY KEY,
            venta_id INT NOT NULL,
            producto_id INT NOT NULL,
            cantidad INT NOT NULL,
            precio_unitario DECIMAL(10,2) NOT NULL,
            subtotal DECIMAL(10,2) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (venta_id) REFERENCES ventas(id) ON DELETE CASCADE
        )";
        
        $pdo->exec($sql);
        echo "<p>✅ Tabla venta_detalles creada</p>";
    } else {
        echo "<p>✅ Tabla venta_detalles ya existe</p>";
    }
    
    echo "<h2>Paso 3: Creando tabla clientes</h2>";
    
    $stmt = $pdo->query("SHOW TABLES LIKE 'clientes'");
    if ($stmt->rowCount() == 0) {
        $sql = "CREATE TABLE clientes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(100) NOT NULL DEFAULT 'Público',
            apellido VARCHAR(100) DEFAULT 'General',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        $pdo->exec($sql);
        echo "<p>✅ Tabla clientes creada</p>";
        
        // Insertar cliente por defecto
        $pdo->exec("INSERT INTO clientes (id, nombre, apellido) VALUES (1, 'Público', 'General')");
        echo "<p>✅ Cliente por defecto insertado</p>";
    } else {
        echo "<p>✅ Tabla clientes ya existe</p>";
    }
    
    echo "<h2>Paso 4: Verificación final</h2>";
    
    // Mostrar estructura final de ventas
    $stmt = $pdo->query("DESCRIBE ventas");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
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
    
    if ($tieneDescuentoTotal) {
        echo "<h2>🎉 ¡ÉXITO TOTAL!</h2>";
        echo "<p style='color: green; font-size: 18px;'>✅ La columna 'descuento_total' existe correctamente</p>";
        echo "<p style='color: green; font-size: 18px;'>✅ Todas las tablas están listas</p>";
        echo "<p style='color: green; font-size: 18px;'>🚀 El sistema POS ya debería funcionar</p>";
        
        echo "<p><strong><a href='index.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔄 PROBAR SISTEMA POS</a></strong></p>";
    } else {
        echo "<h2>❌ Aún hay problemas</h2>";
        echo "<p style='color: red;'>La columna descuento_total no se pudo crear</p>";
    }
    
} catch (Exception $e) {
    echo "<h2>💥 Error:</h2>";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
}

echo "</body></html>";
?>