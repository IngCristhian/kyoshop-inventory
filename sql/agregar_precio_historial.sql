-- =====================================================
-- KYOSHOP INVENTORY - MIGRACIÓN HISTORIAL DE PRECIOS
-- =====================================================
-- Agregar campos para rastrear cambios de precio en el historial
-- Autor: Sistema KyoShop
-- Fecha: 2025-01-24
-- =====================================================

-- Agregar columna precio_anterior
ALTER TABLE historial_movimientos
ADD COLUMN precio_anterior DECIMAL(10,2) NULL COMMENT 'Precio antes del cambio'
AFTER stock_nuevo;

-- Agregar columna precio_nuevo
ALTER TABLE historial_movimientos
ADD COLUMN precio_nuevo DECIMAL(10,2) NULL COMMENT 'Precio después del cambio'
AFTER precio_anterior;

-- Modificar ENUM para incluir 'cambio_precio'
ALTER TABLE historial_movimientos
MODIFY COLUMN tipo_movimiento ENUM(
    'entrada',
    'salida',
    'ajuste',
    'creacion',
    'actualizacion',
    'eliminacion',
    'cambio_precio'
) NOT NULL;

-- =====================================================
-- NOTAS DE MIGRACIÓN
-- =====================================================
-- Los campos precio_anterior y precio_nuevo son NULL porque:
-- 1. Mantienen compatibilidad con registros existentes de stock
-- 2. Solo se llenan cuando hay un cambio de precio
-- 3. Los movimientos de stock no requieren información de precio
-- =====================================================
