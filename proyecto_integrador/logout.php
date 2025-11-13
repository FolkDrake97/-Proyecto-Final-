<?php
/**
 * Cerrar sesión
 * Archivo: logout.php
 */

// Definir ruta raíz
define('ROOT_PATH', __DIR__);

// ✅ Incluir helpers con ruta absoluta
require_once ROOT_PATH . '/includes/helpers.php';

// Iniciar sesión
iniciarSesionSegura();

// Destruir todas las variables de sesión
$_SESSION = array();

// Destruir la cookie de sesión si existe
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destruir la sesión
session_destroy();

// Redirigir al login
header('Location: ' . BASE_URL . '/login.php');
exit;