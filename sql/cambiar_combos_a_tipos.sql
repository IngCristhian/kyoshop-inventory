-- Migración: Cambiar sistema de combos de categorías a tipos
-- Fecha: 2025-01-08
-- Descripción: Renombra combos_categorias a combos_tipos y actualiza referencias

-- Renombrar tabla combos_categorias a combos_tipos
RENAME TABLE combos_categorias TO combos_tipos;

-- Renombrar columna categoria a tipo en combos_tipos
ALTER TABLE combos_tipos
CHANGE COLUMN categoria tipo VARCHAR(100) NOT NULL
COMMENT 'Tipo de producto (Niño, Mujer, Hombre) en el combo';

-- Actualizar columna categoria a tipo en combos_productos
ALTER TABLE combos_productos
CHANGE COLUMN categoria tipo VARCHAR(100) NOT NULL
COMMENT 'Tipo del producto al momento de agregarlo';

-- Actualizar índices
DROP INDEX idx_categoria ON combos_productos;
ALTER TABLE combos_productos ADD INDEX idx_tipo (tipo);

DROP INDEX idx_categoria ON combos_tipos;
ALTER TABLE combos_tipos ADD INDEX idx_tipo (tipo);
