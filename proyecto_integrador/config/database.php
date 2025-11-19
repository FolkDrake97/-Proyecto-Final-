<?php
// Credenciales de Base de Datos
define('DB_HOST', 'fdb1032.awardspace.net');
define('DB_NAME', '4681910_plataforma');
define('DB_USER', '4681910_plataforma');
define('DB_PASS', 'isaac2002');
define('DB_CHARSET', 'utf8mb4');

// Configuración de la Aplicación
define('APP_NAME', 'Plataforma Académica');
define('APP_VERSION', '1.0.0');
define('SITE_NAME', 'Plataforma Académica');

// Configuración de Contraseñas
define('PASSWORD_MIN_LENGTH', 6);

// Zona Horaria
date_default_timezone_set('America/Mexico_City');

// BASE_URL - Detección Automática
if (!defined('BASE_URL')) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    $path = dirname($script);
    $path = str_replace('\\', '/', $path);
    $path = trim($path, '/');
    $path = preg_replace('#/(api|views|includes|config).*$#', '', $path);
    
    define('BASE_URL', $protocol . '://' . $host . ($path ? '/' . $path : ''));
}

// MODO DE DESARROLLO
// ⚠️ CAMBIAR A 0 EN PRODUCCIÓN
error_reporting(E_ALL);
ini_set('display_errors', 1); // Cambiar a 0 en producción