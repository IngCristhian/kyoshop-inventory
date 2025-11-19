-- =============================================
-- MÓDULO DE VENTAS - KYOSHOP INVENTORY SYSTEM
-- Sistema completo de gestión de ventas
-- =============================================

-- Tabla de clientes
CREATE TABLE IF NOT EXISTS clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    email VARCHAR(255) NULL,
    direccion TEXT NULL,
    ciudad VARCHAR(100) DEFAULT 'Medellín',
    activo TINYINT(1) DEFAULT 1,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_telefono (telefono),
    INDEX idx_nombre (nombre),
    INDEX idx_activo (activo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de ventas (header)
CREATE TABLE IF NOT EXISTS ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    usuario_id INT NOT NULL,
    numero_venta VARCHAR(50) NOT NULL UNIQUE,
    fecha_venta DATETIME DEFAULT CURRENT_TIMESTAMP,
    subtotal DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    impuestos DECIMAL(12,2) DEFAULT 0.00,
    total DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    metodo_pago ENUM('transferencia', 'contra_entrega', 'efectivo', 'tarjeta') NOT NULL,
    estado_pago ENUM('pendiente', 'pagado', 'cancelado') DEFAULT 'pendiente',
    observaciones TEXT NULL,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE RESTRICT,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE RESTRICT,

    INDEX idx_numero_venta (numero_venta),
    INDEX idx_fecha_venta (fecha_venta),
    INDEX idx_cliente (cliente_id),
    INDEX idx_usuario (usuario_id),
    INDEX idx_estado_pago (estado_pago),
    INDEX idx_metodo_pago (metodo_pago)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de detalle de ventas (items)
CREATE TABLE IF NOT EXISTS ventas_detalle (
    id INT AUTO_INCREMENT PRIMARY KEY,
    venta_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(12,2) NOT NULL,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (venta_id) REFERENCES ventas(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE RESTRICT,

    INDEX idx_venta (venta_id),
    INDEX idx_producto (producto_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Vista para estadísticas de ventas
CREATE OR REPLACE VIEW estadisticas_ventas AS
SELECT
    COUNT(*) as total_ventas,
    SUM(total) as monto_total_ventas,
    AVG(total) as ticket_promedio,
    COUNT(CASE WHEN estado_pago = 'pagado' THEN 1 END) as ventas_pagadas,
    COUNT(CASE WHEN estado_pago = 'pendiente' THEN 1 END) as ventas_pendientes,
    COUNT(CASE WHEN metodo_pago = 'transferencia' THEN 1 END) as ventas_transferencia,
    COUNT(CASE WHEN metodo_pago = 'contra_entrega' THEN 1 END) as ventas_contra_entrega,
    COUNT(CASE WHEN metodo_pago = 'efectivo' THEN 1 END) as ventas_efectivo,
    COUNT(CASE WHEN metodo_pago = 'tarjeta' THEN 1 END) as ventas_tarjeta,
    COUNT(DISTINCT cliente_id) as clientes_unicos,
    COUNT(DISTINCT DATE(fecha_venta)) as dias_con_ventas
FROM ventas
WHERE fecha_venta >= DATE_SUB(NOW(), INTERVAL 30 DAY);

-- Vista para top productos vendidos
CREATE OR REPLACE VIEW top_productos_vendidos AS
SELECT
    p.id,
    p.nombre,
    p.codigo_producto,
    p.categoria,
    SUM(vd.cantidad) as cantidad_vendida,
    SUM(vd.subtotal) as monto_total,
    COUNT(DISTINCT vd.venta_id) as numero_ventas
FROM productos p
INNER JOIN ventas_detalle vd ON p.id = vd.producto_id
INNER JOIN ventas v ON vd.venta_id = v.id
WHERE v.fecha_venta >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY p.id, p.nombre, p.codigo_producto, p.categoria
ORDER BY cantidad_vendida DESC
LIMIT 10;

-- Insertar algunos clientes de ejemplo para testing
INSERT INTO clientes (nombre, telefono, email, ciudad) VALUES
('Cliente General', '3001234567', 'cliente@example.com', 'Medellín'),
('María González', '3109876543', 'maria.gonzalez@email.com', 'Bogotá'),
('Juan Pérez', '3157654321', NULL, 'Cali');

-- Notas de uso:
-- 1. numero_venta se genera automáticamente con formato: VEN-YYYYMMDD-XXXX
-- 2. El stock de productos se descuenta automáticamente al crear una venta
-- 3. Se registra en historial_movimientos cada producto vendido
-- 4. estado_pago por defecto es 'pendiente' y se puede actualizar después
-- 5. Las ventas NO se pueden eliminar (solo cancelar cambiando estado_pago a 'cancelado')
