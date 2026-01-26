<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Incluir la configuración de la base de datos
require_once __DIR__ . '/../config/db.php';

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
                p.estado,
                c.nombre as categoria_nombre,
                CASE 
                    WHEN p.fecha_vencimiento IS NOT NULL 
                    AND DATEDIFF(p.fecha_vencimiento, CURDATE()) <= 60 
                    AND DATEDIFF(p.fecha_vencimiento, CURDATE()) > 0
                    THEN 1 
                    ELSE 0 
                END as por_vencer
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
        $producto['por_vencer'] = (bool) $producto['por_vencer'];

        // Agregar campos por defecto
        $producto['descuento_manual'] = 0;
        $producto['promocion'] = false;
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