-- =====================================================
-- KYOSHOP INVENTORY - MIGRACIÓN HISTORIAL DE MOVIMIENTOS
-- =====================================================
-- Tabla para registrar todos los movimientos de inventario
-- Autor: Sistema KyoShop
-- Fecha: 2025-01-03
-- =====================================================

-- Crear tabla historial_movimientos
CREATE TABLE IF NOT EXISTS historial_movimientos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT NOT NULL,
    usuario_id INT NOT NULL,
    tipo_movimiento ENUM('entrada', 'salida', 'ajuste', 'creacion', 'actualizacion', 'eliminacion') NOT NULL,
    cantidad INT NOT NULL COMMENT 'Cantidad del movimiento (positivo para entradas, negativo para salidas)',
    stock_anterior INT NOT NULL COMMENT 'Stock antes del movimiento',
    stock_nuevo INT NOT NULL COMMENT 'Stock después del movimiento',
    motivo TEXT NULL COMMENT 'Descripción o motivo del movimiento',
    fecha_movimiento DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_producto (producto_id),
    INDEX idx_usuario (usuario_id),
    INDEX idx_fecha (fecha_movimiento),
    INDEX idx_tipo (tipo_movimiento),
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Comentario de la tabla
ALTER TABLE historial_movimientos COMMENT = 'Registro histórico de todos los movimientos de inventario';

-- =====================================================
-- FIN DE MIGRACIÓN
-- =====================================================
