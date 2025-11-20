<?php
// Asegurar que ROOT_PATH esté definido
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}

// Incluir helpers
require_once ROOT_PATH . '/includes/helpers.php';
require_once ROOT_PATH . '/config/database.php';

// Iniciar sesión
iniciarSesionSegura();

// Variables por defecto
if (!isset($pageTitle)) {
    $pageTitle = 'Plataforma Académica';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - <?php echo APP_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- TU CSS PERSONALIZADO -->
    <link href="<?php echo BASE_URL; ?>/assets/css/style.css" rel="stylesheet">
</head>
<body class="app-container">
    
    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h3><i class="fas fa-graduation-cap me-2"></i><?php echo APP_NAME; ?></h3>
            <p><?php echo obtenerNombreUsuario(); ?></p>
        </div>
        
        <?php 
        // Incluir menú según el rol
        if (file_exists(ROOT_PATH . '/includes/menu.php')) {
            require_once ROOT_PATH . '/includes/menu.php';
        }
        ?>
    </aside>
    
    <!-- MAIN CONTENT -->
    <main class="main-content">
        <!-- TOPBAR -->
        <nav class="topbar">
            <div class="topbar-left">
                <button class="toggle-sidebar" id="toggleSidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <h4 class="mb-0"><?php echo $pageTitle; ?></h4>
            </div>
            
            <div class="topbar-right">
                <div class="user-profile">
                    <div class="user-avatar">
                        <?php 
                        $nombre = obtenerNombreUsuario();
                        echo strtoupper(substr($nombre, 0, 1)); 
                        ?>
                    </div>
                    <div class="user-info">
                        <div class="fw-bold"><?php echo $nombre; ?></div>
                        <small class="text-muted"><?php echo ucfirst(obtenerRol()); ?></small>
                    </div>
                </div>
                
                <a href="<?php echo BASE_URL; ?>/logout.php" class="btn btn-sm btn-outline-danger">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </nav>
        
        <!-- CONTENT WRAPPER -->
        <div class="content-wrapper">