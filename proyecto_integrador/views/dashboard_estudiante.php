<?php
require_once '../includes/header.php';
?>

<div class="container mt-4">
    <h2>Dashboard Estudiante</h2>
    <p>Bienvenido <strong><?= $_SESSION['user_name'] ?></strong> al sistema como Estudiante</p>
    
    <div class="row mt-4">
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-primary">
                <div class="card-body text-center">
                    <h5>Materias</h5>
                    <p>Ver materias disponibles</p>
                    <a href="materias/lista.php" class="btn btn-light btn-sm">Acceder</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-success">
                <div class="card-body text-center">
                    <h5>Mis Tareas</h5>
                    <p>Ver mis tareas</p>
                    <a href="actividades/Estudiantes/asks.html" class="btn btn-light btn-sm">Acceder</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-info">
                <div class="card-body text-center">
                    <h5>Calificaciones</h5>
                    <p>Ver mis calificaciones</p>
                    <a href="calificaciones/mis_calificaciones.php" class="btn btn-light btn-sm">Acceder</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once '../includes/footer.php';
?>