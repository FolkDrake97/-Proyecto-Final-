<?php
/**
 * Dashboard del Estudiante
 * Archivo: views/dashboard_estudiante.php
 */

$pageTitle = 'Dashboard Estudiante';
require_once '../includes/header.php';
require_once '../includes/conexion.php';

requerirRol('estudiante');

$db = Conexion::getInstance()->getConexion();
$idEstudiante = obtenerUsuarioId();

// Obtener estadísticas del estudiante
try {
    // Total de inscripciones
    $stmt = $db->prepare("SELECT COUNT(*) as total FROM inscripciones WHERE id_estudiante = ?");
    $stmt->execute([$idEstudiante]);
    $totalInscripciones = $stmt->fetch()['total'];
    
    // Inscripciones aprobadas
    $stmt = $db->prepare("SELECT COUNT(*) as total FROM inscripciones WHERE id_estudiante = ? AND estado = 'aprobado'");
    $stmt->execute([$idEstudiante]);
    $inscripcionesAprobadas = $stmt->fetch()['total'];
    
    // Inscripciones pendientes
    $stmt = $db->prepare("SELECT COUNT(*) as total FROM inscripciones WHERE id_estudiante = ? AND estado = 'pendiente'");
    $stmt->execute([$idEstudiante]);
    $inscripcionesPendientes = $stmt->fetch()['total'];
    
    // Promedio general
    $stmt = $db->prepare("
        SELECT AVG(c.calificacion) as promedio
        FROM calificaciones c
        INNER JOIN actividades a ON c.id_actividad = a.id_actividad
        INNER JOIN inscripciones i ON a.id_materia = i.id_materia
        WHERE c.id_estudiante = ? AND i.estado = 'aprobado' AND c.calificacion IS NOT NULL
    ");
    $stmt->execute([$idEstudiante]);
    $promedioGeneral = $stmt->fetch()['promedio'] ?? 0;
    $promedioGeneral = round($promedioGeneral, 2);
    
    // Próximas actividades
    $stmt = $db->prepare("
        SELECT a.*, m.nombre as materia_nombre
        FROM actividades a
        INNER JOIN materias m ON a.id_materia = m.id_materia
        INNER JOIN inscripciones i ON m.id_materia = i.id_materia
        WHERE i.id_estudiante = ? AND i.estado = 'aprobado' 
        AND a.fecha_limite >= NOW() AND a.activa = 1
        ORDER BY a.fecha_limite ASC
        LIMIT 5
    ");
    $stmt->execute([$idEstudiante]);
    $proximasActividades = $stmt->fetchAll();
    
    // Últimas calificaciones
    $stmt = $db->prepare("
        SELECT c.*, a.titulo as actividad_titulo, m.nombre as materia_nombre
        FROM calificaciones c
        INNER JOIN actividades a ON c.id_actividad = a.id_actividad
        INNER JOIN materias m ON a.id_materia = m.id_materia
        WHERE c.id_estudiante = ? AND c.calificacion IS NOT NULL
        ORDER BY c.fecha_calificacion DESC
        LIMIT 5
    ");
    $stmt->execute([$idEstudiante]);
    $ultimasCalificaciones = $stmt->fetchAll();
    
} catch (PDOException $e) {
    error_log("Error en dashboard estudiante: " . $e->getMessage());
    $error = "Error al cargar datos";
}
?>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon primary">
            <i class="bi bi-journal-bookmark"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $inscripcionesAprobadas; ?></h3>
            <p>Materias Inscritas</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon warning">
            <i class="bi bi-clock-history"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $inscripcionesPendientes; ?></h3>
            <p>Solicitudes Pendientes</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon <?php echo $promedioGeneral >= 70 ? 'success' : 'danger'; ?>">
            <i class="bi bi-trophy"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $promedioGeneral; ?></h3>
            <p>Promedio General</p>
        </div>
    </div>
</div>

<div class="row">
    <!-- Próximas Entregas -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="bi bi-calendar-check me-2"></i>Próximas Entregas
                </h5>
            </div>
            
            <?php if (empty($proximasActividades)): ?>
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    No tienes actividades pendientes próximamente
                </div>
            <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($proximasActividades as $actividad): 
                        $diasRestantes = diasRestantes($actividad['fecha_limite']);
                        $urgente = $diasRestantes <= 2;
                    ?>
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1"><?php echo htmlspecialchars($actividad['titulo']); ?></h6>
                                <small class="<?php echo $urgente ? 'text-danger fw-bold' : 'text-muted'; ?>">
                                    <?php 
                                    if ($diasRestantes == 0) {
                                        echo 'Hoy';
                                    } elseif ($diasRestantes == 1) {
                                        echo 'Mañana';
                                    } else {
                                        echo "En $diasRestantes días";
                                    }
                                    ?>
                                </small>
                            </div>
                            <p class="mb-1 small text-muted">
                                <i class="bi bi-book me-1"></i><?php echo htmlspecialchars($actividad['materia_nombre']); ?>
                            </p>
                            <small class="text-muted">
                                Fecha límite: <?php echo formatearFecha($actividad['fecha_limite']); ?>
                            </small>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Últimas Calificaciones -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="bi bi-clipboard-check me-2"></i>Últimas Calificaciones
                </h5>
            </div>
            
            <?php if (empty($ultimasCalificaciones)): ?>
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Aún no tienes calificaciones registradas
                </div>
            <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($ultimasCalificaciones as $calif): 
                        $claseCalif = claseCalificacion($calif['calificacion']);
                    ?>
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1"><?php echo htmlspecialchars($calif['actividad_titulo']); ?></h6>
                                    <p class="mb-1 small text-muted">
                                        <i class="bi bi-book me-1"></i><?php echo htmlspecialchars($calif['materia_nombre']); ?>
                                    </p>
                                </div>
                                <span class="calificacion <?php echo $claseCalif; ?>">
                                    <?php echo $calif['calificacion']; ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>