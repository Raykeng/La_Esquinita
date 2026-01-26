<?php
/**
 * api/productos_guardar.php
 * Endpoint para guardar (crear/editar) productos
 */
ob_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Producto.php';
ob_clean();

header('Content-Type: application/json; charset=utf-8');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Método no permitido");
    }

    // Leemos $_POST directamente porque el formulario se envía como FormData
    // pero si usáramos JSON raw, sería file_get_contents.
    // Asumiremos JSON para estandarizar con el Frontend moderno
    $input = json_decode(file_get_contents('php://input'), true);

    // Si viene vacio, intentar $_POST estándar (por flexibilidad)
    if (!$input)
        $input = $_POST;

    if (empty($input['codigo_barras']) || empty($input['nombre']) || empty($input['precio_venta'])) {
        throw new Exception("Faltan datos obligatorios (Código, Nombre, Precio Venta)");
    }

    $modelo = new Producto($pdo);

    // Decidir si es CREAR o EDITAR basado en si viene 'id'
    if (!empty($input['id'])) {
        // ACTUALIZAR
        $id = (int) $input['id'];
        $resultado = $modelo->actualizar($id, $input);
        if ($resultado) {
            echo json_encode(['success' => true, 'id' => $id, 'message' => 'Producto actualizado correctamente']);
        } else {
            throw new Exception("No se pudo actualizar el producto");
        }
    } else {
        // CREAR
        $id = $modelo->crear($input);
        echo json_encode(['success' => true, 'id' => $id, 'message' => 'Producto guardado correctamente']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>