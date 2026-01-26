<?php
require_once __DIR__ . '/config/db.php';

echo "Creando tabla venta_detalles...\n";

try {
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
    echo "✅ Tabla venta_detalles creada correctamente\n";
    echo "🚀 Ahora el sistema de pagos debería funcionar completamente\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>