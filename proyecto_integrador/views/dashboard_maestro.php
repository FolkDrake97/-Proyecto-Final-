<?php
require_once '../includes/header.php';
?>

<div class="container mt-4">
    <h2>Dashboard Maestro</h2>
    <p>Bienvenido <strong><?= $_SESSION['user_name'] ?></strong> al sistema como Maestro</p>
    
    <div class="row mt-4">
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-primary">
                <div class="card-body text-center">
                    <h5>Mis Materias</h5>
                    <p>Gestionar mis materias</p>
                    <a href="materias/lista.php" class="btn btn-light btn-sm">Acceder</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-success">
                <div class="card-body text-center">
                    <h5>Solicitudes</h5>
                    <p>Revisar inscripciones</p>
                    <a href="inscripciones/solicitudes.php" class="btn btn-light btn-sm">Acceder</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-info">
                <div class="card-body text-center">
                    <h5>Tareas</h5>
                    <p>Crear y calificar tareas</p>
                    <a href="actividades/lista.php" class="btn btn-light btn-sm">Acceder</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once '../includes/footer.php';
?>