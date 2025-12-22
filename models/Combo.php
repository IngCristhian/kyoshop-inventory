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
                ORDER BY cp.tipo, p.nombre";

        return $this->db->fetchAll($sql, ['combo_id' => $comboId]);
    }

    /**
     * Obtener configuración de tipos de un combo
     */
    public function obtenerCategoriasCombo($comboId) {
        $sql = "SELECT * FROM combos_tipos
                WHERE combo_id = :combo_id
                ORDER BY tipo";

        return $this->db->fetchAll($sql, ['combo_id' => $comboId]);
    }

    /**
     * Verificar disponibilidad de stock para crear combo
     */
    public function verificarStock($tipos, $ubicacion) {
        $resultado = [
            'disponible' => true,
            'faltantes' => [],
            'advertencias' => []
        ];

        foreach ($tipos as $tipo => $cantidad) {
            if ($cantidad <= 0) continue;

            $condiciones = ['activo = 1', 'tipo = :tipo', 'stock > 0'];
            $parametros = ['tipo' => $tipo];

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
                    'tipo' => $tipo,
                    'necesita' => $cantidad,
                    'disponible' => $disponibilidad['total'],
                    'ubicacion' => $ubicacion
                ];
            }

            // Advertencia si hay stock pero es justo
            if ($disponibilidad['total'] >= $cantidad && $disponibilidad['total'] < ($cantidad * 1.5)) {
                $resultado['advertencias'][] = [
                    'tipo' => $tipo,
                    'mensaje' => "Stock bajo para {$tipo} en {$ubicacion}"
                ];
            }
        }

        return $resultado;
    }

    /**
     * Seleccionar productos aleatoriamente para el combo
     */
    public function seleccionarProductos($tipos, $ubicacion) {
        $productosSeleccionados = [];

        foreach ($tipos as $tipo => $cantidad) {
            if ($cantidad <= 0) continue;

            $condiciones = ['activo = 1', 'tipo = :tipo', 'stock > 0'];
            $parametros = ['tipo' => $tipo];

            if ($ubicacion !== 'Mixto') {
                $condiciones[] = 'ubicacion = :ubicacion';
                $parametros['ubicacion'] = $ubicacion;
            }

            $where = implode(' AND ', $condiciones);

            // Convertir cantidad a entero
            $cantidad = (int)$cantidad;

            // Seleccionar productos aleatoriamente (incluir precio y stock)
            $sql = "SELECT id, nombre, codigo_producto, tipo, ubicacion, precio, stock
                    FROM productos
                    WHERE {$where}
                    ORDER BY RAND()
                    LIMIT {$cantidad}";

            $productos = $this->db->fetchAll($sql, $parametros);

            foreach ($productos as $producto) {
                $productosSeleccionados[] = [
                    'producto_id' => $producto['id'],
                    'tipo' => $producto['tipo'],
                    'precio_original' => $producto['precio'],
                    'stock_actual' => $producto['stock']
                ];
            }
        }

        return $productosSeleccionados;
    }

    /**
     * Crear nuevo combo
     */
    public function crear($datos, $tipos) {
        try {
            // Verificar que el usuario esté autenticado
            if (!isset($_SESSION['usuario_id'])) {
                return ['success' => false, 'error' => 'Usuario no autenticado'];
            }

            $usuarioId = $_SESSION['usuario_id'];

            // Verificar stock antes de crear
            $verificacion = $this->verificarStock($tipos, $datos['ubicacion']);

            if (!$verificacion['disponible']) {
                return [
                    'success' => false,
                    'error' => 'Stock insuficiente',
                    'faltantes' => $verificacion['faltantes']
                ];
            }

            // Iniciar transacción
            $this->db->beginTransaction();

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

            // Guardar configuración de tipos
            foreach ($tipos as $tipo => $cantidad) {
                if ($cantidad <= 0) continue;

                $sqlTipo = "INSERT INTO combos_tipos (combo_id, tipo, cantidad)
                           VALUES (:combo_id, :tipo, :cantidad)";

                $this->db->execute($sqlTipo, [
                    'combo_id' => $comboId,
                    'tipo' => $tipo,
                    'cantidad' => $cantidad
                ]);
            }

            // Seleccionar y asignar productos
            $productosSeleccionados = $this->seleccionarProductos($tipos, $datos['ubicacion']);

            if (empty($productosSeleccionados)) {
                $this->db->rollBack();
                return ['success' => false, 'error' => 'No se pudieron seleccionar productos'];
            }

            // Calcular precio proporcional por prenda
            $sumaPreciosOriginales = array_sum(array_column($productosSeleccionados, 'precio_original'));
            $precioCombo = $datos['precio'];

            // PASO 1: Crear venta para el combo
            $numeroVenta = 'COM-' . date('Ymd') . '-' . str_pad($comboId, 4, '0', STR_PAD_LEFT);

            // Usar el cliente seleccionado o crear cliente general si no se proporcionó
            $clienteId = !empty($datos['cliente_id']) ? $datos['cliente_id'] : $this->obtenerClienteGeneral();

            $sqlVenta = "INSERT INTO ventas (cliente_id, usuario_id, numero_venta, subtotal, total, metodo_pago, estado_pago, observaciones)
                        VALUES (:cliente_id, :usuario_id, :numero_venta, :subtotal, :total, 'transferencia', 'pendiente', :observaciones)";

            $ventaId = $this->db->insert($sqlVenta, [
                'cliente_id' => $clienteId,
                'usuario_id' => $usuarioId,
                'numero_venta' => $numeroVenta,
                'subtotal' => $precioCombo,
                'total' => $precioCombo,
                'observaciones' => 'Venta automática del combo: ' . $datos['nombre']
            ]);

            if (!$ventaId) {
                $this->db->rollBack();
                return ['success' => false, 'error' => 'Error al crear venta'];
            }

            // PASO 2: Procesar cada producto (ventas_detalle, historial, stock)
            foreach ($productosSeleccionados as $item) {
                // Calcular precio proporcional
                $precioProporcional = $sumaPreciosOriginales > 0
                    ? ($item['precio_original'] / $sumaPreciosOriginales) * $precioCombo
                    : $precioCombo / count($productosSeleccionados);

                // Insertar en combos_productos
                $sqlProd = "INSERT INTO combos_productos (combo_id, producto_id, tipo)
                            VALUES (:combo_id, :producto_id, :tipo)";

                $this->db->execute($sqlProd, [
                    'combo_id' => $comboId,
                    'producto_id' => $item['producto_id'],
                    'tipo' => $item['tipo']
                ]);

                // Insertar en ventas_detalle
                $sqlDetalle = "INSERT INTO ventas_detalle (venta_id, producto_id, cantidad, precio_unitario, subtotal)
                              VALUES (:venta_id, :producto_id, 1, :precio_unitario, :subtotal)";

                $this->db->execute($sqlDetalle, [
                    'venta_id' => $ventaId,
                    'producto_id' => $item['producto_id'],
                    'precio_unitario' => $precioProporcional,
                    'subtotal' => $precioProporcional
                ]);

                // Descontar stock
                $stockAnterior = $item['stock_actual'];
                $stockNuevo = $stockAnterior - 1;

                $sqlStock = "UPDATE productos SET stock = stock - 1 WHERE id = :id";
                $this->db->execute($sqlStock, ['id' => $item['producto_id']]);

                // Registrar en historial
                $sqlHistorial = "INSERT INTO historial_movimientos (producto_id, usuario_id, tipo_movimiento, cantidad, stock_anterior, stock_nuevo, motivo)
                                VALUES (:producto_id, :usuario_id, 'salida', -1, :stock_anterior, :stock_nuevo, :motivo)";

                $this->db->execute($sqlHistorial, [
                    'producto_id' => $item['producto_id'],
                    'usuario_id' => $usuarioId,
                    'stock_anterior' => $stockAnterior,
                    'stock_nuevo' => $stockNuevo,
                    'motivo' => 'Producto incluido en combo: ' . $datos['nombre'] . ' (Venta: ' . $numeroVenta . ')'
                ]);
            }

            // Confirmar transacción
            $this->db->commit();

            return [
                'success' => true,
                'combo_id' => $comboId,
                'venta_id' => $ventaId,
                'numero_venta' => $numeroVenta,
                'advertencias' => $verificacion['advertencias']
            ];

        } catch (Exception $e) {
            // Revertir transacción en caso de error
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            return [
                'success' => false,
                'error' => 'Error al crear combo: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtener o crear cliente general para combos
     */
    private function obtenerClienteGeneral() {
        $sql = "SELECT id FROM clientes WHERE nombre = 'Cliente General - Combos' LIMIT 1";
        $cliente = $this->db->fetch($sql);

        if ($cliente) {
            return $cliente['id'];
        }

        // Crear cliente general si no existe
        $sqlInsert = "INSERT INTO clientes (nombre, telefono, email, ciudad)
                     VALUES ('Cliente General - Combos', '0000000000', 'combos@kyoshop.co', 'Medellín')";

        return $this->db->insert($sqlInsert);
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
        try {
            error_log("=== Combo::eliminar() ===");
            error_log("ID recibido: " . $id);

            // Primero verificar que el combo existe y está activo
            $sqlCheck = "SELECT id, nombre, activo FROM combos WHERE id = :id";
            $combo = $this->db->fetch($sqlCheck, ['id' => $id]);

            error_log("Combo encontrado: " . print_r($combo, true));

            if (!$combo) {
                error_log("Combo NO encontrado con ID: " . $id);
                return [
                    'success' => false,
                    'message' => 'Combo no encontrado',
                    'rows' => 0
                ];
            }

            if ($combo['activo'] == 0) {
                error_log("Combo ya está eliminado (activo = 0)");
                return [
                    'success' => false,
                    'message' => 'El combo ya está eliminado',
                    'rows' => 0
                ];
            }

            // Realizar soft delete
            $sqlUpdate = "UPDATE combos
                         SET activo = 0, fecha_actualizacion = CURRENT_TIMESTAMP
                         WHERE id = :id";

            error_log("Ejecutando UPDATE para eliminar combo ID: " . $id);
            $rows = $this->db->execute($sqlUpdate, ['id' => $id]);
            error_log("Filas afectadas: " . $rows);

            if ($rows > 0) {
                error_log("Combo eliminado exitosamente");
                return [
                    'success' => true,
                    'message' => 'Combo eliminado correctamente',
                    'rows' => $rows
                ];
            } else {
                error_log("No se afectaron filas al intentar eliminar");
                return [
                    'success' => false,
                    'message' => 'No se pudo eliminar el combo (0 filas afectadas)',
                    'rows' => 0
                ];
            }

        } catch (Exception $e) {
            error_log("Error en Combo::eliminar() - ID: {$id} - " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return [
                'success' => false,
                'message' => 'Error al eliminar: ' . $e->getMessage(),
                'rows' => 0
            ];
        }
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
