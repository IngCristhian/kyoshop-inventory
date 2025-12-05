<?php
/**
 * CONTROLADOR DE COMPRAS
 * Maneja toda la lógica de compras de insumos (bolsas, etiquetas, embalaje, etc.)
 */

require_once 'models/Compra.php';

class CompraController {
    private $compra;

    public function __construct() {
        $this->compra = new Compra();
    }

    /**
     * Listar todas las compras con filtros y paginación
     */
    public function index() {
        $pagina = $_GET['pagina'] ?? 1;
        $filtros = [
            'categoria_insumo' => $_GET['categoria_insumo'] ?? '',
            'metodo_pago' => $_GET['metodo_pago'] ?? '',
            'fecha_desde' => $_GET['fecha_desde'] ?? '',
            'fecha_hasta' => $_GET['fecha_hasta'] ?? '',
            'proveedor' => $_GET['proveedor'] ?? '',
            'busqueda' => $_GET['busqueda'] ?? ''
        ];

        $compras = $this->compra->obtenerTodos($pagina, ITEMS_PER_PAGE, $filtros);
        $totalCompras = $this->compra->contarTotal($filtros);
        $totalPaginas = ceil($totalCompras / ITEMS_PER_PAGE);
        $estadisticas = $this->compra->obtenerEstadisticas(30);

        $data = [
            'titulo' => 'Compras de Insumos - ' . APP_NAME,
            'compras' => $compras,
            'filtros' => $filtros,
            'estadisticas' => $estadisticas,
            'paginacion' => [
                'pagina_actual' => $pagina,
                'total_paginas' => $totalPaginas,
                'total_compras' => $totalCompras
            ]
        ];

        $this->cargarVista('compras/index', $data);
    }

    /**
     * Mostrar formulario de creación de compra
     */
    public function crear() {
        $data = [
            'titulo' => 'Nueva Compra de Insumos - ' . APP_NAME
        ];

        $this->cargarVista('compras/formulario', $data);
    }

