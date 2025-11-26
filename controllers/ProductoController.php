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

        // Verificar si se están creando variantes
        $crearVariantes = isset($_POST['crear_variantes']) && $_POST['crear_variantes'] == '1';

        if ($crearVariantes) {
            $this->guardarVariantes($_POST, $_FILES);
        } else {
            $this->guardarProductoSimple($_POST, $_FILES);
        }
    }

    /**
     * Guardar producto simple (sin variantes)
     */
    private function guardarProductoSimple($post, $files) {
        $tiempoInicio = microtime(true);
        error_log("=== INICIO CREACIÓN DE PRODUCTO ===");

        $datos = $this->procesarDatos($post);
        error_log("Procesamiento de datos: " . round((microtime(true) - $tiempoInicio) * 1000, 2) . "ms");

        // Validar datos
        $tiempoValidacion = microtime(true);
        $errores = $this->producto->validar($datos);
        error_log("Validación: " . round((microtime(true) - $tiempoValidacion) * 1000, 2) . "ms");

        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $_SESSION['datos_antiguos'] = $datos;
            redirect('productos/crear');
        }

        // Procesar imagen principal si existe
        if (isset($files['imagen']) && $files['imagen']['error'] === UPLOAD_ERR_OK) {
            $tiempoImagen1 = microtime(true);
            $resultadoImagen = $this->subirImagen($files['imagen']);
            error_log("Subir imagen 1: " . round((microtime(true) - $tiempoImagen1) * 1000, 2) . "ms");

            if ($resultadoImagen['success']) {
                $datos['imagen'] = $resultadoImagen['filename'];
            } else {
                $_SESSION['errores'] = $resultadoImagen['errors'];
                $_SESSION['datos_antiguos'] = $datos;
                redirect('productos/crear');
            }
        }

        // Procesar imagen con modelo si existe
        if (isset($files['imagen_modelo']) && $files['imagen_modelo']['error'] === UPLOAD_ERR_OK) {
            $tiempoImagen2 = microtime(true);
            $resultadoImagen = $this->subirImagen($files['imagen_modelo']);
            error_log("Subir imagen 2: " . round((microtime(true) - $tiempoImagen2) * 1000, 2) . "ms");

            if ($resultadoImagen['success']) {
                $datos['imagen_modelo'] = $resultadoImagen['filename'];
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
            $tiempoInsert = microtime(true);
            $id = $this->producto->crear($datos);
            error_log("INSERT producto: " . round((microtime(true) - $tiempoInsert) * 1000, 2) . "ms");

            // Registrar movimiento de creación en historial
            $tiempoHistorial = microtime(true);
            $this->historial->registrar([
                'producto_id' => $id,
                'usuario_id' => $_SESSION['usuario_id'],
                'tipo_movimiento' => 'creacion',
                'cantidad' => $datos['stock'],
                'stock_anterior' => 0,
                'stock_nuevo' => $datos['stock'],
                'motivo' => 'Producto creado'
            ]);
            error_log("Registrar historial: " . round((microtime(true) - $tiempoHistorial) * 1000, 2) . "ms");

            $tiempoTotal = round((microtime(true) - $tiempoInicio) * 1000, 2);
            error_log("=== TIEMPO TOTAL: {$tiempoTotal}ms ===");

            redirect('productos', 'Producto creado exitosamente');
        } catch (Exception $e) {
            error_log("Error al crear producto: " . $e->getMessage());
            redirect('productos/crear', 'Error al crear el producto', 'error');
        }
    }

    /**
     * Guardar múltiples variantes de un producto
     */
    private function guardarVariantes($post, $files) {
        // Validar que existan variantes
        if (empty($post['variantes']) || !is_array($post['variantes'])) {
            $_SESSION['errores'] = ['Debe agregar al menos una variante'];
            redirect('productos/crear');
        }

        $datosBase = [
            'nombre' => sanitize($post['nombre'] ?? ''),
            'descripcion' => sanitize($post['descripcion'] ?? ''),
            'precio' => floatval($post['precio'] ?? 0),
            'categoria' => sanitize($post['categoria'] ?? ''),
            'tipo' => sanitize($post['tipo'] ?? 'Niño'),
            'ubicacion' => sanitize($post['ubicacion'] ?? 'Medellín')
        ];

        // Procesar imagen principal (se usará como default)
        $imagenPrincipal = null;
        if (isset($files['imagen']) && $files['imagen']['error'] === UPLOAD_ERR_OK) {
            $resultadoImagen = $this->subirImagen($files['imagen']);
            if ($resultadoImagen['success']) {
                $imagenPrincipal = $resultadoImagen['filename'];
            }
        }

        $productosCreados = 0;
        $erroresVariantes = [];

        try {
            foreach ($post['variantes'] as $index => $variante) {
                // Validar datos de la variante
                if (empty($variante['color']) || empty($variante['talla']) || !isset($variante['stock'])) {
                    $erroresVariantes[] = "Variante " . ($index + 1) . ": Todos los campos son obligatorios";
                    continue;
                }

                // Crear datos del producto variante
                $datosVariante = $datosBase;
                $datosVariante['nombre'] = $datosBase['nombre'] . ' - ' . $variante['color'] . ' - ' . $variante['talla'];
                $datosVariante['color'] = sanitize($variante['color']);
                $datosVariante['talla'] = sanitize($variante['talla']);
                $datosVariante['stock'] = intval($variante['stock']);

                // Generar código automático para la variante
                $timestamp = time() . str_pad($index, 3, '0', STR_PAD_LEFT);
                $datosVariante['codigo_producto'] = strtoupper(
                    substr($datosVariante['categoria'], 0, 3) . '-' .
                    substr($datosVariante['color'], 0, 3) . '-' .
                    substr($datosVariante['talla'], 0, 2) . '-' .
                    $timestamp
                );

                // Procesar imagen principal de la variante (o usar imagen principal del producto base)
                if (isset($files['variantes']['name'][$index]['imagen']) &&
                    $files['variantes']['error'][$index]['imagen'] === UPLOAD_ERR_OK) {

                    $imagenVariante = [
                        'name' => $files['variantes']['name'][$index]['imagen'],
                        'type' => $files['variantes']['type'][$index]['imagen'],
                        'tmp_name' => $files['variantes']['tmp_name'][$index]['imagen'],
                        'error' => $files['variantes']['error'][$index]['imagen'],
                        'size' => $files['variantes']['size'][$index]['imagen']
                    ];

                    $resultadoImagen = $this->subirImagen($imagenVariante);
                    if ($resultadoImagen['success']) {
                        $datosVariante['imagen'] = $resultadoImagen['filename'];
                    } else {
                        $erroresVariantes[] = "Variante " . ($index + 1) . ": Error al subir imagen principal";
                        continue;
                    }
                } else {
                    // Si no se subió imagen específica, usar la imagen principal del producto base
                    $datosVariante['imagen'] = $imagenPrincipal;
                }

                // Procesar imagen con modelo de la variante (opcional)
                if (isset($files['variantes']['name'][$index]['imagen_modelo']) &&
                    $files['variantes']['error'][$index]['imagen_modelo'] === UPLOAD_ERR_OK) {

                    $imagenModeloVariante = [
                        'name' => $files['variantes']['name'][$index]['imagen_modelo'],
                        'type' => $files['variantes']['type'][$index]['imagen_modelo'],
                        'tmp_name' => $files['variantes']['tmp_name'][$index]['imagen_modelo'],
                        'error' => $files['variantes']['error'][$index]['imagen_modelo'],
                        'size' => $files['variantes']['size'][$index]['imagen_modelo']
                    ];

                    $resultadoImagenModelo = $this->subirImagen($imagenModeloVariante);
                    if ($resultadoImagenModelo['success']) {
                        $datosVariante['imagen_modelo'] = $resultadoImagenModelo['filename'];
                    }
                    // No es error crítico si falla la imagen con modelo
                }

                // Crear el producto variante
                $id = $this->producto->crear($datosVariante);

                // Registrar movimiento en historial
                $this->historial->registrar([
                    'producto_id' => $id,
                    'usuario_id' => $_SESSION['usuario_id'],
                    'tipo_movimiento' => 'creacion',
                    'cantidad' => $datosVariante['stock'],
                    'stock_anterior' => 0,
                    'stock_nuevo' => $datosVariante['stock'],
                    'motivo' => 'Producto creado (variante)'
                ]);

                $productosCreados++;
            }

            if ($productosCreados > 0) {
                $mensaje = "Se crearon $productosCreados variantes exitosamente";
                if (!empty($erroresVariantes)) {
                    $mensaje .= '. Algunos errores: ' . implode(', ', array_slice($erroresVariantes, 0, 3));
                }
                redirect('productos', $mensaje, 'success');
            } else {
                $_SESSION['errores'] = $erroresVariantes;
                redirect('productos/crear');
            }

        } catch (Exception $e) {
            error_log("Error al crear variantes: " . $e->getMessage());
            $_SESSION['errores'] = ['Error al crear las variantes: ' . $e->getMessage()];
            redirect('productos/crear');
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
        
        // Manejar eliminación de imagen principal
        if (isset($_POST['eliminar_imagen']) && $_POST['eliminar_imagen'] == '1') {
            if (!empty($producto['imagen'])) {
                $this->eliminarImagen($producto['imagen']);
            }
            $datos['imagen'] = null;
        }

        // Manejar eliminación de imagen con modelo
        if (isset($_POST['eliminar_imagen_modelo']) && $_POST['eliminar_imagen_modelo'] == '1') {
            if (!empty($producto['imagen_modelo'])) {
                $this->eliminarImagen($producto['imagen_modelo']);
            }
            $datos['imagen_modelo'] = null;
        }

        // Procesar nueva imagen principal si se subió
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

        // Procesar nueva imagen con modelo si se subió
        if (isset($_FILES['imagen_modelo']) && $_FILES['imagen_modelo']['error'] === UPLOAD_ERR_OK) {
            $resultadoImagen = $this->subirImagen($_FILES['imagen_modelo']);
            if ($resultadoImagen['success']) {
                // Eliminar imagen anterior si existe
                if (!empty($producto['imagen_modelo'])) {
                    $this->eliminarImagen($producto['imagen_modelo']);
                }
                $datos['imagen_modelo'] = $resultadoImagen['filename'];
            } else {
                $_SESSION['errores'] = $resultadoImagen['errors'];
                $_SESSION['datos_antiguos'] = $datos;
                redirect("productos/editar/{$id}");
            }
        }
        
        try {
            // Verificar si cambió el stock o el precio para registrar movimientos
            $stockAnterior = $producto['stock'];
            $stockNuevo = $datos['stock'];
            $precioAnterior = $producto['precio'];
            $precioNuevo = $datos['precio'];

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

            // Registrar movimiento solo si cambió el precio
            if ($precioAnterior != $precioNuevo) {
                $diferenciaPrecio = $precioNuevo - $precioAnterior;
                $simbolo = $diferenciaPrecio > 0 ? '+' : '';
                $motivo = "Cambio de precio: $" . number_format($precioAnterior, 0, ',', '.') .
                         " → $" . number_format($precioNuevo, 0, ',', '.') .
                         " ({$simbolo}$" . number_format($diferenciaPrecio, 0, ',', '.') . ")";

                $this->historial->registrar([
                    'producto_id' => $id,
                    'usuario_id' => $_SESSION['usuario_id'],
                    'tipo_movimiento' => 'cambio_precio',
                    'cantidad' => 0, // No afecta el stock
                    'stock_anterior' => $stockNuevo, // Stock actual
                    'stock_nuevo' => $stockNuevo, // Stock no cambia
                    'precio_anterior' => $precioAnterior,
                    'precio_nuevo' => $precioNuevo,
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