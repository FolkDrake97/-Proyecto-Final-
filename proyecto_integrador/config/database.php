<?php
/**
 * Configuración de la base de datos
 * Archivo: config/database.php
 */

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'plataforma_academica');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Configuración de la aplicación
define('BASE_URL', 'http://localhost/programacion-web/proyecto_integrador/');
define('SITE_NAME', 'Plataforma Académica');

// Configuración de seguridad
define('SESSION_LIFETIME', 3600); // 1 hora en segundos
define('PASSWORD_MIN_LENGTH', 8);

// Configuración de archivos
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('MAX_FILE_SIZE', 5242880); // 5MB en bytes

// Zona horaria
date_default_timezone_set('America/Mexico_City');

// Mostrar errores solo en desarrollo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>