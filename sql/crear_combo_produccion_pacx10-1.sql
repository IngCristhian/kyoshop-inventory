-- =====================================================
-- CREAR COMBO PRODUCCIÓN: Pacx10-1
-- Cliente: Johana Galleo Ortiz (ID: 14)
-- Fecha: 2025-12-22
-- =====================================================
-- ADVERTENCIA: Este script es para PRODUCCIÓN
-- Revisar antes de ejecutar
-- =====================================================

START TRANSACTION;

-- Variables para el combo
SET @combo_nombre = 'Pacx10-1';
SET @combo_tipo = 'small';
SET @combo_cantidad = 10;
SET @combo_precio = 180000.00;
SET @combo_ubicacion = 'Bogotá';
SET @cliente_id = 14;
SET @usuario_id = 3;
SET @fecha_actual = NOW();

-- =====================================================
-- PASO 1: Insertar combo
-- =====================================================
INSERT INTO combos (nombre, tipo, cantidad_total, precio, ubicacion, activo, fecha_creacion, fecha_actualizacion)
VALUES (@combo_nombre, @combo_tipo, @combo_cantidad, @combo_precio, @combo_ubicacion, 1, @fecha_actual, @fecha_actual);

SET @combo_id = LAST_INSERT_ID();

-- =====================================================
-- PASO 2: Insertar distribución por tipos
-- =====================================================
INSERT INTO combos_tipos (combo_id, tipo, cantidad) VALUES
(@combo_id, 'Niño', 6),
(@combo_id, 'Hombre', 4);

-- =====================================================
-- PASO 3: Insertar productos en el combo
-- =====================================================
-- Niño (6 productos)
INSERT INTO combos_productos (combo_id, producto_id, tipo, fecha_asignacion) VALUES
(@combo_id, 363, 'Niño', @fecha_actual),  -- BLU-NEG-11-1766306677000 - Blusa cool niña
(@combo_id, 252, 'Niño', @fecha_actual),  -- BUS-MUL-275 - Buso Con Cuello Polo
(@combo_id, 324, 'Niño', @fecha_actual),  -- CAM-BLA-468 - camiseta estampada beach
(@combo_id, 311, 'Niño', @fecha_actual),  -- CAM-BLA-499 - Camisa estampada moño niña
(@combo_id, 254, 'Niño', @fecha_actual),  -- PAN-BEI-12-1765567628001 - Sudadera Unisex
(@combo_id, 255, 'Niño', @fecha_actual);  -- PAN-NEG-12-1765567704000 - Sudadera unisex

-- Hombre (4 productos)
INSERT INTO combos_productos (combo_id, producto_id, tipo, fecha_asignacion) VALUES
(@combo_id, 233, 'Hombre', @fecha_actual),  -- CON-MUL-868 - Conjunto playero
(@combo_id, 158, 'Hombre', @fecha_actual),  -- PAN-AMA-L-1765338832000 - Pantaloneta Amarillo
(@combo_id, 161, 'Hombre', @fecha_actual),  -- PAN-GRI-686 - Pantaloneta Deportiva
(@combo_id, 170, 'Hombre', @fecha_actual);  -- PAN-ROS-M-1765351548000 - Pantaloneta Rosa

-- =====================================================
-- PASO 4: Crear venta automática
-- =====================================================
-- Generar número de venta
SET @numero_venta = CONCAT('COM-', DATE_FORMAT(@fecha_actual, '%Y%m%d'), '-', LPAD(@combo_id, 4, '0'));

INSERT INTO ventas (
    cliente_id,
    usuario_id,
    numero_venta,
    fecha_venta,
    subtotal,
    impuestos,
    total,
    metodo_pago,
    estado_pago,
    observaciones,
    fecha_creacion,
    fecha_actualizacion
)
VALUES (
    @cliente_id,
    @usuario_id,
    @numero_venta,
    @fecha_actual,
    @combo_precio,
    0.00,
    @combo_precio,
    'transferencia',
    'pendiente',
    CONCAT('Venta automática del combo: ', @combo_nombre),
    @fecha_actual,
    @fecha_actual
);

SET @venta_id = LAST_INSERT_ID();

-- =====================================================
-- PASO 5: Insertar detalle de venta con precios proporcionales
-- =====================================================
-- Suma de precios originales: 222700
-- Precio combo: 180000
-- Factor de proporción: 180000 / 222700 = 0.808284

-- Niño (6 productos)
INSERT INTO ventas_detalle (venta_id, producto_id, cantidad, precio_unitario, subtotal, fecha_creacion) VALUES
(@venta_id, 363, 1, ROUND((16000 / 222700) * 180000, 2), ROUND((16000 / 222700) * 180000, 2), @fecha_actual),  -- $12,932.56
(@venta_id, 252, 1, ROUND((24000 / 222700) * 180000, 2), ROUND((24000 / 222700) * 180000, 2), @fecha_actual),  -- $19,398.83
(@venta_id, 324, 1, ROUND((17000 / 222700) * 180000, 2), ROUND((17000 / 222700) * 180000, 2), @fecha_actual),  -- $13,740.78
(@venta_id, 311, 1, ROUND((14000 / 222700) * 180000, 2), ROUND((14000 / 222700) * 180000, 2), @fecha_actual),  -- $11,316.02
(@venta_id, 254, 1, ROUND((22000 / 222700) * 180000, 2), ROUND((22000 / 222700) * 180000, 2), @fecha_actual),  -- $17,782.26
(@venta_id, 255, 1, ROUND((22000 / 222700) * 180000, 2), ROUND((22000 / 222700) * 180000, 2), @fecha_actual);  -- $17,782.26

