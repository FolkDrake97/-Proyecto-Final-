<?php
/**
 * Lista de Materias
 * Archivo: views/materias/lista.php
 */

// 1. Definir ROOT_PATH
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(__DIR__)));
}

// 2. Incluir helpers
require_once ROOT_PATH . '/includes/helpers.php';
require_once ROOT_PATH . '/includes/conexion.php';

// 3. Iniciar sesión
iniciarSesionSegura();

// 4. Verificar autenticación (cualquier rol puede ver)
requerirAutenticacion();

// 5. Variables
$pageTitle = 'Materias';
$rol = obtenerRol();

// 6. Lógica PHP
$db = Conexion::getInstance()->getConexion();

try {
    if ($rol === 'estudiante') {
        // Obtener materias activas
        $stmt = $db->prepare("
            SELECT m.*, u.nombre as maestro_nombre, u.apellido as maestro_apellido
            FROM materias m
            LEFT JOIN usuarios u ON m.id_maestro = u.id_usuario
            WHERE m.activa = 1
            ORDER BY m.nombre
        ");
        $stmt->execute();
    } elseif ($rol === 'maestro') {
        // Obtener solo materias del maestro
        $idMaestro = obtenerUsuarioId();
        $stmt = $db->prepare("
            SELECT m.*, u.nombre as maestro_nombre, u.apellido as maestro_apellido
            FROM materias m
            LEFT JOIN usuarios u ON m.id_maestro = u.id_usuario
            WHERE m.id_maestro = ? AND m.activa = 1
            ORDER BY m.nombre
        ");
        $stmt->execute(array($idMaestro));
    } else {
        // Administrador ve todas
        $stmt = $db->prepare("
            SELECT m.*, u.nombre as maestro_nombre, u.apellido as maestro_apellido
            FROM materias m
            LEFT JOIN usuarios u ON m.id_maestro = u.id_usuario
            WHERE m.activa = 1
            ORDER BY m.nombre
        ");
        $stmt->execute();
    }
    
    $materias = $stmt->fetchAll();
    
} catch (PDOException $e) {
    error_log("Error al cargar materias: " . $e->getMessage());
    $error = "Error al cargar las materias";
}

// 7. Incluir header
require_once ROOT_PATH . '/includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>
                <?php 
                if ($rol === 'maestro') {
                    echo 'Mis Materias';
                } else {
                    echo 'Materias Disponibles';
                }
                ?>
            </h2>
            <?php if ($rol === 'administrador'): ?>
                <a href="<?php echo BASE_URL; ?>/views/materias/crear.php" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Nueva Materia
                </a>
            <?php endif; ?>
        </div>

        <div class="card">
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i><?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <?php if (empty($materias)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-book fa-3x text-muted mb-3"></i>
                        <h5>No hay materias disponibles</h5>
                        <p class="text-muted">No se han registrado materias en el sistema.</p>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($materias as $materia): ?>
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($materia['nombre']); ?></h5>
                                    <p class="card-text"><?php echo htmlspecialchars($materia['descripcion'] ?? 'Sin descripción'); ?></p>
                                    <p class="card-text">
                                        <small class="text-muted">
                                            <strong>Créditos:</strong> <?php echo $materia['creditos']; ?><br>
                                            <strong>Maestro:</strong> 
                                            <?php 
                                            if ($materia['maestro_nombre']) {
                                                echo htmlspecialchars($materia['maestro_nombre'] . ' ' . $materia['maestro_apellido']);
                                            } else {
                                                echo 'No asignado';
                                            }
                                            ?>
                                        </small>
                                    </p>
                                    
                                    <?php if ($rol === 'estudiante'): ?>
                                        <button class="btn btn-primary btn-sm" onclick="solicitarInscripcion(<?php echo $materia['id_materia']; ?>)">
                                            <i class="bi bi-person-plus me-1"></i>Solicitar Inscripción
                                        </button>
                                    <?php elseif ($rol === 'maestro'): ?>
                                        <a href="<?php echo BASE_URL; ?>/views/actividades/lista.php?materia=<?php echo $materia['id_materia']; ?>" 
                                           class="btn btn-primary btn-sm">
                                            <i class="bi bi-list-task me-1"></i>Ver Actividades
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if ($rol === 'estudiante'): ?>
<script>
function solicitarInscripcion(materiaId) {
    if (confirm('¿Deseas solicitar inscripción a esta materia?')) {
        fetch('<?php echo BASE_URL; ?>/api/inscripciones/solicitar.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({id_materia: materiaId})
        })
        .then(response => response.json())
        .then(data => {
            if (data.exito) {
                alert('✓ ' + data.mensaje);
            } else {
                alert('✗ ' + data.mensaje);
            }
        })
        .catch(error => {
            alert('Error de conexión');
            console.error(error);
        });
    }
}
</script>
<?php endif; ?>

<?php require_once ROOT_PATH . '/includes/footer.php'; ?>