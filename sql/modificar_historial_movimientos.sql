-- ==========================================================
-- KYOSHOP INVENTORY - MIGRACIÓN MODIFICAR HISTORIAL
-- ==========================================================
-- Modifica la tabla historial_movimientos para que sea más genérica
-- y pueda registrar eventos no relacionados con productos.
--
-- Autor: Sistema KyoShop (Asistente de IA)
-- Fecha: 2025-12-05
-- ==========================================================

-- 1. Modificar la columna ENUM para añadir nuevos tipos de movimiento
-- NOTA: La sintaxis para modificar ENUM puede variar ligeramente entre versiones de MySQL/MariaDB.
-- Esta es la sintaxis estándar.
ALTER TABLE `historial_movimientos`
MODIFY COLUMN `tipo_movimiento` ENUM(
    'entrada',
    'salida',
    'ajuste',
    'creacion',
    'actualizacion',
    'eliminacion',
    'cambio_precio',
    'eliminacion_compra',
    'creacion_compra'
) NOT NULL;

-- 2. Hacer la columna producto_id opcional (nullable)
ALTER TABLE `historial_movimientos`
MODIFY COLUMN `producto_id` INT NULL;

-- 3. Añadir nuevas columnas para identificar la entidad
ALTER TABLE `historial_movimientos`
ADD COLUMN `entidad` VARCHAR(100) NULL COMMENT 'El tipo de entidad (ej: Producto, Compra)' AFTER `usuario_id`,
ADD COLUMN `entidad_id` INT NULL COMMENT 'El ID de la entidad' AFTER `entidad`;

-- ==========================================================
-- FIN DE MIGRACIÓN
-- ==========================================================
