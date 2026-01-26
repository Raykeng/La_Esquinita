-- Base de Datos: La Esquinita
-- Versión 2.0 - Optimizada para abarrotería de barrio
-- Fecha: 25 de Enero de 2026

DROP DATABASE IF EXISTS la_esquinita;
CREATE DATABASE la_esquinita CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE la_esquinita;

-- ---------------------------------------------------------
-- 1. Usuarios y Seguridad
-- Aquí definimos quiénes pueden entrar al sistema
-- ---------------------------------------------------------

CREATE TABLE roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion TEXT,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre_completo VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL, 
    password_hash VARCHAR(255) NOT NULL,
    telefono VARCHAR(20),
    rol_id INT, 
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultimo_acceso TIMESTAMP NULL,
    FOREIGN KEY (rol_id) REFERENCES roles(id) ON DELETE SET NULL,
    INDEX idx_email (email),
    INDEX idx_estado (estado)
) ENGINE=InnoDB;


-- 2. Inventario y Productos
-- Todo lo relacionado con lo que vendemos


CREATE TABLE categorias (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL UNIQUE, 
    descripcion TEXT,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE proveedores (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(150) NOT NULL,
    contacto VARCHAR(100),
    telefono VARCHAR(20),
    email VARCHAR(100),
    direccion TEXT,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_nombre (nombre)
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
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE SET NULL,
    FOREIGN KEY (proveedor_id) REFERENCES proveedores(id) ON DELETE SET NULL,
    INDEX idx_codigo_barras (codigo_barras),
    INDEX idx_nombre (nombre),
    INDEX idx_categoria (categoria_id),
    INDEX idx_vencimiento (fecha_vencimiento)
) ENGINE=InnoDB;

-- 3. Ofertas y Promociones
-- Para mover productos próximos a vencer o combos

CREATE TABLE promociones (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    tipo ENUM('descuento', 'combo', 'vencimiento') NOT NULL,
    descuento_porcentaje DECIMAL(5,2) DEFAULT 0.00,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_tipo (tipo),
    INDEX idx_fechas (fecha_inicio, fecha_fin)
) ENGINE=InnoDB;

CREATE TABLE promocion_productos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    promocion_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad_requerida INT DEFAULT 1, -- Cuántos hay que llevar para la promo
    precio_promocion DECIMAL(10,2), -- Precio especial si aplica
    FOREIGN KEY (promocion_id) REFERENCES promociones(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE,
    UNIQUE KEY uk_promocion_producto (promocion_id, producto_id)
) ENGINE=InnoDB;

-- 4. Historial de Inventario
-- Para saber qué entró y qué salió

CREATE TABLE movimientos_inventario (
    id INT PRIMARY KEY AUTO_INCREMENT,
    producto_id INT NOT NULL,
    tipo_movimiento ENUM('entrada', 'salida', 'ajuste', 'merma') NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10, 2) DEFAULT 0.00,
    stock_anterior INT NOT NULL,
    stock_nuevo INT NOT NULL,
    motivo VARCHAR(255), -- Ej: "Compra proveedor", "Venta #123", "Caducado"
    proveedor_id INT NULL,
    usuario_id INT, -- Quién hizo el movimiento
    referencia VARCHAR(100),
    fecha_movimiento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE,
    FOREIGN KEY (proveedor_id) REFERENCES proveedores(id) ON DELETE SET NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_producto (producto_id),
    INDEX idx_fecha (fecha_movimiento)
) ENGINE=InnoDB;

-- 5. Clientes
-- Información básica para facturación o contacto

CREATE TABLE clientes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(150) NOT NULL,
    telefono VARCHAR(20),
    nit VARCHAR(20), -- Para facturación si solicitan
    direccion TEXT,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_nombre (nombre)
) ENGINE=InnoDB;

-- 6. Ventas y Facturación
-- Registro de todas las transacciones

