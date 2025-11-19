<?php
/**
 * CONTROLADOR DE VENTAS
 * Maneja toda la lógica de ventas con descuento automático de stock
 */

require_once 'models/Venta.php';
require_once 'models/Cliente.php';
require_once 'models/Producto.php';

class VentaController {
    private $venta;
    private $cliente;
    private $producto;

    public function __construct() {
        $this->venta = new Venta();
        $this->cliente = new Cliente();
        $this->producto = new Producto();
    }

    /**
     * Listar todas las ventas con filtros y paginación
     */
    public function index() {
        $pagina = $_GET['pagina'] ?? 1;
        $filtros = [
            'metodo_pago' => $_GET['metodo_pago'] ?? '',
            'estado_pago' => $_GET['estado_pago'] ?? '',
            'fecha_desde' => $_GET['fecha_desde'] ?? '',
            'fecha_hasta' => $_GET['fecha_hasta'] ?? '',
            'busqueda' => $_GET['busqueda'] ?? ''
        ];

        $ventas = $this->venta->obtenerTodos($pagina, ITEMS_PER_PAGE, $filtros);
        $totalVentas = $this->venta->contarTotal($filtros);
        $totalPaginas = ceil($totalVentas / ITEMS_PER_PAGE);
        $estadisticas = $this->venta->obtenerEstadisticas(30);

        $data = [
            'titulo' => 'Ventas - ' . APP_NAME,
            'ventas' => $ventas,
            'filtros' => $filtros,
            'estadisticas' => $estadisticas,
            'paginacion' => [
                'pagina_actual' => $pagina,
                'total_paginas' => $totalPaginas,
                'total_ventas' => $totalVentas
            ]
        ];

        $this->cargarVista('ventas/index', $data);
    }

    /**
     * Mostrar formulario de creación de venta
     */
    public function crear() {
        $data = [
            'titulo' => 'Nueva Venta - ' . APP_NAME
        ];

        $this->cargarVista('ventas/formulario', $data);
    }

