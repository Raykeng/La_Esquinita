-- Crear tabla usuarios para La Esquinita POS
-- Este script crea la tabla usuarios con las columnas correctas

-- Eliminar tabla si existe (para empezar limpio)
DROP TABLE IF EXISTS usuarios;

-- Crear tabla usuarios
CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    nombre_completo VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    rol ENUM('administrador', 'cajero') NOT NULL DEFAULT 'cajero',
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    ultimo_acceso TIMESTAMP NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    creado_por INT
);

-- Insertar usuario administrador
INSERT INTO usuarios (username, password_hash, nombre_completo, rol) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador', 'administrador');

-- Insertar cajero de ejemplo  
INSERT INTO usuarios (username, password_hash, nombre_completo, rol) 
VALUES ('marlon', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Marlon Cajero', 'cajero');

-- Mostrar usuarios creados
SELECT 'Usuarios creados exitosamente:' as mensaje;
SELECT username, nombre_completo, rol FROM usuarios;