<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Iniciar sesión para obtener usuario_id
session_start();

require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Verificar que el usuario esté autenticado
        if (!isset($_SESSION['usuario_id'])) {
            throw new Exception('Usuario no autenticado');
        }

        $usuario_id = $_SESSION['usuario_id'];

        // Obtener datos del POST
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            throw new Exception('Datos de venta no válidos');
        }

        $cliente_id = $input['cliente_id'] ?? 1;
        $metodo_pago = $input['metodo_pago'] ?? 'efectivo';
        $total = $input['total'] ?? 0;
        $descuento_total = $input['descuento_total'] ?? 0;
        $items = $input['items'] ?? [];
        $monto_recibido = $input['monto_recibido'] ?? $total;

        if (empty($items)) {
            throw new Exception('No hay productos en la venta');
        }

        // Calcular subtotal
        $subtotal = $total + $descuento_total;
        $cambio = max(0, $monto_recibido - $total);

        // Generar folio único
        $folio = 'V' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

        // Iniciar transacción
        $pdo->beginTransaction();

        // Insertar venta principal (AHORA INCLUYE usuario_id)
        $stmt = $pdo->prepare("
            INSERT INTO ventas (folio, usuario_id, cliente_id, subtotal, descuento_total, total, metodo_pago, monto_recibido, cambio, estado) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'completada')
        ");
        $stmt->execute([$folio, $usuario_id, $cliente_id, $subtotal, $descuento_total, $total, $metodo_pago, $monto_recibido, $cambio]);

        $venta_id = $pdo->lastInsertId();

        // Insertar detalles de venta y actualizar stock
        foreach ($items as $item) {
            // Insertar detalle
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

            // Actualizar stock del producto
            $stmt = $pdo->prepare("UPDATE productos SET stock_actual = stock_actual - ? WHERE id = ?");
            $stmt->execute([$item['cantidad'], $item['producto_id']]);
        }

        // Confirmar transacción
        $pdo->commit();

        echo json_encode([
            'success' => true,
            'venta_id' => $venta_id,
            'folio' => $folio,
            'total' => $total,
            'cambio' => $cambio,
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
            SELECT v.*, c.nombre as cliente_nombre 
            FROM ventas v 
            LEFT JOIN clientes c ON v.cliente_id = c.id 
            ORDER BY v.fecha_venta DESC 
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