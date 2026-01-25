-- =====================================================
-- BASE DE DATOS: ABARROTERÍA "LA ESQUINITA"
-- Sistema de Punto de Venta (POS)
-- Versión: 1.0
-- Fecha: 2026-01-23
-- =====================================================

-- Crear base de datos
DROP DATABASE IF EXISTS la_esquinita;
CREATE DATABASE la_esquinita CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE la_esquinita;

-- =====================================================
-- MÓDULO: GESTIÓN DE USUARIOS Y ROLES
-- =====================================================

-- Tabla: roles
CREATE TABLE roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion TEXT,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_estado (estado)
) ENGINE=InnoDB;

-- Tabla: permisos
CREATE TABLE permisos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    modulo VARCHAR(50) NOT NULL,
    accion VARCHAR(50) NOT NULL,
    descripcion TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_modulo_accion (modulo, accion),
    INDEX idx_modulo (modulo)
) ENGINE=InnoDB;

-- Tabla: rol_permisos (relación muchos a muchos)
CREATE TABLE rol_permisos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    rol_id INT NOT NULL,
    permiso_id INT NOT NULL,
    fecha_asignacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (rol_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permiso_id) REFERENCES permisos(id) ON DELETE CASCADE,
    UNIQUE KEY uk_rol_permiso (rol_id, permiso_id)
) ENGINE=InnoDB;

-- Tabla: usuarios
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

-- =====================================================
-- MÓDULO: PRODUCTOS E INVENTARIO
-- =====================================================

-- Tabla: categorias
CREATE TABLE categorias (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_estado (estado)
) ENGINE=InnoDB;

-- Tabla: proveedores
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

-- Tabla: productos
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
    INDEX idx_stock (stock_actual)
) ENGINE=InnoDB;

-- Tabla: movimientos_inventario
CREATE TABLE movimientos_inventario (
    id INT PRIMARY KEY AUTO_INCREMENT,
    producto_id INT NOT NULL,
    tipo_movimiento ENUM('entrada', 'salida', 'ajuste', 'merma', 'devolucion') NOT NULL,
    cantidad INT NOT NULL,
    stock_anterior INT NOT NULL,
    stock_nuevo INT NOT NULL,
    motivo VARCHAR(255),
    usuario_id INT,
    referencia VARCHAR(100), -- Número de venta, compra, etc.
    fecha_movimiento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_producto (producto_id),
    INDEX idx_tipo (tipo_movimiento),
    INDEX idx_fecha (fecha_movimiento)
) ENGINE=InnoDB;

-- =====================================================
-- MÓDULO: VENTAS
-- =====================================================

-- Tabla: clientes
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

-- Tabla: ventas
CREATE TABLE ventas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    folio VARCHAR(20) UNIQUE NOT NULL,
    cliente_id INT,
    usuario_id INT NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    descuento DECIMAL(10, 2) DEFAULT 0.00,
    impuestos DECIMAL(10, 2) DEFAULT 0.00,
    total DECIMAL(10, 2) NOT NULL,
    metodo_pago ENUM('efectivo', 'tarjeta', 'transferencia', 'credito', 'mixto') NOT NULL,
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
    INDEX idx_estado (estado)
) ENGINE=InnoDB;

-- Tabla: detalle_ventas
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

-- Tabla: pagos_venta
CREATE TABLE pagos_venta (
    id INT PRIMARY KEY AUTO_INCREMENT,
    venta_id INT NOT NULL,
    metodo_pago ENUM('efectivo', 'tarjeta', 'transferencia', 'credito') NOT NULL,
    monto DECIMAL(10, 2) NOT NULL,
    referencia VARCHAR(100),
    fecha_pago TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (venta_id) REFERENCES ventas(id) ON DELETE CASCADE,
    INDEX idx_venta (venta_id),
    INDEX idx_metodo (metodo_pago),
    INDEX idx_fecha (fecha_pago)
) ENGINE=InnoDB;

