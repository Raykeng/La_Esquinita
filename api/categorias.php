<?php
/**
 * api/categorias.php
 * Endpoint para listar y crear categorías
 */
ob_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Categoria.php';
ob_clean();

header('Content-Type: application/json; charset=utf-8');

try {
    $modelo = new Categoria($pdo);
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'GET') {
        // Listar
        $datos = $modelo->obtenerTodas();
        echo json_encode(['success' => true, 'data' => $datos]);

    } elseif ($method === 'POST') {
        // Crear
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input || empty($input['nombre'])) {
            throw new Exception("Nombre de categoría requerido");
        }

        $id = $modelo->crear($input['nombre'], $input['descripcion'] ?? '');
        if ($id) {
            echo json_encode(['success' => true, 'id' => $id, 'message' => 'Categoría creada']);
        } else {
            throw new Exception("Error al crear categoría");
        }
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>