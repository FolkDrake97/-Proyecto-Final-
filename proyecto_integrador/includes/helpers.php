<?php
/**
 * Funciones auxiliares del sistema
 * Archivo: includes/helpers.php
 */

// Definir la ruta raíz del proyecto si no está definida
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}

// Incluir config con ruta absoluta
require_once ROOT_PATH . '/config/database.php';

// ============================================
// CONSTANTES DE CONFIGURACIÓN
// ============================================

// Configuración de Base de Datos
if (!defined('DB_HOST')) {
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'plataforma_academica');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_CHARSET', 'utf8mb4');
}

// Configuración de la aplicación
if (!defined('SITE_NAME')) {
    define('SITE_NAME', 'Plataforma Académica');
    define('APP_NAME', 'Plataforma Académica');
    define('APP_VERSION', '1.0.0');
}

// Configuración de URL base
if (!defined('BASE_URL')) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    
    // Detectar la ruta del proyecto correctamente
    $scriptPath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    
    // Si estamos en un subdirectorio (includes, api, views, etc), subir un nivel
    $pathParts = explode('/', trim($scriptPath, '/'));
    
    // Si el último segmento es 'includes', 'api', 'views', etc., removerlo
    $excludeDirs = array('includes', 'api', 'views', 'config', 'models', 'assets');
    if (in_array(end($pathParts), $excludeDirs)) {
        array_pop($pathParts);
    }
    
    $path = '/' . implode('/', $pathParts);
    
    define('BASE_URL', $protocol . '://' . $host . $path);
}

// Configuración de contraseñas
if (!defined('PASSWORD_MIN_LENGTH')) {
    define('PASSWORD_MIN_LENGTH', 8);
}

// ============================================
// FUNCIONES DE SESIÓN
// ============================================

/**
 * Iniciar sesión de forma segura
 */
function iniciarSesionSegura() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Verificar si el usuario está autenticado
 */
function estaAutenticado() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Obtener el ID del usuario actual
 */
function obtenerUsuarioId() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

/**
 * Obtener el rol del usuario actual
 */
function obtenerRol() {
    return isset($_SESSION['user_role']) ? $_SESSION['user_role'] : null;
}

/**
 * Obtener el nombre del usuario actual
 */
function obtenerNombreUsuario() {
    return isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Usuario';
}

/**
 * Verificar si el usuario tiene un rol específico
 */
function tieneRol($rol) {
    if (is_array($rol)) {
        return in_array(obtenerRol(), $rol);
    }
    return obtenerRol() === $rol;
}

/**
 * Requerir autenticación - redirige si no está autenticado
 */
function requerirAutenticacion() {
    if (!estaAutenticado()) {
        redirigir('login.php');
        exit;
    }
}

/**
 * Requerir un rol específico - redirige si no tiene el rol
 */
function requerirRol($roles) {
    requerirAutenticacion();
    
    if (is_string($roles)) {
        $roles = array($roles);
    }
    
    if (!in_array(obtenerRol(), $roles)) {
        http_response_code(403);
        $dashboardUrl = BASE_URL . "/views/dashboard_" . obtenerRol() . ".php";
        echo "<!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Acceso Denegado</title>
            <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
        </head>
        <body class='bg-light'>
            <div class='container'>
                <div class='row justify-content-center align-items-center min-vh-100'>
                    <div class='col-md-6'>
                        <div class='card border-danger'>
                            <div class='card-header bg-danger text-white'>
                                <h4 class='mb-0'><i class='bi bi-shield-x'></i> Acceso Denegado</h4>
                            </div>
                            <div class='card-body text-center'>
                                <p class='lead'>No tienes permisos para acceder a esta página.</p>
                                <a href='" . $dashboardUrl . "' class='btn btn-primary'>Volver al Dashboard</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </body>
        </html>";
        exit;
    }
}

/**
 * Cerrar sesión
 */
function cerrarSesion() {
    $_SESSION = array();
    
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    session_destroy();
}

// ============================================
// FUNCIONES DE VALIDACIÓN
// ============================================

