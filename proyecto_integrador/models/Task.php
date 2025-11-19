<?php
cclass Task {
    private $conn;
    private $table = 'actividades';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Obtener tarea por ID
    public function getById($taskId) {
        $query = "SELECT a.*, m.nombre as materia_nombre 
                  FROM " . $this->table . " a
                  INNER JOIN materias m ON a.id_materia = m.id_materia
                  WHERE a.id_actividad = ? 
                  LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$taskId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtener tareas por materia
    public function getBySubject($subjectId) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE id_materia = ? AND activa = 1 
                  ORDER BY fecha_limite DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$subjectId]);
        return $stmt;
    }

    // Obtener tareas para un estudiante
    public function getForStudent($studentId, $subjectId = null) {
        if ($subjectId) {
            $query = "SELECT a.*, m.nombre as materia_nombre,
                             c.calificacion, c.fecha_entrega, c.comentarios
                      FROM " . $this->table . " a
                      INNER JOIN materias m ON a.id_materia = m.id_materia
                      INNER JOIN inscripciones i ON m.id_materia = i.id_materia
                      LEFT JOIN calificaciones c ON a.id_actividad = c.id_actividad 
                            AND c.id_estudiante = ?
                      WHERE i.id_estudiante = ? 
                        AND i.estado = 'aprobado'
                        AND a.id_materia = ?
                        AND a.activa = 1
                      ORDER BY a.fecha_limite DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$studentId, $studentId, $subjectId]);
        } else {
            $query = "SELECT a.*, m.nombre as materia_nombre,
                             c.calificacion, c.fecha_entrega, c.comentarios
                      FROM " . $this->table . " a
                      INNER JOIN materias m ON a.id_materia = m.id_materia
                      INNER JOIN inscripciones i ON m.id_materia = i.id_materia
                      LEFT JOIN calificaciones c ON a.id_actividad = c.id_actividad 
                            AND c.id_estudiante = ?
                      WHERE i.id_estudiante = ? 
                        AND i.estado = 'aprobado'
                        AND a.activa = 1
                      ORDER BY a.fecha_limite DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$studentId, $studentId]);
        }
        return $stmt;
    }

    // Crear nueva tarea
    public function create($data) {
        $query = "INSERT INTO " . $this->table . "
                  (id_materia, titulo, descripcion, fecha_limite, ponderacion, tipo)
                  VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            $data['id_materia'],
            $data['titulo'],
            $data['descripcion'],
            $data['fecha_limite'],
            $data['ponderacion'],
            $data['tipo'] ?? 'tarea'
        ]);
    }
}?>