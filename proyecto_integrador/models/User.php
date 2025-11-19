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
    public $fecha_registro;
    public $activo;
    public $foto_perfil;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Obtener usuario por ID
     */
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id_usuario = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener usuario por email
     */
    public function getByEmail($email) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Verificar si un email ya existe
     */
    public function emailExists($email = null) {
        $emailToCheck = $email ?: $this->email;
        $query = "SELECT id_usuario FROM " . $this->table . " WHERE email = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$emailToCheck]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Registrar nuevo usuario (solo estudiantes desde registro público)
     */
    public function register() {
        if ($this->emailExists()) {
            return false;
        }

        $query = "INSERT INTO " . $this->table . " 
                  (nombre, apellido, email, password, rol) 
                  VALUES (?, ?, ?, ?, 'estudiante')";
        
        $stmt = $this->conn->prepare($query);
        
        // Hash de contraseña
        $hashedPassword = password_hash($this->password, PASSWORD_BCRYPT, ['cost' => 12]);
        
        if ($stmt->execute([$this->nombre, $this->apellido, $this->email, $hashedPassword])) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    /**
     * Login de usuario (DEPRECADO - usa lógica en login.php)
     * Mantener solo para compatibilidad temporal
     */
    public function login() {
        $query = "SELECT id_usuario, nombre, apellido, email, password, rol, activo 
                  FROM " . $this->table . " 
                  WHERE email = ? AND activo = 1 LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$this->email]);

        if ($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verificar contraseña
            if (password_verify($this->password, $row['password']) || $this->password === $row['password']) {
                $this->id = $row['id_usuario'];
                $this->nombre = $row['nombre'];
                $this->apellido = $row['apellido'];
                $this->email = $row['email'];
                $this->rol = $row['rol'];
                $this->activo = $row['activo'];
                return true;
            }
        }
        return false;
    }

    /**
     * Obtener todos los usuarios activos
     */
    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " WHERE activo = 1 ORDER BY fecha_registro DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener usuarios por rol
     */
    public function getByRole($rol) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE rol = ? AND activo = 1 
                  ORDER BY nombre, apellido";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$rol]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener maestros
     */
    public function getTeachers() {
        return $this->getByRole('maestro');
    }

    /**
     * Obtener estudiantes
     */
    public function getStudents() {
        return $this->getByRole('estudiante');
    }

    /**
     * Obtener total de usuarios
     */
    public function getTotalUsuarios() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE activo = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    /**
     * Obtener total por rol
     */
    public function getTotalPorRol($rol) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " 
                  WHERE rol = ? AND activo = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$rol]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    /**
     * Obtener estadísticas completas
     */
    public function getUserStats() {
        return [
            'total' => $this->getTotalUsuarios(),
            'estudiantes' => $this->getTotalPorRol('estudiante'),
            'maestros' => $this->getTotalPorRol('maestro'),
            'administradores' => $this->getTotalPorRol('administrador')
        ];
    }

    /**
     * Actualizar perfil de usuario
     */
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

    /**
     * Cambiar contraseña
     */
    public function changePassword($id, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        $query = "UPDATE " . $this->table . " SET password = ? WHERE id_usuario = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$hashedPassword, $id]);
    }

    /**
     * Desactivar usuario (eliminación lógica)
     */
    public function deactivate($id) {
        $query = "UPDATE " . $this->table . " SET activo = 0 WHERE id_usuario = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }

    /**
     * Obtener nombre completo
     */
    public function getFullName() {
        return $this->nombre . ' ' . $this->apellido;
    }
}