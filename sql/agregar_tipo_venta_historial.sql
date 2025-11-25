-- =====================================================
-- KYOSHOP INVENTORY - AGREGAR TIPOS VENTA Y DEVOLUCIÓN
-- =====================================================
-- Agregar tipos 'venta' y 'devolucion' al ENUM de historial_movimientos
-- Autor: Sistema KyoShop
-- Fecha: 2025-01-24
-- =====================================================

-- Modificar ENUM para incluir 'venta' y 'devolucion'
ALTER TABLE historial_movimientos
MODIFY COLUMN tipo_movimiento ENUM(
    'entrada',
    'salida',
    'ajuste',
    'creacion',
    'actualizacion',
    'eliminacion',
    'cambio_precio',
    'venta',
    'devolucion'
) NOT NULL;

-- =====================================================
-- NOTAS DE MIGRACIÓN
-- =====================================================
-- 'venta': Registra cuando un producto se vende
-- 'devolucion': Registra cuando se cancela una venta y se devuelve stock
-- =====================================================
