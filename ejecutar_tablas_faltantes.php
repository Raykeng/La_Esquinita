<?php
/**
 * Ejecutor de script para crear tablas faltantes
 * Autor: Marlon
 * Sistema: La Esquinita POS
 */

require_once 'config/db.php';

echo "<h2>🚀 Creando Tablas Faltantes - La Esquinita</h2>";
echo "<hr>";

try {
    // Leer el script SQL
    $sqlScript = file_get_contents('BD/scripts_sql/crear_tablas_faltantes.sql');
    
    if (!$sqlScript) {
        throw new Exception('No se pudo leer el archivo SQL');
    }
    
    echo "<p>📄 <strong>Script cargado:</strong> BD/scripts_sql/crear_tablas_faltantes.sql</p>";
    
    // Dividir el script en comandos individuales
    $commands = array_filter(
        array_map('trim', explode(';', $sqlScript)),
        function($cmd) {
            return !empty($cmd) && !preg_match('/^(--|\/\*|\s*$)/', $cmd);
        }
    );
    
    echo "<p>📊 <strong>Comandos a ejecutar:</strong> " . count($commands) . "</p>";
    echo "<hr>";
    
    $ejecutados = 0;
    $errores = 0;
    
    foreach ($commands as $command) {
        $command = trim($command);
        if (empty($command)) continue;
        
        try {
            $stmt = $pdo->prepare($command);
            $stmt->execute();
            
            // Determinar el tipo de comando
            $tipo = 'COMANDO';
            if (stripos($command, 'CREATE TABLE') !== false) {
                preg_match('/CREATE TABLE.*?`?(\w+)`?/i', $command, $matches);
                $tabla = $matches[1] ?? 'desconocida';
                $tipo = "TABLA: $tabla";
            } elseif (stripos($command, 'INSERT') !== false) {
                $tipo = 'DATOS';
            } elseif (stripos($command, 'ALTER TABLE') !== false) {
                preg_match('/ALTER TABLE.*?`?(\w+)`?/i', $command, $matches);
                $tabla = $matches[1] ?? 'desconocida';
                $tipo = "MODIFICAR: $tabla";
            }
            
            echo "<p>✅ <strong>$tipo</strong> - Ejecutado correctamente</p>";
            $ejecutados++;
            
        } catch (PDOException $e) {
            echo "<p>⚠️ <strong>ADVERTENCIA:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
            $errores++;
        }
    }
    
    echo "<hr>";
    echo "<h3>📋 Resumen de Ejecución</h3>";
    echo "<ul>";
    echo "<li><strong>Comandos ejecutados:</strong> $ejecutados</li>";
    echo "<li><strong>Advertencias:</strong> $errores</li>";
    echo "</ul>";
    
    // Verificar tablas creadas
    echo "<h3>🗃️ Tablas en la Base de Datos</h3>";
    $stmt = $pdo->query("SHOW TABLES");
    $tablas = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<ul>";
    foreach ($tablas as $tabla) {
        // Contar registros
        try {
            $countStmt = $pdo->query("SELECT COUNT(*) FROM `$tabla`");
            $count = $countStmt->fetchColumn();
            echo "<li><strong>$tabla</strong> - $count registros</li>";
        } catch (Exception $e) {
            echo "<li><strong>$tabla</strong> - Error al contar registros</li>";
        }
    }
    echo "</ul>";
    
    echo "<hr>";
    echo "<h3>🎉 ¡Proceso Completado!</h3>";
    echo "<p><strong>Estado:</strong> Las tablas han sido creadas/verificadas exitosamente.</p>";
    echo "<p><strong>Siguiente paso:</strong> Puedes probar el sistema completo.</p>";
    
    // Botones de navegación
    echo "<div style='margin-top: 20px;'>";
    echo "<a href='index.php' class='btn btn-primary' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-right: 10px;'>🏠 Ir al Dashboard</a>";
    echo "<a href='index.php?vista=clientes' class='btn btn-success' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-right: 10px;'>👥 Probar Clientes</a>";
    echo "<a href='index.php?vista=caja' class='btn btn-warning' style='background: #ffc107; color: black; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>💰 Probar Caja</a>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3>❌ Error Fatal</h3>";
    echo "<p><strong>Mensaje:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Archivo:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Línea:</strong> " . $e->getLine() . "</p>";
    echo "</div>";
}
?>

<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    background-color: #f8f9fa;
}

h2, h3 {
    color: #333;
}

p {
    margin: 8px 0;
}

ul {
    background: white;
    padding: 15px;
    border-radius: 5px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

hr {
    border: none;
    height: 2px;
    background: linear-gradient(to right, #007bff, #28a745);
    margin: 20px 0;
}

.btn {
    display: inline-block;
    padding: 10px 20px;
    text-decoration: none;
    border-radius: 5px;
    font-weight: bold;
    transition: all 0.3s;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}
</style>