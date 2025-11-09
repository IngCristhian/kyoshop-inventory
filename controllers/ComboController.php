<?php
/**
 * CONTROLADOR DE COMBOS
 * Maneja la lógica de creación y gestión de combos
 */

require_once 'models/Combo.php';
require_once 'models/Producto.php';

class ComboController {
    private $combo;
    private $producto;

    public function __construct() {
        $this->combo = new Combo();
        $this->producto = new Producto();
    }

    /**
     * Listar todos los combos
     */
    public function index() {
        $filtros = [
            'tipo' => $_GET['tipo'] ?? '',
            'ubicacion' => $_GET['ubicacion'] ?? ''
        ];

        $combos = $this->combo->obtenerTodos($filtros);
        $estadisticas = $this->combo->obtenerEstadisticas();

        $data = [
            'titulo' => 'Combos - ' . APP_NAME,
            'combos' => $combos,
            'estadisticas' => $estadisticas,
            'filtros' => $filtros
        ];

        $this->cargarVista('combos/index', $data);
    }

    /**
     * Mostrar formulario de creación
     */
    public function crear() {
        $data = [
            'titulo' => 'Crear Combo - ' . APP_NAME,
            'combo' => [],
            'accion' => 'crear',
            'tipos_combo' => Combo::TIPOS
        ];

        $this->cargarVista('combos/formulario', $data);
    }

    /**
     * Guardar nuevo combo
     */
    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('combos');
        }

        // Validar token CSRF
        if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
            redirect('combos', 'Token de seguridad inválido', 'error');
        }

        $datos = $this->procesarDatos($_POST);

        // Validar datos básicos
        $errores = $this->validar($datos);
        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $_SESSION['datos_antiguos'] = $_POST;
            redirect('combos/crear');
        }

        // Procesar tipos
        $tipos = $this->procesarTipos($_POST);

        // Validar que la suma de tipos coincida con el total
        $sumaTipos = array_sum($tipos);
        if ($sumaTipos !== (int)$datos['cantidad_total']) {
            $_SESSION['errores'] = ["La suma de tipos ({$sumaTipos}) no coincide con el total del combo ({$datos['cantidad_total']})"];
            $_SESSION['datos_antiguos'] = $_POST;
            redirect('combos/crear');
        }

        // Crear combo
        $resultado = $this->combo->crear($datos, $tipos);

        if ($resultado['success']) {
            $mensaje = 'Combo creado exitosamente';

            // Agregar advertencias si las hay
            if (!empty($resultado['advertencias'])) {
                $advertencias = array_map(function($adv) {
                    return $adv['mensaje'];
                }, $resultado['advertencias']);
                $mensaje .= '. Advertencias: ' . implode(', ', $advertencias);
            }

            redirect('combos/ver/' . $resultado['combo_id'], $mensaje, 'success');
        } else {
            $error = $resultado['error'];

            // Mostrar detalles de faltantes si existen
            if (!empty($resultado['faltantes'])) {
                $error .= ': ';
                foreach ($resultado['faltantes'] as $faltante) {
                    $error .= "{$faltante['tipo']} (necesita {$faltante['necesita']}, disponible {$faltante['disponible']} en {$faltante['ubicacion']}), ";
                }
                $error = rtrim($error, ', ');
            }

            $_SESSION['errores'] = [$error];
            $_SESSION['datos_antiguos'] = $_POST;
            redirect('combos/crear');
        }
    }

    /**
     * Ver detalles de un combo
     */
    public function ver($id) {
        $combo = $this->combo->obtenerPorId($id);

        if (!$combo) {
            redirect('combos', 'Combo no encontrado', 'error');
        }

        $data = [
            'titulo' => 'Combo: ' . $combo['nombre'] . ' - ' . APP_NAME,
            'combo' => $combo
        ];

        $this->cargarVista('combos/detalle', $data);
    }

    /**
     * Mostrar formulario de edición
     */
    public function editar($id) {
        $combo = $this->combo->obtenerPorId($id);

        if (!$combo) {
            redirect('combos', 'Combo no encontrado', 'error');
        }

        $data = [
            'titulo' => 'Editar Combo - ' . APP_NAME,
            'combo' => $combo,
            'accion' => 'editar'
        ];

        $this->cargarVista('combos/editar', $data);
    }

    /**
     * Actualizar combo existente
     */
    public function actualizar($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('combos');
        }

        // Validar token CSRF
        if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
            redirect('combos', 'Token de seguridad inválido', 'error');
        }

        $datos = [
            'nombre' => sanitize($_POST['nombre'] ?? ''),
            'precio' => floatval($_POST['precio'] ?? 0)
        ];

        // Validar
        if (empty($datos['nombre'])) {
            redirect('combos/editar/' . $id, 'El nombre es requerido', 'error');
        }

        if ($datos['precio'] <= 0) {
            redirect('combos/editar/' . $id, 'El precio debe ser mayor a 0', 'error');
        }

        if ($this->combo->actualizar($id, $datos)) {
            redirect('combos/ver/' . $id, 'Combo actualizado exitosamente', 'success');
        } else {
            redirect('combos/editar/' . $id, 'Error al actualizar combo', 'error');
        }
    }

    /**
     * Eliminar combo
     */
    public function eliminar($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('combos');
        }

        if ($this->combo->eliminar($id)) {
            redirect('combos', 'Combo eliminado exitosamente', 'success');
        } else {
            redirect('combos', 'Error al eliminar combo', 'error');
        }
    }

    /**
     * Procesar y sanitizar datos del formulario
     */
    private function procesarDatos($datos) {
        return [
            'nombre' => sanitize($datos['nombre'] ?? ''),
            'tipo' => sanitize($datos['tipo'] ?? ''),
            'cantidad_total' => intval($datos['cantidad_total'] ?? 0),
            'precio' => floatval($datos['precio'] ?? 0),
            'ubicacion' => sanitize($datos['ubicacion'] ?? 'Mixto')
        ];
    }

    /**
     * Procesar tipos del formulario
     */
    private function procesarTipos($datos) {
        $tipos = [];

        if (!empty($datos['tipos']) && is_array($datos['tipos'])) {
            foreach ($datos['tipos'] as $tipo) {
                if (!empty($tipo['nombre']) && !empty($tipo['cantidad'])) {
                    $nombre = sanitize($tipo['nombre']);
                    $cantidad = intval($tipo['cantidad']);

                    if ($cantidad > 0) {
                        $tipos[$nombre] = $cantidad;
                    }
                }
            }
        }

        return $tipos;
    }

    /**
     * Validar datos del combo
     */
    private function validar($datos) {
        $errores = [];

        if (empty($datos['nombre'])) {
            $errores[] = 'El nombre es requerido';
        }

        if (empty($datos['tipo']) || !array_key_exists($datos['tipo'], Combo::TIPOS)) {
            $errores[] = 'El tipo de combo no es válido';
        }

        if ($datos['cantidad_total'] <= 0) {
            $errores[] = 'La cantidad total debe ser mayor a 0';
        }

        if ($datos['precio'] <= 0) {
            $errores[] = 'El precio debe ser mayor a 0';
        }

        if (!in_array($datos['ubicacion'], ['Medellín', 'Bogotá', 'Mixto'])) {
            $errores[] = 'La ubicación no es válida';
        }

        return $errores;
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
