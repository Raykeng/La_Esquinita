<?php
ob_start(); // Iniciar buffer para atrapar cualquier salida indeseada
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/ProductoController.php';

// Limpiar el buffer antes de enviar headers JSON
ob_clean();

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

try {
    if (!isset($pdo)) {
        throw new Exception("Error de conexión a base de datos");
    }

    $controller = new ProductoController($pdo);
    $lista = $controller->listarProductosPOS();

    echo json_encode([
        'success' => true,
        'data' => $lista
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>