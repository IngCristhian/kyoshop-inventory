-- =============================================
-- MÓDULO DE COMPRAS INTERNAS - KYOSHOP INVENTORY SYSTEM
-- Sistema de registro de compras de insumos (bolsas, etiquetas, etc.)
-- =============================================

-- Tabla de compras de insumos
CREATE TABLE IF NOT EXISTS compras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    numero_compra VARCHAR(50) NOT NULL UNIQUE,
    fecha_compra DATETIME DEFAULT CURRENT_TIMESTAMP,
    proveedor VARCHAR(255) NULL,
    descripcion TEXT NOT NULL,
    categoria_insumo ENUM('bolsas', 'etiquetas', 'cajas', 'embalaje', 'publicidad', 'otros') DEFAULT 'otros',
    cantidad INT NULL,
    precio_unitario DECIMAL(10,2) NULL,
    total DECIMAL(12,2) NOT NULL,
    metodo_pago ENUM('transferencia', 'efectivo', 'tarjeta', 'credito') NOT NULL,
    comprobante VARCHAR(255) NULL COMMENT 'Ruta al archivo de comprobante (factura, recibo)',
    observaciones TEXT NULL,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE RESTRICT,

    INDEX idx_numero_compra (numero_compra),
    INDEX idx_fecha_compra (fecha_compra),
    INDEX idx_usuario (usuario_id),
    INDEX idx_categoria (categoria_insumo),
    INDEX idx_proveedor (proveedor)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Vista para estadísticas de compras
CREATE OR REPLACE VIEW estadisticas_compras AS
SELECT
    COUNT(*) as total_compras,
    SUM(total) as monto_total_compras,
    AVG(total) as ticket_promedio,
    COUNT(CASE WHEN categoria_insumo = 'bolsas' THEN 1 END) as compras_bolsas,
    COUNT(CASE WHEN categoria_insumo = 'etiquetas' THEN 1 END) as compras_etiquetas,
    COUNT(CASE WHEN categoria_insumo = 'cajas' THEN 1 END) as compras_cajas,
    COUNT(CASE WHEN categoria_insumo = 'embalaje' THEN 1 END) as compras_embalaje,
    COUNT(CASE WHEN categoria_insumo = 'publicidad' THEN 1 END) as compras_publicidad,
    COUNT(CASE WHEN categoria_insumo = 'otros' THEN 1 END) as compras_otros,
    COUNT(CASE WHEN metodo_pago = 'transferencia' THEN 1 END) as compras_transferencia,
    COUNT(CASE WHEN metodo_pago = 'efectivo' THEN 1 END) as compras_efectivo,
    COUNT(CASE WHEN metodo_pago = 'tarjeta' THEN 1 END) as compras_tarjeta,
    COUNT(CASE WHEN metodo_pago = 'credito' THEN 1 END) as compras_credito,
    COUNT(DISTINCT DATE(fecha_compra)) as dias_con_compras
FROM compras
WHERE fecha_compra >= DATE_SUB(NOW(), INTERVAL 30 DAY);

-- Notas de uso:
-- 1. numero_compra se genera automáticamente con formato: COM-YYYYMMDD-XXXX
-- 2. Las compras afectan el cálculo de ganancias netas en el dashboard
-- 3. Las compras NO se pueden eliminar (solo modificar observaciones si es necesario)
-- 4. El campo 'comprobante' almacena la ruta al archivo PDF/imagen de la factura/recibo
-- 5. Categorías disponibles: bolsas, etiquetas, cajas, embalaje, publicidad, otros
