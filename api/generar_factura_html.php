<?php
header('Content-Type: text/html; charset=UTF-8');

require_once __DIR__ . '/../config/db.php';

$venta_id = $_GET['venta_id'] ?? null;

if (!$venta_id) {
    die('ID de venta requerido');
}

try {
    // Obtener datos de la venta
    $stmt = $pdo->prepare("
        SELECT v.*, c.nombre as cliente_nombre 
        FROM ventas v 
        LEFT JOIN clientes c ON v.cliente_id = c.id 
        WHERE v.id = ?
    ");
    $stmt->execute([$venta_id]);
    $venta = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$venta) {
        die('Venta no encontrada');
    }
    
    // Obtener detalles de la venta
    $stmt = $pdo->prepare("
        SELECT vd.*, p.nombre as producto_nombre 
        FROM venta_detalles vd 
        JOIN productos p ON vd.producto_id = p.id 
        WHERE vd.venta_id = ?
    ");
    $stmt->execute([$venta_id]);
    $detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Factura #<?= $venta['folio'] ?? $venta['id'] ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Arial', sans-serif; 
            font-size: 14px;
            line-height: 1.4;
            color: #000;
            background: white;
            padding: 20px;
        }
        .factura {
            max-width: 400px;
            margin: 0 auto;
            background: white;
            border: 1px solid #ddd;
            padding: 20px;
        }
        .header { 
            text-align: center; 
            border-bottom: 2px solid #000; 
            padding-bottom: 15px; 
            margin-bottom: 15px;
        }
        .header h1 { 
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .header p { 
            font-size: 12px;
            margin: 3px 0;
        }
        .info { 
            margin: 15px 0;
            font-size: 12px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            padding: 2px 0;
        }
        .items { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 15px 0; 
            font-size: 11px;
        }
        .items th, .items td { 
            padding: 5px 3px; 
            text-align: left; 
            border-bottom: 1px solid #ddd;
        }
        .items th { 
            font-weight: bold;
            background: #f5f5f5;
            border-bottom: 2px solid #000;
        }
        .items td.right {
            text-align: right;
        }
        .totals { 
            margin-top: 15px;
            border-top: 2px solid #000;
            padding-top: 10px;
            font-size: 12px;
        }
        .totals .total-line {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
            padding: 2px 0;
        }
        .totals .final-total {
            font-weight: bold;
            font-size: 16px;
            border-top: 1px solid #000;
            padding-top: 8px;
            margin-top: 8px;
        }
        .footer { 
            margin-top: 20px; 
            text-align: center; 
            font-size: 11px;
            border-top: 1px dashed #000;
            padding-top: 15px;
        }
        .buttons {
            text-align: center;
            margin: 20px 0;
        }
        .btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            margin: 0 5px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .btn:hover {
            background: #0056b3;
        }
        @media print {
            .buttons { display: none; }
            body { padding: 0; }
            .factura { border: none; box-shadow: none; }
        }
    </style>
</head>
<body>
    <div class="buttons">
        <button class="btn" onclick="window.print()">🖨️ Imprimir</button>
        <button class="btn" onclick="downloadPDF()">📄 Descargar PDF</button>
        <button class="btn" onclick="window.close()">❌ Cerrar</button>
    </div>
    
    <div class="factura" id="factura">
        <div class="header">
            <h1>🏪 LA ESQUINITA</h1>
            <p>Sistema de Punto de Venta</p>
            <p><strong>FACTURA #<?= str_pad($venta['id'], 6, '0', STR_PAD_LEFT) ?></strong></p>
            <?php if (!empty($venta['folio'])): ?>
            <p>Folio: <?= htmlspecialchars($venta['folio']) ?></p>
            <?php endif; ?>
        </div>
        
        <div class="info">
            <div class="info-row">
                <span><strong>Fecha:</strong></span>
                <span><?= date('d/m/Y H:i:s', strtotime($venta['fecha_venta'])) ?></span>
            </div>
            <div class="info-row">
                <span><strong>Cliente:</strong></span>
                <span><?= htmlspecialchars($venta['cliente_nombre'] ?? 'Público General') ?></span>
            </div>
            <div class="info-row">
                <span><strong>Método:</strong></span>
                <span><?= ucfirst($venta['metodo_pago']) ?></span>
            </div>
        </div>
        
        <table class="items">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cant</th>
                    <th>Precio</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($detalles as $detalle): ?>
                <tr>
                    <td><?= htmlspecialchars($detalle['producto_nombre']) ?></td>
                    <td class="right"><?= $detalle['cantidad'] ?></td>
                    <td class="right">Q<?= number_format($detalle['precio_unitario'], 2) ?></td>
                    <td class="right">Q<?= number_format($detalle['subtotal'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="totals">
            <?php if ($venta['subtotal'] > 0 && $venta['subtotal'] != $venta['total']): ?>
            <div class="total-line">
                <span>Subtotal:</span>
                <span>Q<?= number_format($venta['subtotal'], 2) ?></span>
            </div>
            <?php endif; ?>
            
            <?php if ($venta['descuento_total'] > 0): ?>
            <div class="total-line">
                <span>Descuentos:</span>
                <span>-Q<?= number_format($venta['descuento_total'], 2) ?></span>
            </div>
            <?php endif; ?>
            
            <div class="total-line final-total">
                <span><strong>TOTAL:</strong></span>
                <span><strong>Q<?= number_format($venta['total'], 2) ?></strong></span>
            </div>
            
            <?php if ($venta['metodo_pago'] === 'efectivo' && $venta['monto_recibido'] > 0): ?>
            <div class="total-line">
                <span>Recibido:</span>
                <span>Q<?= number_format($venta['monto_recibido'], 2) ?></span>
            </div>
            <div class="total-line">
                <span>Cambio:</span>
                <span>Q<?= number_format($venta['cambio'], 2) ?></span>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="footer">
            <p><strong>¡Gracias por su compra!</strong></p>
            <p>La Esquinita - Abarrotes y Más</p>
            <p><?= date('d/m/Y H:i:s') ?></p>
        </div>
    </div>
    
    <script>
        function downloadPDF() {
            // Usar la función de imprimir del navegador para generar PDF
            window.print();
        }
        
        // Auto-abrir diálogo de impresión después de 2 segundos
        setTimeout(function() {
            // window.print(); // Descomenta si quieres auto-imprimir
        }, 2000);
    </script>
</body>
</html>