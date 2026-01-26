<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/db.php';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Obtener todas las categorías
        $sql = "SELECT id, nombre, descripcion FROM categorias WHERE estado = 'activo' ORDER BY nombre ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'data' => $categorias,
            'total' => count($categorias)
        ]);
    } 
    elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Crear nueva categoría
        $input = json_decode(file_get_contents('php://input'), true);
        $nombre = trim($input['nombre'] ?? '');
        
        if (empty($nombre)) {
            throw new Exception('El nombre de la categoría es requerido');
        }
        
        $sql = "INSERT INTO categorias (nombre) VALUES (?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombre]);
        
        echo json_encode([
            'success' => true,
            'id' => $pdo->lastInsertId(),
            'message' => 'Categoría creada correctamente'
        ]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error de base de datos: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>