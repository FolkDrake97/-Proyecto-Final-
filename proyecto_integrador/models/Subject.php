<?php
class Subject {
    private $conn;
    private $table = 'materias';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Obtener todas las materias activas
    public function getAll() {
        $query = "SELECT m.*, u.nombre as maestro_nombre, u.apellido as maestro_apellido 
                  FROM " . $this->table . " m 
                  LEFT JOIN usuarios u ON m.id_maestro = u.id_usuario 
                  WHERE m.activa = 1 
                  ORDER BY m.nombre";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener materia por ID
    public function getById($id) {
        $query = "SELECT m.*, u.nombre as maestro_nombre, u.apellido as maestro_apellido 
                  FROM " . $this->table . " m 
                  LEFT JOIN usuarios u ON m.id_maestro = u.id_usuario 
                  WHERE m.id_materia = ? AND m.activa = 1 
                  LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtener materias por maestro
    public function getByTeacher($teacherId) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE id_maestro = ? AND activa = 1 
                  ORDER BY nombre";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$teacherId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>