<?php
// Usar rutas absolutas desde la raíz del proyecto
$root = $_SERVER['DOCUMENT_ROOT'] . '/programacion-web/proyecto_integrador';
require_once $root . '/config/config.php';
?>
<!DOCTYPE html>
<html lang="es" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> - <?= $page_title ?? 'Plataforma Académica' ?></title>
    
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Estilos principales -->
    <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body class="app-container">
    <?php if (isset($_SESSION['user_id'])): ?>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="<?= BASE_URL ?>/views/dashboard_<?= $_SESSION['user_role'] ?>.php">
                <i class="fas fa-graduation-cap"></i>
                <?= APP_NAME ?>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>/views/dashboard_<?= $_SESSION['user_role'] ?>.php">
                            <i class="fas fa-home me-1"></i>Inicio
                        </a>
                    </li>
                    
                    <?php if ($_SESSION['user_role'] === 'administrador'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>/views/usuarios/lista.php">
                            <i class="fas fa-users me-1"></i>Usuarios
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>/views/materias/lista.php">
                            <i class="fas fa-book me-1"></i>Materias
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>/views/tareas/lista.php">
                            <i class="fas fa-tasks me-1"></i>Tareas
                        </a>
                    </li>
                </ul>
                
                <div class="navbar-nav">
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                            <div class="user-info me-2 text-end">
                                <div class="fw-bold"><?= $_SESSION['user_name'] ?></div>
                                <small class="text-muted"><?= ucfirst($_SESSION['user_role']) ?></small>
                            </div>
                            <i class="fas fa-user-circle fa-lg"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Perfil</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Configuración</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <?php endif; ?>
    
    <main class="dashboard">
        <div class="container">