<?php
$rol = obtenerRol();

if ($rol === 'administrador') {
    $menuItems = [
        ['url' => 'views/dashboard_administrador.php', 'icon' => 'bi-speedometer2', 'text' => 'Dashboard'],
        ['url' => 'views/usuarios/lista.php', 'icon' => 'bi-people', 'text' => 'Usuarios'],
        ['url' => 'views/materias/lista.php', 'icon' => 'bi-book', 'text' => 'Materias'],
    ];
} elseif ($rol === 'maestro') {
    $menuItems = [
        ['url' => 'views/dashboard_maestro.php', 'icon' => 'bi-speedometer2', 'text' => 'Dashboard'],
        ['url' => 'views/materias/lista.php', 'icon' => 'bi-book', 'text' => 'Mis Materias'],
        ['url' => 'views/inscripciones/solicitudes.php', 'icon' => 'bi-person-check', 'text' => 'Solicitudes'],
    ];
} elseif ($rol === 'estudiante') {
    $menuItems = [
        ['url' => 'views/dashboard_estudiante.php', 'icon' => 'bi-speedometer2', 'text' => 'Dashboard'],
        ['url' => 'views/materias/lista.php', 'icon' => 'bi-book', 'text' => 'Materias'],
        ['url' => 'views/actividades/lista.php', 'icon' => 'bi-list-task', 'text' => 'Mis Tareas'],
    ];
}
?>

<nav class="sidebar-menu">
    <?php foreach ($menuItems as $item): ?>
        <a href="<?php echo BASE_URL . '/' . $item['url']; ?>" class="menu-item">
            <i class="bi <?php echo $item['icon']; ?>"></i>
            <span><?php echo $item['text']; ?></span>
        </a>
    <?php endforeach; ?>
    
    <hr style="border-color: rgba(255,255,255,0.1); margin: 1rem 0;">
    
    <a href="<?php echo BASE_URL; ?>/logout.php" class="menu-item">
        <i class="bi bi-box-arrow-right"></i>
        <span>Cerrar Sesión</span>
    </a>
</nav>