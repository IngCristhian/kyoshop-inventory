-- Migración: Agregar campo ubicacion a tabla productos
-- Fecha: 2025-01-04
-- Descripción: Permite registrar la ciudad donde está ubicada la mercancía

ALTER TABLE productos
ADD COLUMN ubicacion ENUM('Medellín', 'Bogotá') NOT NULL DEFAULT 'Medellín'
AFTER color,
ADD INDEX idx_ubicacion (ubicacion);

-- Comentario para documentar el campo
ALTER TABLE productos
MODIFY COLUMN ubicacion ENUM('Medellín', 'Bogotá') NOT NULL DEFAULT 'Medellín'
COMMENT 'Ciudad donde se encuentra la mercancía';