    /**
     * Guardar nueva compra
     */
    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('compras');
        }

        // Validar token CSRF
        if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
            redirect('compras', 'Token de seguridad inválido', 'error');
        }

        // Procesar datos
        $datos = [
            'usuario_id' => $_SESSION['usuario_id'],
            'proveedor' => sanitize($_POST['proveedor'] ?? ''),
            'descripcion' => sanitize($_POST['descripcion'] ?? ''),
            'categoria_insumo' => sanitize($_POST['categoria_insumo'] ?? 'otros'),
            'cantidad' => !empty($_POST['cantidad']) ? intval($_POST['cantidad']) : null,
            'precio_unitario' => !empty($_POST['precio_unitario']) ? floatval($_POST['precio_unitario']) : null,
            'total' => floatval($_POST['total'] ?? 0),
            'metodo_pago' => sanitize($_POST['metodo_pago'] ?? ''),
            'observaciones' => sanitize($_POST['observaciones'] ?? '')
        ];

        // Procesar archivo de comprobante si existe
        if (!empty($_FILES['comprobante']['name'])) {
            $uploadResult = $this->procesarComprobante($_FILES['comprobante']);
            if ($uploadResult['success']) {
                $datos['comprobante'] = $uploadResult['filename'];
            } else {
                $_SESSION['errores'] = [$uploadResult['error']];
                $_SESSION['datos_antiguos'] = $datos;
                redirect('compras/crear');
            }
        }

        // Validar datos
        $errores = $this->compra->validar($datos);
        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            $_SESSION['datos_antiguos'] = $datos;
            redirect('compras/crear');
        }

        try {
            // Crear compra
            $resultado = $this->compra->crear($datos);

            if ($resultado['success']) {
                redirect(
                    'compras/ver/' . $resultado['compra_id'],
                    "Compra #{$resultado['numero_compra']} registrada exitosamente",
                    'success'
                );
            } else {
                $_SESSION['errores'] = [$resultado['error']];
                $_SESSION['datos_antiguos'] = $datos;
                redirect('compras/crear');
            }
        } catch (Exception $e) {
            error_log("Error al crear compra: " . $e->getMessage());
            $_SESSION['errores'] = ['Error al registrar la compra: ' . $e->getMessage()];
            redirect('compras/crear');
        }
    }

    /**
     * Ver detalles de una compra
     */
    public function ver($id) {
        $compra = $this->compra->obtenerPorId($id);

        if (!$compra) {
            redirect('compras', 'Compra no encontrada', 'error');
        }

        $data = [
            'titulo' => "Compra #{$compra['numero_compra']} - " . APP_NAME,
            'compra' => $compra
        ];

        $this->cargarVista('compras/detalle', $data);
    }

    /**
     * Mostrar formulario de edición
     */
    public function editar($id) {
        $compra = $this->compra->obtenerPorId($id);

        if (!$compra) {
            redirect('compras', 'Compra no encontrada', 'error');
        }

        $data = [
            'titulo' => "Editar Compra #{$compra['numero_compra']} - " . APP_NAME,
            'compra' => $compra
        ];

        $this->cargarVista('compras/formulario', $data);
    }

    /**
     * Actualizar compra existente
     */
    public function actualizar($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('compras');
        }

        // Validar token CSRF
        if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
            redirect('compras', 'Token de seguridad inválido', 'error');
        }

        // Procesar datos
        $datos = [
            'proveedor' => sanitize($_POST['proveedor'] ?? ''),
            'descripcion' => sanitize($_POST['descripcion'] ?? ''),
            'categoria_insumo' => sanitize($_POST['categoria_insumo'] ?? 'otros'),
            'cantidad' => !empty($_POST['cantidad']) ? intval($_POST['cantidad']) : null,
            'precio_unitario' => !empty($_POST['precio_unitario']) ? floatval($_POST['precio_unitario']) : null,
            'total' => floatval($_POST['total'] ?? 0),
            'metodo_pago' => sanitize($_POST['metodo_pago'] ?? ''),
            'observaciones' => sanitize($_POST['observaciones'] ?? '')
        ];

        // Procesar archivo de comprobante si existe
        if (!empty($_FILES['comprobante']['name'])) {
            $uploadResult = $this->procesarComprobante($_FILES['comprobante']);
            if ($uploadResult['success']) {
                $datos['comprobante'] = $uploadResult['filename'];
            } else {
                $_SESSION['errores'] = [$uploadResult['error']];
                redirect('compras/editar/' . $id);
            }
        } else {
            // Mantener comprobante existente
            $compraExistente = $this->compra->obtenerPorId($id);
            $datos['comprobante'] = $compraExistente['comprobante'] ?? null;
        }

        // Validar datos
        $errores = $this->compra->validar($datos);
        if (!empty($errores)) {
            $_SESSION['errores'] = $errores;
            redirect('compras/editar/' . $id);
        }

        try {
            $resultado = $this->compra->actualizar($id, $datos);

            if ($resultado['success']) {
                redirect('compras/ver/' . $id, 'Compra actualizada exitosamente', 'success');
            } else {
                $_SESSION['errores'] = [$resultado['error']];
                redirect('compras/editar/' . $id);
            }
        } catch (Exception $e) {
            error_log("Error al actualizar compra: " . $e->getMessage());
            $_SESSION['errores'] = ['Error al actualizar la compra: ' . $e->getMessage()];
            redirect('compras/editar/' . $id);
        }
    }

    /**
     * Eliminar compra (soft delete)
     */
    public function eliminar($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('compras');
        }

        // Validar token CSRF
        if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
            redirect('compras', 'Token de seguridad inválido', 'error');
        }

        $usuarioId = $_SESSION['usuario_id'];

        try {
            $resultado = $this->compra->eliminar($id, $usuarioId);

            if ($resultado['success']) {
                redirect('compras', 'Compra eliminada correctamente', 'success');
            } else {
                redirect('compras', 'Error: ' . $resultado['error'], 'error');
            }
        } catch (Exception $e) {
            error_log("Error al eliminar compra: " . $e->getMessage());
            redirect('compras', 'Error al eliminar la compra', 'error');
        }
    }

    /**
     * Procesar archivo de comprobante (PDF, imagen)
     */
    private function procesarComprobante($archivo) {
        $resultado = ['success' => false, 'error' => '', 'filename' => ''];

        // Validar que se haya subido correctamente
        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            $resultado['error'] = 'Error al subir el archivo';
            return $resultado;
        }

        // Validar tamaño (max 5MB)
        $maxSize = 5 * 1024 * 1024; // 5MB
        if ($archivo['size'] > $maxSize) {
            $resultado['error'] = 'El archivo es demasiado grande (máximo 5MB)';
            return $resultado;
        }

        // Validar tipo de archivo
        $tiposPermitidos = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $archivo['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $tiposPermitidos)) {
            $resultado['error'] = 'Tipo de archivo no permitido. Solo JPG, PNG o PDF';
            return $resultado;
        }

        // Generar nombre único
        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        $nombreArchivo = uniqid('comprobante_') . '.' . $extension;

        // Crear directorio si no existe
        $directorioDestino = __DIR__ . '/../uploads/comprobantes/';
        if (!is_dir($directorioDestino)) {
            mkdir($directorioDestino, 0755, true);
        }

        // Mover archivo
        $rutaDestino = $directorioDestino . $nombreArchivo;
        if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
            $resultado['success'] = true;
            $resultado['filename'] = $nombreArchivo;
        } else {
            $resultado['error'] = 'Error al guardar el archivo';
        }

        return $resultado;
    }

    /**
     * Cargar vista con layout
     */
    private function cargarVista($vista, $data = []) {
        extract($data);
        $contenido = 'views/' . $vista . '.php';
        require_once 'views/layouts/master.php';
    }
}
?>
