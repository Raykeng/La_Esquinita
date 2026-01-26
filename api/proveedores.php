<?php
/**
 * api/proveedores.php
 * Endpoint para listar proveedores
 */
ob_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Proveedor.php';
ob_clean();

header('Content-Type: application/json; charset=utf-8');

try {
    $modelo = new Proveedor($pdo);
    $datos = $modelo->obtenerTodos();
    echo json_encode(['success' => true, 'data' => $datos]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>