-- Arreglar tabla de ventas - agregar columnas faltantes
-- Archivo: arreglar_tabla_ventas.sql

-- Verificar si la tabla ventas existe, si no, crearla
CREATE TABLE IF NOT EXISTS ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT DEFAULT 1,
    fecha_venta DATETIME DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(10,2) NOT NULL,
    metodo_pago ENUM('efectivo', 'tarjeta') DEFAULT 'efectivo',
    estado ENUM('completada', 'cancelada') DEFAULT 'completada',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Agregar columnas faltantes si no existen
ALTER TABLE ventas 
ADD COLUMN IF NOT EXISTS subtotal DECIMAL(10,2) DEFAULT 0.00 AFTER fecha_venta,
ADD COLUMN IF NOT EXISTS descuento_total DECIMAL(10,2) DEFAULT 0.00 AFTER subtotal,
ADD COLUMN IF NOT EXISTS monto_recibido DECIMAL(10,2) DEFAULT 0.00 AFTER metodo_pago,
ADD COLUMN IF NOT EXISTS cambio DECIMAL(10,2) DEFAULT 0.00 AFTER monto_recibido;

-- Crear tabla de detalles si no existe
CREATE TABLE IF NOT EXISTS venta_detalles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    venta_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (venta_id) REFERENCES ventas(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id)
);

-- Crear tabla de clientes si no existe
CREATE TABLE IF NOT EXISTS clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL DEFAULT 'Público',
    apellido VARCHAR(100) DEFAULT 'General',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insertar cliente por defecto
INSERT IGNORE INTO clientes (id, nombre, apellido) VALUES (1, 'Público', 'General');

-- Mostrar estructura final
DESCRIBE ventas;