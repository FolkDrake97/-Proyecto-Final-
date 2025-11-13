<?php
define('ROOT_PATH', __DIR__);
require_once ROOT_PATH . '/includes/helpers.php';

iniciarSesionSegura();

if (estaAutenticado()) {
    $rol = obtenerRol();
    redirigir("views/dashboard_$rol.php");
} else {
    redirigir('login.php');
}