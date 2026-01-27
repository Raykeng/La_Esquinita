-- Script para crear tablas faltantes del sistema La Esquinita
-- Autor: Marlon
-- Fecha: 26/01/2026

USE la_esquinita;

-- Tabla de clientes
CREATE TABLE IF NOT EXISTS clientes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    email VARCHAR(100),
    direccion TEXT,
    puntos_acumulados INT DEFAULT 0,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nombre (nombre),
    INDEX idx_telefono (telefono),
    INDEX idx_estado (estado)
);

-- Tabla de cierres de caja
CREATE TABLE IF NOT EXISTS cierres_caja (
    id INT PRIMARY KEY AUTO_INCREMENT,
    fecha_cierre DATE NOT NULL,
    turno ENUM('matutino', 'vespertino', 'nocturno', 'Diurno') DEFAULT 'Diurno',
    total_ventas DECIMAL(10,2) NOT NULL DEFAULT 0,
    total_efectivo DECIMAL(10,2) NOT NULL DEFAULT 0,
    total_tarjeta DECIMAL(10,2) NOT NULL DEFAULT 0,
    total_vales DECIMAL(10,2) NOT NULL DEFAULT 0,
    ingresos_adicionales DECIMAL(10,2) DEFAULT 0,
    egresos DECIMAL(10,2) DEFAULT 0,
    total_final DECIMAL(10,2) NOT NULL DEFAULT 0,
    diferencia DECIMAL(10,2) DEFAULT 0,
    usuario VARCHAR(50) NOT NULL,
    detalles JSON,
    notas TEXT,
    estado ENUM('abierto', 'cerrado') DEFAULT 'abierto',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_fecha_cierre (fecha_cierre),
    INDEX idx_usuario (usuario),
    INDEX idx_estado (estado)
);

-- Tabla de movimientos de caja (ingresos y egresos adicionales)
CREATE TABLE IF NOT EXISTS movimientos_caja (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tipo ENUM('ingreso', 'egreso') NOT NULL,
    concepto VARCHAR(255) NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    usuario VARCHAR(50) NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_tipo (tipo),
    INDEX idx_fecha (fecha),
    INDEX idx_usuario (usuario)
);

-- Tabla de usuarios (para autenticación futura)
CREATE TABLE IF NOT EXISTS usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    nombre_completo VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    rol ENUM('administrador', 'cajero') NOT NULL DEFAULT 'cajero',
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    ultimo_acceso TIMESTAMP NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    creado_por INT,
    INDEX idx_username (username),
    INDEX idx_rol (rol),
    INDEX idx_estado (estado),
    FOREIGN KEY (creado_por) REFERENCES usuarios(id) ON DELETE SET NULL
);

-- Tabla de sesiones (para autenticación futura)
CREATE TABLE IF NOT EXISTS sesiones (
    id VARCHAR(128) PRIMARY KEY,
    usuario_id INT NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    INDEX idx_usuario_id (usuario_id),
    INDEX idx_expires_at (expires_at),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabla de movimientos de inventario (para auditoría)
CREATE TABLE IF NOT EXISTS movimientos_inventario (
    id INT PRIMARY KEY AUTO_INCREMENT,
    producto_id INT NOT NULL,
    tipo_movimiento ENUM('entrada', 'salida', 'ajuste') NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2),
    referencia_id INT NULL, -- venta_id, compra_id, etc.
    tipo_referencia ENUM('venta', 'compra', 'ajuste', 'devolucion') NULL,
    usuario VARCHAR(50) NOT NULL,
    motivo VARCHAR(255),
    fecha_movimiento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_producto_id (producto_id),
    INDEX idx_tipo_movimiento (tipo_movimiento),
    INDEX idx_fecha_movimiento (fecha_movimiento),
    INDEX idx_usuario (usuario),
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE
);

-- Insertar usuario administrador por defecto
INSERT IGNORE INTO usuarios (username, password_hash, nombre_completo, rol, estado) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador', 'administrador', 'activo');

-- Insertar algunos clientes de ejemplo
INSERT IGNORE INTO clientes (nombre, telefono, puntos_acumulados) VALUES 
('Público General', NULL, 0),
('Cliente Frecuente', '12345678', 150),
('María González', '87654321', 75);

-- Verificar que la tabla ventas tenga la columna cliente_id
ALTER TABLE ventas ADD COLUMN IF NOT EXISTS cliente_id INT DEFAULT 1;
ALTER TABLE ventas ADD COLUMN IF NOT EXISTS metodo_pago ENUM('efectivo', 'tarjeta', 'vales') DEFAULT 'efectivo';

-- Agregar índices a la tabla ventas si no existen
ALTER TABLE ventas ADD INDEX IF NOT EXISTS idx_cliente_id (cliente_id);
ALTER TABLE ventas ADD INDEX IF NOT EXISTS idx_metodo_pago (metodo_pago);
ALTER TABLE ventas ADD INDEX IF NOT EXISTS idx_fecha_venta (fecha_venta);

-- Agregar foreign key a ventas si no existe
-- ALTER TABLE ventas ADD CONSTRAINT fk_ventas_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id);

COMMIT;

-- Mostrar resumen de tablas creadas
SELECT 'Tablas creadas exitosamente' as mensaje;
SELECT TABLE_NAME as 'Tablas en la base de datos' 
FROM INFORMATION_SCHEMA.TABLES 
WHERE TABLE_SCHEMA = 'la_esquinita' 
ORDER BY TABLE_NAME;