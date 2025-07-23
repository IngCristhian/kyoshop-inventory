<?php
class Router {
    private $routes = [];
    
    public function add($route, $action) {
        $this->routes[$route] = $action;
    }
    
    public function run() {
        $uri = $this->getUri();
        
        foreach ($this->routes as $route => $action) {
            if (preg_match("#^{$route}$#", $uri, $matches)) {
                $this->callAction($action, array_slice($matches, 1));
                return;
            }
        }
        
        // 404 - Ruta no encontrada
        http_response_code(404);
        require_once 'views/404.php';
    }
    
    private function getUri() {
        $uri = $_SERVER['REQUEST_URI'];
        $uri = parse_url($uri, PHP_URL_PATH);
        $uri = trim($uri, '/');
        
        // Remover el directorio base si existe
        $basePath = dirname($_SERVER['SCRIPT_NAME']);
        if ($basePath !== '/') {
            $uri = str_replace(trim($basePath, '/') . '/', '', $uri);
        }
        
        return $uri;
    }
    
    private function callAction($action, $params = []) {
        list($controller, $method) = explode('@', $action);
        
        require_once "controllers/{$controller}.php";
        
        $controllerInstance = new $controller();
        call_user_func_array([$controllerInstance, $method], $params);
    }
}
?>