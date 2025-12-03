<?php
/**
 * MODELO PRODUCTO VARIANTE
 * Gestión de agrupación de productos como variantes (tallas/colores)
 */

require_once 'config/database.php';

class ProductoVariante {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Obtener todas las variantes de un producto padre
     * @param int $productoId ID del producto padre
     * @return array Lista de variantes
     */
    public function obtenerVariantes($productoId) {
        $sql = "SELECT * FROM productos
                WHERE producto_padre_id = :id AND activo = 1
                ORDER BY talla, color";

        return $this->db->fetchAll($sql, ['id' => $productoId]);
    }

    /**
     * Agrupar productos como variantes de un producto principal
     * @param int $productoPadreId ID del producto que será el padre
     * @param array $variantesIds Array de IDs de productos a convertir en variantes
     * @return bool True si se agrupó correctamente
     */
    public function agruparProductos($productoPadreId, $variantesIds) {
        // Validar que no haya referencias circulares
        if (!$this->validarReferencias($productoPadreId, $variantesIds)) {
            return false;
        }

        try {
            $this->db->beginTransaction();

            // Actualizar cada variante
            $sql = "UPDATE productos
                    SET producto_padre_id = :padre_id
                    WHERE id = :variante_id";

            foreach ($variantesIds as $varianteId) {
                $this->db->execute($sql, [
                    'padre_id' => $productoPadreId,
                    'variante_id' => $varianteId
                ]);
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error agrupando productos: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Desagrupar un producto (convertir variantes en productos independientes)
     * @param int $productoId ID del producto padre a desagrupar
     * @return bool True si se desagrupó correctamente
     */
    public function desagruparProducto($productoId) {
        try {
            $sql = "UPDATE productos
                    SET producto_padre_id = NULL
                    WHERE producto_padre_id = :id";

            $this->db->execute($sql, ['id' => $productoId]);
            return true;

        } catch (Exception $e) {
            error_log("Error desagrupando producto: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener producto padre de una variante
     * @param int $varianteId ID de la variante
     * @return array|null Datos del producto padre o null
     */
    public function obtenerProductoPadre($varianteId) {
        $sql = "SELECT p2.*
                FROM productos p1
                INNER JOIN productos p2 ON p1.producto_padre_id = p2.id
                WHERE p1.id = :id";

        return $this->db->fetch($sql, ['id' => $varianteId]);
    }

    /**
     * Verificar si un producto es padre (tiene variantes)
     * @param int $productoId ID del producto
     * @return bool True si tiene variantes
     */
    public function esProductoPadre($productoId) {
        $sql = "SELECT COUNT(*) as total
                FROM productos
                WHERE producto_padre_id = :id AND activo = 1";

        $result = $this->db->fetch($sql, ['id' => $productoId]);
        return $result['total'] > 0;
    }

    /**
     * Obtener todos los productos sin agrupar (que no son variantes)
     * @return array Lista de productos sin agrupar
     */
    public function obtenerProductosSinAgrupar() {
        $sql = "SELECT * FROM productos
                WHERE producto_padre_id IS NULL
                AND activo = 1
                ORDER BY nombre";

        return $this->db->fetchAll($sql);
    }

    /**
     * Obtener todos los productos agrupados (padres con sus variantes)
     * @return array Lista de productos agrupados con sus variantes
     */
    public function obtenerProductosAgrupados() {
        $sql = "SELECT
                    padre.id,
                    padre.nombre,
                    padre.imagen,
                    padre.precio,
                    COUNT(hijo.id) as total_variantes,
                    SUM(hijo.stock) as stock_total,
                    GROUP_CONCAT(DISTINCT hijo.talla ORDER BY hijo.talla) as tallas_disponibles
                FROM productos padre
                LEFT JOIN productos hijo ON hijo.producto_padre_id = padre.id
                WHERE padre.producto_padre_id IS NULL
                AND padre.activo = 1
                GROUP BY padre.id
                HAVING total_variantes > 0
                ORDER BY padre.nombre";

        return $this->db->fetchAll($sql);
    }

    /**
     * Obtener información completa de un producto con sus variantes
     * @param int $productoId ID del producto padre
     * @return array Datos del producto padre con array de variantes
     */
    public function obtenerProductoConVariantes($productoId) {
        // Obtener producto padre
        $sql = "SELECT * FROM productos WHERE id = :id AND activo = 1";
        $producto = $this->db->fetch($sql, ['id' => $productoId]);

        if (!$producto) {
            return null;
        }

        // Si el producto tiene producto_padre_id, obtener el padre real
        if ($producto['producto_padre_id']) {
            $productoId = $producto['producto_padre_id'];
            $producto = $this->db->fetch($sql, ['id' => $productoId]);
        }

        // Obtener todas las variantes (incluyendo el padre como variante)
        $sqlVariantes = "SELECT * FROM productos
                        WHERE (id = :id1 OR producto_padre_id = :id2)
                        AND activo = 1
                        ORDER BY talla, color";

        $producto['variantes'] = $this->db->fetchAll($sqlVariantes, [
            'id1' => $productoId,
            'id2' => $productoId
        ]);

        return $producto;
    }

    /**
     * Obtener estadísticas de variantes
     * @return array Estadísticas generales
     */
    public function obtenerEstadisticas() {
        $sql = "SELECT
                    COUNT(DISTINCT CASE WHEN producto_padre_id IS NULL THEN id END) as total_productos,
                    COUNT(DISTINCT CASE
                        WHEN producto_padre_id IS NULL
                        AND id IN (SELECT producto_padre_id FROM productos WHERE producto_padre_id IS NOT NULL)
                        THEN id
                    END) as productos_con_variantes,
                    COUNT(CASE WHEN producto_padre_id IS NOT NULL THEN 1 END) as total_variantes
                FROM productos
                WHERE activo = 1";

        return $this->db->fetch($sql, []);
    }

    /**
     * Validar que no existan referencias circulares
     * @param int $padreId ID del producto padre
     * @param array $variantesIds IDs de las variantes
     * @return bool True si las referencias son válidas
     */
    private function validarReferencias($padreId, $variantesIds) {
        error_log("=== VALIDANDO REFERENCIAS ===");
        error_log("Producto Padre ID: " . $padreId);
        error_log("Variantes IDs: " . print_r($variantesIds, true));

        // El padre no puede estar en la lista de variantes
        if (in_array($padreId, $variantesIds)) {
            error_log("ERROR: El producto padre ($padreId) está en la lista de variantes");
            return false;
        }

        // Asegurar que sea un array indexado consecutivo
        $variantesIds = array_values($variantesIds);

        // Verificar que haya variantes para validar
        if (empty($variantesIds)) {
            error_log("ERROR: No hay variantes para validar (array vacío)");
            return false;
        }

        // Verificar que ninguna variante ya sea padre de otros productos
        $sql = "SELECT COUNT(*) as total
                FROM productos
                WHERE producto_padre_id IN (" . implode(',', array_fill(0, count($variantesIds), '?')) . ")";

        $result = $this->db->fetch($sql, $variantesIds);

        if ($result['total'] > 0) {
            error_log("ERROR: Alguna variante ya es padre de otros productos (total: {$result['total']})");
            return false;
        }

        // Verificar que el padre no sea variante de otro producto
        $sqlPadre = "SELECT producto_padre_id FROM productos WHERE id = :id";
        $padre = $this->db->fetch($sqlPadre, ['id' => $padreId]);

        if ($padre && $padre['producto_padre_id'] !== null) {
            error_log("ERROR: El producto padre ($padreId) ya es variante de otro producto (padre: {$padre['producto_padre_id']})");
            return false;
        }

        // Verificar que el producto padre NO tenga variantes ya consolidadas
        $sqlVariantesExistentes = "SELECT COUNT(*) as total FROM producto_variantes WHERE producto_id = :producto_id";
        $variantesExistentes = $this->db->fetch($sqlVariantesExistentes, ['producto_id' => $padreId]);

        if ($variantesExistentes['total'] > 0) {
            error_log("ERROR: El producto padre ($padreId) ya tiene variantes consolidadas (total: {$variantesExistentes['total']}). No se pueden agregar más variantes.");
            return false;
        }

        error_log("✓ Validación exitosa - Producto padre ($padreId) con variantes: " . implode(', ', $variantesIds));
        return true;
    }

    /**
     * Eliminar variante específica (convertirla en producto independiente)
     * @param int $varianteId ID de la variante
     * @return bool True si se eliminó correctamente
     */
    public function eliminarVariante($varianteId) {
        try {
            $sql = "UPDATE productos
                    SET producto_padre_id = NULL
                    WHERE id = :id";

            $this->db->execute($sql, ['id' => $varianteId]);
            return true;

        } catch (Exception $e) {
            error_log("Error eliminando variante: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Buscar productos candidatos para agrupar (similar nombre/categoría)
     * @param string $termino Término de búsqueda
     * @return array Productos que podrían agruparse
     */
    public function buscarCandidatosAgrupacion($termino) {
        $sql = "SELECT
                    p1.id,
                    p1.nombre,
                    p1.talla,
                    p1.color,
                    p1.stock,
                    p1.precio,
                    p1.imagen,
                    p1.categoria,
                    p1.tipo,
                    COUNT(p2.id) as productos_similares
                FROM productos p1
                LEFT JOIN productos p2 ON (
                    p2.nombre LIKE CONCAT('%', SUBSTRING_INDEX(p1.nombre, '-', 1), '%')
                    AND p2.categoria = p1.categoria
                    AND p2.id != p1.id
                    AND p2.producto_padre_id IS NULL
                    AND p2.activo = 1
                )
                WHERE p1.producto_padre_id IS NULL
                AND p1.activo = 1
                AND (p1.nombre LIKE :termino OR p1.codigo_producto LIKE :termino)
                GROUP BY p1.id
                HAVING productos_similares > 0
                ORDER BY productos_similares DESC, p1.nombre";

        $parametro = '%' . $termino . '%';
        return $this->db->fetchAll($sql, ['termino' => $parametro]);
    }

    /**
     * ================================================================
     * SISTEMA DE CONSOLIDACIÓN DE PRODUCTOS
     * Métodos para el nuevo sistema que convierte múltiples productos
     * en uno solo con variantes en tabla separada
     * ================================================================
     */

    /**
     * Consolidar productos agrupados en un solo producto con variantes
     * @param int $productoPadreId ID del producto que será el principal
     * @param array $variantesIds IDs de productos a consolidar como variantes
     * @return bool True si se consolidó correctamente
     */
    public function consolidarProductos($productoPadreId, $variantesIds) {
        // Validar que no haya referencias circulares
        if (!$this->validarReferencias($productoPadreId, $variantesIds)) {
            return false;
        }

        try {
            $this->db->beginTransaction();

            // 1. Obtener datos del producto padre
            require_once 'models/Producto.php';
            $productoModel = new Producto();
            $productoPadre = $productoModel->obtenerPorId($productoPadreId);

            if (!$productoPadre) {
                throw new Exception("Producto padre no encontrado");
            }

            // 2. Crear variante para el producto padre (si tiene talla/color)
            if (!empty($productoPadre['talla']) || !empty($productoPadre['color'])) {
                $this->crearVariante([
                    'producto_id' => $productoPadreId,
                    'talla' => $productoPadre['talla'],
                    'color' => $productoPadre['color'],
                    'stock' => $productoPadre['stock'],
                    'codigo_unico' => $productoPadre['codigo_producto']
                ]);
            }

            // 3. Consolidar cada variante
            $stockTotal = $productoPadre['stock'];
            foreach ($variantesIds as $varianteId) {
                $variante = $productoModel->obtenerPorId($varianteId);

                if (!$variante) {
                    continue;
                }

                // Crear variante en nueva tabla
                $this->crearVariante([
                    'producto_id' => $productoPadreId,
                    'talla' => $variante['talla'],
                    'color' => $variante['color'],
                    'stock' => $variante['stock'],
                    'codigo_unico' => $variante['codigo_producto']
                ]);

                // Acumular stock total
                $stockTotal += $variante['stock'];

                // Marcar producto como inactivo (soft delete)
                $sqlInactivar = "UPDATE productos SET activo = 0 WHERE id = :id";
                $this->db->execute($sqlInactivar, ['id' => $varianteId]);
            }

            // 4. Actualizar stock total del producto padre
            $sqlUpdateStock = "UPDATE productos SET stock = :stock WHERE id = :id";
            $this->db->execute($sqlUpdateStock, [
                'stock' => $stockTotal,
                'id' => $productoPadreId
            ]);

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error consolidando productos: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Crear una variante en la tabla producto_variantes
     * @param array $datos Datos de la variante
     * @return bool True si se creó correctamente
     */
    private function crearVariante($datos) {
        $sql = "INSERT INTO producto_variantes
                (producto_id, talla, color, stock, codigo_unico, activo)
                VALUES (:producto_id, :talla, :color, :stock, :codigo_unico, 1)";

        return $this->db->execute($sql, [
            'producto_id' => $datos['producto_id'],
            'talla' => $datos['talla'] ?? null,
            'color' => $datos['color'] ?? null,
            'stock' => $datos['stock'] ?? 0,
            'codigo_unico' => $datos['codigo_unico'] ?? null
        ]);
    }

    /**
     * Obtener variantes desde la nueva tabla producto_variantes
     * @param int $productoId ID del producto
     * @return array Lista de variantes
     */
    public function obtenerVariantesConsolidadas($productoId) {
        $sql = "SELECT * FROM producto_variantes
                WHERE producto_id = :id AND activo = 1
                ORDER BY talla, color";

        return $this->db->fetchAll($sql, ['id' => $productoId]);
    }

    /**
     * Actualizar stock de una variante específica
     * @param int $varianteId ID de la variante
     * @param int $nuevoStock Nuevo valor de stock
     * @return bool True si se actualizó correctamente
     */
    public function actualizarStockVariante($varianteId, $nuevoStock) {
        try {
            $this->db->beginTransaction();

            // Actualizar stock de la variante
            $sql = "UPDATE producto_variantes SET stock = :stock WHERE id = :id";
            $this->db->execute($sql, ['stock' => $nuevoStock, 'id' => $varianteId]);

            // Recalcular stock total del producto padre
            $sqlVariante = "SELECT producto_id FROM producto_variantes WHERE id = :id";
            $variante = $this->db->fetch($sqlVariante, ['id' => $varianteId]);

            if ($variante) {
                $this->recalcularStockTotal($variante['producto_id']);
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error actualizando stock de variante: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Recalcular stock total del producto padre sumando variantes
     * @param int $productoId ID del producto
     * @return bool True si se recalculó correctamente
     */
    private function recalcularStockTotal($productoId) {
        $sql = "UPDATE productos p
                SET p.stock = (
                    SELECT COALESCE(SUM(pv.stock), 0)
                    FROM producto_variantes pv
                    WHERE pv.producto_id = p.id AND pv.activo = 1
                )
                WHERE p.id = :id";

        return $this->db->execute($sql, ['id' => $productoId]);
    }

    /**
     * Verificar si un producto tiene variantes consolidadas
     * @param int $productoId ID del producto
     * @return bool True si tiene variantes
     */
    public function tieneVariantesConsolidadas($productoId) {
        $sql = "SELECT COUNT(*) as total
                FROM producto_variantes
                WHERE producto_id = :id AND activo = 1";

        $result = $this->db->fetch($sql, ['id' => $productoId]);
        return $result['total'] > 0;
    }

    /**
     * Eliminar una variante consolidada
     * @param int $varianteId ID de la variante
     * @return bool True si se eliminó correctamente
     */
    public function eliminarVarianteConsolidada($varianteId) {
        try {
            $this->db->beginTransaction();

            // Obtener producto_id antes de eliminar
            $sql = "SELECT producto_id FROM producto_variantes WHERE id = :id";
            $variante = $this->db->fetch($sql, ['id' => $varianteId]);

            // Soft delete de la variante
            $sqlDelete = "UPDATE producto_variantes SET activo = 0 WHERE id = :id";
            $this->db->execute($sqlDelete, ['id' => $varianteId]);

            // Recalcular stock total
            if ($variante) {
                $this->recalcularStockTotal($variante['producto_id']);
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error eliminando variante consolidada: " . $e->getMessage());
            return false;
        }
    }
}
