<?php
/**
 * CONTROLADOR DE CLIENTES
 * Gestión completa de clientes
 */

require_once 'models/Cliente.php';

class ClienteController {
    private $cliente;

    public function __construct() {
        $this->cliente = new Cliente();
    }

    /**
     * Listar todos los clientes
     */
    public function index() {
        $filtros = [
            'busqueda' => $_GET['busqueda'] ?? '',
            'ciudad' => $_GET['ciudad'] ?? ''
        ];

        $clientes = $this->cliente->obtenerTodos($filtros);
        $ciudades = $this->cliente->obtenerCiudades();
        $estadisticas = $this->cliente->obtenerEstadisticas();

        $data = [
            'titulo' => 'Clientes - ' . APP_NAME,
            'clientes' => $clientes,
            'ciudades' => $ciudades,
            'filtros' => $filtros,
            'estadisticas' => $estadisticas
        ];

        $this->cargarVista('clientes/index', $data);
    }

    /**
     * Mostrar formulario de creación
     */
    public function crear() {
        $data = [
            'titulo' => 'Nuevo Cliente - ' . APP_NAME,
            'cliente' => [],
            'accion' => 'crear'
        ];

        $this->cargarVista('clientes/formulario', $data);
    }

    /**
     * Guardar nuevo cliente
     */
    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('clientes');
        }

        // Validar token CSRF
        if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
            redirect('clientes', 'Token de seguridad inválido', 'error');
        }

        $datos = $this->procesarDatos($_POST);

        // Validar datos
        $errores = $this->cliente->validar($datos);
        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $_SESSION['datos_antiguos'] = $datos;
            redirect('clientes/crear');
        }

        try {
            $this->cliente->crear($datos);
            redirect('clientes', 'Cliente creado exitosamente', 'success');
        } catch (Exception $e) {
            error_log("Error al crear cliente: " . $e->getMessage());
            $_SESSION['errores'] = ['Error al crear el cliente'];
            redirect('clientes/crear');
        }
    }

    /**
     * Mostrar formulario de edición
     */
    public function editar($id) {
        $cliente = $this->cliente->obtenerPorId($id);

        if (!$cliente) {
            redirect('clientes', 'Cliente no encontrado', 'error');
        }

        $data = [
            'titulo' => 'Editar Cliente - ' . APP_NAME,
            'cliente' => $cliente,
            'accion' => 'editar'
        ];

        $this->cargarVista('clientes/formulario', $data);
    }

    /**
     * Actualizar cliente existente
     */
    public function actualizar($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('clientes');
        }

        // Validar token CSRF
        if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
            redirect('clientes', 'Token de seguridad inválido', 'error');
        }

        $cliente = $this->cliente->obtenerPorId($id);
        if (!$cliente) {
            redirect('clientes', 'Cliente no encontrado', 'error');
        }

        $datos = $this->procesarDatos($_POST);

        // Validar datos
        $errores = $this->cliente->validar($datos, $id);
        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $_SESSION['datos_antiguos'] = $datos;
            redirect("clientes/editar/{$id}");
        }

        try {
            $this->cliente->actualizar($id, $datos);
            redirect('clientes', 'Cliente actualizado exitosamente', 'success');
        } catch (Exception $e) {
            error_log("Error al actualizar cliente: " . $e->getMessage());
            redirect("clientes/editar/{$id}", 'Error al actualizar el cliente', 'error');
        }
    }

    /**
     * Eliminar cliente (soft delete)
     */
    public function eliminar($id) {
        // Logging para debugging
        error_log("ClienteController::eliminar() - Iniciando eliminación del cliente ID: {$id}");
        error_log("Request Method: " . $_SERVER['REQUEST_METHOD']);
        error_log("CSRF Token presente: " . (isset($_POST['csrf_token']) ? 'SI' : 'NO'));

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            error_log("ClienteController::eliminar() - Método no permitido");
            redirect('clientes', 'Método no permitido', 'error');
            return;
        }

        // Validar token CSRF
        if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
            error_log("ClienteController::eliminar() - Token CSRF inválido");
            redirect('clientes', 'Token de seguridad inválido', 'error');
            return;
        }

        // Ejecutar eliminación
        $resultado = $this->cliente->eliminar($id);

        error_log("ClienteController::eliminar() - Resultado: " . json_encode($resultado));

        if ($resultado['success']) {
            redirect('clientes', $resultado['message'], 'success');
        } else {
            redirect('clientes', $resultado['message'], 'error');
        }
    }

    /**
     * Procesar y sanitizar datos del formulario
     */
    private function procesarDatos($datos) {
        return [
            'nombre' => sanitize($datos['nombre'] ?? ''),
            'telefono' => sanitize($datos['telefono'] ?? ''),
            'email' => sanitize($datos['email'] ?? ''),
            'direccion' => sanitize($datos['direccion'] ?? ''),
            'ciudad' => sanitize($datos['ciudad'] ?? 'Medellín')
        ];
    }

    /**
     * Cargar vista con layout
     */
    private function cargarVista($vista, $data = []) {
        $flashMessage = getFlashMessage();
        if ($flashMessage) {
            $data['flash'] = $flashMessage;
        }

        // Obtener errores de sesión si existen
        if (isset($_SESSION['errores'])) {
            $data['errores'] = $_SESSION['errores'];
            unset($_SESSION['errores']);
        }

        // Obtener datos antiguos si existen
        if (isset($_SESSION['datos_antiguos'])) {
            $data['datos_antiguos'] = $_SESSION['datos_antiguos'];
            unset($_SESSION['datos_antiguos']);
        }

        extract($data);

        ob_start();
        include "views/{$vista}.php";
        $contenido = ob_get_clean();

        include 'views/layouts/master.php';
    }
}
?>
