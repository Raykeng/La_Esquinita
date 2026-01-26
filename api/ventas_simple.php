<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Obtener datos del POST
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            throw new Exception('Datos de venta no válidos');
        }
        
        $metodo_pago = $input['metodo_pago'] ?? 'efectivo';
        $total = $input['total'] ?? 0;
        $items = $input['items'] ?? [];
        $monto_recibido = $input['monto_recibido'] ?? $total;
        
        if (empty($items)) {
            throw new Exception('No hay productos en la venta');
        }
        
        $cambio = max(0, $monto_recibido - $total);
        
        // Iniciar transacción
        $pdo->beginTransaction();
        
        // Verificar si la tabla ventas tiene las columnas necesarias
        $stmt = $pdo->query("DESCRIBE ventas");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Insertar venta con solo las columnas que existen
        if (in_array('descuento_total', $columns) && in_array('monto_recibido', $columns)) {
            // Versión completa si las columnas existen
            $descuento_total = $input['descuento_total'] ?? 0;
            $subtotal = $total + $descuento_total;
            
            $stmt = $pdo->prepare("
                INSERT INTO ventas (subtotal, descuento_total, total, metodo_pago, monto_recibido, cambio, estado, fecha_venta) 
                VALUES (?, ?, ?, ?, ?, ?, 'completada', NOW())
            ");
            $stmt->execute([$subtotal, $descuento_total, $total, $metodo_pago, $monto_recibido, $cambio]);
        } else {
            // Versión simple si faltan columnas
            $stmt = $pdo->prepare("
                INSERT INTO ventas (total, metodo_pago, estado, fecha_venta) 
                VALUES (?, ?, 'completada', NOW())
            ");
            $stmt->execute([$total, $metodo_pago]);
        }
        
        $venta_id = $pdo->lastInsertId();
        
        // Verificar si existe la tabla venta_detalles
        $stmt = $pdo->query("SHOW TABLES LIKE 'venta_detalles'");
        if ($stmt->rowCount() > 0) {
            // Insertar detalles si la tabla existe
            foreach ($items as $item) {
                $stmt = $pdo->prepare("
                    INSERT INTO venta_detalles (venta_id, producto_id, cantidad, precio_unitario, subtotal) 
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $venta_id,
                    $item['producto_id'],
                    $item['cantidad'],
                    $item['precio_unitario'],
                    $item['subtotal']
                ]);
            }
        }
        
        // Actualizar stock de productos (si la tabla productos existe)
        $stmt = $pdo->query("SHOW TABLES LIKE 'productos'");
        if ($stmt->rowCount() > 0) {
            foreach ($items as $item) {
                $stmt = $pdo->prepare("UPDATE productos SET stock_actual = stock_actual - ? WHERE id = ?");
                $stmt->execute([$item['cantidad'], $item['producto_id']]);
            }
        }
        
        // Confirmar transacción
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'venta_id' => $venta_id,
            'total' => $total,
            'cambio' => $cambio,
            'monto_recibido' => $monto_recibido,
            'message' => 'Venta procesada exitosamente'
        ]);
        
    } catch (Exception $e) {
        // Revertir transacción en caso de error
        if ($pdo->inTransaction()) {
            $pdo->rollback();
        }
        
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error al procesar venta: ' . $e->getMessage()
        ]);
    }
    
} else {
    // GET - Obtener ventas
    try {
        $stmt = $pdo->query("
            SELECT * FROM ventas 
            ORDER BY fecha_venta DESC 
            LIMIT 50
        ");
        $ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'data' => $ventas
        ]);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error al obtener ventas: ' . $e->getMessage()
        ]);
    }
}
?>