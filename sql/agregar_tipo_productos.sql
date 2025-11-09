-- Migración: Agregar campo tipo a tabla productos
-- Fecha: 2025-01-08
-- Descripción: Permite clasificar productos por tipo (Niño, Mujer, Hombre) para armado de combos

ALTER TABLE productos
ADD COLUMN tipo ENUM('Niño', 'Mujer', 'Hombre') NOT NULL DEFAULT 'Niño'
AFTER categoria,
ADD INDEX idx_tipo (tipo);

-- Comentario para documentar el campo
ALTER TABLE productos
MODIFY COLUMN tipo ENUM('Niño', 'Mujer', 'Hombre') NOT NULL DEFAULT 'Niño'
COMMENT 'Clasificación por género/edad para armado de combos';
