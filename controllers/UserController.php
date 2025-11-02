<?php
/**
 * CONTROLADOR DE USUARIOS
 * Gestión de usuarios (solo para administradores)
 */

require_once 'models/User.php';
require_once 'config/config.php';

class UserController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    /**
     * Listar todos los usuarios
     */
    public function index() {
        // Solo admin puede acceder
        requiereAdmin();

        $titulo = 'Gestión de Usuarios - ' . APP_NAME;
        $usuarios = $this->userModel->obtenerTodos(false); // Incluir inactivos
        $flash = getFlashMessage();

        // Cargar vista
        ob_start();
        include 'views/usuarios/index.php';
        $contenido = ob_get_clean();
        include 'views/layouts/master.php';
    }

    /**
     * Mostrar formulario de creación
     */
    public function crear() {
        // Solo admin puede acceder
        requiereAdmin();

        $titulo = 'Crear Usuario - ' . APP_NAME;
        $flash = getFlashMessage();

        // Cargar vista
        ob_start();
        include 'views/usuarios/formulario.php';
        $contenido = ob_get_clean();
        include 'views/layouts/master.php';
    }

    /**
     * Guardar nuevo usuario
     */
    public function guardar() {
        // Solo admin puede acceder
        requiereAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('usuarios', 'Método no permitido', 'error');
        }

        // Validar CSRF
        if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
            redirect('usuarios', 'Token de seguridad inválido', 'error');
        }

        // Obtener datos del formulario
        $datos = [
            'nombre' => sanitize($_POST['nombre'] ?? ''),
            'email' => sanitize($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'password_confirmacion' => $_POST['password_confirmacion'] ?? '',
            'rol' => sanitize($_POST['rol'] ?? 'vendedor')
        ];

        // Validar datos
        $errores = $this->userModel->validar($datos);

        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $_SESSION['datos_formulario'] = $datos;
            redirect('usuarios/crear', null, 'error');
        }

        // Crear usuario
        $nuevoId = $this->userModel->crear($datos);

        if ($nuevoId) {
            redirect('usuarios', 'Usuario creado exitosamente', 'success');
        } else {
            redirect('usuarios/crear', 'Error al crear el usuario', 'error');
        }
    }

    /**
     * Mostrar formulario de edición
     */
    public function editar($id) {
        // Solo admin puede acceder
        requiereAdmin();

        $usuario = $this->userModel->obtenerPorId($id);

        if (!$usuario) {
            redirect('usuarios', 'Usuario no encontrado', 'error');
        }

        $titulo = 'Editar Usuario - ' . APP_NAME;
        $flash = getFlashMessage();
        $errores = $_SESSION['errores'] ?? [];
        unset($_SESSION['errores']);

        // Cargar vista
        ob_start();
        include 'views/usuarios/formulario.php';
        $contenido = ob_get_clean();
        include 'views/layouts/master.php';
    }

    /**
     * Actualizar usuario existente
     */
    public function actualizar($id) {
        // Solo admin puede acceder
        requiereAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('usuarios', 'Método no permitido', 'error');
        }

        // Validar CSRF
        if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
            redirect('usuarios', 'Token de seguridad inválido', 'error');
        }

        // Verificar que el usuario existe
        $usuarioExistente = $this->userModel->obtenerPorId($id);
        if (!$usuarioExistente) {
            redirect('usuarios', 'Usuario no encontrado', 'error');
        }

        // Obtener datos del formulario
        $datos = [
            'nombre' => sanitize($_POST['nombre'] ?? ''),
            'email' => sanitize($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'password_confirmacion' => $_POST['password_confirmacion'] ?? '',
            'rol' => sanitize($_POST['rol'] ?? 'vendedor')
        ];

        // Validar datos
        $errores = $this->userModel->validar($datos, $id);

        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            redirect('usuarios/editar/' . $id, null, 'error');
        }

        // Actualizar usuario
        $resultado = $this->userModel->actualizar($id, $datos);

        if ($resultado) {
            redirect('usuarios', 'Usuario actualizado exitosamente', 'success');
        } else {
            redirect('usuarios/editar/' . $id, 'Error al actualizar el usuario', 'error');
        }
    }

    /**
     * Desactivar usuario (soft delete)
     */
    public function desactivar($id) {
        // Solo admin puede acceder
        requiereAdmin();

        // No permitir desactivar el propio usuario
        $usuarioActual = usuarioActual();
        if ($usuarioActual['id'] == $id) {
            redirect('usuarios', 'No puedes desactivar tu propio usuario', 'error');
        }

        $resultado = $this->userModel->desactivar($id);

        if ($resultado) {
            redirect('usuarios', 'Usuario desactivado exitosamente', 'success');
        } else {
            redirect('usuarios', 'Error al desactivar el usuario', 'error');
        }
    }

    /**
     * Activar usuario
     */
    public function activar($id) {
        // Solo admin puede acceder
        requiereAdmin();

        $resultado = $this->userModel->activar($id);

        if ($resultado) {
            redirect('usuarios', 'Usuario activado exitosamente', 'success');
        } else {
            redirect('usuarios', 'Error al activar el usuario', 'error');
        }
    }

    /**
     * Cambiar contraseña de usuario
     */
    public function cambiarPassword($id) {
        // Solo admin puede acceder
        requiereAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('usuarios', 'Método no permitido', 'error');
        }

        // Validar CSRF
        if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
            redirect('usuarios', 'Token de seguridad inválido', 'error');
        }

        $passwordNuevo = $_POST['password_nuevo'] ?? '';
        $passwordConfirmacion = $_POST['password_confirmacion'] ?? '';

        // Validar
        if (empty($passwordNuevo)) {
            redirect('usuarios/editar/' . $id, 'La contraseña es requerida', 'error');
        }

        if (strlen($passwordNuevo) < 6) {
            redirect('usuarios/editar/' . $id, 'La contraseña debe tener al menos 6 caracteres', 'error');
        }

        if ($passwordNuevo !== $passwordConfirmacion) {
            redirect('usuarios/editar/' . $id, 'Las contraseñas no coinciden', 'error');
        }

        // Cambiar password
        $resultado = $this->userModel->cambiarPassword($id, $passwordNuevo);

        if ($resultado) {
            redirect('usuarios', 'Contraseña actualizada exitosamente', 'success');
        } else {
            redirect('usuarios/editar/' . $id, 'Error al cambiar la contraseña', 'error');
        }
    }
}
?>
