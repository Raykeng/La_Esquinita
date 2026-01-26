<?php
/**
 * api/actualizar_descuentos.php
 * Endpoint para actualizar descuentos de productos
 */
ob_start();
require_once __DIR__ . '/../config/db.php';
ob_clean();

header('Content-Type: application/json; charset=utf-8');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Método no permitido");
    }

    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['descuentos']) || !is_array($input['descuentos'])) {
        throw new Exception("Datos inválidos");
    }

    $pdo->beginTransaction();

    foreach ($input['descuentos'] as $item) {
        $id = (int) $item['id'];
        $descuento = (float) $item['descuento'];

        // Actualizar el campo descuento_manual en la base de datos
        $sql = "UPDATE productos SET descuento_manual = :descuento WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':descuento' => $descuento,
            ':id' => $id
        ]);
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Descuentos actualizados correctamente'
    ]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>