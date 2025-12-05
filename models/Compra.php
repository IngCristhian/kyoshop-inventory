<?php
/**
 * MODELO COMPRA
 * Manejo de compras de insumos (bolsas, etiquetas, embalaje, etc.)
 */

require_once 'config/database.php';

class Compra {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Obtener todas las compras con paginación y filtros
     */
    public function obtenerTodos($pagina = 1, $limite = 20, $filtros = []) {
        $offset = ($pagina - 1) * $limite;
        $condiciones = ['1=1'];
        $parametros = [];

        // Filtros
        if (!empty($filtros['usuario_id'])) {
            $condiciones[] = 'c.usuario_id = :usuario_id';
            $parametros['usuario_id'] = $filtros['usuario_id'];
        }

        if (!empty($filtros['categoria_insumo'])) {
            $condiciones[] = 'c.categoria_insumo = :categoria_insumo';
            $parametros['categoria_insumo'] = $filtros['categoria_insumo'];
        }

        if (!empty($filtros['metodo_pago'])) {
            $condiciones[] = 'c.metodo_pago = :metodo_pago';
            $parametros['metodo_pago'] = $filtros['metodo_pago'];
        }

        if (!empty($filtros['fecha_desde'])) {
            $condiciones[] = 'DATE(c.fecha_compra) >= :fecha_desde';
            $parametros['fecha_desde'] = $filtros['fecha_desde'];
        }

        if (!empty($filtros['fecha_hasta'])) {
            $condiciones[] = 'DATE(c.fecha_compra) <= :fecha_hasta';
            $parametros['fecha_hasta'] = $filtros['fecha_hasta'];
        }

        if (!empty($filtros['proveedor'])) {
            $condiciones[] = 'c.proveedor LIKE :proveedor';
            $parametros['proveedor'] = '%' . $filtros['proveedor'] . '%';
        }

        if (!empty($filtros['busqueda'])) {
            $condiciones[] = '(c.numero_compra LIKE :busqueda1 OR c.descripcion LIKE :busqueda2 OR c.proveedor LIKE :busqueda3)';
            $parametros['busqueda1'] = '%' . $filtros['busqueda'] . '%';
            $parametros['busqueda2'] = '%' . $filtros['busqueda'] . '%';
            $parametros['busqueda3'] = '%' . $filtros['busqueda'] . '%';
        }

        $where = implode(' AND ', $condiciones);

        $limite = (int)$limite;
        $offset = (int)$offset;

        $sql = "SELECT c.*,
                       u.nombre as usuario_nombre
                FROM compras c
                INNER JOIN usuarios u ON c.usuario_id = u.id
                WHERE {$where}
                ORDER BY c.fecha_compra DESC
                LIMIT {$limite} OFFSET {$offset}";

        return $this->db->fetchAll($sql, $parametros);
    }

    /**
     * Contar total de compras
     */
    public function contarTotal($filtros = []) {
        $condiciones = ['1=1'];
        $parametros = [];

        if (!empty($filtros['usuario_id'])) {
            $condiciones[] = 'c.usuario_id = :usuario_id';
            $parametros['usuario_id'] = $filtros['usuario_id'];
        }

        if (!empty($filtros['categoria_insumo'])) {
            $condiciones[] = 'c.categoria_insumo = :categoria_insumo';
            $parametros['categoria_insumo'] = $filtros['categoria_insumo'];
        }

        if (!empty($filtros['metodo_pago'])) {
            $condiciones[] = 'c.metodo_pago = :metodo_pago';
            $parametros['metodo_pago'] = $filtros['metodo_pago'];
        }

        if (!empty($filtros['fecha_desde'])) {
            $condiciones[] = 'DATE(c.fecha_compra) >= :fecha_desde';
            $parametros['fecha_desde'] = $filtros['fecha_desde'];
        }

        if (!empty($filtros['fecha_hasta'])) {
            $condiciones[] = 'DATE(c.fecha_compra) <= :fecha_hasta';
            $parametros['fecha_hasta'] = $filtros['fecha_hasta'];
        }

        if (!empty($filtros['proveedor'])) {
            $condiciones[] = 'c.proveedor LIKE :proveedor';
            $parametros['proveedor'] = '%' . $filtros['proveedor'] . '%';
        }

        if (!empty($filtros['busqueda'])) {
            $condiciones[] = '(c.numero_compra LIKE :busqueda1 OR c.descripcion LIKE :busqueda2 OR c.proveedor LIKE :busqueda3)';
            $parametros['busqueda1'] = '%' . $filtros['busqueda'] . '%';
            $parametros['busqueda2'] = '%' . $filtros['busqueda'] . '%';
            $parametros['busqueda3'] = '%' . $filtros['busqueda'] . '%';
        }

        $where = implode(' AND ', $condiciones);

        $sql = "SELECT COUNT(*) as total
                FROM compras c
                INNER JOIN usuarios u ON c.usuario_id = u.id
                WHERE {$where}";

        $resultado = $this->db->fetch($sql, $parametros);
        return $resultado['total'];
    }

