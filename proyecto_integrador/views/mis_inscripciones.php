<?php
/**
 * Mis inscripciones (Estudiante)
 * Archivo: views/mis_inscripciones.php
 */

$pageTitle = 'Mis Inscripciones';
require_once '../includes/header.php';
require_once '../includes/conexion.php';

requerirRol('estudiante');

$db = Conexion::getInstance()->getConexion();
$idEstudiante = obtenerUsuarioId();

// Obtener inscripciones del estudiante
try {
    $stmt = $db->prepare("
        SELECT i.*, 
               m.nombre as materia_nombre,
               m.descripcion as materia_descripcion,
               m.cuatrimestre,
               u.nombre as maestro_nombre,
               u.apellido as maestro_apellido
        FROM inscripciones i
        INNER JOIN materias m ON i.id_materia = m.id_materia
        INNER JOIN usuarios u ON m.id_maestro = u.id_usuario
        WHERE i.id_estudiante = ?
        ORDER BY 
            CASE i.estado
                WHEN 'pendiente' THEN 1
                WHEN 'aprobado' THEN 2
                WHEN 'rechazado' THEN 3
            END,
            i.fecha_solicitud DESC
    ");
    $stmt->execute([$idEstudiante]);
    $inscripciones = $stmt->fetchAll();
    
    // Agrupar por estado
    $pendientes = array_filter($inscripciones, fn($i) => $i['estado'] === 'pendiente');
    $aprobadas = array_filter($inscripciones, fn($i) => $i['estado'] === 'aprobado');
    $rechazadas = array_filter($inscripciones, fn($i) => $i['estado'] === 'rechazado');
    
} catch (PDOException $e) {
    error_log("Error al cargar inscripciones: " . $e->getMessage());
    $error = "Error al cargar las inscripciones";
}
?>

<div class="row mb-3">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon success">
                <i class="bi bi-check-circle"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo count($aprobadas); ?></h3>
                <p>Aprobadas</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon warning">
                <i class="bi bi-clock"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo count($pendientes); ?></h3>
                <p>Pendientes</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon danger">
                <i class="bi bi-x-circle"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo count($rechazadas); ?></h3>
                <p>Rechazadas</p>
            </div>
        </div>
    </div>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle me-2"></i><?php echo $error; ?>
    </div>
<?php endif; ?>

<?php if (empty($inscripciones)): ?>
    <div class="card">
        <div class="alert alert-info mb-0">
            <i class="bi bi-info-circle me-2"></i>
            No tienes inscripciones aún. 
            <a href="<?php echo BASE_URL; ?>views/materias_disponibles.php" class="alert-link">
                Explora materias disponibles
            </a>
        </div>
    </div>
<?php else: ?>
    <!-- Inscripciones Aprobadas -->
    <?php if (!empty($aprobadas)): ?>
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-check-circle me-2"></i>Materias Inscritas (Aprobadas)
                </h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Materia</th>
                            <th>Maestro</th>
                            <th>Cuatrimestre</th>
                            <th>Fecha Aprobación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($aprobadas as $inscripcion): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($inscripcion['materia_nombre']); ?></strong>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($inscripcion['maestro_nombre'] . ' ' . $inscripcion['maestro_apellido']); ?>
                                </td>
                                <td><?php echo htmlspecialchars($inscripcion['cuatrimestre'] ?? '-'); ?></td>
                                <td><?php echo formatearFecha($inscripcion['fecha_respuesta'], 'd/m/Y'); ?></td>
                                <td>
                                    <a href="<?php echo BASE_URL; ?>views/materias/detalle.php?id=<?php echo $inscripcion['id_materia']; ?>" 
                                       class="btn btn-sm btn-primary">
                                        <i class="bi bi-eye"></i> Ver
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <!-- Inscripciones Pendientes -->
    <?php if (!empty($pendientes)): ?>
        <div class="card mb-4">
            <div class="card-header bg-warning">
                <h5 class="card-title mb-0">
                    <i class="bi bi-clock me-2"></i>Solicitudes Pendientes
                </h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Materia</th>
                            <th>Maestro</th>
                            <th>Fecha Solicitud</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pendientes as $inscripcion): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($inscripcion['materia_nombre']); ?></strong>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($inscripcion['maestro_nombre'] . ' ' . $inscripcion['maestro_apellido']); ?>
                                </td>
                                <td><?php echo formatearFecha($inscripcion['fecha_solicitud']); ?></td>
                                <td>
                                    <span class="badge badge-pendiente">
                                        <i class="bi bi-clock"></i> Pendiente
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <!-- Inscripciones Rechazadas -->
    <?php if (!empty($rechazadas)): ?>
        <div class="card">
            <div class="card-header bg-danger text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-x-circle me-2"></i>Solicitudes Rechazadas
                </h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Materia</th>
                            <th>Maestro</th>
                            <th>Motivo</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rechazadas as $inscripcion): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($inscripcion['materia_nombre']); ?></strong>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($inscripcion['maestro_nombre'] . ' ' . $inscripcion['maestro_apellido']); ?>
                                </td>
                                <td>
                                    <?php echo $inscripcion['motivo_rechazo'] 
                                        ? htmlspecialchars($inscripcion['motivo_rechazo']) 
                                        : '<em class="text-muted">Sin motivo especificado</em>'; ?>
                                </td>
                                <td><?php echo formatearFecha($inscripcion['fecha_respuesta'], 'd/m/Y'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>