<?php
/**
 * CONTROLADOR DE HISTORIAL DE MOVIMIENTOS
 * Maneja la visualización del historial de inventario
 */

require_once 'models/HistorialMovimiento.php';
require_once 'models/Producto.php';
require_once 'models/User.php';

class HistorialController {
    private $historial;
    private $producto;

    public function __construct() {
        $this->historial = new HistorialMovimiento();
        $this->producto = new Producto();
    }

    /**
     * Ver historial completo con filtros
     */
    public function index() {
        // Obtener filtros de la URL
        $filtros = [
            'producto_id' => $_GET['producto_id'] ?? '',
            'tipo_movimiento' => $_GET['tipo_movimiento'] ?? '',
            'fecha_desde' => $_GET['fecha_desde'] ?? '',
            'fecha_hasta' => $_GET['fecha_hasta'] ?? ''
        ];

        // Paginación
        $limite = 20;
        $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
        $offset = ($pagina - 1) * $limite;

        // Obtener movimientos
        $movimientos = $this->historial->obtenerHistorial($filtros, $limite, $offset);
        $totalMovimientos = $this->historial->contarMovimientos($filtros);
        $totalPaginas = ceil($totalMovimientos / $limite);

        // Obtener productos para el filtro
        $productos = $this->producto->obtenerTodos();

        $data = [
            'titulo' => 'Historial de Movimientos - ' . APP_NAME,
            'movimientos' => $movimientos,
            'productos' => $productos,
            'filtros' => $filtros,
            'paginacion' => [
                'pagina_actual' => $pagina,
                'total_paginas' => $totalPaginas,
                'total_movimientos' => $totalMovimientos
            ]
        ];

        $this->cargarVista('historial/index', $data);
    }

    /**
     * Ver historial de un producto específico
     */
    public function producto($productoId) {
        $producto = $this->producto->obtenerPorId($productoId);

        if (!$producto) {
            redirect('historial', 'Producto no encontrado', 'error');
        }

        $movimientos = $this->historial->obtenerHistorialProducto($productoId);

        $data = [
            'titulo' => 'Historial de ' . $producto['nombre'] . ' - ' . APP_NAME,
            'producto' => $producto,
            'movimientos' => $movimientos
        ];

        $this->cargarVista('historial/producto', $data);
    }

    /**
     * Cargar vista con layout
     */
    private function cargarVista($vista, $data = []) {
        $flashMessage = getFlashMessage();
        if ($flashMessage) {
            $data['flash'] = $flashMessage;
        }

        extract($data);
        $titulo = $data['titulo'] ?? APP_NAME;

        ob_start();
        include "views/{$vista}.php";
        $contenido = ob_get_clean();

        include 'views/layouts/master.php';
    }
}
?>
