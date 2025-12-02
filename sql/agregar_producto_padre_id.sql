-- ========================================
-- Migración: Agregar soporte para variantes de productos
-- Fecha: 2025-12-01
-- Sistema: KyoShop Inventory
-- Descripción: Permite agrupar productos como variantes (tallas/colores)
-- ========================================

-- Agregar columna producto_padre_id
ALTER TABLE productos
ADD COLUMN producto_padre_id INT(11) NULL DEFAULT NULL AFTER id;

-- Agregar foreign key constraint
ALTER TABLE productos
ADD CONSTRAINT fk_producto_padre
    FOREIGN KEY (producto_padre_id)
    REFERENCES productos(id)
    ON DELETE SET NULL;

-- Crear índice para mejorar performance en consultas
CREATE INDEX idx_producto_padre ON productos(producto_padre_id);

-- Verificar que la migración se ejecutó correctamente
SELECT
    'Migración completada exitosamente' as status,
    COUNT(*) as total_productos,
    SUM(CASE WHEN producto_padre_id IS NULL THEN 1 ELSE 0 END) as productos_principales,
    SUM(CASE WHEN producto_padre_id IS NOT NULL THEN 1 ELSE 0 END) as variantes
FROM productos;
