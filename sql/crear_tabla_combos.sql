-- Migración: Crear tablas para sistema de combos
-- Fecha: 2025-01-07
-- Descripción: Permite crear combos de productos con cantidades por categoría

-- Tabla principal de combos
CREATE TABLE IF NOT EXISTS combos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    tipo ENUM('small', 'medium', 'big', 'extra_big') NOT NULL COMMENT 'small=10, medium=25, big=50, extra_big=100',
    cantidad_total INT NOT NULL COMMENT 'Cantidad total de prendas en el combo',
    precio DECIMAL(10,2) NOT NULL,
    ubicacion ENUM('Medellín', 'Bogotá', 'Mixto') NOT NULL DEFAULT 'Mixto' COMMENT 'Ubicación de productos del combo',
    activo TINYINT(1) DEFAULT 1,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_tipo (tipo),
    INDEX idx_activo (activo),
    INDEX idx_ubicacion (ubicacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de relación: productos incluidos en cada combo
CREATE TABLE IF NOT EXISTS combos_productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    combo_id INT NOT NULL,
    producto_id INT NOT NULL,
    categoria VARCHAR(100) NOT NULL COMMENT 'Categoría del producto al momento de agregarlo',
    fecha_asignacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (combo_id) REFERENCES combos(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE,
    INDEX idx_combo (combo_id),
    INDEX idx_producto (producto_id),
    INDEX idx_categoria (categoria)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para guardar la configuración de categorías por combo
CREATE TABLE IF NOT EXISTS combos_categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    combo_id INT NOT NULL,
    categoria VARCHAR(100) NOT NULL,
    cantidad INT NOT NULL COMMENT 'Cantidad de productos de esta categoría en el combo',
    FOREIGN KEY (combo_id) REFERENCES combos(id) ON DELETE CASCADE,
    INDEX idx_combo (combo_id),
    INDEX idx_categoria (categoria)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
