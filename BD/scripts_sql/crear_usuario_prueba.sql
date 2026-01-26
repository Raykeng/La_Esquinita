-- Script para crear usuario de prueba para recuperación de contraseñas
-- Ejecutar después de tener la base de datos principal instalada

USE la_esquinita;

-- Insertar usuario de prueba
INSERT INTO usuarios (nombre_completo, email, telefono, password_hash, rol_id, estado, fecha_creacion) 
VALUES (
    'Usuario Prueba', 
    'mj3u7000@hotmail.com', 
    '3001234567', 
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
    2, 
    'activo', 
    NOW()
);

-- Verificar que se creó correctamente
SELECT id, nombre_completo, email, rol_id, estado 
FROM usuarios 
WHERE email = 'mj3u7000@hotmail.com';

SELECT 'Usuario de prueba creado exitosamente' as resultado;