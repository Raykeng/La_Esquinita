<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Manejar preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once '../config/db.php';

/**
 * API para registro de nuevos usuarios
 */

try {
    // Solo permitir POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido');
    }

    // Obtener datos del request
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Datos inválidos');
    }

    // Validar datos requeridos
    $requiredFields = ['nombre', 'apellido', 'email', 'telefono', 'password'];
    foreach ($requiredFields as $field) {
        if (empty(trim($input[$field] ?? ''))) {
            throw new Exception("El campo {$field} es requerido");
        }
    }

    $nombre = trim($input['nombre']);
    $apellido = trim($input['apellido']);
    $email = trim($input['email']);
    $telefono = trim($input['telefono']);
    $password = $input['password'];

    // Validaciones
    if (strlen($nombre) < 2) {
        throw new Exception('El nombre debe tener al menos 2 caracteres');
    }

    if (strlen($apellido) < 2) {
        throw new Exception('El apellido debe tener al menos 2 caracteres');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Email no válido');
    }

    if (!preg_match('/^\d{10}$/', $telefono)) {
        throw new Exception('El teléfono debe tener exactamente 10 dígitos');
    }

    if (strlen($password) < 6) {
        throw new Exception('La contraseña debe tener al menos 6 caracteres');
    }

    // Verificar si el email ya existe
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        throw new Exception('Este correo electrónico ya está registrado');
    }

    // Verificar si el teléfono ya existe
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE telefono = ?");
    $stmt->execute([$telefono]);
    if ($stmt->fetch()) {
        throw new Exception('Este número de teléfono ya está registrado');
    }

    // Crear hash de la contraseña
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Crear nombre completo
    $nombreCompleto = $nombre . ' ' . $apellido;

    // Insertar nuevo usuario
    $stmt = $pdo->prepare("
        INSERT INTO usuarios (nombre_completo, email, telefono, password_hash, rol, estado, fecha_creacion) 
        VALUES (?, ?, ?, ?, 'empleado', 'activo', NOW())
    ");
    
    $stmt->execute([$nombreCompleto, $email, $telefono, $passwordHash]);
    
    $userId = $pdo->lastInsertId();

    echo json_encode([
        'success' => true,
        'message' => 'Usuario registrado exitosamente',
        'data' => [
            'user_id' => $userId,
            'nombre_completo' => $nombreCompleto,
            'email' => $email,
            'telefono' => $telefono
        ]
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>