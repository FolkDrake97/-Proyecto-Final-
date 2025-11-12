<?php
/**
 * Lista de Actividades - Maestro
 * Archivo: views/actividades/lista.php
 */

$pageTitle = 'Gestión de Actividades';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/conexion.php';

requerirRol('maestro');

$db = Conexion::getInstance()->getConexion();
$idMaestro = obtenerUsuarioId();

try {
    // Obtener actividades del maestro
    $stmt = $db->prepare("
        SELECT a.*, m.nombre as materia_nombre,
               COUNT(DISTINCT c.id_calificacion) as total_entregas,
               COUNT(DISTINCT CASE WHEN c.calificacion IS NOT NULL THEN c.id_calificacion END) as total_calificadas
        FROM actividades a
        INNER JOIN materias m ON a.id_materia = m.id_materia
        LEFT JOIN calificaciones c ON a.id_actividad = c.id_actividad
        WHERE m.id_maestro = ? AND a.activa = 1
        GROUP BY a.id_actividad
        ORDER BY a.fecha_limite ASC
    ");
    $stmt->execute([$idMaestro]);
    $actividades = $stmt->fetchAll();
    
    // Obtener materias del maestro para el formulario
    $stmt = $db->prepare("
        SELECT id_materia, nombre 
        FROM materias 
        WHERE id_maestro = ? AND activa = 1
        ORDER BY nombre
    ");
    $stmt->execute([$idMaestro]);
    $materias = $stmt->fetchAll();
    
} catch (PDOException $e) {
    error_log("Error al cargar actividades: " . $e->getMessage());
    $error = "Error al cargar las actividades";
}
?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            <i class="bi bi-list-task me-2"></i>Gestión de Actividades
        </h5>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalCrearActividad">
            <i class="bi bi-plus-circle me-2"></i>Nueva Actividad
        </button>
    </div>

    <div class="card-body">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle me-2"></i><?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if (empty($actividades)): ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                No hay actividades creadas. 
                <a href="#" class="alert-link" data-bs-toggle="modal" data-bs-target="#modalCrearActividad">
                    Crea tu primera actividad
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Materia</th>
                            <th>Fecha Límite</th>
                            <th>Tipo</th>
                            <th>Ponderación</th>
                            <th>Entregas</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($actividades as $actividad): 
                            $diasRestantes = diasRestantes($actividad['fecha_limite']);
                            $urgente = $diasRestantes <= 2 && $diasRestantes >= 0;
                        ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($actividad['titulo']); ?></strong>
                                    <?php if ($actividad['descripcion']): ?>
                                        <br>
                                        <small class="text-muted">
                                            <?php echo htmlspecialchars(substr($actividad['descripcion'], 0, 50)); ?>
                                            <?php echo strlen($actividad['descripcion']) > 50 ? '...' : ''; ?>
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($actividad['materia_nombre']); ?></td>
                                <td>
                                    <span class="<?php echo $urgente ? 'text-danger fw-bold' : 'text-muted'; ?>">
                                        <?php echo formatearFecha($actividad['fecha_limite']); ?>
                                    </span>
                                    <br>
                                    <small class="text-muted">
                                        <?php 
                                        if ($diasRestantes < 0) {
                                            echo '<span class="text-danger">Vencida</span>';
                                        } elseif ($diasRestantes == 0) {
                                            echo '<span class="text-warning">Hoy</span>';
                                        } elseif ($diasRestantes == 1) {
                                            echo 'Mañana';
                                        } else {
                                            echo "En $diasRestantes días";
                                        }
                                        ?>
                                    </small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary"><?php echo ucfirst($actividad['tipo']); ?></span>
                                </td>
                                <td><?php echo $actividad['ponderacion']; ?>%</td>
                                <td>
                                    <small>
                                        <?php echo $actividad['total_calificadas']; ?>/<?php echo $actividad['total_entregas']; ?> calificadas
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" 
                                                onclick="verActividad(<?php echo $actividad['id_actividad']; ?>)">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-warning" 
                                                onclick="editarActividad(<?php echo $actividad['id_actividad']; ?>)">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" 
                                                onclick="eliminarActividad(<?php echo $actividad['id_actividad']; ?>)">
                                            <i class="bi bi-trash"></i>
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

<!-- Modal Crear Actividad -->
<div class="modal fade" id="modalCrearActividad" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crear Nueva Actividad</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formCrearActividad">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Materia</label>
                                <select class="form-select" name="id_materia" required>
                                    <option value="">Seleccionar materia</option>
                                    <?php foreach ($materias as $materia): ?>
                                        <option value="<?php echo $materia['id_materia']; ?>">
                                            <?php echo htmlspecialchars($materia['nombre']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tipo de Actividad</label>
                                <select class="form-select" name="tipo" required>
                                    <option value="tarea">Tarea</option>
                                    <option value="examen">Examen</option>
                                    <option value="proyecto">Proyecto</option>
                                    <option value="participacion">Participación</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Título</label>
                        <input type="text" class="form-control" name="titulo" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" name="descripcion" rows="3"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Fecha Límite</label>
                                <input type="datetime-local" class="form-control" name="fecha_limite" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Ponderación (%)</label>
                                <input type="number" class="form-control" name="ponderacion" min="1" max="100" step="0.5" required>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="crearActividad()">Crear Actividad</button>
            </div>
        </div>
    </div>
</div>

<script>
function crearActividad() {
    const form = document.getElementById('formCrearActividad');
    const formData = new FormData(form);
    
    fetch('<?php echo BASE_URL; ?>api/actividades/crear.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.exito) {
            Notificacion.exito(data.mensaje);
            $('#modalCrearActividad').modal('hide');
            setTimeout(() => location.reload(), 1500);
        } else {
            Notificacion.error(data.mensaje);
        }
    })
    .catch(error => {
        Notificacion.error('Error de conexión');
    });
}

function verActividad(idActividad) {
    window.location.href = '<?php echo BASE_URL; ?>views/actividades/detalle.php?id=' + idActividad;
}

function editarActividad(idActividad) {
    Notificacion.info('Funcionalidad en desarrollo');
}

function eliminarActividad(idActividad) {
    if (confirm('¿Estás seguro de eliminar esta actividad?')) {
        Notificacion.info('Funcionalidad en desarrollo');
    }
}
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>