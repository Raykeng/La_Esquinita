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
 * API para gestión de recuperación de contraseñas
 * Genera tokens seguros y los almacena en la base de datos
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

    $action = $input['action'] ?? '';
    
    switch ($action) {
        case 'request_reset':
            handlePasswordResetRequest($pdo, $input);
            break;
            
        case 'verify_token':
            handleTokenVerification($pdo, $input);
            break;
            
        case 'reset_password':
            handlePasswordReset($pdo, $input);
            break;
            
        default:
            throw new Exception('Acción no válida');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

/**
 * Solicitar reset de contraseña
 */
function handlePasswordResetRequest($pdo, $input) {
    $email = trim($input['email'] ?? '');
    
    if (empty($email)) {
        throw new Exception('Email es requerido');
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Email no válido');
    }
    
    // Buscar usuario por email
    $stmt = $pdo->prepare("SELECT id, nombre_completo, email FROM usuarios WHERE email = ? AND estado = 'activo'");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        // Por seguridad, no revelar si el email existe o no
        echo json_encode([
            'success' => true,
            'message' => 'Si el email existe, recibirás instrucciones de recuperación'
        ]);
        return;
    }
    
    // Generar token seguro
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', strtotime('+1 hour')); // Expira en 1 hora
    
    // Limpiar tokens anteriores del usuario
    $stmt = $pdo->prepare("DELETE FROM password_resets WHERE user_id = ?");
    $stmt->execute([$user['id']]);
    
    // Insertar nuevo token
    $stmt = $pdo->prepare("
        INSERT INTO password_resets (user_id, token, expires_at, created_at) 
        VALUES (?, ?, ?, NOW())
    ");
    $stmt->execute([$user['id'], $token, $expires]);
    
    // Generar link de recuperación
    $resetLink = "http://localhost/La_Esquinita/La_Esquinita/nueva_clave.php?token=" . $token;
    
    echo json_encode([
        'success' => true,
        'message' => 'Token generado correctamente',
        'data' => [
            'user_name' => $user['nombre_completo'],
            'user_email' => $user['email'],
            'reset_token' => $token,
            'reset_link' => $resetLink,
            'expires_at' => $expires
        ]
    ]);
}

/**
 * Verificar si un token es válido
 */
function handleTokenVerification($pdo, $input) {
    $token = trim($input['token'] ?? '');
    
    if (empty($token)) {
        throw new Exception('Token es requerido');
    }
    
    $stmt = $pdo->prepare("
        SELECT pr.*, u.nombre_completo, u.email 
        FROM password_resets pr 
        JOIN usuarios u ON pr.user_id = u.id 
        WHERE pr.token = ? AND pr.expires_at > NOW() AND pr.used = 0
    ");
    $stmt->execute([$token]);
    $reset = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$reset) {
        throw new Exception('Token inválido o expirado');
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Token válido',
        'data' => [
            'user_name' => $reset['nombre_completo'],
            'user_email' => $reset['email']
        ]
    ]);
}

/**
 * Cambiar contraseña con token
 */
function handlePasswordReset($pdo, $input) {
    $token = trim($input['token'] ?? '');
    $newPassword = trim($input['new_password'] ?? '');
    
    if (empty($token) || empty($newPassword)) {
        throw new Exception('Token y nueva contraseña son requeridos');
    }
    
    if (strlen($newPassword) < 6) {
        throw new Exception('La contraseña debe tener al menos 6 caracteres');
    }
    
    // Verificar token
    $stmt = $pdo->prepare("
        SELECT pr.*, u.id as user_id 
        FROM password_resets pr 
        JOIN usuarios u ON pr.user_id = u.id 
        WHERE pr.token = ? AND pr.expires_at > NOW() AND pr.used = 0
    ");
    $stmt->execute([$token]);
    $reset = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$reset) {
        throw new Exception('Token inválido o expirado');
    }
    
    // Actualizar contraseña
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    
    $pdo->beginTransaction();
    
    try {
        // Actualizar contraseña del usuario
        $stmt = $pdo->prepare("UPDATE usuarios SET password_hash = ? WHERE id = ?");
        $stmt->execute([$hashedPassword, $reset['user_id']]);
        
        // Marcar token como usado
        $stmt = $pdo->prepare("UPDATE password_resets SET used = 1 WHERE id = ?");
        $stmt->execute([$reset['id']]);
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Contraseña actualizada correctamente'
        ]);
        
    } catch (Exception $e) {
        $pdo->rollback();
        throw $e;
    }
}
?>