    /**
     * Guardar nueva venta
     */
    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('ventas');
        }

        // Validar token CSRF
        if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
            redirect('ventas', 'Token de seguridad inválido', 'error');
        }

        // Procesar datos
        $datos = [
            'cliente_id' => intval($_POST['cliente_id'] ?? 0),
            'usuario_id' => $_SESSION['usuario_id'],
            'metodo_pago' => sanitize($_POST['metodo_pago'] ?? ''),
            'estado_pago' => sanitize($_POST['estado_pago'] ?? 'pendiente'),
            'observaciones' => sanitize($_POST['observaciones'] ?? '')
        ];

        // Procesar items de la venta
        $items = [];
        if (!empty($_POST['items']) && is_array($_POST['items'])) {
            foreach ($_POST['items'] as $item) {
                if (!empty($item['producto_id']) && !empty($item['cantidad'])) {
                    $items[] = [
                        'producto_id' => intval($item['producto_id']),
                        'cantidad' => intval($item['cantidad']),
                        'precio_unitario' => floatval($item['precio_unitario'])
                    ];
                }
            }
        }

        // Validar datos
        $errores = $this->venta->validar($datos, $items);
        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $_SESSION['datos_antiguos'] = $datos;
            redirect('ventas/crear');
        }

        try {
            // Crear venta
            $resultado = $this->venta->crear($datos, $items);

            if ($resultado['success']) {
                redirect(
                    'ventas/ver/' . $resultado['venta_id'],
                    "Venta #{$resultado['numero_venta']} creada exitosamente",
                    'success'
                );
            } else {
                $_SESSION['errores'] = [$resultado['error']];
                $_SESSION['datos_antiguos'] = $datos;
                redirect('ventas/crear');
            }
        } catch (Exception $e) {
            error_log("Error al crear venta: " . $e->getMessage());
            $_SESSION['errores'] = ['Error al crear la venta: ' . $e->getMessage()];
            redirect('ventas/crear');
        }
    }

    /**
     * Ver detalles de una venta
     */
    public function ver($id) {
        $venta = $this->venta->obtenerPorId($id);

        if (!$venta) {
            redirect('ventas', 'Venta no encontrada', 'error');
        }

        $data = [
            'titulo' => "Venta #{$venta['numero_venta']} - " . APP_NAME,
            'venta' => $venta
        ];

        $this->cargarVista('ventas/detalle', $data);
    }

    /**
     * Actualizar estado de pago
     */
    public function actualizarEstadoPago($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('ventas');
        }

        // Validar token CSRF
        if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
            redirect('ventas', 'Token de seguridad inválido', 'error');
        }

        $estadoPago = sanitize($_POST['estado_pago'] ?? '');

        try {
            $this->venta->actualizarEstadoPago($id, $estadoPago);
            redirect('ventas/ver/' . $id, 'Estado de pago actualizado correctamente', 'success');
        } catch (Exception $e) {
            error_log("Error al actualizar estado de pago: " . $e->getMessage());
            redirect('ventas/ver/' . $id, 'Error al actualizar el estado de pago', 'error');
        }
    }

    /**
     * Cancelar venta y devolver stock
     */
    public function cancelar($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('ventas');
        }

        // Validar token CSRF
        if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
            redirect('ventas', 'Token de seguridad inválido', 'error');
        }

        $motivo = sanitize($_POST['motivo'] ?? '');
        $usuarioId = $_SESSION['usuario_id'];

        try {
            $resultado = $this->venta->cancelar($id, $usuarioId, $motivo);

            if ($resultado['success']) {
                redirect('ventas/ver/' . $id, 'Venta cancelada y stock devuelto correctamente', 'success');
            } else {
                redirect('ventas/ver/' . $id, 'Error: ' . $resultado['error'], 'error');
            }
        } catch (Exception $e) {
            error_log("Error al cancelar venta: " . $e->getMessage());
            redirect('ventas/ver/' . $id, 'Error al cancelar la venta', 'error');
        }
    }

    /**
     * Búsqueda AJAX de clientes
     */
    public function buscarCliente() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }

        $termino = $_POST['termino'] ?? '';

        if (empty($termino)) {
            $this->enviarJSON(['clientes' => []]);
            return;
        }

        $clientes = $this->cliente->buscar($termino, 10);
        $this->enviarJSON(['clientes' => $clientes]);
    }

    /**
     * Búsqueda AJAX de productos con stock
     */
    public function buscarProducto() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }

        $termino = $_POST['termino'] ?? '';

        if (empty($termino)) {
            $this->enviarJSON(['productos' => []]);
            return;
        }

        // Buscar productos que tengan stock disponible
        $productos = $this->producto->buscar($termino, 20);

        // Filtrar solo productos con stock > 0
        $productosConStock = array_filter($productos, function($producto) {
            return $producto['stock'] > 0;
        });

        $this->enviarJSON(['productos' => array_values($productosConStock)]);
    }

    /**
     * Crear nuevo cliente desde modal (AJAX)
     */
    public function crearCliente() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }

        $datos = [
            'nombre' => sanitize($_POST['nombre'] ?? ''),
            'telefono' => sanitize($_POST['telefono'] ?? ''),
            'email' => sanitize($_POST['email'] ?? ''),
            'direccion' => sanitize($_POST['direccion'] ?? ''),
            'ciudad' => sanitize($_POST['ciudad'] ?? 'Medellín')
        ];

        // Validar datos
        $errores = $this->cliente->validar($datos);

        if (!empty($errores)) {
            $this->enviarJSON([
                'success' => false,
                'errores' => $errores
            ]);
        }

        try {
            $clienteId = $this->cliente->crear($datos);
            $cliente = $this->cliente->obtenerPorId($clienteId);

            $this->enviarJSON([
                'success' => true,
                'cliente' => $cliente
            ]);
        } catch (Exception $e) {
            $this->enviarJSON([
                'success' => false,
                'errores' => ['Error al crear el cliente: ' . $e->getMessage()]
            ]);
        }
    }

    /**
     * Generar factura PDF (placeholder para implementación futura)
     */
    public function factura($id) {
        $venta = $this->venta->obtenerPorId($id);

        if (!$venta) {
            redirect('ventas', 'Venta no encontrada', 'error');
        }

        // TODO: Implementar generación de PDF con librería TCPDF o similar
        $data = [
            'titulo' => "Factura #{$venta['numero_venta']}",
            'venta' => $venta
        ];

        $this->cargarVista('ventas/factura', $data);
    }

    /**
     * Dashboard de ventas (estadísticas)
     */
    public function dashboard() {
        $estadisticas = $this->venta->obtenerEstadisticas(30);
        $ventasDelDia = $this->venta->obtenerVentasDelDia();
        $ventasRecientes = $this->venta->obtenerTodos(1, 5);

        $data = [
            'titulo' => 'Dashboard de Ventas - ' . APP_NAME,
            'estadisticas' => $estadisticas,
            'ventas_del_dia' => $ventasDelDia,
            'ventas_recientes' => $ventasRecientes
        ];

        $this->cargarVista('ventas/dashboard', $data);
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

    /**
     * Enviar respuesta JSON
     */
    private function enviarJSON($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
?>
