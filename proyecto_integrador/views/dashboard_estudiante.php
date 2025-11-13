<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}

require_once ROOT_PATH . '/includes/helpers.php';

iniciarSesionSegura();
requerirRol('estudiante');

$pageTitle = 'Dashboard Estudiante';

$db = Database::getInstance()->getConnection();
$idEstudiante = obtenerUsuarioId();

try {
    // Materias inscritas
    $stmt = $db->prepare("
        SELECT COUNT(*) as total 
        FROM inscripciones 
        WHERE id_estudiante = ? AND estado = 'aprobado'
    ");
    $stmt->execute([$idEstudiante]);
    $totalMaterias = $stmt->fetch()['total'];
    
    // Tareas pendientes
    $stmt = $db->prepare("
        SELECT COUNT(DISTINCT a.id_actividad) as total
        FROM actividades a
        INNER JOIN materias m ON a.id_materia = m.id_materia
        INNER JOIN inscripciones i ON m.id_materia = i.id_materia
        LEFT JOIN calificaciones c ON a.id_actividad = c.id_actividad AND c.id_estudiante = ?
        WHERE i.id_estudiante = ? 
          AND i.estado = 'aprobado'
          AND a.activa = 1
          AND a.fecha_limite >= NOW()
          AND c.id_calificacion IS NULL
    ");
    $stmt->execute([$idEstudiante, $idEstudiante]);
    $tareasPendientes = $stmt->fetch()['total'];
    
    // Promedio general
    $stmt = $db->prepare("
        SELECT AVG(c.calificacion) as promedio
        FROM calificaciones c
        WHERE c.id_estudiante = ? AND c.calificacion IS NOT NULL
    ");
    $stmt->execute([$idEstudiante]);
    $promedioGeneral = round($stmt->fetch()['promedio'] ?? 0, 2);
    
    // Solicitudes pendientes
    $stmt = $db->prepare("
        SELECT COUNT(*) as total 
        FROM inscripciones 
        WHERE id_estudiante = ? AND estado = 'pendiente'
    ");
    $stmt->execute([$idEstudiante]);
    $solicitudesPendientes = $stmt->fetch()['total'];
    
} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage());
}

require_once ROOT_PATH . '/includes/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h2>¡Bienvenido, <?php echo obtenerNombreUsuario(); ?>! 👋</h2>
                <p class="mb-0">Aquí está el resumen de tu actividad académica</p>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="fas fa-book fa-2x text-primary mb-2"></i>
                <h3 class="mb-1"><?php echo $totalMaterias; ?></h3>
                <p class="text-muted mb-0">Materias Inscritas</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="fas fa-tasks fa-2x text-warning mb-2"></i>
                <h3 class="mb-1"><?php echo $tareasPendientes; ?></h3>
                <p class="text-muted mb-0">Tareas Pendientes</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="fas fa-chart-line fa-2x text-success mb-2"></i>
                <h3 class="mb-1"><?php echo $promedioGeneral; ?></h3>
                <p class="text-muted mb-0">Promedio General</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="fas fa-clock fa-2x text-info mb-2"></i>
                <h3 class="mb-1"><?php echo $solicitudesPendientes; ?></h3>
                <p class="text-muted mb-0">Solicitudes Pendientes</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Acciones Rápidas</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <a href="<?php echo BASE_URL; ?>/views/materias/lista.php" class="btn btn-outline-primary w-100 p-3">
                            <i class="fas fa-book fa-2x d-block mb-2"></i>
                            Ver Materias
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="<?php echo BASE_URL; ?>/views/calificaciones/mis_calificaciones.php" class="btn btn-outline-success w-100 p-3">
                            <i class="fas fa-trophy fa-2x d-block mb-2"></i>
                            Calificaciones
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="#" class="btn btn-outline-warning w-100 p-3">
                            <i class="fas fa-tasks fa-2x d-block mb-2"></i>
                            Mis Tareas
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="#" class="btn btn-outline-info w-100 p-3">
                            <i class="fas fa-file-alt fa-2x d-block mb-2"></i>
                            Reportes
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . '/includes/footer.php'; ?>