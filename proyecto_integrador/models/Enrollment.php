<?php
class Enrollment {
    private $conn;
    private $table = 'inscripciones';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Crear solicitud de inscripción
    public function create($studentId, $subjectId) {
        $query = "INSERT INTO " . $this->table . " 
                  (id_estudiante, id_materia, estado, fecha_solicitud)
                  VALUES (?, ?, 'pendiente', NOW())";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$studentId, $subjectId]);
    }

    // Actualizar estado
    public function updateStatus($id, $status, $motivo = null) {
        $query = "UPDATE " . $this->table . " 
                  SET estado = ?, motivo_rechazo = ?, fecha_respuesta = NOW()
                  WHERE id_inscripcion = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$status, $motivo, $id]);
    }
}
?>