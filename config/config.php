<?php
/**
 * CONFIGURACIONES GENERALES DEL SISTEMA
 * KyoShop Inventory System
 */

// Configuración de la aplicación
define('APP_NAME', 'KyoShop Inventory');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost:8000'); // URL para servidor PHP integrado

// Configuración de archivos
define('UPLOAD_DIR', 'uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

// Configuración de paginación
define('ITEMS_PER_PAGE', 12);

// Configuración de seguridad
define('CSRF_TOKEN_NAME', 'csrf_token');

// Timezone
date_default_timezone_set('America/Bogota');

/**
 * Generar token CSRF
 */
function generateCSRFToken() {
    if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

/**
 * Validar token CSRF
 */
function validateCSRFToken($token) {
    return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

/**
 * Sanitizar entrada de datos
 */
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Formatear precio en pesos colombianos
 */
function formatPrice($price) {
    return '$' . number_format($price, 0, ',', '.');
}

/**
 * Generar código de producto único
 */
function generateProductCode($categoria, $color) {
    $categoriaCod = strtoupper(substr($categoria, 0, 3));
    $colorCod = strtoupper(substr($color, 0, 3));
    $numero = str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
    return $categoriaCod . '-' . $colorCod . '-' . $numero;
}

/**
 * Redireccionar con mensaje
 */
function redirect($url, $message = null, $type = 'success') {
    if ($message) {
        $_SESSION['flash_message'] = $message;
        $_SESSION['flash_type'] = $type;
    }
    header('Location: ' . APP_URL . '/' . ltrim($url, '/'));
    exit;
}

/**
 * Mostrar mensaje flash
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'] ?? 'success';
        unset($_SESSION['flash_message'], $_SESSION['flash_type']);
        return ['message' => $message, 'type' => $type];
    }
    return null;
}

/**
 * Validar imagen subida
 */
function validateImage($file) {
    $errors = [];
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Error al subir el archivo';
        return $errors;
    }
    
    if ($file['size'] > MAX_FILE_SIZE) {
        $errors[] = 'El archivo es demasiado grande (máximo 5MB)';
    }
    
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ALLOWED_EXTENSIONS)) {
        $errors[] = 'Tipo de archivo no permitido (solo JPG, PNG, GIF)';
    }
    
    return $errors;
}
?>