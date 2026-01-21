<?php
/**
 * MODELO VENTA
 * Manejo de ventas con descuento automático de stock
 */

require_once 'config/database.php';
require_once 'models/Producto.php';
require_once 'models/HistorialMovimiento.php';

class Venta {
    private $db;
    private $producto;
    private $historial;

    public function __construct() {
        $this->db = getDB();
        $this->producto = new Producto();
        $this->historial = new HistorialMovimiento();
    }

    /**
     * Obtener todas las ventas con paginación y filtros
     */
    public function obtenerTodos($pagina = 1, $limite = 20, $filtros = []) {
        $offset = ($pagina - 1) * $limite;
        $condiciones = ['1=1'];
        $parametros = [];

        // Filtros
        if (!empty($filtros['cliente_id'])) {
            $condiciones[] = 'v.cliente_id = :cliente_id';
            $parametros['cliente_id'] = $filtros['cliente_id'];
        }

        if (!empty($filtros['usuario_id'])) {
            $condiciones[] = 'v.usuario_id = :usuario_id';
            $parametros['usuario_id'] = $filtros['usuario_id'];
        }

        if (!empty($filtros['metodo_pago'])) {
            $condiciones[] = 'v.metodo_pago = :metodo_pago';
            $parametros['metodo_pago'] = $filtros['metodo_pago'];
        }

        if (!empty($filtros['estado_pago'])) {
            $condiciones[] = 'v.estado_pago = :estado_pago';
            $parametros['estado_pago'] = $filtros['estado_pago'];
        }

        if (!empty($filtros['fecha_desde'])) {
            $condiciones[] = 'DATE(v.fecha_venta) >= :fecha_desde';
            $parametros['fecha_desde'] = $filtros['fecha_desde'];
        }

        if (!empty($filtros['fecha_hasta'])) {
            $condiciones[] = 'DATE(v.fecha_venta) <= :fecha_hasta';
            $parametros['fecha_hasta'] = $filtros['fecha_hasta'];
        }

        if (!empty($filtros['busqueda'])) {
            $condiciones[] = '(v.numero_venta LIKE :busqueda1 OR c.nombre LIKE :busqueda2)';
            $parametros['busqueda1'] = '%' . $filtros['busqueda'] . '%';
            $parametros['busqueda2'] = '%' . $filtros['busqueda'] . '%';
        }

        $where = implode(' AND ', $condiciones);

        $limite = (int)$limite;
        $offset = (int)$offset;

        $sql = "SELECT v.*,
                       c.nombre as cliente_nombre, c.telefono as cliente_telefono,
                       u.nombre as vendedor_nombre
                FROM ventas v
                INNER JOIN clientes c ON v.cliente_id = c.id
                INNER JOIN usuarios u ON v.usuario_id = u.id
                WHERE {$where}
                ORDER BY v.fecha_venta DESC
                LIMIT {$limite} OFFSET {$offset}";

        return $this->db->fetchAll($sql, $parametros);
    }

    /**
     * Contar total de ventas
     */
    public function contarTotal($filtros = []) {
        $condiciones = ['1=1'];
        $parametros = [];

        if (!empty($filtros['cliente_id'])) {
            $condiciones[] = 'v.cliente_id = :cliente_id';
            $parametros['cliente_id'] = $filtros['cliente_id'];
        }

        if (!empty($filtros['usuario_id'])) {
            $condiciones[] = 'v.usuario_id = :usuario_id';
            $parametros['usuario_id'] = $filtros['usuario_id'];
        }

        if (!empty($filtros['metodo_pago'])) {
            $condiciones[] = 'v.metodo_pago = :metodo_pago';
            $parametros['metodo_pago'] = $filtros['metodo_pago'];
        }

        if (!empty($filtros['estado_pago'])) {
            $condiciones[] = 'v.estado_pago = :estado_pago';
            $parametros['estado_pago'] = $filtros['estado_pago'];
        }

        if (!empty($filtros['fecha_desde'])) {
            $condiciones[] = 'DATE(v.fecha_venta) >= :fecha_desde';
            $parametros['fecha_desde'] = $filtros['fecha_desde'];
        }

        if (!empty($filtros['fecha_hasta'])) {
            $condiciones[] = 'DATE(v.fecha_venta) <= :fecha_hasta';
            $parametros['fecha_hasta'] = $filtros['fecha_hasta'];
        }

        if (!empty($filtros['busqueda'])) {
            $condiciones[] = '(v.numero_venta LIKE :busqueda1 OR c.nombre LIKE :busqueda2)';
            $parametros['busqueda1'] = '%' . $filtros['busqueda'] . '%';
            $parametros['busqueda2'] = '%' . $filtros['busqueda'] . '%';
        }

        $where = implode(' AND ', $condiciones);

        $sql = "SELECT COUNT(*) as total
                FROM ventas v
                INNER JOIN clientes c ON v.cliente_id = c.id
                WHERE {$where}";

        $resultado = $this->db->fetch($sql, $parametros);
        return $resultado['total'];
    }

