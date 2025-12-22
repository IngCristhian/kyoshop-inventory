-- =====================================================
-- CREAR COMBO MANUAL: pacx10 manual
-- Cliente: Nadia de la Rosa (ID: 4)
-- Fecha: 2025-12-22
-- =====================================================

START TRANSACTION;

-- Variables para el combo
SET @combo_nombre = 'pacx10 manual';
SET @combo_tipo = 'small';
SET @combo_cantidad = 10;
SET @combo_precio = 200000.00;
SET @combo_ubicacion = 'Bogotá';
SET @cliente_id = 4;
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
(@combo_id, 'Mujer', 5),
(@combo_id, 'Hombre', 5);

-- =====================================================
-- PASO 3: Insertar productos en el combo
-- =====================================================
-- Mujer (5 productos)
INSERT INTO combos_productos (combo_id, producto_id, tipo, fecha_asignacion) VALUES
(@combo_id, 12, 'Mujer', @fecha_actual),  -- CAM-BLA-S-1762717647001
(@combo_id, 17, 'Mujer', @fecha_actual),  -- BUS-BLA-L-1762720597002
(@combo_id, 27, 'Mujer', @fecha_actual),  -- CAM-AMA-L-1762724376003
(@combo_id, 94, 'Mujer', @fecha_actual),  -- FAL-DOR-264
(@combo_id, 55, 'Mujer', @fecha_actual);  -- ESQ-NEG-961

-- Hombre (5 productos)
INSERT INTO combos_productos (combo_id, producto_id, tipo, fecha_asignacion) VALUES
(@combo_id, 149, 'Hombre', @fecha_actual),  -- PAN-NEG-S-1765341022000
(@combo_id, 142, 'Hombre', @fecha_actual),  -- PAN-NEG-L-1765002104000
(@combo_id, 146, 'Hombre', @fecha_actual),  -- PAN-ROS-XL-1765322349001
(@combo_id, 150, 'Hombre', @fecha_actual),  -- PAN-AZU-XX-1765341022001
(@combo_id, 156, 'Hombre', @fecha_actual);  -- PAN-NEG-432

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
-- Suma de precios originales: 257200
-- Precio combo: 200000
-- Factor de proporción: 200000 / 257200 = 0.777546

-- Mujer
INSERT INTO ventas_detalle (venta_id, producto_id, cantidad, precio_unitario, subtotal, fecha_creacion) VALUES
(@venta_id, 12, 1, ROUND((19900 / 257200) * 200000, 2), ROUND((19900 / 257200) * 200000, 2), @fecha_actual),  -- $15,472.72
(@venta_id, 17, 1, ROUND((19900 / 257200) * 200000, 2), ROUND((19900 / 257200) * 200000, 2), @fecha_actual),  -- $15,472.72
(@venta_id, 27, 1, ROUND((14900 / 257200) * 200000, 2), ROUND((14900 / 257200) * 200000, 2), @fecha_actual),  -- $11,585.49
(@venta_id, 94, 1, ROUND((39900 / 257200) * 200000, 2), ROUND((39900 / 257200) * 200000, 2), @fecha_actual),  -- $31,021.07
(@venta_id, 55, 1, ROUND((31900 / 257200) * 200000, 2), ROUND((31900 / 257200) * 200000, 2), @fecha_actual);  -- $24,805.28

-- Hombre
INSERT INTO ventas_detalle (venta_id, producto_id, cantidad, precio_unitario, subtotal, fecha_creacion) VALUES
(@venta_id, 149, 1, ROUND((30100 / 257200) * 200000, 2), ROUND((30100 / 257200) * 200000, 2), @fecha_actual),  -- $23,402.18
(@venta_id, 142, 1, ROUND((27900 / 257200) * 200000, 2), ROUND((27900 / 257200) * 200000, 2), @fecha_actual),  -- $21,691.87
(@venta_id, 146, 1, ROUND((30100 / 257200) * 200000, 2), ROUND((30100 / 257200) * 200000, 2), @fecha_actual),  -- $23,402.18
(@venta_id, 150, 1, ROUND((30100 / 257200) * 200000, 2), ROUND((30100 / 257200) * 200000, 2), @fecha_actual),  -- $23,402.18
(@venta_id, 156, 1, ROUND((12500 / 257200) * 200000, 2), ROUND((12500 / 257200) * 200000, 2), @fecha_actual);  -- $9,719.16