    /**
     * Obtener compra por ID
     */
    public function obtenerPorId($id) {
        $sql = "SELECT c.*,
                       u.nombre as usuario_nombre, u.email as usuario_email
                FROM compras c
                INNER JOIN usuarios u ON c.usuario_id = u.id
                WHERE c.id = :id";

        return $this->db->fetch($sql, ['id' => $id]);
    }

    /**
     * Crear nueva compra
     */
    public function crear($datos) {
        try {
            // Generar número de compra
            $numeroCompra = $this->generarNumeroCompra();

            $sql = "INSERT INTO compras (
                        usuario_id, numero_compra, fecha_compra, proveedor,
                        descripcion, categoria_insumo, cantidad, precio_unitario,
                        total, metodo_pago, comprobante, observaciones
                    ) VALUES (
                        :usuario_id, :numero_compra, :fecha_compra, :proveedor,
                        :descripcion, :categoria_insumo, :cantidad, :precio_unitario,
                        :total, :metodo_pago, :comprobante, :observaciones
                    )";

            $compraId = $this->db->insert($sql, [
                'usuario_id' => $datos['usuario_id'],
                'numero_compra' => $numeroCompra,
                'fecha_compra' => $datos['fecha_compra'] ?? date('Y-m-d H:i:s'),
                'proveedor' => $datos['proveedor'] ?? null,
                'descripcion' => $datos['descripcion'],
                'categoria_insumo' => $datos['categoria_insumo'] ?? 'otros',
                'cantidad' => $datos['cantidad'] ?? null,
                'precio_unitario' => $datos['precio_unitario'] ?? null,
                'total' => $datos['total'],
                'metodo_pago' => $datos['metodo_pago'],
                'comprobante' => $datos['comprobante'] ?? null,
                'observaciones' => $datos['observaciones'] ?? null
            ]);

            if (!$compraId) {
                throw new Exception('Error al crear la compra');
            }

            return [
                'success' => true,
                'compra_id' => $compraId,
                'numero_compra' => $numeroCompra
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Actualizar compra existente
     */
    public function actualizar($id, $datos) {
        try {
            $sql = "UPDATE compras
                    SET proveedor = :proveedor,
                        descripcion = :descripcion,
                        categoria_insumo = :categoria_insumo,
                        cantidad = :cantidad,
                        precio_unitario = :precio_unitario,
                        total = :total,
                        metodo_pago = :metodo_pago,
                        comprobante = :comprobante,
                        observaciones = :observaciones,
                        fecha_actualizacion = CURRENT_TIMESTAMP
                    WHERE id = :id";

            $resultado = $this->db->execute($sql, [
                'id' => $id,
                'proveedor' => $datos['proveedor'] ?? null,
                'descripcion' => $datos['descripcion'],
                'categoria_insumo' => $datos['categoria_insumo'] ?? 'otros',
                'cantidad' => $datos['cantidad'] ?? null,
                'precio_unitario' => $datos['precio_unitario'] ?? null,
                'total' => $datos['total'],
                'metodo_pago' => $datos['metodo_pago'],
                'comprobante' => $datos['comprobante'] ?? null,
                'observaciones' => $datos['observaciones'] ?? null
            ]);

            if (!$resultado) {
                throw new Exception('Error al actualizar la compra');
            }

            return ['success' => true];

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Eliminar compra (soft delete cambiando observaciones)
     */
    public function eliminar($id, $usuarioId) {
        try {
            $sql = "UPDATE compras
                    SET observaciones = CONCAT(COALESCE(observaciones, ''), '\nELIMINADA por usuario ID: {$usuarioId} en " . date('Y-m-d H:i:s') . "'),
                        fecha_actualizacion = CURRENT_TIMESTAMP
                    WHERE id = :id";

            $resultado = $this->db->execute($sql, ['id' => $id]);

            if (!$resultado) {
                throw new Exception('Error al eliminar la compra');
            }

            return ['success' => true];

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Generar número de compra único
     */
    private function generarNumeroCompra() {
        $fecha = date('Ymd');

        // Obtener el último número del día
        $sql = "SELECT numero_compra FROM compras
                WHERE numero_compra LIKE :patron
                ORDER BY numero_compra DESC
                LIMIT 1";

        $resultado = $this->db->fetch($sql, ['patron' => "COM-{$fecha}-%"]);

        if ($resultado) {
            // Extraer el número secuencial
            $partes = explode('-', $resultado['numero_compra']);
            $secuencial = intval($partes[2] ?? 0) + 1;
        } else {
            $secuencial = 1;
        }

        return "COM-{$fecha}-" . str_pad($secuencial, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Obtener estadísticas de compras
     */
    public function obtenerEstadisticas($dias = 30) {
        $sql = "SELECT
                    COUNT(*) as total_compras,
                    SUM(total) as monto_total_compras,
                    AVG(total) as ticket_promedio,
                    COUNT(CASE WHEN categoria_insumo = 'bolsas' THEN 1 END) as compras_bolsas,
                    COUNT(CASE WHEN categoria_insumo = 'etiquetas' THEN 1 END) as compras_etiquetas,
                    COUNT(CASE WHEN categoria_insumo = 'cajas' THEN 1 END) as compras_cajas,
                    COUNT(CASE WHEN categoria_insumo = 'embalaje' THEN 1 END) as compras_embalaje,
                    COUNT(CASE WHEN categoria_insumo = 'publicidad' THEN 1 END) as compras_publicidad,
                    COUNT(CASE WHEN categoria_insumo = 'otros' THEN 1 END) as compras_otros
                FROM compras
                WHERE fecha_compra >= DATE_SUB(NOW(), INTERVAL :dias DAY)";

        return $this->db->fetch($sql, ['dias' => $dias]);
    }

    /**
     * Obtener compras del día
     */
    public function obtenerComprasDelDia() {
        $sql = "SELECT c.*,
                       u.nombre as usuario_nombre
                FROM compras c
                INNER JOIN usuarios u ON c.usuario_id = u.id
                WHERE DATE(c.fecha_compra) = CURDATE()
                ORDER BY c.fecha_compra DESC";

        return $this->db->fetchAll($sql);
    }

    /**
     * Obtener total de compras para un período (para dashboard)
     */
    public function obtenerTotalComprasPeriodo($dias = 30) {
        $sql = "SELECT SUM(total) as total_compras
                FROM compras
                WHERE fecha_compra >= DATE_SUB(NOW(), INTERVAL :dias DAY)";

        $resultado = $this->db->fetch($sql, ['dias' => $dias]);
        return $resultado['total_compras'] ?? 0;
    }

    /**
     * Validar datos de compra
     */
    public function validar($datos) {
        $errores = [];

        // Validar descripción
        if (empty($datos['descripcion']) || trim($datos['descripcion']) === '') {
            $errores[] = 'La descripción es obligatoria';
        }

        // Validar total
        if (empty($datos['total']) || $datos['total'] <= 0) {
            $errores[] = 'El total debe ser mayor a 0';
        }

        // Validar método de pago
        $metodosValidos = ['transferencia', 'efectivo', 'tarjeta', 'credito'];
        if (empty($datos['metodo_pago']) || !in_array($datos['metodo_pago'], $metodosValidos)) {
            $errores[] = 'Método de pago inválido';
        }

        // Validar categoría
        $categoriasValidas = ['bolsas', 'etiquetas', 'cajas', 'embalaje', 'publicidad', 'otros'];
        if (!empty($datos['categoria_insumo']) && !in_array($datos['categoria_insumo'], $categoriasValidas)) {
            $errores[] = 'Categoría de insumo inválida';
        }

        // Si hay cantidad, validar precio unitario
        if (!empty($datos['cantidad']) && empty($datos['precio_unitario'])) {
            $errores[] = 'Si especifica cantidad, debe ingresar precio unitario';
        }

        return $errores;
    }
}
?>
