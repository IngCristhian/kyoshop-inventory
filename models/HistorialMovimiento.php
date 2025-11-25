<?php
/**
 * MODELO DE HISTORIAL DE MOVIMIENTOS
 * Gestión del registro histórico de movimientos de inventario
 */

require_once 'config/database.php';

class HistorialMovimiento {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Registrar un movimiento de inventario
     */
    public function registrar($datos) {
        $sql = "INSERT INTO historial_movimientos
                (producto_id, usuario_id, tipo_movimiento, cantidad, stock_anterior, stock_nuevo, precio_anterior, precio_nuevo, motivo)
                VALUES
                (:producto_id, :usuario_id, :tipo_movimiento, :cantidad, :stock_anterior, :stock_nuevo, :precio_anterior, :precio_nuevo, :motivo)";

        return $this->db->insert($sql, [
            'producto_id' => $datos['producto_id'],
            'usuario_id' => $datos['usuario_id'],
            'tipo_movimiento' => $datos['tipo_movimiento'],
            'cantidad' => $datos['cantidad'],
            'stock_anterior' => $datos['stock_anterior'],
            'stock_nuevo' => $datos['stock_nuevo'],
            'precio_anterior' => $datos['precio_anterior'] ?? null,
            'precio_nuevo' => $datos['precio_nuevo'] ?? null,
            'motivo' => $datos['motivo'] ?? null
        ]);
    }

    /**
     * Obtener historial completo con filtros
     */
    public function obtenerHistorial($filtros = [], $limite = 50, $offset = 0) {
        $where = ['1=1'];
        $params = [];

        // Filtro por producto
        if (!empty($filtros['producto_id'])) {
            $where[] = 'h.producto_id = :producto_id';
            $params['producto_id'] = $filtros['producto_id'];
        }

        // Filtro por tipo de movimiento
        if (!empty($filtros['tipo_movimiento'])) {
            $where[] = 'h.tipo_movimiento = :tipo_movimiento';
            $params['tipo_movimiento'] = $filtros['tipo_movimiento'];
        }

        // Filtro por usuario
        if (!empty($filtros['usuario_id'])) {
            $where[] = 'h.usuario_id = :usuario_id';
            $params['usuario_id'] = $filtros['usuario_id'];
        }

        // Filtro por rango de fechas
        if (!empty($filtros['fecha_desde'])) {
            $where[] = 'DATE(h.fecha_movimiento) >= :fecha_desde';
            $params['fecha_desde'] = $filtros['fecha_desde'];
        }

        if (!empty($filtros['fecha_hasta'])) {
            $where[] = 'DATE(h.fecha_movimiento) <= :fecha_hasta';
            $params['fecha_hasta'] = $filtros['fecha_hasta'];
        }

        $whereClause = implode(' AND ', $where);

        // Convertir límite y offset a enteros
        $limite = (int)$limite;
        $offset = (int)$offset;

        $sql = "SELECT
                    h.*,
                    p.nombre as producto_nombre,
                    p.codigo_producto,
                    u.nombre as usuario_nombre,
                    u.rol as usuario_rol
                FROM historial_movimientos h
                LEFT JOIN productos p ON h.producto_id = p.id
                LEFT JOIN usuarios u ON h.usuario_id = u.id
                WHERE {$whereClause}
                ORDER BY h.fecha_movimiento DESC
                LIMIT {$limite} OFFSET {$offset}";

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Obtener historial de un producto específico
     */
    public function obtenerHistorialProducto($productoId, $limite = 20) {
        return $this->obtenerHistorial(['producto_id' => $productoId], $limite);
    }

    /**
     * Contar total de movimientos con filtros
     */
    public function contarMovimientos($filtros = []) {
        $where = ['1=1'];
        $params = [];

        if (!empty($filtros['producto_id'])) {
            $where[] = 'producto_id = :producto_id';
            $params['producto_id'] = $filtros['producto_id'];
        }

        if (!empty($filtros['tipo_movimiento'])) {
            $where[] = 'tipo_movimiento = :tipo_movimiento';
            $params['tipo_movimiento'] = $filtros['tipo_movimiento'];
        }

        if (!empty($filtros['usuario_id'])) {
            $where[] = 'usuario_id = :usuario_id';
            $params['usuario_id'] = $filtros['usuario_id'];
        }

        if (!empty($filtros['fecha_desde'])) {
            $where[] = 'DATE(fecha_movimiento) >= :fecha_desde';
            $params['fecha_desde'] = $filtros['fecha_desde'];
        }

        if (!empty($filtros['fecha_hasta'])) {
            $where[] = 'DATE(fecha_movimiento) <= :fecha_hasta';
            $params['fecha_hasta'] = $filtros['fecha_hasta'];
        }

        $whereClause = implode(' AND ', $where);

        $sql = "SELECT COUNT(*) as total FROM historial_movimientos WHERE {$whereClause}";
        $result = $this->db->fetch($sql, $params);

        return $result['total'] ?? 0;
    }

    /**
     * Obtener estadísticas de movimientos
     */
    public function obtenerEstadisticas($dias = 30) {
        $sql = "SELECT
                    tipo_movimiento,
                    COUNT(*) as total_movimientos,
                    SUM(ABS(cantidad)) as cantidad_total
                FROM historial_movimientos
                WHERE fecha_movimiento >= DATE_SUB(NOW(), INTERVAL :dias DAY)
                GROUP BY tipo_movimiento";

        return $this->db->fetchAll($sql, ['dias' => $dias]);
    }

    /**
     * Obtener últimos movimientos (para dashboard)
     */
    public function obtenerUltimosMovimientos($limite = 10) {
        $limite = (int)$limite;

        $sql = "SELECT
                    h.*,
                    p.nombre as producto_nombre,
                    p.codigo_producto,
                    u.nombre as usuario_nombre
                FROM historial_movimientos h
                LEFT JOIN productos p ON h.producto_id = p.id
                LEFT JOIN usuarios u ON h.usuario_id = u.id
                ORDER BY h.fecha_movimiento DESC
                LIMIT {$limite}";

        return $this->db->fetchAll($sql);
    }
}
?>
