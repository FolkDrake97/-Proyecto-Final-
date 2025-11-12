<?php
/**
 * Dashboard del Maestro
 * Archivo: views/dashboard_maestro.php
 */

$pageTitle = 'Dashboard Maestro';
require_once '../includes/header.php';
require_once '../includes/conexion.php';

requerirRol('maestro');

$db = Conexion::getInstance()->getConexion();
$idMaestro = obtenerUsuarioId();

try {
    $stmt = $db->prepare("SELECT COUNT(*) as total FROM materias WHERE id_maestro = ? AND activa = 1");
    $stmt->execute([$idMaestro]);
    $totalMaterias = $stmt->fetch()['total'];
    
    $stmt = $db->prepare("
        SELECT COUNT(*) as total 
        FROM inscripciones i
        INNER JOIN materias m ON i.id_materia = m.id_materia
        WHERE m.id_maestro = ? AND i.estado = 'pendiente'
    ");
    $stmt->execute([$idMaestro]);
    $solicitudesPendientes = $stmt->fetch()['total'];
    
    $stmt = $db->prepare("
        SELECT COUNT(DISTINCT i.id_estudiante) as total
        FROM inscripciones i
        INNER JOIN materias m ON i.id_materia = m.id_materia
        WHERE m.id_maestro = ? AND i.estado = 'aprobado'
    ");
    $stmt->execute([$idMaestro]);
    $totalEstudiantes = $stmt->fetch()['total'];
    
    $stmt = $db->prepare("
        SELECT COUNT(*) as total
        FROM actividades a
        INNER JOIN materias m ON a.id_materia = m.id_materia
        WHERE m.id_maestro = ? 
        AND a.fecha_limite BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 7 DAY)
        AND a.activa = 1
    ");
    $stmt->execute([$idMaestro]);
    $actividadesPorVencer = $stmt->fetch()['total'];
    
    $stmt = $db->prepare("
        SELECT i.*, u.nombre, u.apellido, u.email, m.nombre as materia_nombre
        FROM inscripciones i
        INNER JOIN usuarios u ON i.id_estudiante = u.id_usuario
        INNER JOIN materias m ON i.id_materia = m.id_materia
        WHERE m.id_maestro = ? AND i.estado = 'pendiente'
        ORDER BY i.fecha_solicitud DESC
        LIMIT 5
    ");
    $stmt->execute([$idMaestro]);
    $solicitudesRecientes = $stmt->fetchAll();
    
    $stmt = $db->prepare("
        SELECT m.*, 
        (SELECT COUNT(*) FROM inscripciones WHERE id_materia = m.id_materia AND estado = 'aprobado') as total_estudiantes
        FROM materias m
        WHERE m.id_maestro = ? AND m.activa = 1
        ORDER BY m.nombre
    ");
    $stmt->execute([$idMaestro]);
    $materias = $stmt->fetchAll();
    
} catch (PDOException $e) {
    error_log("Error en dashboard maestro: " . $e->getMessage());
    $error = "Error al cargar datos";
}
?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon primary">
            <i class="bi bi-book"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $totalMaterias; ?></h3>
            <p>Materias Activas</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon warning">
            <i class="bi bi-person-check"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $solicitudesPendientes; ?></h3>
            <p>Solicitudes Pendientes</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon success">
            <i class="bi bi-people"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $totalEstudiantes; ?></h3>
            <p>Estudiantes Inscritos</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon danger">
            <i class="bi bi-clock"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $actividadesPorVencer; ?></h3>
            <p>Actividades por Vencer</p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="bi bi-person-check me-2"></i>Solicitudes de Inscripción
                </h5>
                <a href="<?php echo BASE_URL; ?>views/inscripciones/solicitudes.php" class="btn btn-sm btn-primary">
                    Ver Todas
                </a>
            </div>
            
            <?php if (empty($solicitudesRecientes)): ?>
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    No hay solicitudes pendientes
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Estudiante</th>
                                <th>Materia</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($solicitudesRecientes as $solicitud): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($solicitud['nombre'] . ' ' . $solicitud['apellido']); ?></strong><br>
                                        <small class="text-muted"><?php echo htmlspecialchars($solicitud['email']); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($solicitud['materia_nombre']); ?></td>
                                    <td><?php echo formatearFecha($solicitud['fecha_solicitud'], 'd/m/Y'); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-success" onclick="aprobarSolicitud(<?php echo $solicitud['id_inscripcion']; ?>)">
                                            <i class="bi bi-check"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="rechazarSolicitud(<?php echo $solicitud['id_inscripcion']; ?>)">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="bi bi-book me-2"></i>Mis Materias
                </h5>
                <a href="<?php echo BASE_URL; ?>views/materias/crear.php" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus"></i> Nueva
                </a>
            </div>
            
            <?php if (empty($materias)): ?>
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    No tienes materias registradas
                </div>
            <?php else: ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($materias as $materia): ?>
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1"><?php echo htmlspecialchars($materia['nombre']); ?></h6>
                                    <p class="mb-0 small text-muted">
                                        <i class="bi bi-people me-1"></i><?php echo $materia['total_estudiantes']; ?> estudiantes inscritos
                                    </p>
                                </div>
                                <a href="<?php echo BASE_URL; ?>views/materias/detalle.php?id=<?php echo $materia['id_materia']; ?>" class="btn btn-sm btn-outline-primary">
                                    Ver
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function aprobarSolicitud(idInscripcion) {
    if (confirm('¿Aprobar esta solicitud de inscripción?')) {
        fetch('<?php echo BASE_URL; ?>api/inscripciones/aprobar.php', {
            method: 'PUT',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({id_inscripcion: idInscripcion})
        })
        .then(response => response.json())
        .then(data => {
            if (data.exito) {
                location.reload();
            } else {
                alert('Error: ' + data.mensaje);
            }
        });
    }
}

function rechazarSolicitud(idInscripcion) {
    const motivo = prompt('¿Motivo del rechazo? (opcional)');
    if (motivo !== null) {
        fetch('<?php echo BASE_URL; ?>api/inscripciones/rechazar.php', {
            method: 'PUT',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({id_inscripcion: idInscripcion, motivo: motivo})
        })
        .then(response => response.json())
        .then(data => {
            if (data.exito) {
                location.reload();
            } else {
                alert('Error: ' + data.mensaje);
            }
        });
    }
}
</script>

<?php require_once '../includes/footer.php'; ?>