-- =====================================================
-- PASO 6: Descontar stock de productos
-- =====================================================
UPDATE productos SET stock = stock - 1 WHERE id = 12;   -- CAM-BLA-S (2 -> 1)
UPDATE productos SET stock = stock - 1 WHERE id = 17;   -- BUS-BLA-L (2 -> 1)
UPDATE productos SET stock = stock - 1 WHERE id = 27;   -- CAM-AMA-L (1 -> 0)
UPDATE productos SET stock = stock - 1 WHERE id = 94;   -- FAL-DOR (3 -> 2)
UPDATE productos SET stock = stock - 1 WHERE id = 55;   -- ESQ-NEG (3 -> 2)
UPDATE productos SET stock = stock - 1 WHERE id = 149;  -- PAN-NEG-S (2 -> 1)
UPDATE productos SET stock = stock - 1 WHERE id = 142;  -- PAN-NEG-L (2 -> 1)
UPDATE productos SET stock = stock - 1 WHERE id = 146;  -- PAN-ROS-XL (8 -> 7)
UPDATE productos SET stock = stock - 1 WHERE id = 150;  -- PAN-AZU-XX (4 -> 3)
UPDATE productos SET stock = stock - 1 WHERE id = 156;  -- PAN-NEG-432 (5 -> 4)

-- =====================================================
-- PASO 7: Registrar en historial de movimientos
-- =====================================================
-- Mujer
INSERT INTO historial_movimientos (producto_id, usuario_id, tipo_movimiento, cantidad, stock_anterior, stock_nuevo, motivo, fecha_movimiento)
VALUES
(12, @usuario_id, 'salida', -1, 2, 1, CONCAT('Producto incluido en combo: ', @combo_nombre, ' (Venta: ', @numero_venta, ')'), @fecha_actual),
(17, @usuario_id, 'salida', -1, 2, 1, CONCAT('Producto incluido en combo: ', @combo_nombre, ' (Venta: ', @numero_venta, ')'), @fecha_actual),
(27, @usuario_id, 'salida', -1, 1, 0, CONCAT('Producto incluido en combo: ', @combo_nombre, ' (Venta: ', @numero_venta, ')'), @fecha_actual),
(94, @usuario_id, 'salida', -1, 3, 2, CONCAT('Producto incluido en combo: ', @combo_nombre, ' (Venta: ', @numero_venta, ')'), @fecha_actual),
(55, @usuario_id, 'salida', -1, 3, 2, CONCAT('Producto incluido en combo: ', @combo_nombre, ' (Venta: ', @numero_venta, ')'), @fecha_actual);

-- Hombre
INSERT INTO historial_movimientos (producto_id, usuario_id, tipo_movimiento, cantidad, stock_anterior, stock_nuevo, motivo, fecha_movimiento)
VALUES
(149, @usuario_id, 'salida', -1, 2, 1, CONCAT('Producto incluido en combo: ', @combo_nombre, ' (Venta: ', @numero_venta, ')'), @fecha_actual),
(142, @usuario_id, 'salida', -1, 2, 1, CONCAT('Producto incluido en combo: ', @combo_nombre, ' (Venta: ', @numero_venta, ')'), @fecha_actual),
(146, @usuario_id, 'salida', -1, 8, 7, CONCAT('Producto incluido en combo: ', @combo_nombre, ' (Venta: ', @numero_venta, ')'), @fecha_actual),
(150, @usuario_id, 'salida', -1, 4, 3, CONCAT('Producto incluido en combo: ', @combo_nombre, ' (Venta: ', @numero_venta, ')'), @fecha_actual),
(156, @usuario_id, 'salida', -1, 5, 4, CONCAT('Producto incluido en combo: ', @combo_nombre, ' (Venta: ', @numero_venta, ')'), @fecha_actual);

-- =====================================================
-- Verificar resultados
-- =====================================================
SELECT
    'Combo creado' as mensaje,
    @combo_id as combo_id,
    @combo_nombre as nombre,
    @numero_venta as venta,
    @combo_precio as precio;

-- Confirmar transacción
COMMIT;

-- =====================================================
-- FIN DEL SCRIPT
-- =====================================================