-- =====================================================
-- MÓDULO: CAJA
-- =====================================================

-- Tabla: cajas
CREATE TABLE cajas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL,
    descripcion TEXT,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_estado (estado)
) ENGINE=InnoDB;

-- Tabla: turnos_caja
CREATE TABLE turnos_caja (
    id INT PRIMARY KEY AUTO_INCREMENT,
    caja_id INT NOT NULL,
    usuario_id INT NOT NULL,
    monto_inicial DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    monto_final DECIMAL(10, 2),
    total_ventas DECIMAL(10, 2) DEFAULT 0.00,
    total_efectivo DECIMAL(10, 2) DEFAULT 0.00,
    total_tarjeta DECIMAL(10, 2) DEFAULT 0.00,
    total_transferencia DECIMAL(10, 2) DEFAULT 0.00,
    retiros DECIMAL(10, 2) DEFAULT 0.00,
    diferencia DECIMAL(10, 2) DEFAULT 0.00,
    estado ENUM('abierto', 'cerrado') DEFAULT 'abierto',
    observaciones TEXT,
    fecha_apertura TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_cierre TIMESTAMP NULL,
    FOREIGN KEY (caja_id) REFERENCES cajas(id) ON DELETE RESTRICT,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    INDEX idx_caja (caja_id),
    INDEX idx_usuario (usuario_id),
    INDEX idx_estado (estado),
    INDEX idx_fecha_apertura (fecha_apertura)
) ENGINE=InnoDB;

-- Tabla: movimientos_caja
CREATE TABLE movimientos_caja (
    id INT PRIMARY KEY AUTO_INCREMENT,
    turno_id INT NOT NULL,
    tipo_movimiento ENUM('venta', 'retiro', 'ingreso', 'ajuste') NOT NULL,
    monto DECIMAL(10, 2) NOT NULL,
    metodo_pago ENUM('efectivo', 'tarjeta', 'transferencia') DEFAULT 'efectivo',
    concepto VARCHAR(255),
    venta_id INT,
    usuario_id INT NOT NULL,
    fecha_movimiento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (turno_id) REFERENCES turnos_caja(id) ON DELETE CASCADE,
    FOREIGN KEY (venta_id) REFERENCES ventas(id) ON DELETE SET NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    INDEX idx_turno (turno_id),
    INDEX idx_tipo (tipo_movimiento),
    INDEX idx_fecha (fecha_movimiento)
) ENGINE=InnoDB;

-- =====================================================
-- MÓDULO: COMPRAS
-- =====================================================

-- Tabla: compras
CREATE TABLE compras (
    id INT PRIMARY KEY AUTO_INCREMENT,
    folio VARCHAR(20) UNIQUE NOT NULL,
    proveedor_id INT NOT NULL,
    usuario_id INT NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    impuestos DECIMAL(10, 2) DEFAULT 0.00,
    total DECIMAL(10, 2) NOT NULL,
    estado ENUM('pendiente', 'recibida', 'cancelada') DEFAULT 'pendiente',
    observaciones TEXT,
    fecha_compra TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_recepcion TIMESTAMP NULL,
    FOREIGN KEY (proveedor_id) REFERENCES proveedores(id) ON DELETE RESTRICT,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    INDEX idx_folio (folio),
    INDEX idx_proveedor (proveedor_id),
    INDEX idx_estado (estado),
    INDEX idx_fecha (fecha_compra)
) ENGINE=InnoDB;

