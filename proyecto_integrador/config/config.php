<?php
// Configuración general
define('BASE_URL', 'http://localhost/programacion-web/proyecto_integrador');

// Configuración de Base de Datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'plataforma_academica');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Configuración de la aplicación
define('APP_NAME', 'Plataforma Académica');
define('APP_VERSION', '1.0');

// Configuración de sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Función para sanitarizar datos
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)));
}

// Función para redireccionar
function redirect($url) {
    header("Location: " . BASE_URL . "/" . $url);
    exit;
}

// Función para verificar autenticación
function requireAuth() {
    if (!isset($_SESSION['user_id'])) {
        redirect('views/login.php');
    }
}

// Función para verificar rol
function requireRole($allowedRoles) {
    requireAuth();
    
    if (is_string($allowedRoles)) {
        $allowedRoles = [$allowedRoles];
    }
    
    if (!in_array($_SESSION['user_role'], $allowedRoles)) {
        http_response_code(403);
        include 'views/errors/403.php';
        exit;
    }
}
?>