    /**
     * Obtener venta por ID con detalles
     */
    public function obtenerPorId($id) {
        $sql = "SELECT v.*,
                       c.nombre as cliente_nombre, c.telefono as cliente_telefono,
                       c.email as cliente_email, c.direccion as cliente_direccion,
                       c.ciudad as cliente_ciudad,
                       u.nombre as vendedor_nombre, u.email as vendedor_email
                FROM ventas v
                INNER JOIN clientes c ON v.cliente_id = c.id
                INNER JOIN usuarios u ON v.usuario_id = u.id
                WHERE v.id = :id";

        $venta = $this->db->fetch($sql, ['id' => $id]);

        if (!$venta) {
            return null;
        }

        // Obtener detalles de la venta
        $venta['items'] = $this->obtenerDetallesVenta($id);

        return $venta;
    }

    /**
     * Obtener detalles (items) de una venta
     */
    public function obtenerDetallesVenta($ventaId) {
        $sql = "SELECT vd.*,
                       p.nombre as producto_nombre,
                       p.codigo_producto,
                       p.categoria,
                       p.talla,
                       p.color
                FROM ventas_detalle vd
                INNER JOIN productos p ON vd.producto_id = p.id
                WHERE vd.venta_id = :venta_id
                ORDER BY vd.id ASC";

        return $this->db->fetchAll($sql, ['venta_id' => $ventaId]);
    }