CREATE TABLE ventas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    folio VARCHAR(20) UNIQUE NOT NULL, -- Código único de venta (Ej: V20260125001)
    cliente_id INT,
    usuario_id INT NOT NULL, -- Cajero que atendió
    subtotal DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    descuento DECIMAL(10, 2) DEFAULT 0.00,
    total DECIMAL(10, 2) NOT NULL,
    metodo_pago ENUM('efectivo', 'tarjeta', 'vales') NOT NULL DEFAULT 'efectivo',
    monto_recibido DECIMAL(10, 2) DEFAULT 0.00,
    cambio DECIMAL(10, 2) DEFAULT 0.00,
    estado ENUM('completada', 'cancelada') DEFAULT 'completada',
    observaciones TEXT,
    fecha_venta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    hora_venta TIME GENERATED ALWAYS AS (TIME(fecha_venta)) STORED, -- Útil para ver horas pico
    fecha_cancelacion TIMESTAMP NULL,
    motivo_cancelacion TEXT,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE SET NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    INDEX idx_folio (folio),
    INDEX idx_fecha (fecha_venta),
    INDEX idx_hora (hora_venta),
    INDEX idx_estado (estado)
) ENGINE=InnoDB;

CREATE TABLE detalle_ventas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    venta_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10, 2) NOT NULL,
    descuento DECIMAL(10, 2) DEFAULT 0.00,
    subtotal DECIMAL(10, 2) NOT NULL,
    promocion_id INT NULL, -- Si aplicó alguna promo específica
    FOREIGN KEY (venta_id) REFERENCES ventas(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE RESTRICT,
    FOREIGN KEY (promocion_id) REFERENCES promociones(id) ON DELETE SET NULL,
    INDEX idx_venta (venta_id),
    INDEX idx_producto (producto_id)
) ENGINE=InnoDB;

-- 7. Control de Caja (Turnos)
-- Para cuadrar el dinero al final del día

CREATE TABLE turnos_caja (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    monto_inicial DECIMAL(10, 2) NOT NULL DEFAULT 0.00, -- Con cuánto empezó
    monto_final DECIMAL(10, 2), -- Con cuánto terminó
    total_ventas DECIMAL(10, 2) DEFAULT 0.00,
    total_efectivo DECIMAL(10, 2) DEFAULT 0.00,
    total_tarjeta DECIMAL(10, 2) DEFAULT 0.00,
    total_vales DECIMAL(10, 2) DEFAULT 0.00,
    retiros DECIMAL(10, 2) DEFAULT 0.00, -- dinero sacado de caja
    ingresos_extra DECIMAL(10, 2) DEFAULT 0.00, -- dinero ingresado extra
    diferencia DECIMAL(10, 2) DEFAULT 0.00, -- sobrante o faltante
    estado ENUM('abierto', 'cerrado') DEFAULT 'abierto',
    observaciones TEXT,
    fecha_apertura TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_cierre TIMESTAMP NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    INDEX idx_usuario (usuario_id),
    INDEX idx_estado (estado),
    INDEX idx_fecha_apertura (fecha_apertura)
) ENGINE=InnoDB;

CREATE TABLE movimientos_caja (
    id INT PRIMARY KEY AUTO_INCREMENT,
    turno_id INT NOT NULL,
    tipo ENUM('ingreso', 'egreso') NOT NULL,
    monto DECIMAL(10, 2) NOT NULL,
    concepto VARCHAR(255) NOT NULL, -- Razón del movimiento
    observaciones TEXT,
    fecha_movimiento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (turno_id) REFERENCES turnos_caja(id) ON DELETE CASCADE,
    INDEX idx_turno (turno_id)
) ENGINE=InnoDB;

-- 8. Datos Iniciales
-- Configuración básica para arrancar

-- Roles del sistema
INSERT INTO roles (nombre, descripcion) VALUES
('Administrador', 'Dueño o encargado. Puede hacer todo.'),
('Cajero', 'Atiende al público y cobra.');

