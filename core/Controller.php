<?php
class Controller {
    protected function view($view, $data = []) {
        extract($data);
        require_once "views/{$view}.php";
    }
    
    protected function redirect($url) {
        $baseUrl = $this->getBaseUrl();
        header("Location: {$baseUrl}/{$url}");
        exit;
    }
    
    protected function json($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    private function getBaseUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $path = dirname($_SERVER['SCRIPT_NAME']);
        return $protocol . '://' . $host . rtrim($path, '/');
    }
}
?>