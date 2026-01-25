-- Base de Datos: La Esquinita
-- Sistema POS para Abarrotería
-- Fecha: 2026-01-24

DROP DATABASE IF EXISTS la_esquinita;
CREATE DATABASE la_esquinita CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE la_esquinita;

-- Gestión de Roles y Permisos

CREATE TABLE roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion TEXT,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_estado (estado)
) ENGINE=InnoDB;

CREATE TABLE permisos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    modulo VARCHAR(50) NOT NULL,
    accion VARCHAR(50) NOT NULL,
    descripcion TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_modulo_accion (modulo, accion),
    INDEX idx_modulo (modulo)
) ENGINE=InnoDB;

CREATE TABLE rol_permisos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    rol_id INT NOT NULL,
    permiso_id INT NOT NULL,
    fecha_asignacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (rol_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permiso_id) REFERENCES permisos(id) ON DELETE CASCADE,
    UNIQUE KEY uk_rol_permiso (rol_id, permiso_id)
) ENGINE=InnoDB;

CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre_completo VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    telefono VARCHAR(20),
    rol_id INT,
    foto_perfil VARCHAR(255),
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    ultimo_acceso TIMESTAMP NULL,
    FOREIGN KEY (rol_id) REFERENCES roles(id) ON DELETE SET NULL,
    INDEX idx_email (email),
    INDEX idx_estado (estado),
    INDEX idx_rol (rol_id)
) ENGINE=InnoDB;

-- Catálogo de Productos

CREATE TABLE categorias (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_estado (estado)
) ENGINE=InnoDB;

CREATE TABLE proveedores (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(150) NOT NULL,
    rfc VARCHAR(13),
    contacto VARCHAR(100),
    telefono VARCHAR(20),
    email VARCHAR(100),
    direccion TEXT,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nombre (nombre),
    INDEX idx_estado (estado)
) ENGINE=InnoDB;

CREATE TABLE productos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    codigo_barras VARCHAR(50) UNIQUE,
    nombre VARCHAR(200) NOT NULL,
    descripcion TEXT,
    categoria_id INT,
    proveedor_id INT,
    precio_compra DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    precio_venta DECIMAL(10, 2) NOT NULL,
    stock_actual INT NOT NULL DEFAULT 0,
    stock_minimo INT DEFAULT 5,
    stock_maximo INT DEFAULT 100,
    unidad_medida ENUM('pieza', 'kg', 'litro', 'paquete', 'caja', 'otro') DEFAULT 'pieza',
    fecha_vencimiento DATE NULL,
    imagen VARCHAR(255),
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE SET NULL,
    FOREIGN KEY (proveedor_id) REFERENCES proveedores(id) ON DELETE SET NULL,
    INDEX idx_codigo_barras (codigo_barras),
    INDEX idx_nombre (nombre),
    INDEX idx_categoria (categoria_id),
    INDEX idx_estado (estado),
    INDEX idx_stock (stock_actual),
    INDEX idx_vencimiento (fecha_vencimiento)
) ENGINE=InnoDB;

-- Historial de movimientos (incluye compras a proveedores)
CREATE TABLE movimientos_inventario (
    id INT PRIMARY KEY AUTO_INCREMENT,
    producto_id INT NOT NULL,
    tipo_movimiento ENUM('entrada', 'salida', 'ajuste', 'merma') NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10, 2) DEFAULT 0.00,
    stock_anterior INT NOT NULL,
    stock_nuevo INT NOT NULL,
    motivo VARCHAR(255),
    proveedor_id INT NULL,
    usuario_id INT,
    referencia VARCHAR(100),
    fecha_movimiento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE,
    FOREIGN KEY (proveedor_id) REFERENCES proveedores(id) ON DELETE SET NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_producto (producto_id),
    INDEX idx_tipo (tipo_movimiento),
    INDEX idx_fecha (fecha_movimiento),
    INDEX idx_proveedor (proveedor_id)
) ENGINE=InnoDB;

-- Módulo de Ventas

CREATE TABLE clientes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(150) NOT NULL,
    telefono VARCHAR(20),
    email VARCHAR(100),
    direccion TEXT,
    rfc VARCHAR(13),
    credito_disponible DECIMAL(10, 2) DEFAULT 0.00,
    credito_utilizado DECIMAL(10, 2) DEFAULT 0.00,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nombre (nombre),
    INDEX idx_telefono (telefono),
    INDEX idx_estado (estado)
) ENGINE=InnoDB;

