<?php
/**
 * KYOSHOP INVENTORY SYSTEM
 * Sistema de inventario para tienda de ropa
 * Punto de entrada principal (Front Controller)
 */

session_start();

// Configuración de errores para desarrollo
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Incluir archivos necesarios
require_once 'config/database.php';
require_once 'config/config.php';
require_once 'controllers/AuthController.php';

// Obtener la ruta solicitada
$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);
$path = trim($path, '/');

// Enrutamiento simple
switch ($path) {
    // Rutas de autenticación (públicas)
    case 'login':
        $controller = new AuthController();
        $controller->login();
        break;

    case 'logout':
        $controller = new AuthController();
        $controller->logout();
        break;

    // Rutas protegidas (requieren autenticación)
    case '':
        // Ruta raíz: redirigir a login sin mensaje si no está autenticado
        $auth = new AuthController();
        if (!$auth->estaAutenticado()) {
            header('Location: ' . APP_URL . '/login');
            exit;
        }
        require_once 'controllers/ProductoController.php';
        $controller = new ProductoController();
        $controller->dashboard();
        break;

    case 'dashboard':
        requiereAuth();
        require_once 'controllers/ProductoController.php';
        $controller = new ProductoController();
        $controller->dashboard();
        break;
        
    case 'productos':
        requiereAuth();
        require_once 'controllers/ProductoController.php';
        $controller = new ProductoController();
        $controller->index();
        break;

    case 'productos/crear':
        requiereAuth();
        require_once 'controllers/ProductoController.php';
        $controller = new ProductoController();
        $controller->crear();
        break;

    case 'productos/guardar':
        requiereAuth();
        require_once 'controllers/ProductoController.php';
        $controller = new ProductoController();
        $controller->guardar();
        break;

    case (preg_match('/productos\/editar\/(\d+)/', $path, $matches) ? true : false):
        requiereAuth();
        require_once 'controllers/ProductoController.php';
        $controller = new ProductoController();
        $controller->editar($matches[1]);
        break;

    case (preg_match('/productos\/actualizar\/(\d+)/', $path, $matches) ? true : false):
        requiereAuth();
        require_once 'controllers/ProductoController.php';
        $controller = new ProductoController();
        $controller->actualizar($matches[1]);
        break;

    case (preg_match('/productos\/eliminar\/(\d+)/', $path, $matches) ? true : false):
        requiereAuth();
        require_once 'controllers/ProductoController.php';
        $controller = new ProductoController();
        $controller->eliminar($matches[1]);
        break;

    case 'productos/buscar':
        requiereAuth();
        require_once 'controllers/ProductoController.php';
        $controller = new ProductoController();
        $controller->buscar();
        break;

    // Rutas de gestión de usuarios (solo admin)
    case 'usuarios':
        requiereAdmin();
        require_once 'controllers/UserController.php';
        $controller = new UserController();
        $controller->index();
        break;

    case 'usuarios/crear':
        requiereAdmin();
        require_once 'controllers/UserController.php';
        $controller = new UserController();
        $controller->crear();
        break;

    case 'usuarios/guardar':
        requiereAdmin();
        require_once 'controllers/UserController.php';
        $controller = new UserController();
        $controller->guardar();
        break;

    case (preg_match('/usuarios\/editar\/(\d+)/', $path, $matches) ? true : false):
        requiereAdmin();
        require_once 'controllers/UserController.php';
        $controller = new UserController();
        $controller->editar($matches[1]);
        break;

    case (preg_match('/usuarios\/actualizar\/(\d+)/', $path, $matches) ? true : false):
        requiereAdmin();
        require_once 'controllers/UserController.php';
        $controller = new UserController();
        $controller->actualizar($matches[1]);
        break;

    case (preg_match('/usuarios\/desactivar\/(\d+)/', $path, $matches) ? true : false):
        requiereAdmin();
        require_once 'controllers/UserController.php';
        $controller = new UserController();
        $controller->desactivar($matches[1]);
        break;

    case (preg_match('/usuarios\/activar\/(\d+)/', $path, $matches) ? true : false):
        requiereAdmin();
        require_once 'controllers/UserController.php';
        $controller = new UserController();
        $controller->activar($matches[1]);
        break;

    // Rutas de clientes
    case 'clientes':
        requiereAuth();
        require_once 'controllers/ClienteController.php';
        $controller = new ClienteController();
        $controller->index();
        break;

    case 'clientes/crear':
        requiereAuth();
        require_once 'controllers/ClienteController.php';
        $controller = new ClienteController();
        $controller->crear();
        break;

    case 'clientes/guardar':
        requiereAuth();
        require_once 'controllers/ClienteController.php';
        $controller = new ClienteController();
        $controller->guardar();
        break;

    case (preg_match('/clientes\/editar\/(\d+)/', $path, $matches) ? true : false):
        requiereAuth();
        require_once 'controllers/ClienteController.php';
        $controller = new ClienteController();
        $controller->editar($matches[1]);
        break;

    case (preg_match('/clientes\/actualizar\/(\d+)/', $path, $matches) ? true : false):
        requiereAuth();
        require_once 'controllers/ClienteController.php';
        $controller = new ClienteController();
        $controller->actualizar($matches[1]);
        break;

    case (preg_match('/clientes\/eliminar\/(\d+)/', $path, $matches) ? true : false):
        requiereAuth();
        require_once 'controllers/ClienteController.php';
        $controller = new ClienteController();
        $controller->eliminar($matches[1]);
        break;

    // Rutas de ventas
    case 'ventas':
        requiereAuth();
        require_once 'controllers/VentaController.php';
        $controller = new VentaController();
        $controller->index();
        break;

    case 'ventas/crear':
        requiereAuth();
        require_once 'controllers/VentaController.php';
        $controller = new VentaController();
        $controller->crear();
        break;

    case 'ventas/guardar':
        requiereAuth();
        require_once 'controllers/VentaController.php';
        $controller = new VentaController();
        $controller->guardar();
        break;

    case (preg_match('/ventas\/ver\/(\d+)/', $path, $matches) ? true : false):
        requiereAuth();
        require_once 'controllers/VentaController.php';
        $controller = new VentaController();
        $controller->ver($matches[1]);
        break;

    case (preg_match('/ventas\/actualizarEstadoPago\/(\d+)/', $path, $matches) ? true : false):
        requiereAuth();
        require_once 'controllers/VentaController.php';
        $controller = new VentaController();
        $controller->actualizarEstadoPago($matches[1]);
        break;

    case (preg_match('/ventas\/cancelar\/(\d+)/', $path, $matches) ? true : false):
        requiereAuth();
        require_once 'controllers/VentaController.php';
        $controller = new VentaController();
        $controller->cancelar($matches[1]);
        break;

    case (preg_match('/ventas\/factura\/(\d+)/', $path, $matches) ? true : false):
        requiereAuth();
        require_once 'controllers/VentaController.php';
        $controller = new VentaController();
        $controller->factura($matches[1]);
        break;

    case 'ventas/buscarCliente':
        requiereAuth();
        require_once 'controllers/VentaController.php';
        $controller = new VentaController();
        $controller->buscarCliente();
        break;

    case 'ventas/buscarProducto':
        requiereAuth();
        require_once 'controllers/VentaController.php';
        $controller = new VentaController();
        $controller->buscarProducto();
        break;

    case 'ventas/crearCliente':
        requiereAuth();
        require_once 'controllers/VentaController.php';
        $controller = new VentaController();
        $controller->crearCliente();
        break;

    // Rutas de historial
    case 'historial':
        requiereAuth();
        require_once 'controllers/HistorialController.php';
        $controller = new HistorialController();
        $controller->index();
        break;

    // Rutas de combos
    case 'combos':
        requiereAuth();
        require_once 'controllers/ComboController.php';
        $controller = new ComboController();
        $controller->index();
        break;

    case 'combos/crear':
        requiereAuth();
        require_once 'controllers/ComboController.php';
        $controller = new ComboController();
        $controller->crear();
        break;

    case 'combos/guardar':
        requiereAuth();
        require_once 'controllers/ComboController.php';
        $controller = new ComboController();
        $controller->guardar();
        break;

    case (preg_match('/combos\/ver\/(\d+)/', $path, $matches) ? true : false):
        requiereAuth();
        require_once 'controllers/ComboController.php';
        $controller = new ComboController();
        $controller->ver($matches[1]);
        break;

    case (preg_match('/combos\/editar\/(\d+)/', $path, $matches) ? true : false):
        requiereAuth();
        require_once 'controllers/ComboController.php';
        $controller = new ComboController();
        $controller->editar($matches[1]);
        break;

    case (preg_match('/combos\/actualizar\/(\d+)/', $path, $matches) ? true : false):
        requiereAuth();
        require_once 'controllers/ComboController.php';
        $controller = new ComboController();
        $controller->actualizar($matches[1]);
        break;

    case (preg_match('/combos\/eliminar\/(\d+)/', $path, $matches) ? true : false):
        requiereAuth();
        require_once 'controllers/ComboController.php';
        $controller = new ComboController();
        $controller->eliminar($matches[1]);
        break;

    case (preg_match('/historial\/producto\/(\d+)/', $path, $matches) ? true : false):
        requiereAuth();
        require_once 'controllers/HistorialController.php';
        $controller = new HistorialController();
        $controller->producto($matches[1]);
        break;

    default:
        http_response_code(404);
        include 'views/404.php';
        break;
}
?>