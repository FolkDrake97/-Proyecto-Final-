<?php
/**
 * Materias disponibles para inscripción (Estudiante)
 * Archivo: views/materias_disponibles.php
 */

$pageTitle = 'Materias Disponibles';
require_once '../includes/header.php';
require_once '../includes/conexion.php';

requerirRol('estudiante');

$db = Conexion::getInstance()->getConexion();
$idEstudiante = obtenerUsuarioId();

try {
    $stmt = $db->prepare("
        SELECT m.*, 
               u.nombre as maestro_nombre, 
               u.apellido as maestro_apellido,
               (SELECT COUNT(*) FROM inscripciones WHERE id_materia = m.id_materia AND estado = 'aprobado') as total_inscritos
        FROM materias m
        INNER JOIN usuarios u ON m.id_maestro = u.id_usuario
        WHERE m.activa = 1
        AND m.id_materia NOT IN (
            SELECT id_materia FROM inscripciones WHERE id_estudiante = ?
        )
        ORDER BY m.nombre
    ");
    $stmt->execute([$idEstudiante]);
    $materias = $stmt->fetchAll();
    
} catch (PDOException $e) {
    error_log("Error al cargar materias: " . $e->getMessage());
    $error = "Error al cargar las materias";
}
?>

<div class="card">
    <div class="card-header">
        <h5 class="card-title">
            <i class="bi bi-book me-2"></i>Materias Disponibles
        </h5>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger m-3">
            <i class="bi bi-exclamation-triangle me-2"></i><?php echo $error; ?>
        </div>
    <?php endif; ?>

    <?php if (empty($materias)): ?>
        <div class="alert alert-info m-3">
            <i class="bi bi-info-circle me-2"></i>
            No hay materias disponibles en este momento o ya estás inscrito en todas.
        </div>
    <?php else: ?>
        <div class="row g-4 p-4">
            <?php foreach ($materias as $materia): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-primary">
                                <?php echo htmlspecialchars($materia['nombre']); ?>
                            </h5>
                            
                            <?php if ($materia['descripcion']): ?>
                                <p class="card-text text-muted small">
                                    <?php echo htmlspecialchars(substr($materia['descripcion'], 0, 100)); ?>
                                    <?php echo strlen($materia['descripcion']) > 100 ? '...' : ''; ?>
                                </p>
                            <?php endif; ?>
                            
                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="bi bi-person me-1"></i>
                                    <strong>Maestro:</strong> 
                                    <?php echo htmlspecialchars($materia['maestro_nombre'] . ' ' . $materia['maestro_apellido']); ?>
                                </small>
                            </div>
                            
                            <?php if ($materia['cuatrimestre']): ?>
                                <div class="mb-2">
                                    <small class="text-muted">
                                        <i class="bi bi-calendar me-1"></i>
                                        <strong>Cuatrimestre:</strong> 
                                        <?php echo htmlspecialchars($materia['cuatrimestre']); ?>
                                    </small>
                                </div>
                            <?php endif; ?>
                            
                            <div class="mb-3">
                                <small class="text-muted">
                                    <i class="bi bi-people me-1"></i>
                                    <strong>Inscritos:</strong> 
                                    <?php echo $materia['total_inscritos']; ?> estudiantes
                                </small>
                            </div>
                            
                            <button class="btn btn-primary w-100" onclick="solicitarInscripcion(<?php echo $materia['id_materia']; ?>)">
                                <i class="bi bi-plus-circle me-2"></i>Solicitar Inscripción
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
async function solicitarInscripcion(idMateria) {
    if (!confirm('¿Deseas solicitar inscripción a esta materia?')) {
        return;
    }
    
    Loader.mostrar();
    
    try {
        const response = await fetch('<?php echo BASE_URL; ?>api/inscripciones/solicitar.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({id_materia: idMateria})
        });
        
        const resultado = await response.json();
        
        Loader.ocultar();
        
        if (resultado.exito) {
            Notificacion.exito(resultado.mensaje);
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            Notificacion.error(resultado.mensaje);
        }
    } catch (error) {
        Loader.ocultar();
        Notificacion.error('Error al enviar la solicitud');
    }
}
</script>

<?php require_once '../includes/footer.php'; ?>