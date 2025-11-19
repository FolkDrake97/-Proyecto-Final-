<?php
class User {
    private $conn;
    private $table = 'usuarios';

    public $id;
    public $nombre;
    public $apellido;
    public $email;
    public $password;
    public $rol;
    public $activo;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Obtener usuario por ID
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id_usuario = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtener usuario por email
    public function getByEmail($email) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Verificar si email existe
    public function emailExists($email = null) {
        $emailToCheck = $email ?: $this->email;
        $query = "SELECT id_usuario FROM " . $this->table . " WHERE email = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$emailToCheck]);
        return $stmt->rowCount() > 0;
    }

    // Registrar nuevo usuario
    public function register() {
        if ($this->emailExists()) {
            return false;
        }

        $query = "INSERT INTO " . $this->table . " 
                  (nombre, apellido, email, password, rol) 
                  VALUES (?, ?, ?, ?, 'estudiante')";
        
        $stmt = $this->conn->prepare($query);
        $hashedPassword = password_hash($this->password, PASSWORD_BCRYPT, ['cost' => 12]);
        
        if ($stmt->execute([$this->nombre, $this->apellido, $this->email, $hashedPassword])) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Obtener todos los usuarios activos
    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " WHERE activo = 1 ORDER BY fecha_registro DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener usuarios por rol
    public function getByRole($rol) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE rol = ? AND activo = 1 
                  ORDER BY nombre, apellido";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$rol]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener maestros
    public function getTeachers() {
        return $this->getByRole('maestro');
    }

    // Obtener estudiantes
    public function getStudents() {
        return $this->getByRole('estudiante');
    }

    // Actualizar usuario
    public function update($id, $data) {
        $fields = [];
        $values = [];
        
        foreach ($data as $key => $value) {
            if (in_array($key, ['nombre', 'apellido', 'email', 'rol', 'activo'])) {
                $fields[] = "$key = ?";
                $values[] = $value;
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $values[] = $id;
        $query = "UPDATE " . $this->table . " SET " . implode(', ', $fields) . " WHERE id_usuario = ?";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($values);
    }

    // Cambiar contraseña
    public function changePassword($id, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        $query = "UPDATE " . $this->table . " SET password = ? WHERE id_usuario = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$hashedPassword, $id]);
    }

    // Desactivar usuario
    public function deactivate($id) {
        $query = "UPDATE " . $this->table . " SET activo = 0 WHERE id_usuario = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }
}