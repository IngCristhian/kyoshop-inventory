<?php
/**
 * MODELO CLIENTE
 * Manejo de datos de clientes en la base de datos
 */

require_once 'config/database.php';

class Cliente {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Obtener todos los clientes activos
     */
    public function obtenerTodos($filtros = []) {
        $condiciones = ['activo = 1'];
        $parametros = [];

        // Filtro por búsqueda
        if (!empty($filtros['busqueda'])) {
            $condiciones[] = '(nombre LIKE :busqueda1 OR telefono LIKE :busqueda2 OR email LIKE :busqueda3)';
            $parametros['busqueda1'] = '%' . $filtros['busqueda'] . '%';
            $parametros['busqueda2'] = '%' . $filtros['busqueda'] . '%';
            $parametros['busqueda3'] = '%' . $filtros['busqueda'] . '%';
        }

        // Filtro por ciudad
        if (!empty($filtros['ciudad'])) {
            $condiciones[] = 'ciudad = :ciudad';
            $parametros['ciudad'] = $filtros['ciudad'];
        }

        $where = implode(' AND ', $condiciones);

        $sql = "SELECT * FROM clientes
                WHERE {$where}
                ORDER BY nombre ASC";

        // Aplicar límite si se especifica
        if (!empty($filtros['limite'])) {
            $sql .= " LIMIT " . (int)$filtros['limite'];
        }

        return $this->db->fetchAll($sql, $parametros);
    }

    /**
     * Obtener cliente por ID
     */
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM clientes WHERE id = :id AND activo = 1";
        return $this->db->fetch($sql, ['id' => $id]);
    }

    /**
     * Obtener cliente por teléfono
     */
    public function obtenerPorTelefono($telefono) {
        $sql = "SELECT * FROM clientes WHERE telefono = :telefono AND activo = 1";
        return $this->db->fetch($sql, ['telefono' => $telefono]);
    }

    /**
     * Buscar clientes (para autocompletar)
     */
    public function buscar($termino, $limite = 10) {
        $limite = (int)$limite;

        $sql = "SELECT * FROM clientes
                WHERE activo = 1
                AND (nombre LIKE :termino1
                     OR telefono LIKE :termino2
                     OR email LIKE :termino3)
                ORDER BY nombre ASC
                LIMIT {$limite}";

        return $this->db->fetchAll($sql, [
            'termino1' => '%' . $termino . '%',
            'termino2' => '%' . $termino . '%',
            'termino3' => '%' . $termino . '%'
        ]);
    }

    /**
     * Crear nuevo cliente
     */
    public function crear($datos) {
        $sql = "INSERT INTO clientes (
                    nombre, telefono, email, direccion, ciudad
                ) VALUES (
                    :nombre, :telefono, :email, :direccion, :ciudad
                )";

        return $this->db->insert($sql, [
            'nombre' => $datos['nombre'],
            'telefono' => $datos['telefono'],
            'email' => $datos['email'] ?? null,
            'direccion' => $datos['direccion'] ?? null,
            'ciudad' => $datos['ciudad'] ?? 'Medellín'
        ]);
    }

    /**
     * Actualizar cliente existente
     */
    public function actualizar($id, $datos) {
        $sql = "UPDATE clientes SET
                    nombre = :nombre,
                    telefono = :telefono,
                    email = :email,
                    direccion = :direccion,
                    ciudad = :ciudad,
                    fecha_actualizacion = CURRENT_TIMESTAMP
                WHERE id = :id";

        return $this->db->execute($sql, [
            'id' => $id,
            'nombre' => $datos['nombre'],
            'telefono' => $datos['telefono'],
            'email' => $datos['email'] ?? null,
            'direccion' => $datos['direccion'] ?? null,
            'ciudad' => $datos['ciudad'] ?? 'Medellín'
        ]);
    }

    /**
     * Eliminar cliente (soft delete)
     */
    public function eliminar($id) {
        $sql = "UPDATE clientes
                SET activo = 0, fecha_actualizacion = CURRENT_TIMESTAMP
                WHERE id = :id AND activo = 1";
        return $this->db->execute($sql, ['id' => $id]);
    }

    /**
     * Obtener estadísticas de clientes
     */
    public function obtenerEstadisticas() {
        $sql = "SELECT
                    COUNT(*) as total_clientes,
                    COUNT(DISTINCT ciudad) as total_ciudades,
                    COUNT(CASE WHEN email IS NOT NULL THEN 1 END) as clientes_con_email
                FROM clientes
                WHERE activo = 1";
        return $this->db->fetch($sql);
    }

    /**
     * Validar datos del cliente
     */
    public function validar($datos, $id = null) {
        $errores = [];

        // Validar nombre
        if (empty($datos['nombre'])) {
            $errores[] = 'El nombre es requerido';
        } elseif (strlen($datos['nombre']) > 255) {
            $errores[] = 'El nombre no puede exceder 255 caracteres';
        }

        // Validar teléfono
        if (empty($datos['telefono'])) {
            $errores[] = 'El teléfono es requerido';
        } elseif (!preg_match('/^[0-9]{7,20}$/', $datos['telefono'])) {
            $errores[] = 'El teléfono debe contener entre 7 y 20 dígitos';
        } else {
            // Validar que el teléfono no esté duplicado
            $existente = $this->obtenerPorTelefono($datos['telefono']);
            if ($existente && $existente['id'] != $id) {
                $errores[] = 'El teléfono ya está registrado';
            }
        }

        // Validar email si se proporciona
        if (!empty($datos['email']) && !filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El email no tiene un formato válido';
        }

        return $errores;
    }

    /**
     * Obtener ciudades únicas
     */
    public function obtenerCiudades() {
        $sql = "SELECT DISTINCT ciudad FROM clientes WHERE activo = 1 ORDER BY ciudad";
        $resultado = $this->db->fetchAll($sql);
        return array_column($resultado, 'ciudad');
    }
}
?>
