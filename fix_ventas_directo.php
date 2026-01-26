<?php
require_once __DIR__ . '/config/db.php';

echo "🔧 Arreglando tabla ventas directamente...\n\n";

try {
    // 1. Verificar estructura actual
    echo "1. Verificando estructura actual de ventas:\n";
    $stmt = $pdo->query("DESCRIBE ventas");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $col) {
        echo "   - {$col['Field']} ({$col['Type']})\n";
    }
    
    // 2. Verificar foreign keys problemáticas
    echo "\n2. Verificando foreign keys:\n";
    $stmt = $pdo->query("
        SELECT CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME 
        FROM information_schema.KEY_COLUMN_USAGE 
        WHERE TABLE_SCHEMA = 'la_esquinita' 
        AND TABLE_NAME = 'ventas' 
        AND REFERENCED_TABLE_NAME IS NOT NULL
    ");
    $foreignKeys = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($foreignKeys as $fk) {
        echo "   - {$fk['CONSTRAINT_NAME']}: {$fk['COLUMN_NAME']} -> {$fk['REFERENCED_TABLE_NAME']}.{$fk['REFERENCED_COLUMN_NAME']}\n";
        
        // Si referencia a usuarios, eliminar la foreign key
        if ($fk['REFERENCED_TABLE_NAME'] === 'usuarios') {
            echo "   ❌ Eliminando foreign key problemática: {$fk['CONSTRAINT_NAME']}\n";
            $pdo->exec("ALTER TABLE ventas DROP FOREIGN KEY {$fk['CONSTRAINT_NAME']}");
            echo "   ✅ Foreign key eliminada\n";
        }
    }
    
    // 3. Agregar columnas faltantes si no existen
    echo "\n3. Verificando columnas necesarias:\n";
    $columnasNecesarias = [
        'descuento_total' => 'DECIMAL(10,2) DEFAULT 0.00',
        'subtotal' => 'DECIMAL(10,2) DEFAULT 0.00',
        'monto_recibido' => 'DECIMAL(10,2) DEFAULT 0.00',
        'cambio' => 'DECIMAL(10,2) DEFAULT 0.00'
    ];
    
    $columnasExistentes = array_column($columns, 'Field');
    
    foreach ($columnasNecesarias as $columna => $tipo) {
        if (!in_array($columna, $columnasExistentes)) {
            echo "   ➕ Agregando columna: $columna\n";
            $pdo->exec("ALTER TABLE ventas ADD COLUMN $columna $tipo");
            echo "   ✅ Columna $columna agregada\n";
        } else {
            echo "   ✅ Columna $columna ya existe\n";
        }
    }
    
    // 4. Verificación final
    echo "\n4. Verificación final:\n";
    $stmt = $pdo->query("DESCRIBE ventas");
    $columnasFinales = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $todasExisten = true;
    foreach (array_keys($columnasNecesarias) as $columna) {
        if (in_array($columna, $columnasFinales)) {
            echo "   ✅ $columna: OK\n";
        } else {
            echo "   ❌ $columna: FALTA\n";
            $todasExisten = false;
        }
    }
    
    if ($todasExisten) {
        echo "\n🎉 ¡ÉXITO! Tabla ventas arreglada correctamente\n";
        echo "🚀 El sistema de pagos ya debería funcionar\n";
    } else {
        echo "\n❌ Aún faltan columnas\n";
    }
    
} catch (Exception $e) {
    echo "💥 Error: " . $e->getMessage() . "\n";
}
?>