<?php
$page_title = "Gestión de Usuarios";
require_once '../../includes/header.php';

require_once '../../config/database.php';
require_once '../../models/User.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

// Obtener todos los usuarios
$usuarios = $user->getAll();
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Gestión de Usuarios</h2>
                <a href="crear.php" class="btn btn-primary">
                    <i class="fas fa-user-plus me-2"></i>Nuevo Usuario
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <?php if (empty($usuarios)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5>No hay usuarios registrados</h5>
                            <p class="text-muted">Comienza creando el primer usuario del sistema.</p>
                            <a href="crear.php" class="btn btn-primary">Crear Usuario</a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Email</th>
                                        <th>Rol</th>
                                        <th>Fecha Registro</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($usuarios as $usuario): ?>
                                    <tr>
                                        <td><?= $usuario['id_usuario'] ?></td>
                                        <td>
                                            <strong><?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']) ?></strong>
                                        </td>
                                        <td><?= htmlspecialchars($usuario['email']) ?></td>
                                        <td>
                                            <span class="badge 
                                                <?= $usuario['rol'] === 'administrador' ? 'bg-danger' : 
                                                   ($usuario['rol'] === 'maestro' ? 'bg-warning' : 'bg-info') ?>">
                                                <?= ucfirst($usuario['rol']) ?>
                                            </span>
                                        </td>
                                        <td><?= date('d/m/Y', strtotime($usuario['fecha_registro'])) ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="editar.php?id=<?= $usuario['id_usuario'] ?>" 
                                                   class="btn btn-outline-primary" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="eliminar.php?id=<?= $usuario['id_usuario'] ?>" 
                                                   class="btn btn-outline-danger" 
                                                   onclick="return confirm('¿Estás seguro de eliminar este usuario?')"
                                                   title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </a>
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
        </div>
    </div>
</div>

<?php
require_once '../../includes/footer.php';
?>