CREATE TABLE ventas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    folio VARCHAR(20) UNIQUE NOT NULL,
    cliente_id INT,
    usuario_id INT NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    descuento DECIMAL(10, 2) DEFAULT 0.00,
    impuestos DECIMAL(10, 2) DEFAULT 0.00,
    total DECIMAL(10, 2) NOT NULL,
    metodo_pago ENUM('efectivo', 'tarjeta', 'transferencia', 'credito') NOT NULL,
    monto_recibido DECIMAL(10, 2) DEFAULT 0.00,
    cambio DECIMAL(10, 2) DEFAULT 0.00,
    estado ENUM('completada', 'cancelada', 'pendiente') DEFAULT 'completada',
    observaciones TEXT,
    fecha_venta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_cancelacion TIMESTAMP NULL,
    motivo_cancelacion TEXT,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE SET NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    INDEX idx_folio (folio),
    INDEX idx_cliente (cliente_id),
    INDEX idx_usuario (usuario_id),
    INDEX idx_fecha (fecha_venta),
    INDEX idx_estado (estado),
    INDEX idx_metodo_pago (metodo_pago)
) ENGINE=InnoDB;

CREATE TABLE detalle_ventas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    venta_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10, 2) NOT NULL,
    descuento DECIMAL(10, 2) DEFAULT 0.00,
    subtotal DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (venta_id) REFERENCES ventas(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE RESTRICT,
    INDEX idx_venta (venta_id),
    INDEX idx_producto (producto_id)
) ENGINE=InnoDB;

-- Control de Caja

CREATE TABLE turnos_caja (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    monto_inicial DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    monto_final DECIMAL(10, 2),
    total_ventas DECIMAL(10, 2) DEFAULT 0.00,
    total_efectivo DECIMAL(10, 2) DEFAULT 0.00,
    total_tarjeta DECIMAL(10, 2) DEFAULT 0.00,
    total_transferencia DECIMAL(10, 2) DEFAULT 0.00,
    retiros DECIMAL(10, 2) DEFAULT 0.00,
    ingresos_extra DECIMAL(10, 2) DEFAULT 0.00,
    diferencia DECIMAL(10, 2) DEFAULT 0.00,
    estado ENUM('abierto', 'cerrado') DEFAULT 'abierto',
    observaciones TEXT,
    fecha_apertura TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_cierre TIMESTAMP NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    INDEX idx_usuario (usuario_id),
    INDEX idx_estado (estado),
    INDEX idx_fecha_apertura (fecha_apertura)
) ENGINE=InnoDB;

-- Datos iniciales

INSERT INTO roles (nombre, descripcion) VALUES
('Administrador', 'Acceso total al sistema, gestión de usuarios y configuración'),
('Cajero', 'Realizar ventas, consultar inventario y gestionar caja'),
('Contador', 'Acceso a reportes financieros, ventas y auditoría');

INSERT INTO permisos (modulo, accion, descripcion) VALUES
('ventas', 'crear', 'Realizar nuevas ventas'),
('ventas', 'ver', 'Consultar ventas realizadas'),
('ventas', 'cancelar', 'Cancelar ventas'),
('ventas', 'aplicar_descuento', 'Aplicar descuentos en ventas'),
('ventas', 'ver_historial', 'Ver historial completo de ventas'),
('inventario', 'crear_producto', 'Agregar nuevos productos'),
('inventario', 'editar_producto', 'Modificar información de productos'),
('inventario', 'eliminar_producto', 'Eliminar productos'),
('inventario', 'ver_stock', 'Consultar existencias'),
('inventario', 'ajustar_stock', 'Realizar ajustes de inventario'),
('inventario', 'registrar_compra', 'Registrar compras a proveedores'),
('reportes', 'ventas_diarias', 'Ver reporte de ventas diarias'),
('reportes', 'ventas_mensuales', 'Ver reporte de ventas mensuales'),
('reportes', 'inventario', 'Ver reporte de inventario'),
('reportes', 'financiero', 'Ver reportes financieros'),
('reportes', 'exportar', 'Exportar reportes a PDF/Excel'),
('caja', 'abrir', 'Abrir caja'),
('caja', 'cerrar', 'Cerrar caja'),
('caja', 'ver_movimientos', 'Ver movimientos de caja'),
('caja', 'retiro_efectivo', 'Realizar retiros de efectivo'),
('configuracion', 'usuarios', 'Gestionar usuarios del sistema'),
('configuracion', 'roles', 'Gestionar roles y permisos'),
('configuracion', 'empresa', 'Configurar datos de la empresa'),
('clientes', 'crear', 'Registrar nuevos clientes'),
('clientes', 'editar', 'Modificar información de clientes'),
('clientes', 'ver', 'Consultar información de clientes'),
('clientes', 'credito', 'Gestionar crédito de clientes'),
('proveedores', 'crear', 'Registrar nuevos proveedores'),
('proveedores', 'editar', 'Modificar información de proveedores'),
('proveedores', 'ver', 'Consultar información de proveedores');

