<?php
/**
 * Header común para todas las páginas internas
 * Archivo: includes/header.php
 */

if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../config/database.php';
}

require_once __DIR__ . '/helpers.php';

iniciarSesionSegura();
requerirAutenticacion();

$nombreCompleto = obtenerNombreCompleto();
$rol = obtenerRol();
$iniciales = strtoupper(substr($_SESSION['nombre'], 0, 1) . substr($_SESSION['apellido'], 0, 1));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Dashboard'; ?> - <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/dashboard.css">
    
    <!-- Chart.js (opcional) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body>
    <div class="main-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <i class="bi bi-mortarboard-fill" style="font-size: 2rem;"></i>
                <h3 class="mt-2"><?php echo SITE_NAME; ?></h3>
                <p class="small mb-0"><?php echo ucfirst($rol); ?></p>
            </div>
            
            <?php include __DIR__ . '/menu.php'; ?>
        </aside>

        <!-- Main Content -->
        <main class="main-content" id="mainContent">
            <!-- Topbar -->
            <nav class="topbar">
                <div class="topbar-left">
                    <button class="toggle-sidebar" id="toggleSidebar">
                        <i class="bi bi-list"></i>
                    </button>
                    <h4 class="mb-0"><?php echo $pageTitle ?? 'Dashboard'; ?></h4>
                </div>
                
                <div class="topbar-right">
                    <!-- Notificaciones -->
                    <div class="dropdown">
                        <button class="btn btn-link text-dark" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-bell" style="font-size: 1.25rem;"></i>
                            <span class="badge bg-danger position-absolute translate-middle rounded-pill" style="font-size: 0.6rem;">
                                3
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header">Notificaciones</h6></li>
                            <li><a class="dropdown-item" href="#">Nueva tarea asignada</a></li>
                            <li><a class="dropdown-item" href="#">Calificación registrada</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-center" href="#">Ver todas</a></li>
                        </ul>
                    </div>
                    
                    <!-- Usuario -->
                    <div class="dropdown">
                        <div class="user-profile" data-bs-toggle="dropdown">
                            <div class="user-avatar"><?php echo $iniciales; ?></div>
                            <div class="user-info d-none d-md-block">
                                <div class="fw-bold"><?php echo $nombreCompleto; ?></div>
                                <div class="small text-muted"><?php echo ucfirst($rol); ?></div>
                            </div>
                            <i class="bi bi-chevron-down"></i>
                        </div>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="<?php echo BASE_URL; ?>views/usuarios/perfil.php">
                                    <i class="bi bi-person me-2"></i>Mi Perfil
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="<?php echo BASE_URL; ?>logout.php">
                                    <i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
            
            <!-- Contenido -->
            <div class="content-wrapper">