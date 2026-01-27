<?php
session_start();
require_once 'middleware/auth.php';

// Limpiar sesión completamente
limpiarSesion();

// Redirigir al login
header('Location: login.php?logout=1');
exit;
?>