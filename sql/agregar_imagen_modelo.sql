-- =====================================================
-- KYOSHOP INVENTORY - AGREGAR IMAGEN CON MODELO
-- =====================================================
-- Agregar campo para segunda imagen (producto en modelo)
-- Autor: Sistema KyoShop
-- Fecha: 2025-01-24
-- =====================================================

-- Agregar columna imagen_modelo
ALTER TABLE productos
ADD COLUMN imagen_modelo VARCHAR(255) NULL
COMMENT 'Imagen del producto puesto en modelo'
AFTER imagen;

-- =====================================================
-- NOTAS DE MIGRACIÓN
-- =====================================================
-- imagen: Foto del producto solo
-- imagen_modelo: Foto del producto puesto en modelo
-- Ambos campos son opcionales (NULL) para mantener compatibilidad
-- =====================================================
