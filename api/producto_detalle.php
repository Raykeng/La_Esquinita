<?php
/**
 * api/producto_detalle.php
 * Endpoint para obtener un producto por ID
 */
ob_start();
require_once __DIR__ . '/../config/db.php';
ob_clean();

header('Content-Type: application/json; charset=utf-8');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        throw new Exception("Método no permitido");
    }

    if (!isset($_GET['id']) || empty($_GET['id'])) {
        throw new Exception("ID de producto requerido");
    }

    $id = (int) $_GET['id'];

    $sql = "SELECT 
                p.id,
                p.codigo_barras,
                p.nombre,
                p.categoria_id,
                p.proveedor_id,
                p.precio_compra,
                p.precio_venta,
                p.stock_actual,
                p.stock_minimo,
                p.unidad_medida,
                p.fecha_vencimiento,
                c.nombre as categoria_nombre
            FROM productos p
            LEFT JOIN categorias c ON p.categoria_id = c.id
            WHERE p.id = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$producto) {
        throw new Exception("Producto no encontrado");
    }

    echo json_encode([
        'success' => true,
        'data' => $producto
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>