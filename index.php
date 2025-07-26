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

// Obtener la ruta solicitada
$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);
$path = trim($path, '/');

// Enrutamiento simple
switch ($path) {
    case '':
    case 'dashboard':
        require_once 'controllers/ProductoController.php';
        $controller = new ProductoController();
        $controller->dashboard();
        break;
        
    case 'productos':
        require_once 'controllers/ProductoController.php';
        $controller = new ProductoController();
        $controller->index();
        break;
        
    case 'productos/crear':
        require_once 'controllers/ProductoController.php';
        $controller = new ProductoController();
        $controller->crear();
        break;
        
    case 'productos/guardar':
        require_once 'controllers/ProductoController.php';
        $controller = new ProductoController();
        $controller->guardar();
        break;
        
    case (preg_match('/productos\/editar\/(\d+)/', $path, $matches) ? true : false):
        require_once 'controllers/ProductoController.php';
        $controller = new ProductoController();
        $controller->editar($matches[1]);
        break;
        
    case (preg_match('/productos\/actualizar\/(\d+)/', $path, $matches) ? true : false):
        require_once 'controllers/ProductoController.php';
        $controller = new ProductoController();
        $controller->actualizar($matches[1]);
        break;
        
    case (preg_match('/productos\/eliminar\/(\d+)/', $path, $matches) ? true : false):
        require_once 'controllers/ProductoController.php';
        $controller = new ProductoController();
        $controller->eliminar($matches[1]);
        break;
        
    case 'productos/buscar':
        require_once 'controllers/ProductoController.php';
        $controller = new ProductoController();
        $controller->buscar();
        break;
        
    default:
        http_response_code(404);
        include 'views/404.php';
        break;
}
?>