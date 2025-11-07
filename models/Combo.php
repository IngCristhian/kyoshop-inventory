<?php
/**
 * MODELO COMBO
 * Manejo de combos de productos
 */

require_once 'config/database.php';
require_once 'models/Producto.php';

class Combo {
    private $db;
    private $producto;

    // Configuración de tipos de combo
    const TIPOS = [
        'small' => 10,
        'medium' => 25,
        'big' => 50,
        'extra_big' => 100
    ];

    public function __construct() {
        $this->db = getDB();
        $this->producto = new Producto();
    }

    /**
     * Obtener todos los combos activos
     */
    public function obtenerTodos($filtros = []) {
        $condiciones = ['activo = 1'];
        $parametros = [];

        if (!empty($filtros['tipo'])) {
            $condiciones[] = 'tipo = :tipo';
            $parametros['tipo'] = $filtros['tipo'];
        }

        if (!empty($filtros['ubicacion'])) {
            $condiciones[] = 'ubicacion = :ubicacion';
            $parametros['ubicacion'] = $filtros['ubicacion'];
        }

        $where = implode(' AND ', $condiciones);

        $sql = "SELECT * FROM combos
                WHERE {$where}
                ORDER BY fecha_creacion DESC";

        return $this->db->fetchAll($sql, $parametros);
    }

    /**
     * Obtener combo por ID con productos y categorías
     */
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM combos WHERE id = :id AND activo = 1";
        $combo = $this->db->fetch($sql, ['id' => $id]);

        if (!$combo) {
            return null;
        }

        // Obtener productos del combo
        $combo['productos'] = $this->obtenerProductosCombo($id);

        // Obtener configuración de categorías
        $combo['categorias'] = $this->obtenerCategoriasCombo($id);

