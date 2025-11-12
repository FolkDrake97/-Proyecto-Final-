<?php
/**
 * Solicitudes de Inscripción - Maestro
 * Archivo: views/inscripciones/solicitudes.php
 */

$pageTitle = 'Solicitudes de Inscripción';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/conexion.php';

requerirRol('maestro');

$db = Conexion::getInstance()->getConexion();
$idMaestro = obtenerUsuarioId();

try {
    // Obtener solicitudes pendientes para las materias del maestro
    $stmt = $db->prepare("
        SELECT i.*, 
               u.nombre as estudiante_nombre, u.apellido as estudiante_apellido, u.email as estudiante_email,
               m.nombre as materia_nombre, m.id_materia
        FROM inscripciones i
        INNER JOIN usuarios u ON i.id_estudiante = u.id_usuario
        INNER JOIN materias m ON i.id_materia = m.id_materia
        WHERE m.id_maestro = ? AND i.estado = 'pendiente'
        ORDER BY i.fecha_solicitud DESC
    ");
    $stmt->execute([$idMaestro]);
    $solicitudes = $stmt->fetchAll();
    
} catch (PDOException $e) {
    error_log("Error al cargar solicitudes: " . $e->getMessage());
    $error = "Error al cargar las solicitudes";
}
?>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="bi bi-person-check me-2"></i>Solicitudes de Inscripción Pendientes
        </h5>
    </div>

    <div class="card-body">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle me-2"></i><?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if (empty($solicitudes)): ?>
            <div class="alert alert-success">
                <i class="bi bi-check-circle me-2"></i>
                No hay solicitudes de inscripción pendientes.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th>Email</th>
                            <th>Materia</th>
                            <th>Fecha Solicitud</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($solicitudes as $solicitud): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($solicitud['estudiante_nombre'] . ' ' . $solicitud['estudiante_apellido']); ?></strong>
                                </td>
                                <td><?php echo htmlspecialchars($solicitud['estudiante_email']); ?></td>
                                <td><?php echo htmlspecialchars($solicitud['materia_nombre']); ?></td>
                                <td><?php echo formatearFecha($solicitud['fecha_solicitud']); ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-success" onclick="aprobarSolicitud(<?php echo $solicitud['id_inscripcion']; ?>)">
                                            <i class="bi bi-check"></i> Aprobar
                                        </button>
                                        <button class="btn btn-danger" onclick="rechazarSolicitud(<?php echo $solicitud['id_inscripcion']; ?>)">
                                            <i class="bi bi-x"></i> Rechazar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function aprobarSolicitud(idInscripcion) {
    if (confirm('¿Estás seguro de aprobar esta solicitud de inscripción?')) {
        fetch('<?php echo BASE_URL; ?>api/inscripciones/aprobar.php', {
            method: 'PUT',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({id_inscripcion: idInscripcion})
        })
        .then(response => response.json())
        .then(data => {
            if (data.exito) {
                Notificacion.exito(data.mensaje);
                setTimeout(() => location.reload(), 1500);
            } else {
                Notificacion.error(data.mensaje);
            }
        })
        .catch(error => {
            Notificacion.error('Error de conexión');
        });
    }
}

function rechazarSolicitud(idInscripcion) {
    const motivo = prompt('Ingresa el motivo del rechazo (opcional):');
    if (motivo !== null) {
        fetch('<?php echo BASE_URL; ?>api/inscripciones/rechazar.php', {
            method: 'PUT',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                id_inscripcion: idInscripcion,
                motivo: motivo
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.exito) {
                Notificacion.exito(data.mensaje);
                setTimeout(() => location.reload(), 1500);
            } else {
                Notificacion.error(data.mensaje);
            }
        })
        .catch(error => {
            Notificacion.error('Error de conexión');
        });
    }
}
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>