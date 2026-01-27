<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        $accion = $input['accion'] ?? '';
        
        switch ($accion) {
            case 'agregar_stock':
                $producto_id = $input['producto_id'] ?? 0;
                $cantidad = $input['cantidad'] ?? 0;
                $motivo = $input['motivo'] ?? 'ajuste';
                $observaciones = $input['observaciones'] ?? '';
                
                if (!$producto_id || !$cantidad) {
                    throw new Exception('Producto ID y cantidad son requeridos');
                }
                
                // Iniciar transacción
                $pdo->beginTransaction();
                
                // Actualizar stock del producto
                $stmt = $pdo->prepare("UPDATE productos SET stock_actual = stock_actual + ? WHERE id = ?");
                $stmt->execute([$cantidad, $producto_id]);
                
                // Registrar movimiento de inventario (si existe la tabla)
                try {
                    $stmt = $pdo->prepare("
                        INSERT INTO movimientos_inventario 
                        (producto_id, tipo_movimiento, cantidad, motivo, observaciones, fecha_movimiento) 
                        VALUES (?, 'entrada', ?, ?, ?, NOW())
                    ");
                    $stmt->execute([$producto_id, $cantidad, $motivo, $observaciones]);
                } catch (Exception $e) {
                    // Si no existe la tabla, continuar sin registrar el movimiento
                }
                
                // Obtener información actualizada del producto
                $stmt = $pdo->prepare("SELECT nombre, stock_actual FROM productos WHERE id = ?");
                $stmt->execute([$producto_id]);
                $producto = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $pdo->commit();
                
                echo json_encode([
                    'success' => true,
                    'message' => "Stock agregado correctamente a {$producto['nombre']}",
                    'nuevo_stock' => $producto['stock_actual']
                ]);
                break;
                
            case 'reducir_stock':
                $producto_id = $input['producto_id'] ?? 0;
                $cantidad = $input['cantidad'] ?? 0;
                $motivo = $input['motivo'] ?? 'ajuste';
                $observaciones = $input['observaciones'] ?? '';
                
                if (!$producto_id || !$cantidad) {
                    throw new Exception('Producto ID y cantidad son requeridos');
                }
                
                // Verificar stock disponible
                $stmt = $pdo->prepare("SELECT stock_actual, nombre FROM productos WHERE id = ?");
                $stmt->execute([$producto_id]);
                $producto = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$producto) {
                    throw new Exception('Producto no encontrado');
                }
                
                if ($producto['stock_actual'] < $cantidad) {
                    throw new Exception("Stock insuficiente. Disponible: {$producto['stock_actual']}");
                }
                
                // Iniciar transacción
                $pdo->beginTransaction();
                
                // Reducir stock del producto
                $stmt = $pdo->prepare("UPDATE productos SET stock_actual = stock_actual - ? WHERE id = ?");
                $stmt->execute([$cantidad, $producto_id]);
                
                // Registrar movimiento de inventario
                try {
                    $stmt = $pdo->prepare("
                        INSERT INTO movimientos_inventario 
                        (producto_id, tipo_movimiento, cantidad, motivo, observaciones, fecha_movimiento) 
                        VALUES (?, 'salida', ?, ?, ?, NOW())
                    ");
                    $stmt->execute([$producto_id, $cantidad, $motivo, $observaciones]);
                } catch (Exception $e) {
                    // Si no existe la tabla, continuar
                }
                
                $pdo->commit();
                
                echo json_encode([
                    'success' => true,
                    'message' => "Stock reducido correctamente de {$producto['nombre']}",
                    'nuevo_stock' => $producto['stock_actual'] - $cantidad
                ]);
                break;
                
            case 'ajustar_stock':
                $producto_id = $input['producto_id'] ?? 0;
                $nuevo_stock = $input['nuevo_stock'] ?? 0;
                $motivo = $input['motivo'] ?? 'ajuste_inventario';
                $observaciones = $input['observaciones'] ?? '';
                
                if (!$producto_id || $nuevo_stock < 0) {
                    throw new Exception('Datos inválidos para ajuste de stock');
                }
                
                // Obtener stock actual
                $stmt = $pdo->prepare("SELECT stock_actual, nombre FROM productos WHERE id = ?");
                $stmt->execute([$producto_id]);
                $producto = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$producto) {
                    throw new Exception('Producto no encontrado');
                }
                
                $diferencia = $nuevo_stock - $producto['stock_actual'];
                $tipo_movimiento = $diferencia >= 0 ? 'entrada' : 'salida';
                
                // Iniciar transacción
                $pdo->beginTransaction();
                
                // Actualizar stock
                $stmt = $pdo->prepare("UPDATE productos SET stock_actual = ? WHERE id = ?");
                $stmt->execute([$nuevo_stock, $producto_id]);
                
                // Registrar movimiento
                try {
                    $stmt = $pdo->prepare("
                        INSERT INTO movimientos_inventario 
                        (producto_id, tipo_movimiento, cantidad, motivo, observaciones, fecha_movimiento) 
                        VALUES (?, ?, ?, ?, ?, NOW())
                    ");
                    $stmt->execute([$producto_id, $tipo_movimiento, abs($diferencia), $motivo, $observaciones]);
                } catch (Exception $e) {
                    // Continuar si no existe la tabla
                }
                
                $pdo->commit();
                
                echo json_encode([
                    'success' => true,
                    'message' => "Stock ajustado correctamente para {$producto['nombre']}",
                    'stock_anterior' => $producto['stock_actual'],
                    'nuevo_stock' => $nuevo_stock,
                    'diferencia' => $diferencia
                ]);
                break;
                
            default:
                throw new Exception('Acción no válida');
        }
        
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollback();
        }
        
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $accion = $_GET['accion'] ?? 'estadisticas';
        
        switch ($accion) {
            case 'estadisticas':
                // Estadísticas del inventario
                $stats = [];
                
                // Total productos
                $stmt = $pdo->query("SELECT COUNT(*) as total FROM productos");
                $stats['total_productos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                
                // Productos con stock bajo
                $stmt = $pdo->query("SELECT COUNT(*) as stock_bajo FROM productos WHERE stock_actual <= stock_minimo");
                $stats['stock_bajo'] = $stmt->fetch(PDO::FETCH_ASSOC)['stock_bajo'];
                
                // Productos próximos a vencer (30 días)
                $stmt = $pdo->query("
                    SELECT COUNT(*) as por_vencer 
                    FROM productos 
                    WHERE fecha_vencimiento IS NOT NULL 
                    AND fecha_vencimiento <= DATE_ADD(CURRENT_DATE(), INTERVAL 30 DAY)
                    AND fecha_vencimiento > CURRENT_DATE()
                ");
                $stats['por_vencer'] = $stmt->fetch(PDO::FETCH_ASSOC)['por_vencer'];
                
                // Valor total del inventario
                $stmt = $pdo->query("
                    SELECT SUM(stock_actual * COALESCE(precio_costo, precio_venta * 0.7)) as valor_total 
                    FROM productos
                ");
                $stats['valor_total'] = $stmt->fetch(PDO::FETCH_ASSOC)['valor_total'] ?? 0;
                
                echo json_encode([
                    'success' => true,
                    'data' => $stats
                ]);
                break;
                
            case 'movimientos':
                $producto_id = $_GET['producto_id'] ?? null;
                $limit = $_GET['limit'] ?? 50;
                
                $sql = "
                    SELECT mi.*, p.nombre as producto_nombre 
                    FROM movimientos_inventario mi
                    JOIN productos p ON mi.producto_id = p.id
                ";
                
                $params = [];
                if ($producto_id) {
                    $sql .= " WHERE mi.producto_id = ?";
                    $params[] = $producto_id;
                }
                
                $sql .= " ORDER BY mi.fecha_movimiento DESC LIMIT ?";
                $params[] = (int)$limit;
                
                try {
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($params);
                    $movimientos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    echo json_encode([
                        'success' => true,
                        'data' => $movimientos
                    ]);
                } catch (Exception $e) {
                    // Si no existe la tabla de movimientos
                    echo json_encode([
                        'success' => true,
                        'data' => [],
                        'message' => 'Tabla de movimientos no disponible'
                    ]);
                }
                break;
                
            case 'alertas':
                // Productos que requieren atención
                $alertas = [];
                
                // Stock bajo
                $stmt = $pdo->query("
                    SELECT id, nombre, stock_actual, stock_minimo, 'stock_bajo' as tipo_alerta
                    FROM productos 
                    WHERE stock_actual <= stock_minimo
                    ORDER BY (stock_actual / stock_minimo) ASC
                    LIMIT 10
                ");
                $alertas = array_merge($alertas, $stmt->fetchAll(PDO::FETCH_ASSOC));
                
                // Próximos a vencer
                $stmt = $pdo->query("
                    SELECT id, nombre, fecha_vencimiento, 
                           DATEDIFF(fecha_vencimiento, CURRENT_DATE()) as dias_restantes,
                           'por_vencer' as tipo_alerta
                    FROM productos 
                    WHERE fecha_vencimiento IS NOT NULL 
                    AND fecha_vencimiento <= DATE_ADD(CURRENT_DATE(), INTERVAL 30 DAY)
                    AND fecha_vencimiento > CURRENT_DATE()
                    ORDER BY fecha_vencimiento ASC
                    LIMIT 10
                ");
                $alertas = array_merge($alertas, $stmt->fetchAll(PDO::FETCH_ASSOC));
                
                echo json_encode([
                    'success' => true,
                    'data' => $alertas
                ]);
                break;
                
            default:
                throw new Exception('Acción no válida');
        }
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}
?>