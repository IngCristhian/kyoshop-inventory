<?php
/**
 * CONTROLADOR DE VARIANTES
 * Gestiona la agrupación de productos como variantes (tallas/colores)
 */

require_once 'models/Producto.php';
require_once 'models/ProductoVariante.php';

class VariantesController {
    private $producto;
    private $variante;

    public function __construct() {
        $this->producto = new Producto();
        $this->variante = new ProductoVariante();
    }

    /**
     * Página principal de gestión de variantes
     * Muestra productos agrupados y productos sin agrupar
     */
    public function index() {
        try {
            $productosAgrupados = $this->variante->obtenerProductosAgrupados();
            $productosSinAgrupar = $this->variante->obtenerProductosSinAgrupar();
            $estadisticas = $this->variante->obtenerEstadisticas();

            $data = [
                'titulo' => 'Gestión de Variantes - ' . APP_NAME,
                'productos_agrupados' => $productosAgrupados,
                'productos_sin_agrupar' => $productosSinAgrupar,
                'estadisticas' => $estadisticas
            ];

            $this->cargarVista('variantes/index', $data);
        } catch (Exception $e) {
            error_log("Error en VariantesController::index - " . $e->getMessage());
            error_log($e->getTraceAsString());

            // Mostrar error al usuario
            $data = [
                'titulo' => 'Error - ' . APP_NAME,
                'productos_agrupados' => [],
                'productos_sin_agrupar' => [],
                'estadisticas' => [
                    'total_productos' => 0,
                    'productos_con_variantes' => 0,
                    'total_variantes' => 0
                ],
                'error' => 'Error al cargar datos: ' . $e->getMessage()
            ];

            $this->cargarVista('variantes/index', $data);
        }
    }

    /**
     * Página de selección de productos para agrupar
     */
    public function seleccionar() {
        $busqueda = $_GET['busqueda'] ?? '';
        $productos = [];

        if (!empty($busqueda)) {
            // Buscar productos candidatos
            $productos = $this->variante->buscarCandidatosAgrupacion($busqueda);
        } else {
            // Mostrar todos los productos sin agrupar
            $productos = $this->variante->obtenerProductosSinAgrupar();
        }

        $data = [
            'titulo' => 'Seleccionar Productos para Agrupar - ' . APP_NAME,
            'productos' => $productos,
            'busqueda' => $busqueda
        ];

        $this->cargarVista('variantes/seleccionar', $data);
    }

