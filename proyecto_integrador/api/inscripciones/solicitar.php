<?php
/**
 * API: Solicitar inscripción a una materia
 * Archivo: api/inscripciones/solicitar.php
 * Método: POST
 */

header('Content-Type: application/json; charset=utf-8');
require_once '../../config/database.php';
require_once '../../includes/helpers.php';
require_once '../../includes/conexion.php';

iniciarSesionSegura();

// Verificar autenticación y rol de estudiante
if (!estaAutenticado() || !tieneRol('estudiante')) {
    respuestaJSON(false, null, 'No autorizado', 403);
}

// Solo aceptar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respuestaJSON(false, null, 'Método no permitido', 405);
}

// Obtener datos JSON
$input = json_decode(file_get_contents('php://input'), true);
$idMateria = $input['id_materia'] ?? null;

if (!$idMateria) {
    respuestaJSON(false, null, 'ID de materia requerido', 400);
}

try {
    $db = Conexion::getInstance()->getConexion();
    $idEstudiante = obtenerUsuarioId();
    
    // Verificar que la materia existe y está activa
    $stmt = $db->prepare("SELECT * FROM materias WHERE id_materia = ? AND activa = 1");
    $stmt->execute([$idMateria]);
    $materia = $stmt->fetch();
    
    if (!$materia) {
        respuestaJSON(false, null, 'Materia no encontrada o inactiva', 404);
    }
    
    // Verificar si ya existe una solicitud
    $stmt = $db->prepare("
        SELECT * FROM inscripciones 
        WHERE id_estudiante = ? AND id_materia = ?
    ");
    $stmt->execute([$idEstudiante, $idMateria]);
    $inscripcionExistente = $stmt->fetch();
    
    if ($inscripcionExistente) {
        $estado = $inscripcionExistente['estado'];
        if ($estado === 'pendiente') {
            respuestaJSON(false, null, 'Ya tienes una solicitud pendiente para esta materia', 400);
        } elseif ($estado === 'aprobado') {
            respuestaJSON(false, null, 'Ya estás inscrito en esta materia', 400);
        } elseif ($estado === 'rechazado') {
            respuestaJSON(false, null, 'Tu solicitud fue rechazada anteriormente. Contacta al maestro.', 400);
        }
    }
    
    // Crear la solicitud
    $stmt = $db->prepare("
        INSERT INTO inscripciones (id_estudiante, id_materia, estado, fecha_solicitud)
        VALUES (?, ?, 'pendiente', NOW())
    ");
    
    if ($stmt->execute([$idEstudiante, $idMateria])) {
        respuestaJSON(true, [
            'id_inscripcion' => $db->lastInsertId(),
            'id_materia' => $idMateria
        ], 'Solicitud enviada exitosamente. Espera la aprobación del maestro.');
    } else {
        respuestaJSON(false, null, 'Error al enviar la solicitud', 500);
    }
    
} catch (PDOException $e) {
    error_log("Error al solicitar inscripción: " . $e->getMessage());
    respuestaJSON(false, null, 'Error del servidor', 500);
}
?>