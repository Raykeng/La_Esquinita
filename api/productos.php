<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Incluir la configuración de la base de datos
require_once '../config/db.php';

try {
    // Consulta para obtener productos con información de categoría
    $sql = "SELECT 
                p.id,
                p.codigo_barras,
                p.nombre,
                p.descripcion,
                p.precio_compra,
                p.precio_venta,
                p.stock_actual,
                p.stock_minimo,
                p.stock_maximo,
                p.unidad_medida,
                p.fecha_vencimiento,
                p.descuento_manual,
                p.estado,
                c.nombre as categoria_nombre
            FROM productos p
            LEFT JOIN categorias c ON p.categoria_id = c.id
            WHERE p.estado = 'activo'
            ORDER BY p.nombre ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Convertir tipos de datos para JavaScript
    foreach ($productos as &$producto) {
        $producto['id'] = (int) $producto['id'];
        $producto['precio_compra'] = (float) $producto['precio_compra'];
        $producto['precio_venta'] = (float) $producto['precio_venta'];
        $producto['stock_actual'] = (int) $producto['stock_actual'];
        $producto['stock_minimo'] = (int) $producto['stock_minimo'];
        $producto['stock_maximo'] = (int) $producto['stock_maximo'];

        // Mantener el descuento manual si existe
        $producto['descuento_manual'] = isset($producto['descuento_manual']) ? (float) $producto['descuento_manual'] : 0;
        $producto['promocion'] = $producto['descuento_manual'] > 0;
    }

    echo json_encode([
        'success' => true,
        'data' => $productos,
        'total' => count($productos)
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error de base de datos: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error del servidor: ' . $e->getMessage()
    ]);
}
?>