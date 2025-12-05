-- Nueva tabla de auditoría para registrar todas las acciones importantes
CREATE TABLE IF NOT EXISTS `auditoria` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `usuario_id` INT NULL,
  `accion` VARCHAR(255) NOT NULL COMMENT 'Ej: creacion, actualizacion, eliminacion',
  `tipo_entidad` VARCHAR(100) NOT NULL COMMENT 'Ej: Producto, Compra, Venta, Cliente',
  `entidad_id` INT NOT NULL,
  `detalles` TEXT COMMENT 'Datos relevantes en formato JSON. Ej: datos anteriores al cambio',
  `fecha` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Comentario sobre la tabla
ALTER TABLE `auditoria` COMMENT = 'Registra acciones clave de los usuarios en el sistema para auditoría.';