-- Hombre (4 productos)
INSERT INTO ventas_detalle (venta_id, producto_id, cantidad, precio_unitario, subtotal, fecha_creacion) VALUES
(@venta_id, 233, 1, ROUND((33000 / 222700) * 180000, 2), ROUND((33000 / 222700) * 180000, 2), @fecha_actual),  -- $26,673.40
(@venta_id, 158, 1, ROUND((24900 / 222700) * 180000, 2), ROUND((24900 / 222700) * 180000, 2), @fecha_actual),  -- $20,126.41
(@venta_id, 161, 1, ROUND((24900 / 222700) * 180000, 2), ROUND((24900 / 222700) * 180000, 2), @fecha_actual),  -- $20,126.41
(@venta_id, 170, 1, ROUND((24900 / 222700) * 180000, 2), ROUND((24900 / 222700) * 180000, 2), @fecha_actual);  -- $20,126.41

-- =====================================================
-- PASO 6: Descontar stock de productos
-- =====================================================
-- Niño
UPDATE productos SET stock = stock - 1 WHERE id = 363;  -- BLU-NEG (1 -> 0)
UPDATE productos SET stock = stock - 1 WHERE id = 252;  -- BUS-MUL (1 -> 0)
UPDATE productos SET stock = stock - 1 WHERE id = 324;  -- CAM-BLA-468 (5 -> 4)
UPDATE productos SET stock = stock - 1 WHERE id = 311;  -- CAM-BLA-499 (1 -> 0)
UPDATE productos SET stock = stock - 1 WHERE id = 254;  -- PAN-BEI (1 -> 0)
UPDATE productos SET stock = stock - 1 WHERE id = 255;  -- PAN-NEG (7 -> 6)

-- Hombre
UPDATE productos SET stock = stock - 1 WHERE id = 233;  -- CON-MUL (1 -> 0)
UPDATE productos SET stock = stock - 1 WHERE id = 158;  -- PAN-AMA (1 -> 0)
UPDATE productos SET stock = stock - 1 WHERE id = 161;  -- PAN-GRI (1 -> 0)
UPDATE productos SET stock = stock - 1 WHERE id = 170;  -- PAN-ROS (1 -> 0)

-- =====================================================
-- PASO 7: Registrar en historial de movimientos
-- =====================================================
-- Niño (6 productos)
INSERT INTO historial_movimientos (producto_id, usuario_id, tipo_movimiento, cantidad, stock_anterior, stock_nuevo, motivo, fecha_movimiento)
VALUES
(363, @usuario_id, 'salida', -1, 1, 0, CONCAT('Producto incluido en combo: ', @combo_nombre, ' (Venta: ', @numero_venta, ')'), @fecha_actual),
(252, @usuario_id, 'salida', -1, 1, 0, CONCAT('Producto incluido en combo: ', @combo_nombre, ' (Venta: ', @numero_venta, ')'), @fecha_actual),
(324, @usuario_id, 'salida', -1, 5, 4, CONCAT('Producto incluido en combo: ', @combo_nombre, ' (Venta: ', @numero_venta, ')'), @fecha_actual),
(311, @usuario_id, 'salida', -1, 1, 0, CONCAT('Producto incluido en combo: ', @combo_nombre, ' (Venta: ', @numero_venta, ')'), @fecha_actual),
(254, @usuario_id, 'salida', -1, 1, 0, CONCAT('Producto incluido en combo: ', @combo_nombre, ' (Venta: ', @numero_venta, ')'), @fecha_actual),
(255, @usuario_id, 'salida', -1, 7, 6, CONCAT('Producto incluido en combo: ', @combo_nombre, ' (Venta: ', @numero_venta, ')'), @fecha_actual);

-- Hombre (4 productos)
INSERT INTO historial_movimientos (producto_id, usuario_id, tipo_movimiento, cantidad, stock_anterior, stock_nuevo, motivo, fecha_movimiento)
VALUES
(233, @usuario_id, 'salida', -1, 1, 0, CONCAT('Producto incluido en combo: ', @combo_nombre, ' (Venta: ', @numero_venta, ')'), @fecha_actual),
(158, @usuario_id, 'salida', -1, 1, 0, CONCAT('Producto incluido en combo: ', @combo_nombre, ' (Venta: ', @numero_venta, ')'), @fecha_actual),
(161, @usuario_id, 'salida', -1, 1, 0, CONCAT('Producto incluido en combo: ', @combo_nombre, ' (Venta: ', @numero_venta, ')'), @fecha_actual),
(170, @usuario_id, 'salida', -1, 1, 0, CONCAT('Producto incluido en combo: ', @combo_nombre, ' (Venta: ', @numero_venta, ')'), @fecha_actual);

-- =====================================================
-- Verificar resultados
-- =====================================================
SELECT
    'Combo creado en PRODUCCIÓN' as mensaje,
    @combo_id as combo_id,
    @combo_nombre as nombre,
    @numero_venta as venta,
    @combo_precio as precio;

-- Confirmar transacción
COMMIT;

-- =====================================================
-- FIN DEL SCRIPT - PRODUCCIÓN
-- =====================================================
