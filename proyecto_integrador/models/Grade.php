<?php
class Grade {
    private $conn;
    private $table = 'calificaciones';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Registrar o actualizar calificación
    public function save($data) {
        // Verificar si ya existe
        $stmt = $this->conn->prepare("
            SELECT id_calificacion FROM " . $this->table . " 
            WHERE id_estudiante = ? AND id_actividad = ?
        ");
        $stmt->execute([$data['id_estudiante'], $data['id_actividad']]);
        
        if ($stmt->fetch()) {
            // Actualizar
            $query = "UPDATE " . $this->table . " 
                      SET calificacion = ?, comentarios = ?, fecha_calificacion = NOW()
                      WHERE id_estudiante = ? AND id_actividad = ?";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([
                $data['calificacion'],
                $data['comentarios'] ?? '',
                $data['id_estudiante'],
                $data['id_actividad']
            ]);
        } else {
            // Insertar
            $query = "INSERT INTO " . $this->table . " 
                      (id_estudiante, id_actividad, calificacion, comentarios, fecha_calificacion)
                      VALUES (?, ?, ?, ?, NOW())";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([
                $data['id_estudiante'],
                $data['id_actividad'],
                $data['calificacion'],
                $data['comentarios'] ?? ''
            ]);
        }
    }
}
?>