        return $combo;
    }

    /**
     * Obtener productos incluidos en un combo
     */
    public function obtenerProductosCombo($comboId) {
        $sql = "SELECT cp.*, p.nombre, p.codigo_producto, p.precio as precio_unitario,
                       p.imagen, p.talla, p.color, p.ubicacion
                FROM combos_productos cp
                INNER JOIN productos p ON cp.producto_id = p.id
                WHERE cp.combo_id = :combo_id
                ORDER BY cp.categoria, p.nombre";

        return $this->db->fetchAll($sql, ['combo_id' => $comboId]);
    }

    /**
     * Obtener configuración de categorías de un combo
     */
    public function obtenerCategoriasCombo($comboId) {
        $sql = "SELECT * FROM combos_categorias
                WHERE combo_id = :combo_id
                ORDER BY categoria";

        return $this->db->fetchAll($sql, ['combo_id' => $comboId]);
    }

    /**
     * Verificar disponibilidad de stock para crear combo
     */
    public function verificarStock($categorias, $ubicacion) {
        $resultado = [
            'disponible' => true,
            'faltantes' => [],
            'advertencias' => []
        ];

        foreach ($categorias as $categoria => $cantidad) {
            if ($cantidad <= 0) continue;

            $condiciones = ['activo = 1', 'categoria = :categoria', 'stock > 0'];
            $parametros = ['categoria' => $categoria];

            // Filtrar por ubicación si no es mixto
            if ($ubicacion !== 'Mixto') {
                $condiciones[] = 'ubicacion = :ubicacion';
                $parametros['ubicacion'] = $ubicacion;
            }

            $where = implode(' AND ', $condiciones);

            $sql = "SELECT COUNT(*) as total, SUM(stock) as stock_total
                    FROM productos
                    WHERE {$where}";

            $disponibilidad = $this->db->fetch($sql, $parametros);

            if ($disponibilidad['total'] < $cantidad) {
                $resultado['disponible'] = false;
                $resultado['faltantes'][] = [
                    'categoria' => $categoria,
                    'necesita' => $cantidad,
                    'disponible' => $disponibilidad['total'],
                    'ubicacion' => $ubicacion
                ];
            }

            // Advertencia si hay stock pero es justo
            if ($disponibilidad['total'] >= $cantidad && $disponibilidad['total'] < ($cantidad * 1.5)) {
                $resultado['advertencias'][] = [
                    'categoria' => $categoria,
                    'mensaje' => "Stock bajo para {$categoria} en {$ubicacion}"
                ];
            }
        }

        return $resultado;
    }

    /**
     * Seleccionar productos aleatoriamente para el combo
     */
    public function seleccionarProductos($categorias, $ubicacion) {
        $productosSeleccionados = [];

        foreach ($categorias as $categoria => $cantidad) {
            if ($cantidad <= 0) continue;

            $condiciones = ['activo = 1', 'categoria = :categoria', 'stock > 0'];
            $parametros = ['categoria' => $categoria, 'limite' => $cantidad];

            if ($ubicacion !== 'Mixto') {
                $condiciones[] = 'ubicacion = :ubicacion';
                $parametros['ubicacion'] = $ubicacion;
            }

            $where = implode(' AND ', $condiciones);

            // Seleccionar productos aleatoriamente
            $sql = "SELECT id, nombre, codigo_producto, categoria, ubicacion
                    FROM productos
                    WHERE {$where}
                    ORDER BY RAND()
                    LIMIT :limite";

            $productos = $this->db->fetchAll($sql, $parametros);

            foreach ($productos as $producto) {
                $productosSeleccionados[] = [
                    'producto_id' => $producto['id'],
                    'categoria' => $producto['categoria']
                ];
            }
        }

        return $productosSeleccionados;
    }

    /**
     * Crear nuevo combo
     */
    public function crear($datos, $categorias) {
        try {
            // Verificar stock antes de crear
            $verificacion = $this->verificarStock($categorias, $datos['ubicacion']);

            if (!$verificacion['disponible']) {
                return [
                    'success' => false,
                    'error' => 'Stock insuficiente',
                    'faltantes' => $verificacion['faltantes']
                ];
            }

            // Insertar combo
            $sql = "INSERT INTO combos (nombre, tipo, cantidad_total, precio, ubicacion)
                    VALUES (:nombre, :tipo, :cantidad_total, :precio, :ubicacion)";

            $comboId = $this->db->insert($sql, [
                'nombre' => $datos['nombre'],
                'tipo' => $datos['tipo'],
                'cantidad_total' => $datos['cantidad_total'],
                'precio' => $datos['precio'],
                'ubicacion' => $datos['ubicacion']
            ]);

            if (!$comboId) {
                return ['success' => false, 'error' => 'Error al crear combo'];
            }

            // Guardar configuración de categorías
            foreach ($categorias as $categoria => $cantidad) {
                if ($cantidad <= 0) continue;

                $sqlCat = "INSERT INTO combos_categorias (combo_id, categoria, cantidad)
                           VALUES (:combo_id, :categoria, :cantidad)";

                $this->db->execute($sqlCat, [
                    'combo_id' => $comboId,
                    'categoria' => $categoria,
                    'cantidad' => $cantidad
                ]);
            }

            // Seleccionar y asignar productos
            $productosSeleccionados = $this->seleccionarProductos($categorias, $datos['ubicacion']);

            foreach ($productosSeleccionados as $item) {
                $sqlProd = "INSERT INTO combos_productos (combo_id, producto_id, categoria)
                            VALUES (:combo_id, :producto_id, :categoria)";

                $this->db->execute($sqlProd, [
                    'combo_id' => $comboId,
                    'producto_id' => $item['producto_id'],
                    'categoria' => $item['categoria']
                ]);
            }

            return [
                'success' => true,
                'combo_id' => $comboId,
                'advertencias' => $verificacion['advertencias']
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Error al crear combo: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Actualizar combo (solo precio y nombre)
     */
    public function actualizar($id, $datos) {
        $sql = "UPDATE combos
                SET nombre = :nombre,
                    precio = :precio,
                    fecha_actualizacion = CURRENT_TIMESTAMP
                WHERE id = :id";

        return $this->db->execute($sql, [
            'id' => $id,
            'nombre' => $datos['nombre'],
            'precio' => $datos['precio']
        ]);
    }

    /**
     * Eliminar combo (soft delete)
     */
    public function eliminar($id) {
        $sql = "UPDATE combos
                SET activo = 0, fecha_actualizacion = CURRENT_TIMESTAMP
                WHERE id = :id";
        return $this->db->execute($sql, ['id' => $id]);
    }

    /**
     * Obtener estadísticas de combos
     */
    public function obtenerEstadisticas() {
        $sql = "SELECT
                    COUNT(*) as total_combos,
                    SUM(cantidad_total) as total_prendas,
                    AVG(precio) as precio_promedio,
                    SUM(precio) as valor_total_combos,
                    COUNT(CASE WHEN tipo = 'small' THEN 1 END) as combos_small,
                    COUNT(CASE WHEN tipo = 'medium' THEN 1 END) as combos_medium,
                    COUNT(CASE WHEN tipo = 'big' THEN 1 END) as combos_big,
                    COUNT(CASE WHEN tipo = 'extra_big' THEN 1 END) as combos_extra_big
                FROM combos
                WHERE activo = 1";

        return $this->db->fetch($sql);
    }
}
?>
