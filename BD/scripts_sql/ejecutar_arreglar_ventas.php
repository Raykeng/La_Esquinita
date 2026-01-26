<?php
/**
 * Script para ejecutar automáticamente el arreglo de la tabla ventas
 * Ejecutar una sola vez desde el navegador
 */

require_once __DIR__ . '/../../config/db.php';

echo "<h2>🔧 Ejecutando Script: Arreglar Tabla Ventas</h2>";
echo "<pre>";

try {
    // Leer el script SQL
    $sqlFile = __DIR__ . '/arreglar_tabla_ventas.sql';
    
    if (!file_exists($sqlFile)) {
        throw new Exception("No se encontró el archivo SQL: $sqlFile");
    }
    
    $sql = file_get_contents($sqlFile);
    
    // Dividir en comandos individuales
    $commands = array_filter(array_map('trim', explode(';', $sql)));
    
    echo "📋 Ejecutando " . count($commands) . " comandos SQL...\n\n";
    
    foreach ($commands as $index => $command) {
        if (empty($command) || strpos($command, '--') === 0) {
            continue; // Saltar comentarios y líneas vacías
        }
        
        echo "🔄 Comando " . ($index + 1) . ": ";
        
        try {
            $stmt = $pdo->prepare($command);
            $stmt->execute();
            echo "✅ EXITOSO\n";
            
            // Si es un DESCRIBE, mostrar resultado
            if (stripos($command, 'DESCRIBE') !== false) {
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo "📊 Estructura de la tabla:\n";
                foreach ($result as $row) {
                    echo "   - {$row['Field']} ({$row['Type']})\n";
                }
                echo "\n";
            }
            
        } catch (Exception $e) {
            echo "⚠️  ERROR: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n🎉 Script completado!\n";
    echo "\n🔍 Verificando tabla ventas:\n";
    
    // Verificar estructura final
    $stmt = $pdo->query("DESCRIBE ventas");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "📋 Columnas en la tabla ventas:\n";
    foreach ($columns as $col) {
        echo "   ✓ {$col['Field']} - {$col['Type']}\n";
    }
    
    // Verificar si existe la columna descuento_total
    $hasDescuentoTotal = false;
    foreach ($columns as $col) {
        if ($col['Field'] === 'descuento_total') {
            $hasDescuentoTotal = true;
            break;
        }
    }
    
    if ($hasDescuentoTotal) {
        echo "\n✅ ¡PERFECTO! La columna 'descuento_total' ya existe.\n";
        echo "🚀 Ahora el sistema POS debería funcionar correctamente.\n";
    } else {
        echo "\n❌ ERROR: La columna 'descuento_total' aún no existe.\n";
    }
    
} catch (Exception $e) {
    echo "💥 ERROR GENERAL: " . $e->getMessage() . "\n";
}

echo "</pre>";
echo "<p><strong>🔄 <a href='../../index.php'>Volver al Sistema POS</a></strong></p>";
?>