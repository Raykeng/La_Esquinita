<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../config/db.php';

try {
    // Obtener estadísticas del dashboard
    $stats = [];
    
    // 1. Ventas del mes actual
    $stmt = $pdo->query("
        SELECT 
            COALESCE(SUM(total), 0) as ventas_mes,
            COUNT(*) as num_ventas
        FROM ventas 
        WHERE MONTH(fecha_venta) = MONTH(CURRENT_DATE()) 
        AND YEAR(fecha_venta) = YEAR(CURRENT_DATE())
    ");
    $ventas = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats['ventas_mes'] = $ventas['ventas_mes'];
    $stats['num_ventas'] = $ventas['num_ventas'];
    
    // 2. Productos con stock bajo
    $stmt = $pdo->query("
        SELECT COUNT(*) as productos_stock_bajo 
        FROM productos 
        WHERE stock_actual <= stock_minimo
    ");
    $stock = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats['productos_stock_bajo'] = $stock['productos_stock_bajo'];
    
    // 3. Total de productos
    $stmt = $pdo->query("SELECT COUNT(*) as total_productos FROM productos");
    $productos = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats['total_productos'] = $productos['total_productos'];
    
    // 4. Productos próximos a vencer (30 días)
    $stmt = $pdo->query("
        SELECT COUNT(*) as productos_vencimiento 
        FROM productos 
        WHERE fecha_vencimiento IS NOT NULL 
        AND fecha_vencimiento <= DATE_ADD(CURRENT_DATE(), INTERVAL 30 DAY)
        AND fecha_vencimiento > CURRENT_DATE()
    ");
    $vencimiento = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats['productos_vencimiento'] = $vencimiento['productos_vencimiento'];
    
    // 5. Ventas de los últimos 7 días
    $stmt = $pdo->query("
        SELECT 
            DATE(fecha_venta) as fecha,
            SUM(total) as total_dia,
            COUNT(*) as ventas_dia
        FROM ventas 
        WHERE fecha_venta >= DATE_SUB(CURRENT_DATE(), INTERVAL 7 DAY)
        GROUP BY DATE(fecha_venta)
        ORDER BY fecha_venta ASC
    ");
    $ventas_semana = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stats['ventas_semana'] = $ventas_semana;
    
    // 6. Productos más vendidos (top 5)
    $stmt = $pdo->query("
        SELECT 
            p.nombre,
            SUM(vd.cantidad) as total_vendido,
            SUM(vd.subtotal) as total_ingresos
        FROM venta_detalles vd
        JOIN productos p ON vd.producto_id = p.id
        JOIN ventas v ON vd.venta_id = v.id
        WHERE v.fecha_venta >= DATE_SUB(CURRENT_DATE(), INTERVAL 30 DAY)
        GROUP BY p.id, p.nombre
        ORDER BY total_vendido DESC
        LIMIT 5
    ");
    $productos_top = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stats['productos_top'] = $productos_top;
    
    // 7. Ventas por método de pago (mes actual)
    $stmt = $pdo->query("
        SELECT 
            metodo_pago,
            COUNT(*) as cantidad,
            SUM(total) as total_monto
        FROM ventas 
        WHERE MONTH(fecha_venta) = MONTH(CURRENT_DATE()) 
        AND YEAR(fecha_venta) = YEAR(CURRENT_DATE())
        GROUP BY metodo_pago
    ");
    $metodos_pago = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stats['metodos_pago'] = $metodos_pago;
    
    echo json_encode([
        'success' => true,
        'data' => $stats
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener estadísticas: ' . $e->getMessage()
    ]);
}
?>