/**
 * Validar email
 */
function validarEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validar contraseña (longitud mínima)
 */
function validarPassword($password) {
    return strlen($password) >= PASSWORD_MIN_LENGTH;
}

/**
 * Sanitizar entrada (prevenir XSS)
 */
function sanitizar($data) {
    if (is_array($data)) {
        return array_map('sanitizar', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

// ============================================
// FUNCIONES DE SEGURIDAD
// ============================================

/**
 * Hash de contraseña
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, array('cost' => 12));
}

/**
 * Verificar contraseña
 */
function verificarPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Generar token aleatorio
 */
function generarToken($length = 32) {
    return bin2hex(random_bytes($length));
}

// ============================================
// FUNCIONES DE REDIRECCIÓN
// ============================================

/**
 * Redirigir a una URL
 */
function redirigir($url) {
    // Si la URL no empieza con http, asumimos que es relativa al proyecto
    if (!preg_match('/^https?:\/\//', $url)) {
        $url = BASE_URL . '/' . ltrim($url, '/');
    }
    
    header('Location: ' . $url);
    exit;
}

// ============================================
// FUNCIONES DE FORMATO
// ============================================

/**
 * Formatear fecha
 */
function formatearFecha($fecha, $formato = 'd/m/Y H:i') {
    if (empty($fecha)) {
        return '-';
    }
    
    $timestamp = is_numeric($fecha) ? $fecha : strtotime($fecha);
    return date($formato, $timestamp);
}

/**
 * Calcular días restantes
 */
function diasRestantes($fechaLimite) {
    $ahora = time();
    $limite = is_numeric($fechaLimite) ? $fechaLimite : strtotime($fechaLimite);
    $diferencia = $limite - $ahora;
    return floor($diferencia / (60 * 60 * 24));
}

/**
 * Clase CSS según calificación
 */
function claseCalificacion($calificacion) {
    if ($calificacion >= 90) {
        return 'text-success fw-bold';
    }
    if ($calificacion >= 80) {
        return 'text-info fw-bold';
    }
    if ($calificacion >= 70) {
        return 'text-warning fw-bold';
    }
    return 'text-danger fw-bold';
}

/**
 * Formatear calificación con color
 */
function formatearCalificacion($calificacion) {
    $clase = claseCalificacion($calificacion);
    return "<span class='$clase'>$calificacion</span>";
}

// ============================================
// FUNCIONES DE RESPUESTA JSON (Para APIs)
// ============================================

/**
 * Enviar respuesta JSON
 */
function respuestaJSON($exito, $datos = null, $mensaje = '', $codigoHTTP = 200) {
    http_response_code($codigoHTTP);
    header('Content-Type: application/json; charset=utf-8');
    
    echo json_encode(array(
        'exito' => $exito,
        'datos' => $datos,
        'mensaje' => $mensaje
    ), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
    exit;
}

// ============================================
// FUNCIONES DE DEBUG (Solo para desarrollo)
// ============================================

/**
 * Función de debug (solo en desarrollo)
 */
function debug($data, $die = false) {
    echo '<pre style="background: #f4f4f4; padding: 15px; border: 1px solid #ddd; margin: 10px;">';
    print_r($data);
    echo '</pre>';
    
    if ($die) {
        die('Debug Stop');
    }
}

/**
 * Log de errores
 */
function logError($mensaje, $contexto = array()) {
    $fecha = date('Y-m-d H:i:s');
    $log = "[$fecha] $mensaje";
    
    if (!empty($contexto)) {
        $log .= " | Contexto: " . json_encode($contexto);
    }
    
    error_log($log);
}

// ============================================
// ALIAS PARA COMPATIBILIDAD
// ============================================

// Alias para mantener compatibilidad con código existente
function requireAuth() {
    return requerirAutenticacion();
}

function requireRole($roles) {
    return requerirRol($roles);
}

function sanitizeInput($data) {
    return sanitizar($data);
}

function redirect($url) {
    return redirigir($url);
}