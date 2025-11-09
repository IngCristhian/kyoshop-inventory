-- Script para limpiar datos de prueba antes de usar en producción
-- Ejecutar este script para eliminar todos los productos, combos e historial de prueba

-- 1. Eliminar todos los productos del historial
DELETE FROM historial_movimientos;

-- 2. Eliminar todos los productos de los combos
DELETE FROM combos_productos;

-- 3. Eliminar todos los tipos de los combos
DELETE FROM combos_tipos;

-- 4. Eliminar todos los combos
DELETE FROM combos;

-- 5. Eliminar todos los productos
DELETE FROM productos;

-- 6. Resetear auto_increment de todas las tablas
ALTER TABLE productos AUTO_INCREMENT = 1;
ALTER TABLE combos AUTO_INCREMENT = 1;
ALTER TABLE historial_movimientos AUTO_INCREMENT = 1;

-- Verificar que las tablas estén vacías
SELECT 'Productos restantes:' as tabla, COUNT(*) as total FROM productos
UNION ALL
SELECT 'Combos restantes:', COUNT(*) FROM combos
UNION ALL
SELECT 'Historial restante:', COUNT(*) FROM historial_movimientos;
