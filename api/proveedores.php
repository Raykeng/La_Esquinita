<?php
/**
 * api/proveedores.php
 * Endpoint para listar y crear proveedores
 */
ob_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Proveedor.php';
ob_clean();

header('Content-Type: application/json; charset=utf-8');

try {
    $modelo = new Proveedor($pdo);
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'GET') {
        // Listar
        $datos = $modelo->obtenerTodos();
        echo json_encode(['success' => true, 'data' => $datos]);

    } elseif ($method === 'POST') {
        // Crear
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input || empty($input['nombre'])) {
            throw new Exception("Nombre de proveedor requerido");
        }

        $id = $modelo->crear(
            $input['nombre'],
            $input['contacto'] ?? '',
            $input['telefono'] ?? '',
            $input['email'] ?? ''
        );

        if ($id) {
            echo json_encode(['success' => true, 'id' => $id, 'message' => 'Proveedor creado']);
        } else {
            throw new Exception("Error al crear proveedor");
        }
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>