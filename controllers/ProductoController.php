<?php
/**
 * CONTROLADOR DE PRODUCTOS
 * Maneja toda la lógica CRUD de productos
 */

require_once 'models/Producto.php';
require_once 'models/HistorialMovimiento.php';

class ProductoController {
    private $producto;
    private $historial;

    public function __construct() {
        $this->producto = new Producto();
        $this->historial = new HistorialMovimiento();
    }
    
    /**
     * Dashboard principal con estadísticas
     */
    public function dashboard() {
        $estadisticas = $this->producto->obtenerEstadisticas();
        $productosStockBajo = $this->producto->obtenerStockBajo();
        $productosRecientes = $this->producto->obtenerTodos(1, 5);
        
        $data = [
            'titulo' => 'Dashboard - ' . APP_NAME,
            'estadisticas' => $estadisticas,
            'productos_stock_bajo' => $productosStockBajo,
            'productos_recientes' => $productosRecientes
        ];
        
        $this->cargarVista('dashboard', $data);
    }
    
    /**
     * Listar todos los productos con filtros y paginación
     */
    public function index() {
        $pagina = $_GET['pagina'] ?? 1;
        $filtros = [
            'categoria' => $_GET['categoria'] ?? '',
            'tipo' => $_GET['tipo'] ?? '',
            'ubicacion' => $_GET['ubicacion'] ?? '',
            'busqueda' => $_GET['busqueda'] ?? '',
            'stock_bajo' => isset($_GET['stock_bajo'])
        ];
        
        $productos = $this->producto->obtenerTodos($pagina, ITEMS_PER_PAGE, $filtros);
        $totalProductos = $this->producto->contarTotal($filtros);
        $totalPaginas = ceil($totalProductos / ITEMS_PER_PAGE);
        $categorias = $this->producto->obtenerCategorias();
        
        $data = [
            'titulo' => 'Productos - ' . APP_NAME,
            'productos' => $productos,
            'categorias' => $categorias,
            'filtros' => $filtros,
            'paginacion' => [
                'pagina_actual' => $pagina,
                'total_paginas' => $totalPaginas,
                'total_productos' => $totalProductos
            ]
        ];
        
        $this->cargarVista('productos/index', $data);
    }
    
    /**
     * Mostrar formulario de creación
     */
    public function crear() {
        $categorias = $this->producto->obtenerCategorias();
        
        $data = [
            'titulo' => 'Crear Producto - ' . APP_NAME,
            'categorias' => $categorias,
            'producto' => [], // Para formulario vacío
            'accion' => 'crear'
        ];
        
        $this->cargarVista('productos/formulario', $data);
    }
    
