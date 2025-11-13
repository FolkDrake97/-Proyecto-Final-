<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}

require_once ROOT_PATH . '/includes/helpers.php';

iniciarSesionSegura();
requerirRol('administrador');

$pageTitle = 'Dashboard Administrador';

$db = Database::getInstance()->getConnection();

try {
    // Total usuarios
    $stmt = $db->prepare("SELECT COUNT(*) as total FROM usuarios WHERE activo = 1");
    $stmt->execute();
    $totalUsuarios = $stmt->fetch()['total'];
    
    // Total estudiantes
    $stmt = $db->prepare("SELECT COUNT(*) as total FROM usuarios WHERE rol = 'estudiante' AND activo = 1");
    $stmt->execute();
    $totalEstudiantes = $stmt->fetch()['total'];
    
    // Total maestros
    $stmt = $db->prepare("SELECT COUNT(*) as total FROM usuarios WHERE rol = 'maestro' AND activo = 1");
    $stmt->execute();
    $totalMaestros = $stmt->fetch()['total'];
    
    // Total materias
    $stmt = $db->prepare("SELECT COUNT(*) as total FROM materias WHERE activa = 1");
    $stmt->execute();
    $totalMaterias = $stmt->fetch()['total'];
    
    // Inscripciones pendientes
    $stmt = $db->prepare("SELECT COUNT(*) as total FROM inscripciones WHERE estado = 'pendiente'");
    $stmt->execute();
    $inscripcionesPendientes = $stmt->fetch()['total'];
    
    // Actividades activas
    $stmt = $db->prepare("SELECT COUNT(*) as total FROM actividades WHERE activa = 1");
    $stmt->execute();
    $actividadesActivas = $stmt->fetch()['total'];
    
} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage());
    $totalUsuarios = $totalEstudiantes = $totalMaestros = 0;
    $totalMaterias = $inscripcionesPendientes = $actividadesActivas = 0;
}

require_once ROOT_PATH . '/includes/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h2>¡Bienvenido, Administrador! ⚙️</h2>
                <p class="mb-0">Panel de control del sistema</p>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="fas fa-users fa-2x text-primary mb-2"></i>
                <h3 class="mb-1"><?php echo $totalUsuarios; ?></h3>
                <p class="text-muted mb-0">Total Usuarios</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="fas fa-book fa-2x text-success mb-2"></i>
                <h3 class="mb-1"><?php echo $totalMaterias; ?></h3>
                <p class="text-muted mb-0">Materias Activas</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="fas fa-clipboard-list fa-2x text-info mb-2"></i>
                <h3 class="mb-1"><?php echo $actividadesActivas; ?></h3>
                <p class="text-muted mb-0">Actividades</p>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="fas fa-user-graduate fa-2x text-info mb-2"></i>
                <h3 class="mb-1"><?php echo $totalEstudiantes; ?></h3>
                <p class="text-muted mb-0">Estudiantes</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="fas fa-chalkboard-teacher fa-2x text-warning mb-2"></i>
                <h3 class="mb-1"><?php echo $totalMaestros; ?></h3>
                <p class="text-muted mb-0">Maestros</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="fas fa-user-clock fa-2x text-danger mb-2"></i>
                <h3 class="mb-1"><?php echo $inscripcionesPendientes; ?></h3>
                <p class="text-muted mb-0">Solicitudes Pendientes</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-cog me-2"></i>Administración del Sistema</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <a href="<?php echo BASE_URL; ?>/views/usuarios/lista.php" class="btn btn-outline-primary w-100 p-3">
                            <i class="fas fa-users fa-2x d-block mb-2"></i>
                            Gestionar Usuarios
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="<?php echo BASE_URL; ?>/views/materias/lista.php" class="btn btn-outline-success w-100 p-3">
                            <i class="fas fa-book fa-2x d-block mb-2"></i>
                            Gestionar Materias
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="#" class="btn btn-outline-info w-100 p-3">
                            <i class="fas fa-chart-bar fa-2x d-block mb-2"></i>
                            Ver Reportes
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="#" class="btn btn-outline-warning w-100 p-3">
                            <i class="fas fa-cog fa-2x d-block mb-2"></i>
                            Configuración
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-user-plus me-2"></i>Usuarios Registrados Recientemente</h5>
            </div>
            <div class="card-body">
                <?php
                $stmt = $db->prepare("
                    SELECT * FROM usuarios 
                    WHERE activo = 1 
                    ORDER BY fecha_registro DESC 
                    LIMIT 5
                ");
                $stmt->execute();
                $usuariosRecientes = $stmt->fetchAll();
                ?>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Fecha Registro</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuariosRecientes as $usuario): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                <td>
                                    <span class="badge bg-<?php 
                                        echo $usuario['rol'] === 'administrador' ? 'danger' : 
                                            ($usuario['rol'] === 'maestro' ? 'warning' : 'info'); 
                                    ?>">
                                        <?php echo ucfirst($usuario['rol']); ?>
                                    </span>
                                </td>
                                <td><?php echo formatearFecha($usuario['fecha_registro']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . '/includes/footer.php'; ?>