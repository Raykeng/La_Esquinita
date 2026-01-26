-- Script para crear tabla de ventas limpia y funcional
-- Archivo: crear_ventas_limpio.sql

-- Eliminar tablas si existen (para empezar limpio)
DROP TABLE IF EXISTS venta_detalles;
DROP TABLE IF EXISTS ventas;
DROP TABLE IF EXISTS clientes;

-- Crear tabla de ventas simple y funcional
CREATE TABLE ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha_venta DATETIME DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(10,2) NOT NULL,
    metodo_pago ENUM('efectivo', 'tarjeta') DEFAULT 'efectivo',
    estado ENUM('completada', 'cancelada') DEFAULT 'completada',
    subtotal DECIMAL(10,2) DEFAULT 0.00,
    descuento_total DECIMAL(10,2) DEFAULT 0.00,
    monto_recibido DECIMAL(10,2) DEFAULT 0.00,
    cambio DECIMAL(10,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Crear tabla de detalles de venta
CREATE TABLE venta_detalles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    venta_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (venta_id) REFERENCES ventas(id) ON DELETE CASCADE
);

-- Crear tabla de clientes simple
CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL DEFAULT 'Público',
    apellido VARCHAR(100) DEFAULT 'General',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insertar cliente por defecto
INSERT INTO clientes (id, nombre, apellido) VALUES (1, 'Público', 'General');

-- Mostrar estructura creada
DESCRIBE ventas;
DESCRIBE venta_detalles;
DESCRIBE clientes;