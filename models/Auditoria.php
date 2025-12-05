<?php
/**
 * MODELO DE AUDITORÍA
 * Maneja el registro de acciones importantes en el sistema.
 */

require_once 'config/database.php';

class Auditoria {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Registrar una acción en la tabla de auditoría.
     *
     * @param array $datos Los datos para el registro de auditoría.
     *   - usuario_id: (int) ID del usuario que realiza la acción.
     *   - accion: (string) La acción realizada (ej: 'creacion', 'eliminacion').
     *   - tipo_entidad: (string) El tipo de entidad afectada (ej: 'Compra', 'Producto').
     *   - entidad_id: (int) El ID de la entidad afectada.
     *   - detalles: (array) Datos adicionales para guardar en formato JSON.
     *
     * @return bool|int El ID del registro insertado o false en caso de error.
     */
    public function registrar($datos) {
        // Validar datos básicos
        if (empty($datos['usuario_id']) || empty($datos['accion']) || empty($datos['tipo_entidad']) || empty($datos['entidad_id'])) {
            error_log('Auditoria::registrar - Faltan datos obligatorios.');
            return false;
        }

        $sql = "INSERT INTO auditoria (usuario_id, accion, tipo_entidad, entidad_id, detalles)
                VALUES (:usuario_id, :accion, :tipo_entidad, :entidad_id, :detalles)";

        $parametros = [
            'usuario_id' => $datos['usuario_id'],
            'accion' => $datos['accion'],
            'tipo_entidad' => $datos['tipo_entidad'],
            'entidad_id' => $datos['entidad_id'],
            'detalles' => isset($datos['detalles']) ? json_encode($datos['detalles']) : null
        ];

        try {
            return $this->db->insert($sql, $parametros);
        } catch (Exception $e) {
            error_log("Error en Auditoria::registrar: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener registros de auditoría con filtros.
     *
     * @param array $filtros Filtros para la búsqueda.
     * @param int $limite Límite de registros.
     * @param int $offset Desplazamiento.
     * @return array
     */
    public function obtenerRegistros($filtros = [], $limite = 50, $offset = 0) {
        $where = ['1=1'];
        $params = [];

        if (!empty($filtros['usuario_id'])) {
            $where[] = 'a.usuario_id = :usuario_id';
            $params['usuario_id'] = $filtros['usuario_id'];
        }

        if (!empty($filtros['tipo_entidad'])) {
            $where[] = 'a.tipo_entidad = :tipo_entidad';
            $params['tipo_entidad'] = $filtros['tipo_entidad'];
        }
        
        if (!empty($filtros['entidad_id'])) {
            $where[] = 'a.entidad_id = :entidad_id';
            $params['entidad_id'] = $filtros['entidad_id'];
        }

        if (!empty($filtros['accion'])) {
            $where[] = 'a.accion = :accion';
            $params['accion'] = $filtros['accion'];
        }

        $whereClause = implode(' AND ', $where);
        $limite = (int)$limite;
        $offset = (int)$offset;

        $sql = "SELECT a.*, u.nombre as usuario_nombre, u.email as usuario_email
                FROM auditoria a
                LEFT JOIN usuarios u ON a.usuario_id = u.id
                WHERE {$whereClause}
                ORDER BY a.fecha DESC
                LIMIT {$limite} OFFSET {$offset}";

        return $this->db->fetchAll($sql, $params);
    }
}
?>