-- Tabla: detalle_compras
CREATE TABLE detalle_compras (
    id INT PRIMARY KEY AUTO_INCREMENT,
    compra_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (compra_id) REFERENCES compras(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE RESTRICT,
    INDEX idx_compra (compra_id),
    INDEX idx_producto (producto_id)
) ENGINE=InnoDB;

-- =====================================================
-- MÓDULO: CONFIGURACIÓN
-- =====================================================

-- Tabla: configuracion
CREATE TABLE configuracion (
    id INT PRIMARY KEY AUTO_INCREMENT,
    clave VARCHAR(100) UNIQUE NOT NULL,
    valor TEXT,
    descripcion TEXT,
    tipo ENUM('texto', 'numero', 'booleano', 'json') DEFAULT 'texto',
    fecha_modificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_clave (clave)
) ENGINE=InnoDB;

-- Tabla: auditoria
CREATE TABLE auditoria (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT,
    accion VARCHAR(100) NOT NULL,
    modulo VARCHAR(50) NOT NULL,
    descripcion TEXT,
    datos_anteriores JSON,
    datos_nuevos JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    fecha_accion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_usuario (usuario_id),
    INDEX idx_modulo (modulo),
    INDEX idx_fecha (fecha_accion)
) ENGINE=InnoDB;

-- =====================================================
-- DATOS INICIALES
-- =====================================================

-- Insertar roles predefinidos
INSERT INTO roles (nombre, descripcion) VALUES
('Administrador', 'Acceso total al sistema, gestión de usuarios y configuración'),
('Gerente', 'Gestión de inventario, reportes, ventas y configuración básica'),
('Cajero', 'Realizar ventas, consultar inventario y gestionar caja'),
('Almacenista', 'Gestión de inventario, productos y recepción de mercancía'),
('Contador', 'Acceso a reportes financieros, ventas y auditoría');

-- Insertar permisos por módulo
INSERT INTO permisos (modulo, accion, descripcion) VALUES
-- Ventas
('ventas', 'crear', 'Realizar nuevas ventas'),
('ventas', 'ver', 'Consultar ventas realizadas'),
('ventas', 'cancelar', 'Cancelar ventas'),
('ventas', 'aplicar_descuento', 'Aplicar descuentos en ventas'),
('ventas', 'ver_historial', 'Ver historial completo de ventas'),

-- Inventario
('inventario', 'crear_producto', 'Agregar nuevos productos'),
('inventario', 'editar_producto', 'Modificar información de productos'),
('inventario', 'eliminar_producto', 'Eliminar productos'),
('inventario', 'ver_stock', 'Consultar existencias'),
('inventario', 'ajustar_stock', 'Realizar ajustes de inventario'),

-- Reportes
('reportes', 'ventas_diarias', 'Ver reporte de ventas diarias'),
('reportes', 'ventas_mensuales', 'Ver reporte de ventas mensuales'),
('reportes', 'inventario', 'Ver reporte de inventario'),
('reportes', 'financiero', 'Ver reportes financieros'),
('reportes', 'exportar', 'Exportar reportes a PDF/Excel'),

-- Caja
('caja', 'abrir', 'Abrir caja'),
('caja', 'cerrar', 'Cerrar caja'),
('caja', 'ver_movimientos', 'Ver movimientos de caja'),
('caja', 'retiro_efectivo', 'Realizar retiros de efectivo'),

-- Configuración
('configuracion', 'usuarios', 'Gestionar usuarios del sistema'),
('configuracion', 'roles', 'Gestionar roles y permisos'),
('configuracion', 'empresa', 'Configurar datos de la empresa'),
('configuracion', 'impresora', 'Configurar impresora de tickets'),

-- Compras
('compras', 'crear', 'Registrar nuevas compras'),
('compras', 'ver', 'Consultar compras'),
('compras', 'recibir', 'Recibir mercancía'),
('compras', 'cancelar', 'Cancelar compras'),

-- Clientes
('clientes', 'crear', 'Registrar nuevos clientes'),
('clientes', 'editar', 'Modificar información de clientes'),
('clientes', 'ver', 'Consultar información de clientes'),
('clientes', 'credito', 'Gestionar crédito de clientes');

-- Asignar permisos al rol Administrador (todos los permisos)
INSERT INTO rol_permisos (rol_id, permiso_id)
SELECT 1, id FROM permisos;

-- Asignar permisos al rol Gerente
INSERT INTO rol_permisos (rol_id, permiso_id)
SELECT 2, id FROM permisos 
WHERE modulo IN ('ventas', 'inventario', 'reportes', 'compras', 'clientes')
   OR (modulo = 'caja' AND accion IN ('abrir', 'cerrar', 'ver_movimientos', 'retiro_efectivo'))
   OR (modulo = 'configuracion' AND accion IN ('empresa', 'impresora'));

-- Asignar permisos al rol Cajero
INSERT INTO rol_permisos (rol_id, permiso_id)
SELECT 3, id FROM permisos 
WHERE (modulo = 'ventas' AND accion IN ('crear', 'ver'))
   OR (modulo = 'inventario' AND accion = 'ver_stock')
   OR (modulo = 'caja' AND accion IN ('abrir', 'cerrar', 'ver_movimientos'))
   OR (modulo = 'clientes' AND accion IN ('crear', 'ver'))
   OR (modulo = 'configuracion' AND accion = 'impresora');

-- Asignar permisos al rol Almacenista
INSERT INTO rol_permisos (rol_id, permiso_id)
SELECT 4, id FROM permisos 
WHERE modulo IN ('inventario', 'compras')
   OR (modulo = 'reportes' AND accion = 'inventario');

-- Asignar permisos al rol Contador
INSERT INTO rol_permisos (rol_id, permiso_id)
SELECT 5, id FROM permisos 
WHERE modulo = 'reportes'
   OR (modulo = 'ventas' AND accion IN ('ver', 'ver_historial'))
   OR (modulo = 'caja' AND accion = 'ver_movimientos');

-- Crear usuario administrador por defecto
-- Contraseña: admin123 (hash bcrypt)
INSERT INTO usuarios (nombre_completo, email, password_hash, rol_id, estado) VALUES
('Administrador', 'admin@laesquinita.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'activo');

-- Insertar categorías iniciales
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

-- Insertar caja principal
INSERT INTO cajas (nombre, descripcion) VALUES
('Caja Principal', 'Caja principal del punto de venta');

-- Insertar configuración inicial
INSERT INTO configuracion (clave, valor, descripcion, tipo) VALUES
('nombre_empresa', 'Abarrotería La Esquinita', 'Nombre de la empresa', 'texto'),
('rfc_empresa', 'XAXX010101000', 'RFC de la empresa', 'texto'),
('direccion_empresa', 'Calle Principal #123, Colonia Centro', 'Dirección de la empresa', 'texto'),
('telefono_empresa', '555-1234', 'Teléfono de contacto', 'texto'),
('email_empresa', 'contacto@laesquinita.com', 'Email de contacto', 'texto'),
('iva_porcentaje', '16', 'Porcentaje de IVA', 'numero'),
('moneda', 'MXN', 'Moneda del sistema', 'texto'),
('ticket_mensaje_pie', '¡Gracias por su compra!', 'Mensaje al pie del ticket', 'texto'),
('alerta_stock_minimo', 'true', 'Activar alertas de stock mínimo', 'booleano');

-- =====================================================
-- VISTAS ÚTILES
-- =====================================================

-- Vista: productos con stock bajo
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

-- Vista: ventas del día
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

-- Vista: productos más vendidos
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

-- =====================================================
-- PROCEDIMIENTOS ALMACENADOS
-- =====================================================

DELIMITER $$

-- Procedimiento: Registrar venta completa
CREATE PROCEDURE sp_registrar_venta(
    IN p_cliente_id INT,
    IN p_usuario_id INT,
    IN p_metodo_pago VARCHAR(20),
    IN p_productos JSON,
    OUT p_venta_id INT,
    OUT p_folio VARCHAR(20)
)
BEGIN
    DECLARE v_subtotal DECIMAL(10,2) DEFAULT 0;
    DECLARE v_total DECIMAL(10,2) DEFAULT 0;
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
    
    -- Generar folio único
    SET p_folio = CONCAT('V', LPAD(FLOOR(RAND() * 999999), 6, '0'));
    
    -- Crear venta
    INSERT INTO ventas (folio, cliente_id, usuario_id, metodo_pago, subtotal, total)
    VALUES (p_folio, p_cliente_id, p_usuario_id, p_metodo_pago, 0, 0);
    
    SET p_venta_id = LAST_INSERT_ID();
    
    -- Procesar productos
    SET v_count = JSON_LENGTH(p_productos);
    
    WHILE v_idx < v_count DO
        SET v_producto_id = JSON_UNQUOTE(JSON_EXTRACT(p_productos, CONCAT('$[', v_idx, '].producto_id')));
        SET v_cantidad = JSON_UNQUOTE(JSON_EXTRACT(p_productos, CONCAT('$[', v_idx, '].cantidad')));
        
        -- Obtener precio y stock
        SELECT precio_venta, stock_actual INTO v_precio, v_stock_actual
        FROM productos WHERE id = v_producto_id;
        
        -- Verificar stock
        IF v_stock_actual < v_cantidad THEN
            SIGNAL SQLSTATE '45000' 
            SET MESSAGE_TEXT = 'Stock insuficiente';
        END IF;
        
        -- Insertar detalle
        INSERT INTO detalle_ventas (venta_id, producto_id, cantidad, precio_unitario, subtotal)
        VALUES (p_venta_id, v_producto_id, v_cantidad, v_precio, v_cantidad * v_precio);
        
        SET v_subtotal = v_subtotal + (v_cantidad * v_precio);
        
        -- Actualizar stock
        UPDATE productos 
        SET stock_actual = stock_actual - v_cantidad
        WHERE id = v_producto_id;
        
        -- Registrar movimiento de inventario
        INSERT INTO movimientos_inventario 
        (producto_id, tipo_movimiento, cantidad, stock_anterior, stock_nuevo, usuario_id, referencia)
        VALUES (v_producto_id, 'salida', v_cantidad, v_stock_actual, v_stock_actual - v_cantidad, p_usuario_id, p_folio);
        
        SET v_idx = v_idx + 1;
    END WHILE;
    
    -- Actualizar totales de venta
    SET v_total = v_subtotal;
    UPDATE ventas SET subtotal = v_subtotal, total = v_total WHERE id = p_venta_id;
    
    COMMIT;
END$$

DELIMITER ;

-- =====================================================
-- TRIGGERS
-- =====================================================

DELIMITER $$

-- Trigger: Actualizar fecha de modificación en productos
CREATE TRIGGER tr_productos_before_update
BEFORE UPDATE ON productos
FOR EACH ROW
BEGIN
    SET NEW.fecha_modificacion = CURRENT_TIMESTAMP;
END$$

-- Trigger: Validar stock mínimo al actualizar productos
CREATE TRIGGER tr_productos_after_update
AFTER UPDATE ON productos
FOR EACH ROW
BEGIN
    IF NEW.stock_actual <= NEW.stock_minimo THEN
        INSERT INTO auditoria (usuario_id, accion, modulo, descripcion)
        VALUES (NULL, 'alerta_stock_bajo', 'inventario', 
                CONCAT('Producto "', NEW.nombre, '" tiene stock bajo: ', NEW.stock_actual));
    END IF;
END$$

DELIMITER ;

-- =====================================================
-- ÍNDICES ADICIONALES PARA OPTIMIZACIÓN
-- =====================================================

-- Índices compuestos para consultas frecuentes
CREATE INDEX idx_ventas_fecha_estado ON ventas(fecha_venta, estado);
CREATE INDEX idx_productos_categoria_estado ON productos(categoria_id, estado);
CREATE INDEX idx_detalle_ventas_venta_producto ON detalle_ventas(venta_id, producto_id);

-- =====================================================
-- FIN DEL SCRIPT
-- =====================================================
