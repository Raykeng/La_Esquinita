-- Script para agregar la columna descuento_manual a la tabla productos
-- Ejecutar este script en phpMyAdmin o tu cliente MySQL

-- Verificar si la columna existe antes de agregarla
ALTER TABLE productos 
ADD COLUMN IF NOT EXISTS descuento_manual DECIMAL(5,2) DEFAULT 0.00 
COMMENT 'Descuento manual aplicado al producto (porcentaje)' 
AFTER precio_venta;
