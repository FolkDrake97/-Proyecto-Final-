<?php
/**
 * Cerrar sesión
 * Archivo: logout.php
 */

require_once 'config/database.php';
require_once 'includes/helpers.php';

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
redirigir('login.php');
?>