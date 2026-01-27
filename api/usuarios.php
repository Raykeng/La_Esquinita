<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../middleware/auth.php';

// Verificar que solo administradores puedan acceder
verificarAdmin();

$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'listar':
            listarUsuarios();
            break;
        case 'crear':
            crearUsuario();
            break;
        case 'actualizar':
            actualizarUsuario();
            break;
        case 'cambiar_estado':
            cambiarEstadoUsuario();
            break;
        case 'resetear_password':
            resetearPassword();
            break;
        default:
            throw new Exception('Acción no válida');
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

function listarUsuarios() {
    global $pdo;
    
    try {
        $sql = "SELECT 
                    id, username, nombre_completo, email, rol, estado, 
                    ultimo_acceso, fecha_creacion
                FROM usuarios 
                ORDER BY fecha_creacion DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Formatear datos
        foreach ($usuarios as &$usuario) {
            $usuario['id'] = (int) $usuario['id'];
        }
        
        echo json_encode([
            'success' => true,
            'usuarios' => $usuarios
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Error al listar usuarios: ' . $e->getMessage());
    }
}

function crearUsuario() {
    global $pdo;
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    $username = trim($input['username'] ?? '');
    $password = trim($input['password'] ?? '');
    $nombre_completo = trim($input['nombre_completo'] ?? '');
    $email = trim($input['email'] ?? '');
    $rol = trim($input['rol'] ?? '');
    $estado = trim($input['estado'] ?? 'activo');
    
    // Validaciones
    if (empty($username) || empty($password) || empty($nombre_completo) || empty($rol)) {
        throw new Exception('Username, contraseña, nombre completo y rol son requeridos');
    }
    
    if (strlen($password) < 6) {
        throw new Exception('La contraseña debe tener al menos 6 caracteres');
    }
    
    if (!in_array($rol, ['administrador', 'cajero'])) {
        throw new Exception('Rol no válido');
    }
    
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        throw new Exception('El username solo puede contener letras, números y guiones bajos');
    }
    
    try {
        // Verificar si el username ya existe
        $checkSql = "SELECT id FROM usuarios WHERE username = ?";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([$username]);
        
        if ($checkStmt->rowCount() > 0) {
            throw new Exception('El username ya está en uso');
        }
        
        // Crear usuario
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $creado_por = $_SESSION['usuario_id'];
        
        $sql = "INSERT INTO usuarios 
                (username, password_hash, nombre_completo, email, rol, estado, creado_por) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $username, $passwordHash, $nombre_completo, $email, $rol, $estado, $creado_por
        ]);
        
        $usuarioId = $pdo->lastInsertId();
        
        echo json_encode([
            'success' => true,
            'message' => 'Usuario creado exitosamente',
            'usuario_id' => $usuarioId
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Error al crear usuario: ' . $e->getMessage());
    }
}

function actualizarUsuario() {
    global $pdo;
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    $id = (int) ($input['id'] ?? 0);
    $username = trim($input['username'] ?? '');
    $nombre_completo = trim($input['nombre_completo'] ?? '');
    $email = trim($input['email'] ?? '');
    $rol = trim($input['rol'] ?? '');
    $estado = trim($input['estado'] ?? 'activo');
    
    // Validaciones
    if (!$id || empty($username) || empty($nombre_completo) || empty($rol)) {
        throw new Exception('ID, username, nombre completo y rol son requeridos');
    }
    
    if (!in_array($rol, ['administrador', 'cajero'])) {
        throw new Exception('Rol no válido');
    }
    
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        throw new Exception('El username solo puede contener letras, números y guiones bajos');
    }
    
    try {
        // Verificar si el username ya existe (excluyendo el usuario actual)
        $checkSql = "SELECT id FROM usuarios WHERE username = ? AND id != ?";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([$username, $id]);
        
        if ($checkStmt->rowCount() > 0) {
            throw new Exception('El username ya está en uso por otro usuario');
        }
        
        // Actualizar usuario
        $sql = "UPDATE usuarios 
                SET username = ?, nombre_completo = ?, email = ?, rol = ?, estado = ?
                WHERE id = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username, $nombre_completo, $email, $rol, $estado, $id]);
        
        if ($stmt->rowCount() == 0) {
            throw new Exception('Usuario no encontrado');
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Usuario actualizado exitosamente'
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Error al actualizar usuario: ' . $e->getMessage());
    }
}

function cambiarEstadoUsuario() {
    global $pdo;
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    $id = (int) ($input['id'] ?? 0);
    $estado = trim($input['estado'] ?? '');
    
    if (!$id || !in_array($estado, ['activo', 'inactivo'])) {
        throw new Exception('ID y estado válido son requeridos');
    }
    
    // No permitir desactivar al usuario admin principal
    if ($id == 1 && $estado == 'inactivo') {
        throw new Exception('No se puede desactivar al usuario administrador principal');
    }
    
    try {
        $sql = "UPDATE usuarios SET estado = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$estado, $id]);
        
        if ($stmt->rowCount() == 0) {
            throw new Exception('Usuario no encontrado');
        }
        
        $accion = $estado === 'activo' ? 'activado' : 'desactivado';
        
        echo json_encode([
            'success' => true,
            'message' => "Usuario $accion exitosamente"
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Error al cambiar estado: ' . $e->getMessage());
    }
}

function resetearPassword() {
    global $pdo;
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    $id = (int) ($input['id'] ?? 0);
    $nueva_password = trim($input['nueva_password'] ?? '');
    
    if (!$id) {
        throw new Exception('ID del usuario es requerido');
    }
    
    // Si no se proporciona nueva contraseña, generar una temporal
    if (empty($nueva_password)) {
        $nueva_password = 'temp' . rand(1000, 9999);
    }
    
    if (strlen($nueva_password) < 6) {
        throw new Exception('La nueva contraseña debe tener al menos 6 caracteres');
    }
    
    try {
        // Obtener información del usuario
        $userSql = "SELECT username, nombre_completo FROM usuarios WHERE id = ?";
        $userStmt = $pdo->prepare($userSql);
        $userStmt->execute([$id]);
        $usuario = $userStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$usuario) {
            throw new Exception('Usuario no encontrado');
        }
        
        // Actualizar contraseña
        $passwordHash = password_hash($nueva_password, PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET password_hash = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$passwordHash, $id]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Contraseña reseteada exitosamente',
            'nueva_password' => $nueva_password,
            'usuario' => $usuario['username']
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Error al resetear contraseña: ' . $e->getMessage());
    }
}
?>