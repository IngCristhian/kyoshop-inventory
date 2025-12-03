-- =====================================================
-- MIGRACIÓN: Crear tabla producto_variantes
-- Fecha: 2025-12-01
-- Descripción: Sistema de variantes para consolidar productos con diferentes tallas/colores
-- =====================================================

-- Crear tabla producto_variantes
CREATE TABLE IF NOT EXISTS producto_variantes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT NOT NULL,
    talla VARCHAR(20) DEFAULT NULL,
    color VARCHAR(50) DEFAULT NULL,
    stock INT NOT NULL DEFAULT 0,
    codigo_unico VARCHAR(100) DEFAULT NULL,
    activo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE,
    INDEX idx_producto_id (producto_id),
    INDEX idx_activo (activo),
    INDEX idx_codigo_unico (codigo_unico),
    UNIQUE KEY unique_variante (producto_id, talla, color)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- NOTAS DE IMPLEMENTACIÓN:
-- =====================================================
-- 1. Esta tabla permite que un producto tenga múltiples variantes
-- 2. Cada variante tiene su propio stock independiente
-- 3. El campo 'codigo_unico' permite preservar códigos de productos originales
-- 4. La restricción UNIQUE previene variantes duplicadas
-- 5. ON DELETE CASCADE elimina variantes si se elimina el producto padre
-- 6. Compatible con el sistema actual de producto_padre_id durante la transición
-- =====================================================