-- Permisos para Administrador
INSERT INTO rol_permisos (rol_id, permiso_id)
SELECT 1, id FROM permisos;

-- Permisos para Cajero
INSERT INTO rol_permisos (rol_id, permiso_id)
SELECT 2, id FROM permisos 
WHERE (modulo = 'ventas' AND accion IN ('crear', 'ver'))
   OR (modulo = 'inventario' AND accion = 'ver_stock')
   OR (modulo = 'caja' AND accion IN ('abrir', 'cerrar', 'ver_movimientos'))
   OR (modulo = 'clientes' AND accion IN ('crear', 'ver'));

-- Permisos para Contador
INSERT INTO rol_permisos (rol_id, permiso_id)
SELECT 3, id FROM permisos 
WHERE modulo = 'reportes'
   OR (modulo = 'ventas' AND accion IN ('ver', 'ver_historial'))
   OR (modulo = 'caja' AND accion = 'ver_movimientos')
   OR (modulo = 'inventario' AND accion = 'ver_stock');

-- Usuario admin por defecto (password: admin123)
INSERT INTO usuarios (nombre_completo, email, password_hash, rol_id, estado) VALUES
('Administrador', 'admin@laesquinita.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'activo');

INSERT INTO categorias (nombre, descripcion) VALUES
('Abarrotes', 'Productos básicos de despensa'),
('Bebidas', 'Refrescos, jugos y bebidas'),
('Lácteos', 'Leche, queso, yogurt'),
('Panadería', 'Pan y productos de panadería'),
('Limpieza', 'Productos de limpieza del hogar'),
('Botanas', 'Frituras y snacks'),
('Dulces', 'Dulces y chocolates'),
('Higiene Personal', 'Productos de cuidado personal'),
('Enlatados', 'Productos enlatados y conservas'),
('Congelados', 'Productos congelados');

-- Vistas para reportes

CREATE VIEW v_productos_stock_bajo AS
SELECT 
    p.id,
    p.codigo_barras,
    p.nombre,
    c.nombre AS categoria,
    p.stock_actual,
    p.stock_minimo,
    p.precio_venta,
    pr.nombre AS proveedor,
    pr.telefono AS telefono_proveedor
FROM productos p
LEFT JOIN categorias c ON p.categoria_id = c.id
LEFT JOIN proveedores pr ON p.proveedor_id = pr.id
WHERE p.stock_actual <= p.stock_minimo
  AND p.estado = 'activo';

CREATE VIEW v_productos_por_vencer AS
SELECT 
    p.id,
    p.codigo_barras,
    p.nombre,
    c.nombre AS categoria,
    p.fecha_vencimiento,
    DATEDIFF(p.fecha_vencimiento, CURDATE()) AS dias_restantes,
    p.stock_actual,
    p.precio_venta
FROM productos p
LEFT JOIN categorias c ON p.categoria_id = c.id
WHERE p.fecha_vencimiento IS NOT NULL
  AND p.fecha_vencimiento <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
  AND p.estado = 'activo'
ORDER BY p.fecha_vencimiento ASC;

CREATE VIEW v_ventas_hoy AS
SELECT 
    v.id,
    v.folio,
    c.nombre AS cliente,
    u.nombre_completo AS vendedor,
    v.total,
    v.metodo_pago,
    v.estado,
    v.fecha_venta
FROM ventas v
LEFT JOIN clientes c ON v.cliente_id = c.id
INNER JOIN usuarios u ON v.usuario_id = u.id
WHERE DATE(v.fecha_venta) = CURDATE()
ORDER BY v.fecha_venta DESC;

CREATE VIEW v_productos_mas_vendidos AS
SELECT 
    p.id,
    p.nombre,
    p.codigo_barras,
    c.nombre AS categoria,
    SUM(dv.cantidad) AS total_vendido,
    SUM(dv.subtotal) AS ingresos_totales,
    COUNT(DISTINCT dv.venta_id) AS numero_ventas
FROM productos p
INNER JOIN detalle_ventas dv ON p.id = dv.producto_id
INNER JOIN ventas v ON dv.venta_id = v.id
LEFT JOIN categorias c ON p.categoria_id = c.id
WHERE v.estado = 'completada'
  AND v.fecha_venta >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY p.id, p.nombre, p.codigo_barras, c.nombre
ORDER BY total_vendido DESC;

CREATE VIEW v_permisos_por_rol AS
SELECT 
    r.id AS rol_id,
    r.nombre AS rol,
    p.modulo,
    p.accion,
    p.descripcion
FROM roles r
INNER JOIN rol_permisos rp ON r.id = rp.rol_id
INNER JOIN permisos p ON rp.permiso_id = p.id
WHERE r.estado = 'activo'
ORDER BY r.nombre, p.modulo, p.accion;

-- Procedimiento para registrar ventas

DELIMITER $$

CREATE PROCEDURE sp_registrar_venta(
    IN p_cliente_id INT,
    IN p_usuario_id INT,
    IN p_metodo_pago VARCHAR(20),
    IN p_monto_recibido DECIMAL(10,2),
    IN p_productos JSON,
    OUT p_venta_id INT,
    OUT p_folio VARCHAR(20)
)
BEGIN
    DECLARE v_subtotal DECIMAL(10,2) DEFAULT 0;
    DECLARE v_total DECIMAL(10,2) DEFAULT 0;
    DECLARE v_cambio DECIMAL(10,2) DEFAULT 0;
    DECLARE v_producto_id INT;
    DECLARE v_cantidad INT;
    DECLARE v_precio DECIMAL(10,2);
    DECLARE v_stock_actual INT;
    DECLARE v_idx INT DEFAULT 0;
    DECLARE v_count INT;
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    
    START TRANSACTION;
    
    SET p_folio = CONCAT('V', DATE_FORMAT(NOW(), '%Y%m%d'), LPAD(FLOOR(RAND() * 9999), 4, '0'));
    
    INSERT INTO ventas (folio, cliente_id, usuario_id, metodo_pago, monto_recibido, subtotal, total)
    VALUES (p_folio, p_cliente_id, p_usuario_id, p_metodo_pago, p_monto_recibido, 0, 0);
    
    SET p_venta_id = LAST_INSERT_ID();
    SET v_count = JSON_LENGTH(p_productos);
    
    WHILE v_idx < v_count DO
        SET v_producto_id = JSON_UNQUOTE(JSON_EXTRACT(p_productos, CONCAT('$[', v_idx, '].producto_id')));
        SET v_cantidad = JSON_UNQUOTE(JSON_EXTRACT(p_productos, CONCAT('$[', v_idx, '].cantidad')));
        
        SELECT precio_venta, stock_actual INTO v_precio, v_stock_actual
        FROM productos WHERE id = v_producto_id;
        
        IF v_stock_actual < v_cantidad THEN
            SIGNAL SQLSTATE '45000' 
            SET MESSAGE_TEXT = 'Stock insuficiente';
        END IF;
        
        INSERT INTO detalle_ventas (venta_id, producto_id, cantidad, precio_unitario, subtotal)
        VALUES (p_venta_id, v_producto_id, v_cantidad, v_precio, v_cantidad * v_precio);
        
        SET v_subtotal = v_subtotal + (v_cantidad * v_precio);
        
        UPDATE productos 
        SET stock_actual = stock_actual - v_cantidad
        WHERE id = v_producto_id;
        
        INSERT INTO movimientos_inventario 
        (producto_id, tipo_movimiento, cantidad, stock_anterior, stock_nuevo, usuario_id, referencia)
        VALUES (v_producto_id, 'salida', v_cantidad, v_stock_actual, v_stock_actual - v_cantidad, p_usuario_id, p_folio);
        
        SET v_idx = v_idx + 1;
    END WHILE;
    
    SET v_total = v_subtotal;
    SET v_cambio = p_monto_recibido - v_total;
    
    UPDATE ventas 
    SET subtotal = v_subtotal, total = v_total, cambio = v_cambio
    WHERE id = p_venta_id;
    
    COMMIT;
END$$

DELIMITER ;

-- Triggers

DELIMITER $$

CREATE TRIGGER tr_productos_before_update
BEFORE UPDATE ON productos
FOR EACH ROW
BEGIN
    SET NEW.fecha_modificacion = CURRENT_TIMESTAMP;
END$$

DELIMITER ;

-- Índices adicionales para optimización

CREATE INDEX idx_ventas_fecha_estado ON ventas(fecha_venta, estado);
CREATE INDEX idx_productos_categoria_estado ON productos(categoria_id, estado);
CREATE INDEX idx_detalle_ventas_venta_producto ON detalle_ventas(venta_id, producto_id);
CREATE INDEX idx_movimientos_producto_fecha ON movimientos_inventario(producto_id, fecha_movimiento);