INSERT INTO usuarios (nombre_completo, email, password_hash, rol_id, estado) VALUES
('Administrador', 'admin@laesquinita.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'activo');

INSERT INTO categorias (nombre, descripcion) VALUES
('Bebidas', 'Refrescos, jugos, agua'),
('Enlatados', 'Conservas, frijoles, verduras'),
('Frescos', 'Frutas, verduras, perecederos'),
('Abarrotes', 'Arroz, frijol, azúcar, aceite'),
('Lácteos', 'Leche, queso, crema'),
('Panadería', 'Pan dulce, francés'),
('Limpieza', 'Jabón, cloro, detergente'),
('Botanas', 'Frituras, galletas'),
('Higiene', 'Papel, pasta dental, champú'),
('Congelados', 'Helados, hielos, carnes');

-- Cliente "Público General" para ventas rápidas
INSERT INTO clientes (nombre, telefono) VALUES
('Público General', NULL);

-- 9. Reportes y Consultas (Vistas)
-- Consultas listas para usar en el sistema

-- Reporte: Qué productos se están acabando
CREATE VIEW v_productos_stock_bajo AS
SELECT 
    p.id,
    p.codigo_barras,
    p.nombre,
    c.nombre AS categoria,
    p.stock_actual,
    p.stock_minimo,
    pr.nombre AS proveedor
FROM productos p
LEFT JOIN categorias c ON p.categoria_id = c.id
LEFT JOIN proveedores pr ON p.proveedor_id = pr.id
WHERE p.stock_actual <= p.stock_minimo AND p.estado = 'activo'
ORDER BY p.stock_actual ASC;

-- Reporte: Qué productos van a vencer pronto (7 días)
CREATE VIEW v_productos_por_vencer AS
SELECT 
    p.id,
    p.nombre,
    p.fecha_vencimiento,
    DATEDIFF(p.fecha_vencimiento, CURDATE()) AS dias_restantes,
    p.stock_actual
FROM productos p
WHERE p.fecha_vencimiento IS NOT NULL
  AND p.fecha_vencimiento <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
  AND p.estado = 'activo'
ORDER BY p.fecha_vencimiento ASC;

-- Reporte: Ventas del día
CREATE VIEW v_ventas_hoy AS
SELECT 
    v.id,
    v.folio,
    c.nombre AS cliente,
    u.nombre_completo AS vendedor,
    v.total,
    v.metodo_pago,
    v.estado,
    TIME(v.fecha_venta) AS hora
FROM ventas v
LEFT JOIN clientes c ON v.cliente_id = c.id
INNER JOIN usuarios u ON v.usuario_id = u.id
WHERE DATE(v.fecha_venta) = CURDATE()
ORDER BY v.fecha_venta DESC;

-- Reporte: Lo más vendido (Top 20 del mes)
CREATE VIEW v_productos_mas_vendidos AS
SELECT 
    p.nombre,
    c.nombre AS categoria,
    SUM(dv.cantidad) AS total_vendido,
    SUM(dv.subtotal) AS dinero_generado
FROM ventas v
JOIN detalle_ventas dv ON v.id = dv.venta_id
JOIN productos p ON dv.producto_id = p.id
LEFT JOIN categorias c ON p.categoria_id = c.id
WHERE v.estado = 'completada'
  AND v.fecha_venta >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY p.id
ORDER BY total_vendido DESC
LIMIT 20;

-- Reporte de Inteligencia: Horas Pico
-- ¿A qué hora viene más gente?
CREATE VIEW v_horas_pico AS
SELECT 
    HOUR(fecha_venta) AS hora,
    COUNT(*) AS total_ventas,
    SUM(total) AS dinero
FROM ventas
WHERE estado = 'completada'
  AND fecha_venta >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY HOUR(fecha_venta)
ORDER BY total_ventas DESC;

-- Reporte de Inteligencia: Qué pedir
-- Sugiere qué comprar basado en ventas recientes y stock bajo
CREATE VIEW v_sugerencias_pedido AS
SELECT 
    p.nombre,
    pr.nombre AS proveedor,
    p.stock_actual,
    p.stock_minimo,
    CEIL((p.stock_minimo * 2) - p.stock_actual) AS cantidad_sugerida,
    CASE 
        WHEN p.stock_actual = 0 THEN 'URGENTE'
        ELSE 'Normal'
    END AS prioridad
FROM productos p
LEFT JOIN proveedores pr ON p.proveedor_id = pr.id
WHERE p.stock_actual <= p.stock_minimo
ORDER BY p.stock_actual ASC;

-- 10. Procedimientos Automáticos
-- Lógica compleja guardada en la base de datos

DELIMITER $$

-- Proceso para registrar una venta completa
-- Maneja inventario, totales y errores
CREATE PROCEDURE sp_registrar_venta(
    IN p_cliente_id INT,
    IN p_usuario_id INT,
    IN p_metodo_pago VARCHAR(20),
    IN p_monto_recibido DECIMAL(10,2),
    IN p_productos JSON, -- Lista de productos en formato JSON
    OUT p_venta_id INT,
    OUT p_folio VARCHAR(20)
)
BEGIN
    DECLARE v_subtotal DECIMAL(10,2) DEFAULT 0;
    DECLARE v_total DECIMAL(10,2);
    DECLARE v_cambio DECIMAL(10,2);
    DECLARE v_idx INT DEFAULT 0;
    DECLARE v_count INT;
    DECLARE v_prod_id INT;
    DECLARE v_cant INT;
    DECLARE v_desc DECIMAL(10,2);
    DECLARE v_precio DECIMAL(10,2);
    DECLARE v_stock INT;
    
    -- Si algo falla, deshacemos todo
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    
    START TRANSACTION;
    
    -- 1. Crear el folio de venta (EJ: V202601258832)
    SET p_folio = CONCAT('V', DATE_FORMAT(NOW(), '%Y%m%d'), LPAD(FLOOR(RAND() * 9999), 4, '0'));
    
    -- 2. Crear la venta (cabecera)
    INSERT INTO ventas (folio, cliente_id, usuario_id, metodo_pago, monto_recibido, subtotal, total)
    VALUES (p_folio, p_cliente_id, p_usuario_id, p_metodo_pago, p_monto_recibido, 0, 0);
    
    SET p_venta_id = LAST_INSERT_ID();
    SET v_count = JSON_LENGTH(p_productos);
    
    -- 3. Procesar cada producto del carrito
    WHILE v_idx < v_count DO
        -- Extraer datos del JSON
        SET v_prod_id = JSON_UNQUOTE(JSON_EXTRACT(p_productos, CONCAT('$[', v_idx, '].producto_id')));
        SET v_cant = JSON_UNQUOTE(JSON_EXTRACT(p_productos, CONCAT('$[', v_idx, '].cantidad')));
        SET v_desc = COALESCE(JSON_UNQUOTE(JSON_EXTRACT(p_productos, CONCAT('$[', v_idx, '].descuento'))), 0);
        
        -- Verificar precio y stock actual
        SELECT precio_venta, stock_actual INTO v_precio, v_stock
        FROM productos WHERE id = v_prod_id;
        
        -- Si no alcanza el producto, error
        IF v_stock < v_cant THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No hay suficiente stock para uno de los productos';
        END IF;
        
        -- Guardar detalle
        INSERT INTO detalle_ventas (venta_id, producto_id, cantidad, precio_unitario, descuento, subtotal)
        VALUES (p_venta_id, v_prod_id, v_cant, v_precio, v_desc, (v_cant * v_precio) - v_desc);
        
        -- Sumar al total
        SET v_subtotal = v_subtotal + ((v_cant * v_precio) - v_desc);
        
        -- Restar del inventario
        UPDATE productos SET stock_actual = stock_actual - v_cant WHERE id = v_prod_id;
        
        -- Registrar el movimiento en el historial
        INSERT INTO movimientos_inventario (producto_id, tipo_movimiento, cantidad, stock_anterior, stock_nuevo, usuario_id, referencia)
        VALUES (v_prod_id, 'salida', v_cant, v_stock, v_stock - v_cant, p_usuario_id, p_folio);
        
        SET v_idx = v_idx + 1;
    END WHILE;
    
    -- 4. Finalizar cuenta
    SET v_total = v_subtotal;
    SET v_cambio = p_monto_recibido - v_total;
    
    UPDATE ventas SET subtotal = v_subtotal, total = v_total, cambio = v_cambio WHERE id = p_venta_id;
    
    COMMIT;
END$$

-- Trigger: Actualizar fecha de modificación automáticamente
CREATE TRIGGER tr_update_producto_fecha
BEFORE UPDATE ON productos
FOR EACH ROW
BEGIN
    SET NEW.fecha_modificacion = CURRENT_TIMESTAMP;
END$$

DELIMITER ;

-- Índices extra para que sea rápido
CREATE INDEX idx_ventas_rapidas ON ventas(fecha_venta, estado);
