<?php
/**
 * MODELO USER
 * Manejo de autenticación y datos de usuarios
 */

require_once 'config/database.php';

class User {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Autenticar usuario por email y password
     * @param string $email
     * @param string $password
     * @return array|false Retorna datos del usuario o false si falla
     */
    public function autenticar($email, $password) {
        $sql = "SELECT * FROM usuarios WHERE email = :email AND activo = 1";
        $usuario = $this->db->fetch($sql, ['email' => $email]);

        if (!$usuario) {
            return false;
        }

        // Verificar password con hash
        if (password_verify($password, $usuario['password'])) {
            // Actualizar último acceso
            $this->actualizarUltimoAcceso($usuario['id']);

            // Registrar login exitoso
            $this->registrarAcceso($usuario['id'], $email, 'login_exitoso');

            // No retornar el password
            unset($usuario['password']);
            return $usuario;
        }

        // Registrar login fallido
        $this->registrarAcceso(null, $email, 'login_fallido');
        return false;
    }

    /**
     * Obtener usuario por ID
     */
    public function obtenerPorId($id) {
        $sql = "SELECT id, nombre, email, rol, activo, ultimo_acceso, fecha_creacion
                FROM usuarios WHERE id = :id AND activo = 1";
        return $this->db->fetch($sql, ['id' => $id]);
    }

    /**
     * Obtener usuario por email
     */
    public function obtenerPorEmail($email) {
        $sql = "SELECT id, nombre, email, rol, activo, ultimo_acceso, fecha_creacion
                FROM usuarios WHERE email = :email";
        return $this->db->fetch($sql, ['email' => $email]);
    }

    /**
     * Crear nuevo usuario
     */
    public function crear($datos) {
        // Hashear password
        $passwordHash = password_hash($datos['password'], PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuarios (nombre, email, password, rol)
                VALUES (:nombre, :email, :password, :rol)";

        return $this->db->insert($sql, [
            'nombre' => $datos['nombre'],
            'email' => $datos['email'],
            'password' => $passwordHash,
            'rol' => $datos['rol'] ?? 'vendedor'
        ]);
    }

    /**
     * Actualizar usuario
     */
    public function actualizar($id, $datos) {
        $campos = [
            'nombre = :nombre',
            'email = :email',
            'rol = :rol',
            'fecha_actualizacion = CURRENT_TIMESTAMP'
        ];

        $parametros = [
            'id' => $id,
            'nombre' => $datos['nombre'],
            'email' => $datos['email'],
            'rol' => $datos['rol']
        ];

        // Solo actualizar password si se proporciona uno nuevo
        if (!empty($datos['password'])) {
            $campos[] = 'password = :password';
            $parametros['password'] = password_hash($datos['password'], PASSWORD_DEFAULT);
        }

        $sql = "UPDATE usuarios SET " . implode(', ', $campos) . " WHERE id = :id";

        return $this->db->execute($sql, $parametros);
    }

    /**
     * Desactivar usuario (soft delete)
     */
    public function desactivar($id) {
        $sql = "UPDATE usuarios
                SET activo = 0, fecha_actualizacion = CURRENT_TIMESTAMP
                WHERE id = :id";
        return $this->db->execute($sql, ['id' => $id]);
    }

    /**
     * Activar usuario
     */
    public function activar($id) {
        $sql = "UPDATE usuarios
                SET activo = 1, fecha_actualizacion = CURRENT_TIMESTAMP
                WHERE id = :id";
        return $this->db->execute($sql, ['id' => $id]);
    }

    /**
     * Cambiar password de usuario
     */
    public function cambiarPassword($id, $passwordNuevo) {
        $passwordHash = password_hash($passwordNuevo, PASSWORD_DEFAULT);

        $sql = "UPDATE usuarios
                SET password = :password, fecha_actualizacion = CURRENT_TIMESTAMP
                WHERE id = :id";

        return $this->db->execute($sql, [
            'id' => $id,
            'password' => $passwordHash
        ]);
    }

    /**
     * Actualizar último acceso del usuario
     */
    private function actualizarUltimoAcceso($id) {
        $sql = "UPDATE usuarios SET ultimo_acceso = CURRENT_TIMESTAMP WHERE id = :id";
        $this->db->execute($sql, ['id' => $id]);
    }

    /**
     * Registrar acceso en log
     */
    private function registrarAcceso($usuarioId, $email, $accion) {
        $sql = "INSERT INTO log_accesos (usuario_id, email, accion, ip_address, user_agent)
                VALUES (:usuario_id, :email, :accion, :ip_address, :user_agent)";

        $this->db->execute($sql, [
            'usuario_id' => $usuarioId,
            'email' => $email,
            'accion' => $accion,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    }

    /**
     * Registrar logout en log
     */
    public function registrarLogout($usuarioId, $email) {
        $this->registrarAcceso($usuarioId, $email, 'logout');
    }

    /**
     * Obtener todos los usuarios
     */
    public function obtenerTodos($soloActivos = true) {
        $where = $soloActivos ? 'WHERE activo = 1' : '';
        $sql = "SELECT id, nombre, email, rol, activo, ultimo_acceso, fecha_creacion
                FROM usuarios {$where} ORDER BY nombre ASC";
        return $this->db->fetchAll($sql);
    }

    /**
     * Validar datos del usuario
     */
    public function validar($datos, $id = null) {
        $errores = [];

        // Validar nombre
        if (empty($datos['nombre'])) {
            $errores[] = 'El nombre es requerido';
        } elseif (strlen($datos['nombre']) > 100) {
            $errores[] = 'El nombre no puede exceder 100 caracteres';
        }

        // Validar email
        if (empty($datos['email'])) {
            $errores[] = 'El email es requerido';
        } elseif (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El email no es válido';
        } else {
            // Verificar email único
            $existente = $this->obtenerPorEmail($datos['email']);
            if ($existente && $existente['id'] != $id) {
                $errores[] = 'El email ya está registrado';
            }
        }

        // Validar password (solo si es nuevo usuario o se está cambiando)
        if (!$id || !empty($datos['password'])) {
            if (empty($datos['password'])) {
                $errores[] = 'El password es requerido';
            } elseif (strlen($datos['password']) < 6) {
                $errores[] = 'El password debe tener al menos 6 caracteres';
            }

            // Validar confirmación de password
            if (isset($datos['password_confirmacion']) && $datos['password'] !== $datos['password_confirmacion']) {
                $errores[] = 'Las contraseñas no coinciden';
            }
        }

        // Validar rol
        if (!empty($datos['rol']) && !in_array($datos['rol'], ['admin', 'vendedor', 'supervisor'])) {
            $errores[] = 'El rol seleccionado no es válido';
        }

        return $errores;
    }

    /**
     * Verificar si un usuario tiene un rol específico
     */
    public function tieneRol($usuarioId, $rol) {
        $usuario = $this->obtenerPorId($usuarioId);
        return $usuario && $usuario['rol'] === $rol;
    }

    /**
     * Verificar si un usuario es admin
     */
    public function esAdmin($usuarioId) {
        return $this->tieneRol($usuarioId, 'admin');
    }
}
?>
