-- KYOSHOP INVENTORY SYSTEM - DEVELOPMENT
-- Script de creación de base de datos para desarrollo
-- Ejecutar este script en phpMyAdmin o terminal MySQL

CREATE DATABASE IF NOT EXISTS kyosankk_inventory_dev 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE kyosankk_inventory_dev;

-- Tabla de productos
CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    imagen VARCHAR(255),
    categoria VARCHAR(100),
    talla VARCHAR(50),
    color VARCHAR(50),
    codigo_producto VARCHAR(100) UNIQUE,
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_categoria (categoria),
    INDEX idx_codigo (codigo_producto),
    INDEX idx_activo (activo),
    INDEX idx_stock (stock)
) ENGINE=InnoDB;

-- Datos de ejemplo para testing desarrollo
INSERT INTO productos (nombre, descripcion, precio, stock, categoria, talla, color, codigo_producto) VALUES
('Camiseta Básica Blanca', 'Camiseta de algodón 100% básica color blanco', 25000.00, 15, 'Camisetas', 'M', 'Blanco', 'CAM-BAS-BLA-001'),
('Jeans Clásico Azul', 'Pantalón jean clásico corte recto', 85000.00, 8, 'Pantalones', '32', 'Azul', 'JEAN-CLA-AZU-001'),
('Blusa Elegante Negra', 'Blusa elegante para ocasiones especiales', 45000.00, 12, 'Blusas', 'S', 'Negro', 'BLU-ELE-NEG-001'),
('Chaqueta Deportiva', 'Chaqueta deportiva con capucha', 75000.00, 6, 'Chaquetas', 'L', 'Gris', 'CHA-DEP-GRI-001'),
('Falda Casual Rosa', 'Falda casual para uso diario', 35000.00, 10, 'Faldas', 'M', 'Rosa', 'FAL-CAS-ROS-001'),
('Sweater Desarrollo', 'Producto específico para pruebas de desarrollo', 50000.00, 3, 'Sweaters', 'L', 'Verde', 'SWE-DEV-VER-001'),
('Pantalón Testing', 'Producto para testing de funcionalidades', 40000.00, 2, 'Pantalones', '30', 'Negro', 'PAN-TES-NEG-001');

-- Vista para estadísticas rápidas
CREATE VIEW estadisticas_productos AS
SELECT 
    COUNT(*) as total_productos,
    SUM(stock) as total_stock,
    COUNT(DISTINCT categoria) as total_categorias,
    AVG(precio) as precio_promedio,
    COUNT(CASE WHEN stock <= 5 THEN 1 END) as productos_bajo_stock
FROM productos 
WHERE activo = TRUE;