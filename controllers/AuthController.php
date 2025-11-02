<?php
/**
 * CONTROLADOR DE AUTENTICACIÓN
 * Manejo de login, logout y sesiones
 */

require_once 'models/User.php';
require_once 'config/config.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    /**
     * Mostrar formulario de login
     */
    public function login() {
        // Si ya está autenticado, redirigir al dashboard
        if ($this->estaAutenticado()) {
            redirect('dashboard');
        }

        $titulo = 'Iniciar Sesión - ' . APP_NAME;
        $errores = [];

        // Procesar formulario de login
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar CSRF
            if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
                $errores[] = 'Token de seguridad inválido';
            } else {
                $email = sanitize($_POST['email'] ?? '');
                $password = $_POST['password'] ?? '';

                // Validar campos
                if (empty($email)) {
                    $errores[] = 'El email es requerido';
                }
                if (empty($password)) {
                    $errores[] = 'La contraseña es requerida';
                }

                // Si no hay errores, intentar autenticar
                if (empty($errores)) {
                    $usuario = $this->userModel->autenticar($email, $password);

                    if ($usuario) {
                        // Guardar datos en sesión
                        $_SESSION['usuario_id'] = $usuario['id'];
                        $_SESSION['usuario_nombre'] = $usuario['nombre'];
                        $_SESSION['usuario_email'] = $usuario['email'];
                        $_SESSION['usuario_rol'] = $usuario['rol'];
                        $_SESSION['autenticado'] = true;

                        // Regenerar ID de sesión por seguridad
                        session_regenerate_id(true);

                        // Redirigir al dashboard
                        redirect('dashboard', 'Bienvenido ' . $usuario['nombre'], 'success');
                    } else {
                        $errores[] = 'Credenciales incorrectas';
                    }
                }
            }
        }

        // Cargar vista de login (sin layout)
        include 'views/auth/login.php';
    }

    /**
     * Procesar logout
     */
    public function logout() {
        // Registrar logout en log
        if (isset($_SESSION['usuario_id']) && isset($_SESSION['usuario_email'])) {
            $this->userModel->registrarLogout($_SESSION['usuario_id'], $_SESSION['usuario_email']);
        }

        // Destruir sesión
        $_SESSION = [];

        // Destruir cookie de sesión si existe
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        session_destroy();

        // Redirigir al login
        header('Location: ' . APP_URL . '/login');
        exit;
    }

    /**
     * Verificar si el usuario está autenticado
     */
    public function estaAutenticado() {
        return isset($_SESSION['autenticado']) && $_SESSION['autenticado'] === true;
    }

    /**
     * Obtener datos del usuario actual
     */
    public function usuarioActual() {
        if (!$this->estaAutenticado()) {
            return null;
        }

        return [
            'id' => $_SESSION['usuario_id'] ?? null,
            'nombre' => $_SESSION['usuario_nombre'] ?? null,
            'email' => $_SESSION['usuario_email'] ?? null,
            'rol' => $_SESSION['usuario_rol'] ?? null
        ];
    }

    /**
     * Verificar si el usuario tiene un rol específico
     */
    public function tieneRol($rol) {
        return $this->estaAutenticado() && isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === $rol;
    }

    /**
     * Verificar si el usuario es admin
     */
    public function esAdmin() {
        return $this->tieneRol('admin');
    }

    /**
     * Middleware: Requerir autenticación
     * Redirige al login si no está autenticado
     */
    public function requerirAuth() {
        if (!$this->estaAutenticado()) {
            $_SESSION['url_destino'] = $_SERVER['REQUEST_URI'];
            redirect('login', 'Debes iniciar sesión para acceder', 'error');
        }
    }

    /**
     * Middleware: Requerir rol admin
     * Redirige si no es admin
     */
    public function requerirAdmin() {
        $this->requerirAuth();

        if (!$this->esAdmin()) {
            redirect('dashboard', 'No tienes permisos para acceder a esta sección', 'error');
        }
    }

    /**
     * Mostrar formulario de registro (opcional)
     */
    public function registro() {
        // Si ya está autenticado, redirigir al dashboard
        if ($this->estaAutenticado()) {
            redirect('dashboard');
        }

        $titulo = 'Registro - ' . APP_NAME;
        $errores = [];

        // Procesar formulario de registro
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar CSRF
            if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
                $errores[] = 'Token de seguridad inválido';
            } else {
                $datos = [
                    'nombre' => sanitize($_POST['nombre'] ?? ''),
                    'email' => sanitize($_POST['email'] ?? ''),
                    'password' => $_POST['password'] ?? '',
                    'password_confirmacion' => $_POST['password_confirmacion'] ?? '',
                    'rol' => 'vendedor' // Por defecto
                ];

                // Validar datos
                $errores = $this->userModel->validar($datos);

                // Si no hay errores, crear usuario
                if (empty($errores)) {
                    $nuevoId = $this->userModel->crear($datos);

                    if ($nuevoId) {
                        redirect('login', 'Usuario registrado exitosamente. Ya puedes iniciar sesión', 'success');
                    } else {
                        $errores[] = 'Error al crear el usuario';
                    }
                }
            }
        }

        // Cargar vista de registro (sin layout)
        include 'views/auth/registro.php';
    }
}

/**
 * Función helper global para verificar autenticación
 */
function requiereAuth() {
    $auth = new AuthController();
    $auth->requerirAuth();
}

/**
 * Función helper global para verificar admin
 */
function requiereAdmin() {
    $auth = new AuthController();
    $auth->requerirAdmin();
}

/**
 * Función helper global para obtener usuario actual
 */
function usuarioActual() {
    $auth = new AuthController();
    return $auth->usuarioActual();
}
?>
