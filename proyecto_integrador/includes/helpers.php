<?php
require_once '../config/config.php';

function obtenerRol() {
    return $_SESSION['user_role'] ?? null;
}

function estaAutenticado() {
    return isset($_SESSION['user_id']);
}

function requerirAutenticacion() {
    if (!estaAutenticado()) {
        redirigir('login.php');
    }
}

function requerirRol($roles) {
    requerirAutenticacion();
    
    if (is_string($roles)) {
        $roles = [$roles];
    }
    
    if (!in_array($_SESSION['user_role'], $roles)) {
        redirigir('index.php');
    }
}

function redirigir($url) {
    header("Location: " . BASE_URL . "/" . $url);
    exit;
}

function mostrarError($mensaje) {
    echo "<div class='alert alert-danger'>$mensaje</div>";
}

function mostrarExito($mensaje) {
    echo "<div class='alert alert-success'>$mensaje</div>";
}
?>