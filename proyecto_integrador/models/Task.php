<?php
class Task {
    private $conn;
    private $table = 'tareas';

    public $id;
    public $id_materia;
    public $titulo;
    public $descripcion;
    public $fecha_creacion;
    public $fecha_limite;
    public $ponderacion;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Métodos básicos para reportes
    public function getTotalTareas() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function getTareasPendientes() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " 
                  WHERE fecha_limite >= CURDATE()";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function getTaskStats() {
        return [
            'total' => $this->getTotalTareas(),
            'pendientes' => $this->getTareasPendientes(),
            'vencidas' => $this->getTareasVencidas()
        ];
    }

    private function getTareasVencidas() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " 
                  WHERE fecha_limite < CURDATE()";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
}
?>