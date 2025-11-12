<?php
/**
 * Funciones auxiliares globales
 * Archivo: includes/helpers.php
 */

function sanitizar($data) {
    if (is_array($data)) {
        return array_map('sanitizar', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

function validarEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

function verificarPassword($password, $hash) {
    return password_verify($password, $hash);
}

function generarToken($length = 32) {
    return bin2hex(random_bytes($length));
}

function estaAutenticado() {
    return isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id']);
}

function obtenerUsuarioId() {
    return $_SESSION['usuario_id'] ?? null;
}

function obtenerRol() {
    return $_SESSION['rol'] ?? null;
}

function tieneRol($roles) {
    if (!estaAutenticado()) {
        return false;
    }
    
    $rolUsuario = obtenerRol();
    
    if (is_array($roles)) {
        return in_array($rolUsuario, $roles);
    }
    
    return $rolUsuario === $roles;
}

function redirigir($url) {
    header("Location: " . BASE_URL . $url);
    exit;
}

function requerirAutenticacion() {
    if (!estaAutenticado()) {
        redirigir('login.php');
    }
}

function requerirRol($roles) {
    requerirAutenticacion();
    
    if (!tieneRol($roles)) {
        redirigir('index.php');
    }
}

function formatearFecha($fecha, $formato = 'd/m/Y H:i') {
    if (empty($fecha)) {
        return '-';
    }
    $dt = new DateTime($fecha);
    return $dt->format($formato);
}

function diasRestantes($fechaLimite) {
    $ahora = new DateTime();
    $limite = new DateTime($fechaLimite);
    $diferencia = $ahora->diff($limite);
    
    if ($limite < $ahora) {
        return -$diferencia->days;
    }
    
    return $diferencia->days;
}

function respuestaJSON($exito, $data = null, $mensaje = '', $codigo = 200) {
    http_response_code($codigo);
    header('Content-Type: application/json; charset=utf-8');
    
    echo json_encode([
        'exito' => $exito,
        'mensaje' => $mensaje,
        'data' => $data
    ], JSON_UNESCAPED_UNICODE);
    
    exit;
}

function validarPassword($password) {
    return strlen($password) >= PASSWORD_MIN_LENGTH;
}

function obtenerNombreCompleto() {
    if (!estaAutenticado()) {
        return '';
    }
    return ($_SESSION['nombre'] ?? '') . ' ' . ($_SESSION['apellido'] ?? '');
}

function calcularPromedioPonderado($calificaciones) {
    if (empty($calificaciones)) {
        return 0;
    }
    
    $sumaCalificaciones = 0;
    $sumaPonderaciones = 0;
    
    foreach ($calificaciones as $calif) {
        $sumaCalificaciones += $calif['calificacion'] * $calif['ponderacion'];
        $sumaPonderaciones += $calif['ponderacion'];
    }
    
    if ($sumaPonderaciones == 0) {
        return 0;
    }
    
    return round($sumaCalificaciones / $sumaPonderaciones, 2);
}

function claseCalificacion($calificacion) {
    if ($calificacion >= 90) return 'excelente';
    if ($calificacion >= 80) return 'bien';
    if ($calificacion >= 70) return 'regular';
    return 'reprobado';
}

function fechaPasada($fechaLimite) {
    return strtotime($fechaLimite) < time();
}

function iniciarSesionSegura() {
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_secure', 0);
        session_start();
    }
}
?>