    /**
     * Guardar nuevo producto
     */
    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('productos');
        }
        
        // Validar token CSRF
        if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
            redirect('productos', 'Token de seguridad inválido', 'error');
        }
        
        $datos = $this->procesarDatos($_POST);
        
        // Validar datos
        $errores = $this->producto->validar($datos);
        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $_SESSION['datos_antiguos'] = $datos;
            redirect('productos/crear');
        }
        
        // Procesar imagen si existe
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $resultadoImagen = $this->subirImagen($_FILES['imagen']);
            if ($resultadoImagen['success']) {
                $datos['imagen'] = $resultadoImagen['filename'];
            } else {
                $_SESSION['errores'] = $resultadoImagen['errors'];
                $_SESSION['datos_antiguos'] = $datos;
                redirect('productos/crear');
            }
        }
        
        // Generar código si no existe
        if (empty($datos['codigo_producto'])) {
            $datos['codigo_producto'] = generateProductCode($datos['categoria'], $datos['color']);
        }
        
        try {
            $id = $this->producto->crear($datos);

            // Registrar movimiento de creación en historial
            $this->historial->registrar([
                'producto_id' => $id,
                'usuario_id' => $_SESSION['usuario_id'],
                'tipo_movimiento' => 'creacion',
                'cantidad' => $datos['stock'],
                'stock_anterior' => 0,
                'stock_nuevo' => $datos['stock'],
                'motivo' => 'Producto creado'
            ]);

            redirect('productos', 'Producto creado exitosamente');
        } catch (Exception $e) {
            error_log("Error al crear producto: " . $e->getMessage());
            redirect('productos/crear', 'Error al crear el producto', 'error');
        }
    }
    
    /**
     * Mostrar formulario de edición
     */
    public function editar($id) {
        $producto = $this->producto->obtenerPorId($id);
        
        if (!$producto) {
            redirect('productos', 'Producto no encontrado', 'error');
        }
        
        $categorias = $this->producto->obtenerCategorias();
        
        $data = [
            'titulo' => 'Editar Producto - ' . APP_NAME,
            'categorias' => $categorias,
            'producto' => $producto,
            'accion' => 'editar'
        ];
        
        $this->cargarVista('productos/formulario', $data);
    }
    
    /**
     * Actualizar producto existente
     */
    public function actualizar($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('productos');
        }
        
        // Validar token CSRF
        if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
            redirect('productos', 'Token de seguridad inválido', 'error');
        }
        
        $producto = $this->producto->obtenerPorId($id);
        if (!$producto) {
            redirect('productos', 'Producto no encontrado', 'error');
        }
        
        $datos = $this->procesarDatos($_POST);
        
        // Validar datos
        $errores = $this->producto->validar($datos, $id);
        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $_SESSION['datos_antiguos'] = $datos;
            redirect("productos/editar/{$id}");
        }
        
        // Procesar nueva imagen si se subió
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $resultadoImagen = $this->subirImagen($_FILES['imagen']);
            if ($resultadoImagen['success']) {
                // Eliminar imagen anterior si existe
                if (!empty($producto['imagen'])) {
                    $this->eliminarImagen($producto['imagen']);
                }
                $datos['imagen'] = $resultadoImagen['filename'];
            } else {
                $_SESSION['errores'] = $resultadoImagen['errors'];
                $_SESSION['datos_antiguos'] = $datos;
                redirect("productos/editar/{$id}");
            }
        }
        
        try {
            // Verificar si cambió el stock para registrar movimiento
            $stockAnterior = $producto['stock'];
            $stockNuevo = $datos['stock'];

            $this->producto->actualizar($id, $datos);

            // Registrar movimiento solo si cambió el stock
            if ($stockAnterior != $stockNuevo) {
                $diferencia = $stockNuevo - $stockAnterior;
                $tipoMovimiento = $diferencia > 0 ? 'entrada' : 'salida';
                $motivo = $diferencia > 0
                    ? "Entrada de stock (+{$diferencia} unidades)"
                    : "Salida de stock ({$diferencia} unidades)";

                $this->historial->registrar([
                    'producto_id' => $id,
                    'usuario_id' => $_SESSION['usuario_id'],
                    'tipo_movimiento' => $tipoMovimiento,
                    'cantidad' => $diferencia,
                    'stock_anterior' => $stockAnterior,
                    'stock_nuevo' => $stockNuevo,
                    'motivo' => $motivo
                ]);
            }

            redirect('productos', 'Producto actualizado exitosamente', 'success');
        } catch (Exception $e) {
            error_log("Error al actualizar producto: " . $e->getMessage());
            redirect("productos/editar/{$id}", 'Error al actualizar el producto', 'error');
        }
    }
    
    /**
     * Eliminar producto (soft delete)
     */
    public function eliminar($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('productos');
        }
        
        // Validar token CSRF
        if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
            redirect('productos', 'Token de seguridad inválido', 'error');
        }
        
        try {
            // Obtener producto antes de eliminarlo para el historial
            $producto = $this->producto->obtenerPorId($id);

            $this->producto->eliminar($id);

            // Registrar movimiento de eliminación
            if ($producto) {
                $this->historial->registrar([
                    'producto_id' => $id,
                    'usuario_id' => $_SESSION['usuario_id'],
                    'tipo_movimiento' => 'eliminacion',
                    'cantidad' => -$producto['stock'],
                    'stock_anterior' => $producto['stock'],
                    'stock_nuevo' => 0,
                    'motivo' => 'Producto eliminado del sistema'
                ]);
            }

            redirect('productos', 'Producto eliminado exitosamente');
        } catch (Exception $e) {
            error_log("Error al eliminar producto: " . $e->getMessage());
            redirect('productos', 'Error al eliminar el producto', 'error');
        }
    }
    
    /**
     * Búsqueda AJAX de productos
     */
    public function buscar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }
        
        $termino = $_POST['termino'] ?? '';
        
        if (empty($termino)) {
            $this->enviarJSON(['productos' => []]);
        }
        
        $productos = $this->producto->buscar($termino);
        $this->enviarJSON(['productos' => $productos]);
    }
    
    /**
     * Procesar y sanitizar datos del formulario
     */
    private function procesarDatos($datos) {
        return [
            'nombre' => sanitize($datos['nombre'] ?? ''),
            'descripcion' => sanitize($datos['descripcion'] ?? ''),
            'precio' => floatval($datos['precio'] ?? 0),
            'stock' => intval($datos['stock'] ?? 0),
            'categoria' => sanitize($datos['categoria'] ?? ''),
            'tipo' => sanitize($datos['tipo'] ?? 'Niño'),
            'talla' => sanitize($datos['talla'] ?? ''),
            'color' => sanitize($datos['color'] ?? ''),
            'ubicacion' => sanitize($datos['ubicacion'] ?? 'Medellín'),
            'codigo_producto' => sanitize($datos['codigo_producto'] ?? '')
        ];
    }
    
    /**
     * Subir imagen del producto
     */
    private function subirImagen($archivo) {
        $errores = validateImage($archivo);
        
        if (!empty($errores)) {
            return ['success' => false, 'errors' => $errores];
        }
        
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        $nombreArchivo = uniqid('producto_') . '.' . $extension;
        $rutaDestino = UPLOAD_DIR . $nombreArchivo;
        
        // Crear directorio si no existe
        if (!is_dir(UPLOAD_DIR)) {
            mkdir(UPLOAD_DIR, 0755, true);
        }
        
        if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
            return ['success' => true, 'filename' => $nombreArchivo];
        } else {
            return ['success' => false, 'errors' => ['Error al guardar la imagen']];
        }
    }
    
    /**
     * Eliminar imagen del servidor
     */
    private function eliminarImagen($nombreArchivo) {
        $ruta = UPLOAD_DIR . $nombreArchivo;
        if (file_exists($ruta)) {
            unlink($ruta);
        }
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