    /**
     * Página de configuración de agrupación
     * Permite configurar el producto padre y opciones
     */
    public function configurar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('variantes/seleccionar');
        }

        // Validar token CSRF
        if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
            redirect('variantes/seleccionar', 'Token de seguridad inválido', 'error');
        }

        // Obtener IDs de productos seleccionados
        $productosSeleccionados = $_POST['productos'] ?? [];

        if (empty($productosSeleccionados) || count($productosSeleccionados) < 2) {
            redirect('variantes/seleccionar', 'Debes seleccionar al menos 2 productos para agrupar', 'error');
        }

        // Obtener datos de productos seleccionados
        $productos = [];
        foreach ($productosSeleccionados as $id) {
            $producto = $this->producto->obtenerPorId($id);
            if ($producto) {
                $productos[] = $producto;
            }
        }

        $data = [
            'titulo' => 'Configurar Agrupación - ' . APP_NAME,
            'productos' => $productos,
            'productos_ids' => $productosSeleccionados
        ];

        $this->cargarVista('variantes/configurar', $data);
    }

    /**
     * Ejecutar agrupación de productos
     */
    public function agrupar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('variantes');
        }

        // Validar token CSRF
        if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
            redirect('variantes', 'Token de seguridad inválido', 'error');
        }

        // Obtener datos del formulario
        $productoPadreId = (int)($_POST['producto_padre_id'] ?? 0);
        $variantesIds = $_POST['variantes_ids'] ?? [];
        $nombrePadre = $_POST['nombre_padre'] ?? '';
        $descripcionPadre = $_POST['descripcion_padre'] ?? '';

        // Validaciones
        if (empty($productoPadreId) || empty($variantesIds)) {
            redirect('variantes', 'Datos incompletos para la agrupación', 'error');
        }

        if (!is_array($variantesIds)) {
            redirect('variantes', 'Formato de datos inválido', 'error');
        }

        // Convertir IDs a enteros y remover el padre de las variantes
        $variantesIds = array_map('intval', $variantesIds);
        $variantesIds = array_filter($variantesIds, function($id) use ($productoPadreId) {
            return $id != $productoPadreId;
        });

        if (empty($variantesIds)) {
            redirect('variantes', 'Debes seleccionar al menos una variante además del producto padre', 'error');
        }

        // Actualizar nombre y descripción del producto padre si se proporcionaron
        if (!empty($nombrePadre) || !empty($descripcionPadre)) {
            $productoPadre = $this->producto->obtenerPorId($productoPadreId);
            $datosActualizar = $productoPadre;

            if (!empty($nombrePadre)) {
                $datosActualizar['nombre'] = $nombrePadre;
            }
            if (!empty($descripcionPadre)) {
                $datosActualizar['descripcion'] = $descripcionPadre;
            }

            $this->producto->actualizar($productoPadreId, $datosActualizar);
        }

        // Ejecutar agrupación
        $resultado = $this->variante->agruparProductos($productoPadreId, $variantesIds);

        if ($resultado) {
            redirect('variantes', 'Productos agrupados exitosamente', 'success');
        } else {
            redirect('variantes', 'Error al agrupar productos. Verifica que no existan referencias circulares.', 'error');
        }
    }

    /**
     * Desagrupar un producto (convertir variantes en productos independientes)
     */
    public function desagrupar($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('variantes');
        }

        // Validar token CSRF
        if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
            redirect('variantes', 'Token de seguridad inválido', 'error');
        }

        $resultado = $this->variante->desagruparProducto($id);

        if ($resultado) {
            redirect('variantes', 'Producto desagrupado exitosamente', 'success');
        } else {
            redirect('variantes', 'Error al desagrupar producto', 'error');
        }
    }

    /**
     * Ver detalles de un producto con sus variantes
     */
    public function ver($id) {
        $producto = $this->variante->obtenerProductoConVariantes($id);

        if (!$producto) {
            redirect('variantes', 'Producto no encontrado', 'error');
        }

        $data = [
            'titulo' => 'Detalles del Producto - ' . APP_NAME,
            'producto' => $producto
        ];

        $this->cargarVista('variantes/ver', $data);
    }

    /**
     * Eliminar una variante específica (convertirla en producto independiente)
     */
    public function eliminarVariante($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('variantes');
        }

        // Validar token CSRF
        if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
            redirect('variantes', 'Token de seguridad inválido', 'error');
        }

        $resultado = $this->variante->eliminarVariante($id);

        if ($resultado) {
            redirect($_SERVER['HTTP_REFERER'] ?? 'variantes', 'Variante eliminada exitosamente', 'success');
        } else {
            redirect($_SERVER['HTTP_REFERER'] ?? 'variantes', 'Error al eliminar variante', 'error');
        }
    }

    /**
     * Vista previa de agrupación (AJAX)
     */
    public function preview() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['error' => 'Método no permitido']);
            return;
        }

        $productoPadreId = (int)($_POST['producto_padre_id'] ?? 0);
        $variantesIds = $_POST['variantes_ids'] ?? [];

        if (empty($productoPadreId) || empty($variantesIds)) {
            echo json_encode(['error' => 'Datos incompletos']);
            return;
        }

        // Obtener producto padre
        $productoPadre = $this->producto->obtenerPorId($productoPadreId);

        // Obtener variantes
        $variantes = [];
        foreach ($variantesIds as $id) {
            if ($id != $productoPadreId) {
                $variante = $this->producto->obtenerPorId($id);
                if ($variante) {
                    $variantes[] = $variante;
                }
            }
        }

        // Calcular stock total
        $stockTotal = $productoPadre['stock'] ?? 0;
        foreach ($variantes as $variante) {
            $stockTotal += $variante['stock'] ?? 0;
        }

        // Preparar respuesta
        $response = [
            'producto_padre' => $productoPadre,
            'variantes' => $variantes,
            'stock_total' => $stockTotal,
            'total_variantes' => count($variantes) + 1 // +1 por el padre
        ];

        echo json_encode($response);
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

        extract($data);
        $contenido = 'views/' . $vista . '.php';

        if (!file_exists($contenido)) {
            die('Vista no encontrada: ' . $vista);
        }

        require_once 'views/layouts/master.php';
    }
}
