<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../config/db.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'login':
            procesarLogin();
            break;
        case 'logout':
            procesarLogout();
            break;
        case 'verificar':
            verificarSesion();
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

function procesarLogin() {
    global $pdo;
    
    $input = json_decode(file_get_contents('php://input'), true);
    $username = trim($input['username'] ?? '');
    $password = trim($input['password'] ?? '');
    
    if (empty($username) || empty($password)) {
        throw new Exception('Usuario y contraseña son requeridos');
    }
    
    try {
        // Buscar usuario por email (estructura actual de tu BD)
        $sql = "SELECT u.id, u.email, u.password_hash, u.nombre_completo, r.nombre as rol, u.estado 
                FROM usuarios u 
                LEFT JOIN roles r ON u.rol_id = r.id 
                WHERE u.email = ? AND u.estado = 'activo'";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$usuario) {
            throw new Exception('Usuario no encontrado o inactivo');
        }
        
        // Verificar contraseña
        if (!password_verify($password, $usuario['password_hash'])) {
            throw new Exception('Contraseña incorrecta');
        }
        
        // Crear sesión
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['username'] = $usuario['email']; // Usar email como username
        $_SESSION['nombre_completo'] = $usuario['nombre_completo'];
        $_SESSION['rol'] = $usuario['rol'] ?? 'cajero';
        $_SESSION['login_time'] = time();
        
        // Actualizar último acceso
        $updateSql = "UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = ?";
        $updateStmt = $pdo->prepare($updateSql);
        $updateStmt->execute([$usuario['id']]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Login exitoso',
            'usuario' => [
                'id' => $usuario['id'],
                'username' => $usuario['email'],
                'nombre_completo' => $usuario['nombre_completo'],
                'rol' => $usuario['rol'] ?? 'cajero'
            ]
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Error en login: ' . $e->getMessage());
    }
}

function procesarLogout() {
    try {
        // Destruir sesión PHP
        session_destroy();
        
        echo json_encode([
            'success' => true,
            'message' => 'Logout exitoso'
        ]);
        
    } catch (Exception $e) {
        throw new Exception('Error en logout: ' . $e->getMessage());
    }
}

function verificarSesion() {
    try {
        if (!isset($_SESSION['usuario_id'])) {
            throw new Exception('No hay sesión activa');
        }
        
        // Verificar timeout de sesión (30 minutos)
        $timeout = 30 * 60; // 30 minutos en segundos
        if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > $timeout) {
            session_destroy();
            throw new Exception('Sesión expirada');
        }
        
        echo json_encode([
            'success' => true,
            'usuario' => [
                'id' => $_SESSION['usuario_id'],
                'username' => $_SESSION['username'],
                'nombre_completo' => $_SESSION['nombre_completo'],
                'rol' => $_SESSION['rol']
            ]
        ]);
        
    } catch (Exception $e) {
        throw new Exception($e->getMessage());
    }
}
?>