    /**
     * Crear nueva venta (con transacción)
     */
    public function crear($datos, $items) {
        try {
            // Iniciar transacción
            $this->db->beginTransaction();

            // 1. Generar número de venta
            $numeroVenta = $this->generarNumeroVenta();

            // 2. Calcular totales
            $subtotal = 0;
            foreach ($items as $item) {
                $subtotal += $item['precio_unitario'] * $item['cantidad'];
            }

            $impuestos = 0; // Por ahora sin impuestos
            $total = $subtotal + $impuestos;

            // 3. Insertar venta
            $sqlVenta = "INSERT INTO ventas (
                            cliente_id, usuario_id, numero_venta, fecha_venta,
                            subtotal, impuestos, total, metodo_pago, estado_pago, observaciones
                        ) VALUES (
                            :cliente_id, :usuario_id, :numero_venta, :fecha_venta,
                            :subtotal, :impuestos, :total, :metodo_pago, :estado_pago, :observaciones
                        )";

            $ventaId = $this->db->insert($sqlVenta, [
                'cliente_id' => $datos['cliente_id'],
                'usuario_id' => $datos['usuario_id'],
                'numero_venta' => $numeroVenta,
                'fecha_venta' => $datos['fecha_venta'] ?? date('Y-m-d H:i:s'),
                'subtotal' => $subtotal,
                'impuestos' => $impuestos,
                'total' => $total,
                'metodo_pago' => $datos['metodo_pago'],
                'estado_pago' => $datos['estado_pago'] ?? 'pendiente',
                'observaciones' => $datos['observaciones'] ?? null
            ]);

            if (!$ventaId) {
                throw new Exception('Error al crear la venta');
            }

            // 4. Insertar items y descontar stock
            foreach ($items as $item) {
                // Verificar que haya stock suficiente
                $producto = $this->producto->obtenerPorId($item['producto_id']);

                if (!$producto) {
                    throw new Exception("Producto ID {$item['producto_id']} no encontrado");
                }

                if ($producto['stock'] < $item['cantidad']) {
                    throw new Exception("Stock insuficiente para {$producto['nombre']}. Stock disponible: {$producto['stock']}, solicitado: {$item['cantidad']}");
                }

                // Insertar detalle de venta
                $sqlDetalle = "INSERT INTO ventas_detalle (
                                  venta_id, producto_id, cantidad, precio_unitario, subtotal
                              ) VALUES (
                                  :venta_id, :producto_id, :cantidad, :precio_unitario, :subtotal
                              )";

                $this->db->execute($sqlDetalle, [
                    'venta_id' => $ventaId,
                    'producto_id' => $item['producto_id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'subtotal' => $item['precio_unitario'] * $item['cantidad']
                ]);

                // Descontar stock
                $nuevoStock = $producto['stock'] - $item['cantidad'];
                $sqlUpdateStock = "UPDATE productos SET stock = :stock WHERE id = :id";
                $this->db->execute($sqlUpdateStock, [
                    'stock' => $nuevoStock,
                    'id' => $item['producto_id']
                ]);

                // Registrar en historial
                $this->historial->registrar([
                    'producto_id' => $item['producto_id'],
                    'usuario_id' => $datos['usuario_id'],
                    'tipo_movimiento' => 'venta',
                    'cantidad' => -$item['cantidad'],
                    'stock_anterior' => $producto['stock'],
                    'stock_nuevo' => $nuevoStock,
                    'motivo' => "Venta #{$numeroVenta}"
                ]);
            }

            // Confirmar transacción
            $this->db->commit();

            return [
                'success' => true,
                'venta_id' => $ventaId,
                'numero_venta' => $numeroVenta
            ];

        } catch (Exception $e) {
            // Revertir transacción en caso de error
            $this->db->rollBack();

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Actualizar estado de pago de una venta
     */
    public function actualizarEstadoPago($id, $estadoPago) {
        $sql = "UPDATE ventas
                SET estado_pago = :estado_pago,
                    fecha_actualizacion = CURRENT_TIMESTAMP
                WHERE id = :id";

        return $this->db->execute($sql, [
            'id' => $id,
            'estado_pago' => $estadoPago
        ]);
    }

    /**
     * Cancelar venta (con devolución de stock)
     */
    public function cancelar($id, $usuarioId, $motivo = null) {
        try {
            $this->db->beginTransaction();

            // Obtener venta y sus items
            $venta = $this->obtenerPorId($id);

            if (!$venta) {
                throw new Exception('Venta no encontrada');
            }

            if ($venta['estado_pago'] === 'cancelado') {
                throw new Exception('La venta ya está cancelada');
            }

            // Devolver stock de cada producto
            foreach ($venta['items'] as $item) {
                $producto = $this->producto->obtenerPorId($item['producto_id']);
                $nuevoStock = $producto['stock'] + $item['cantidad'];

                $sqlUpdateStock = "UPDATE productos SET stock = :stock WHERE id = :id";
                $this->db->execute($sqlUpdateStock, [
                    'stock' => $nuevoStock,
                    'id' => $item['producto_id']
                ]);

                // Registrar en historial
                $this->historial->registrar([
                    'producto_id' => $item['producto_id'],
                    'usuario_id' => $usuarioId,
                    'tipo_movimiento' => 'devolucion',
                    'cantidad' => $item['cantidad'],
                    'stock_anterior' => $producto['stock'],
                    'stock_nuevo' => $nuevoStock,
                    'motivo' => "Cancelación venta #{$venta['numero_venta']}" . ($motivo ? " - {$motivo}" : '')
                ]);
            }

            // Actualizar estado de la venta
            $sqlUpdateVenta = "UPDATE ventas
                              SET estado_pago = 'cancelado',
                                  observaciones = CONCAT(COALESCE(observaciones, ''), '\nCANCELADA: " . ($motivo ?? 'Sin motivo especificado') . "'),
                                  fecha_actualizacion = CURRENT_TIMESTAMP
                              WHERE id = :id";

            $this->db->execute($sqlUpdateVenta, ['id' => $id]);

            $this->db->commit();

            return ['success' => true];

        } catch (Exception $e) {
            $this->db->rollBack();
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Generar número de venta único
     */
    private function generarNumeroVenta() {
        $fecha = date('Ymd');

        // Obtener el último número del día
        $sql = "SELECT numero_venta FROM ventas
                WHERE numero_venta LIKE :patron
                ORDER BY numero_venta DESC
                LIMIT 1";

        $resultado = $this->db->fetch($sql, ['patron' => "VEN-{$fecha}-%"]);

        if ($resultado) {
            // Extraer el número secuencial
            $partes = explode('-', $resultado['numero_venta']);
            $secuencial = intval($partes[2] ?? 0) + 1;
        } else {
            $secuencial = 1;
        }

        return "VEN-{$fecha}-" . str_pad($secuencial, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Obtener estadísticas de ventas
     * @param int|null $dias Número de días (null para todas las ventas)
     */
    public function obtenerEstadisticas($dias = null) {
        $whereClause = "";
        $parametros = [];

        if ($dias !== null) {
            $whereClause = "WHERE fecha_venta >= DATE_SUB(NOW(), INTERVAL :dias DAY)";
            $parametros['dias'] = $dias;
        }

        $sql = "SELECT
                    COUNT(*) as total_ventas,
                    SUM(CASE WHEN estado_pago != 'cancelado' THEN total ELSE 0 END) as monto_total,
                    AVG(CASE WHEN estado_pago != 'cancelado' THEN total ELSE NULL END) as ticket_promedio,
                    COUNT(CASE WHEN estado_pago = 'pagado' THEN 1 END) as ventas_pagadas,
                    COUNT(CASE WHEN estado_pago = 'pendiente' THEN 1 END) as ventas_pendientes,
                    COUNT(CASE WHEN estado_pago = 'cancelado' THEN 1 END) as ventas_canceladas,
                    COUNT(DISTINCT cliente_id) as clientes_unicos
                FROM ventas
                {$whereClause}";

        return $this->db->fetch($sql, $parametros);
    }

    /**
     * Obtener ventas del día
     */
    public function obtenerVentasDelDia() {
        $sql = "SELECT v.*,
                       c.nombre as cliente_nombre,
                       u.nombre as vendedor_nombre
                FROM ventas v
                INNER JOIN clientes c ON v.cliente_id = c.id
                INNER JOIN usuarios u ON v.usuario_id = u.id
                WHERE DATE(v.fecha_venta) = CURDATE()
                ORDER BY v.fecha_venta DESC";

        return $this->db->fetchAll($sql);
    }

    /**
     * Validar datos de venta
     */
    public function validar($datos, $items) {
        $errores = [];

        // Validar cliente
        if (empty($datos['cliente_id'])) {
            $errores[] = 'Debe seleccionar un cliente';
        }

        // Validar método de pago
        $metodosValidos = ['transferencia', 'contra_entrega', 'efectivo', 'tarjeta'];
        if (empty($datos['metodo_pago']) || !in_array($datos['metodo_pago'], $metodosValidos)) {
            $errores[] = 'Método de pago inválido';
        }

        // Validar items
        if (empty($items) || !is_array($items)) {
            $errores[] = 'Debe agregar al menos un producto a la venta';
        } else {
            foreach ($items as $index => $item) {
                if (empty($item['producto_id'])) {
                    $errores[] = "Item " . ($index + 1) . ": Producto no especificado";
                }
                if (empty($item['cantidad']) || $item['cantidad'] <= 0) {
                    $errores[] = "Item " . ($index + 1) . ": Cantidad inválida";
                }
                if (empty($item['precio_unitario']) || $item['precio_unitario'] <= 0) {
                    $errores[] = "Item " . ($index + 1) . ": Precio inválido";
                }
            }
        }

        return $errores;
    }
}
?>
