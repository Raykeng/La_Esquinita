<?php
/**
 * Middleware de Autenticación
 * Autor: Marlon
 * Sistema: La Esquinita POS
 */

session_start();

/**
 * Verificar si el usuario está autenticado
 */
function verificarAutenticacion() {
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: login.php');
        exit;
    }
    
    // Verificar timeout de sesión (30 minutos)
    $timeout = 30 * 60; // 30 minutos en segundos
    if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > $timeout) {
        session_destroy();
        header('Location: login.php?timeout=1');
        exit;
    }
    
    // Actualizar tiempo de actividad
    $_SESSION['last_activity'] = time();
}

/**
 * Verificar si el usuario tiene el rol requerido
 */
function verificarRol($rolRequerido) {
    verificarAutenticacion();
    
    if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== $rolRequerido) {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'message' => 'No tienes permisos para realizar esta acción'
        ]);
        exit;
    }
}

/**
 * Verificar si el usuario es administrador
 */
function verificarAdmin() {
    verificarRol('administrador');
}

/**
 * Verificar si el usuario puede acceder a una vista específica
 */
function verificarAccesoVista($vista) {
    verificarAutenticacion();
    
    $rol = $_SESSION['rol'] ?? '';
    
    // Vistas restringidas para cajeros
    $vistasAdmin = ['usuarios', 'reportes', 'configuracion'];
    
    if ($rol === 'cajero' && in_array($vista, $vistasAdmin)) {
        header('Location: index.php?error=acceso_denegado');
        exit;
    }
}

/**
 * Obtener información del usuario actual
 */
function obtenerUsuarioActual() {
    verificarAutenticacion();
    
    return [
        'id' => $_SESSION['usuario_id'],
        'username' => $_SESSION['username'],
        'nombre_completo' => $_SESSION['nombre_completo'],
        'rol' => $_SESSION['rol']
    ];
}

/**
 * Verificar si el usuario puede cerrar caja
 */
function puedecerrarCaja() {
    verificarAutenticacion();
    
    // Solo administradores pueden cerrar caja
    return $_SESSION['rol'] === 'administrador';
}

/**
 * Verificar si el usuario puede gestionar usuarios
 */
function puedeGestionarUsuarios() {
    verificarAutenticacion();
    
    // Solo administradores pueden gestionar usuarios
    return $_SESSION['rol'] === 'administrador';
}

/**
 * Verificar si el usuario puede ver reportes
 */
function puedeVerReportes() {
    verificarAutenticacion();
    
    // Solo administradores pueden ver reportes completos
    return $_SESSION['rol'] === 'administrador';
}

/**
 * Generar token CSRF
 */
function generarTokenCSRF() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verificar token CSRF
 */
function verificarTokenCSRF($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Limpiar sesión completamente
 */
function limpiarSesion() {
    session_unset();
    session_destroy();
    
    // Limpiar cookie de sesión
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
}
?>