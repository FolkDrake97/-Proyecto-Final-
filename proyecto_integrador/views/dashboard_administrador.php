<?php
$page_title = "Dashboard Administrador";
require_once '../includes/header.php';
?>

<!-- Welcome Section -->
<div class="welcome-section">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="welcome-title">¡Bienvenido de vuelta, <?= $_SESSION['user_name'] ?>! 👋</h1>
            <p class="welcome-subtitle">Aquí tienes un resumen de tu plataforma académica</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="date-info">
                <i class="fas fa-calendar-alt me-2"></i>
                <?= date('d F Y') ?>
            </div>
        </div>
    </div>
</div>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-content">
            <h3>156</h3>
            <p>Total de Estudiantes</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-chalkboard-teacher"></i>
        </div>
        <div class="stat-content">
            <h3>24</h3>
            <p>Profesores Activos</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-book"></i>
        </div>
        <div class="stat-content">
            <h3>18</h3>
            <p>Materias Impartidas</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-chart-line"></i>
        </div>
        <div class="stat-content">
            <h3>96%</h3>
            <p>Rendimiento General</p>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="quick-actions">
    <h4 class="mb-4 fw-bold">Acciones Rápidas</h4>
    <div class="actions-grid">
        <a href="usuarios/crear.php" class="action-btn">
            <div class="action-icon">
                <i class="fas fa-user-plus"></i>
            </div>
            <span>Nuevo Usuario</span>
        </a>
        
        <a href="materias/crear.php" class="action-btn">
            <div class="action-icon">
                <i class="fas fa-plus-circle"></i>
            </div>
            <span>Crear Materia</span>
        </a>
        
        <a href="reportes/general.php" class="action-btn">
            <div class="action-icon">
                <i class="fas fa-chart-bar"></i>
            </div>
            <span>Ver Reportes</span>
        </a>
        
        <a href="../logout.php" class="action-btn">
            <div class="action-icon">
                <i class="fas fa-sign-out-alt"></i>
            </div>
            <span>Cerrar Sesión</span>
        </a>
    </div>
</div>

<?php
require_once '../includes/footer.php';
?>