<?php
require_once __DIR__ . '/../../bootstrap.php';
require_once ROOT_DIR . '/models/Subject.php';

requireAuth(); // Cualquier usuario autenticado puede ver

$page_title = "Materias";

$database = new Database();
$db = $database->getConnection();
$subject = new Subject($db);

// Obtener materias según el rol
if ($_SESSION['user_role'] === 'estudiante') {
    $materias = $subject->getAllActive();
} else {
    $materias = $subject->getAll();
}

require_once ROOT_DIR . '/includes/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Materias Disponibles</h2>
                <?php if ($_SESSION['user_role'] !== 'estudiante'): ?>
                <a href="crear.php" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Nueva Materia
                </a>
                <?php endif; ?>
            </div>

            <div class="card">
                <div class="card-body">
                    <?php if (empty($materias)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-book fa-3x text-muted mb-3"></i>
                            <h5>No hay materias disponibles</h5>
                            <p class="text-muted">No se han registrado materias en el sistema.</p>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($materias as $materia): ?>
                            <div class="col-md-6 mb-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars($materia['nombre']) ?></h5>
                                        <p class="card-text"><?= htmlspecialchars($materia['descripcion'] ?? 'Sin descripción') ?></p>
                                        <p class="card-text">
                                            <small class="text-muted">
                                                <strong>Créditos:</strong> <?= $materia['creditos'] ?><br>
                                                <strong>Maestro:</strong> <?= htmlspecialchars($materia['maestro_nombre'] ?? 'No asignado') ?>
                                            </small>
                                        </p>
                                        
                                        <?php if ($_SESSION['user_role'] === 'estudiante'): ?>
                                        <button class="btn btn-primary btn-sm" onclick="solicitarInscripcion(<?= $materia['id'] ?>)">
                                            Solicitar Inscripción
                                        </button>
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
</div>

<script>
function solicitarInscripcion(materiaId) {
    if (confirm('¿Deseas solicitar inscripción a esta materia?')) {
        alert('Solicitud enviada. El maestro debe aprobar tu inscripción.');
        // Aquí iría la llamada AJAX para enviar la solicitud
    }
}
</script>

<?php
require_once ROOT_DIR . '/includes/footer.php';
?>