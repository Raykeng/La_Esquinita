<?php
// verificar_bd.php - Verificación simple de base de datos
?>
<!DOCTYPE html>
<html>
<head>
    <title>Verificar Base de Datos</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        .ok { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .info { background: #f0f8ff; padding: 10px; border-left: 4px solid #007bff; margin: 10px 0; }
    </style>
</head>
<body>
    <h2>🔍 Verificación de Base de Datos - La Esquinita</h2>
    
    <?php
    // Configuración de la base de datos
    $host = 'localhost';
    $db = 'la_esquinita';
    $user = 'root';
    $pass = '';
    
    echo "<h3>1. Intentando conectar a la base de datos...</h3>";
    echo "<p><strong>Host:</strong> $host</p>";
    echo "<p><strong>Base de datos:</strong> $db</p>";
    echo "<p><strong>Usuario:</strong> $user</p>";
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
        echo "<p class='ok'>✅ CONEXIÓN EXITOSA a la base de datos</p>";
        
        // Verificar si hay tablas
        $stmt = $pdo->query("SHOW TABLES");
        $tablas = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (count($tablas) > 0) {
            echo "<p class='ok'>✅ La base de datos tiene " . count($tablas) . " tablas</p>";
            echo "<p><strong>Tablas encontradas:</strong> " . implode(', ', $tablas) . "</p>";
            
            // Verificar productos específicamente
            if (in_array('productos', $tablas)) {
                $stmt = $pdo->query("SELECT COUNT(*) FROM productos");
                $count = $stmt->fetchColumn();
                echo "<p class='ok'>✅ Tabla 'productos' tiene $count registros</p>";
                
                if ($count == 0) {
                    echo "<div class='info'>💡 <strong>La tabla productos está vacía.</strong> Necesitas insertar productos para que aparezcan en el POS.</div>";
                }
            } else {
                echo "<p class='error'>❌ No existe la tabla 'productos'</p>";
            }
            
        } else {
            echo "<p class='error'>❌ La base de datos está vacía (no tiene tablas)</p>";
            echo "<div class='info'>💡 <strong>Solución:</strong> Necesitas ejecutar el archivo SQL para crear las tablas.</div>";
        }
        
    } catch (PDOException $e) {
        echo "<p class='error'>❌ ERROR DE CONEXIÓN: " . $e->getMessage() . "</p>";
        
        if (strpos($e->getMessage(), 'Unknown database') !== false) {
            echo "<div class='info'>💡 <strong>La base de datos 'la_esquinita' no existe.</strong><br>";
            echo "Necesitas crearla ejecutando el archivo SQL.</div>";
        } elseif (strpos($e->getMessage(), 'Access denied') !== false) {
            echo "<div class='info'>💡 <strong>Problema de permisos.</strong><br>";
            echo "Verifica que MySQL esté corriendo y que el usuario 'root' no tenga contraseña.</div>";
        } else {
            echo "<div class='info'>💡 <strong>Verifica que:</strong><br>";
            echo "- MySQL esté corriendo<br>";
            echo "- La base de datos 'la_esquinita' exista<br>";
            echo "- Las credenciales sean correctas</div>";
        }
    }
    ?>
    
    <hr>
    <h3>📋 ¿Qué hacer según el resultado?</h3>
    
    <p><strong>Si ves ✅ CONEXIÓN EXITOSA:</strong></p>
    <ul>
        <li>La base de datos funciona correctamente</li>
        <li>El problema puede ser en el JavaScript o en los productos</li>
    </ul>
    
    <p><strong>Si ves ❌ ERROR:</strong></p>
    <ul>
        <li>Necesitas instalar/configurar la base de datos primero</li>
        <li>Ejecutar el archivo <code>BD/la_esquinita_optimizada.sql</code></li>
    </ul>
    
    <p><strong>Si la tabla productos está vacía:</strong></p>
    <ul>
        <li>Necesitas insertar productos de prueba</li>
        <li>O ejecutar el script SQL completo</li>
    </ul>
    
</body>
</html>