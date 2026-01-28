-- =====================================================
-- MIGRACIÓN: Eliminar Puntos Acumulados y Usar NIT
-- Fecha: 27 de enero de 2026
-- Descripción: Reemplazar sistema de puntos por NIT (8 dígitos)
-- =====================================================

USE `la_esquinita`;

-- 1. Verificar estructura actual de la tabla clientes
SELECT COLUMN_NAME, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH, IS_NULLABLE, COLUMN_DEFAULT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'la_esquinita' 
AND TABLE_NAME = 'clientes'
ORDER BY ORDINAL_POSITION;

-- 2. Eliminar columna puntos_acumulados SI EXISTE
SET @query = (
    SELECT IF(
        COUNT(*) > 0,
        'ALTER TABLE clientes DROP COLUMN puntos_acumulados;',
        'SELECT "La columna puntos_acumulados no existe" AS mensaje;'
    )
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = 'la_esquinita'
    AND TABLE_NAME = 'clientes'
    AND COLUMN_NAME = 'puntos_acumulados'
);

PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 3. Modificar columna NIT para asegurar que sea VARCHAR(8)
ALTER TABLE `clientes` 
MODIFY COLUMN `nit` VARCHAR(8) DEFAULT NULL COMMENT 'NIT del cliente (8 dígitos)';

-- 4. Agregar índice para búsquedas rápidas por NIT
ALTER TABLE `clientes` 
ADD INDEX `idx_nit` (`nit`);

-- 5. Verificar cambios
DESCRIBE clientes;

-- 6. Mostrar estructura final
SHOW CREATE TABLE clientes;

-- =====================================================
-- NOTAS IMPORTANTES:
-- =====================================================
-- 1. El campo NIT ahora acepta 8 caracteres
-- 2. Se eliminó completamente el sistema de puntos acumulados
-- 3. Se agregó un índice para búsquedas rápidas por NIT
-- 4. El campo NIT permite NULL para clientes sin NIT
-- =====================================================

-- Ejemplo de actualización de datos (opcional):
-- UPDATE clientes SET nit = '12345678' WHERE id = 2;
