-- Base de datos: plataforma_academica
CREATE DATABASE IF NOT EXISTS plataforma_academica;
USE plataforma_academica;

-- Tabla de usuarios (administradores, maestros y estudiantes)
CREATE TABLE usuarios (
    id_usuario INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol ENUM('administrador', 'maestro', 'estudiante') NOT NULL,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    activo BOOLEAN DEFAULT TRUE,
    foto_perfil VARCHAR(255) DEFAULT NULL
);

-- Tabla de materias
CREATE TABLE materias (
    id_materia INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    id_maestro INT NOT NULL,
    cuatrimestre VARCHAR(20),
    creditos INT DEFAULT 0,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    activa BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (id_maestro) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
);

-- Tabla de inscripciones (con sistema de aprobación)
CREATE TABLE inscripciones (
    id_inscripcion INT PRIMARY KEY AUTO_INCREMENT,
    id_estudiante INT NOT NULL,
    id_materia INT NOT NULL,
    estado ENUM('pendiente', 'aprobado', 'rechazado') DEFAULT 'pendiente',
    motivo_rechazo TEXT,
    fecha_solicitud DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_respuesta DATETIME,
    FOREIGN KEY (id_estudiante) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_materia) REFERENCES materias(id_materia) ON DELETE CASCADE,
    UNIQUE KEY (id_estudiante, id_materia)
);

-- Tabla de actividades/tareas
CREATE TABLE actividades (
    id_actividad INT PRIMARY KEY AUTO_INCREMENT,
    id_materia INT NOT NULL,
    titulo VARCHAR(200) NOT NULL,
    descripcion TEXT,
    fecha_limite DATETIME NOT NULL,
    ponderacion DECIMAL(5,2) NOT NULL, -- Porcentaje que vale (ej: 15.50)
    tipo ENUM('tarea', 'examen', 'proyecto', 'participacion') DEFAULT 'tarea',
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    activa BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (id_materia) REFERENCES materias(id_materia) ON DELETE CASCADE
);

-- Tabla de calificaciones
CREATE TABLE calificaciones (
    id_calificacion INT PRIMARY KEY AUTO_INCREMENT,
    id_estudiante INT NOT NULL,
    id_actividad INT NOT NULL,
    calificacion DECIMAL(5,2), -- Calificación numérica (0-100)
    comentarios TEXT,
    fecha_entrega DATETIME,
    fecha_calificacion DATETIME,
    FOREIGN KEY (id_estudiante) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_actividad) REFERENCES actividades(id_actividad) ON DELETE CASCADE,
    UNIQUE KEY (id_estudiante, id_actividad)
);

-- Tabla de logros (gamificación - opcional)
CREATE TABLE logros (
    id_logro INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    icono VARCHAR(255),
    criterio VARCHAR(200) -- ej: "promedio_alto", "cumplido_tiempo", "constancia"
);

-- Tabla intermedia: logros obtenidos por usuarios
CREATE TABLE usuarios_logros (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    id_logro INT NOT NULL,
    fecha_obtencion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_logro) REFERENCES logros(id_logro) ON DELETE CASCADE,
    UNIQUE KEY (id_usuario, id_logro)
);

-- Tabla para tokens de recuperación de contraseña
CREATE TABLE password_reset (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    fecha_expiracion DATETIME NOT NULL,
    usado BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
);

-- Índices para mejorar rendimiento
CREATE INDEX idx_email ON usuarios(email);
CREATE INDEX idx_materia_maestro ON materias(id_maestro);
CREATE INDEX idx_inscripcion_estado ON inscripciones(estado);
CREATE INDEX idx_actividad_materia ON actividades(id_materia);
CREATE INDEX idx_calificacion_estudiante ON calificaciones(id_estudiante);

-- Insertar usuario administrador por defecto
INSERT INTO usuarios (nombre, apellido, email, password, rol) 
VALUES ('Admin', 'Sistema', 'admin@plataforma.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'administrador');
-- Password por defecto: password123

-- Insertar algunos logros de ejemplo
INSERT INTO logros (nombre, descripcion, icono, criterio) VALUES
('Primera Entrega', 'Entregaste tu primera tarea a tiempo', '🎯', 'primera_entrega'),
('Promedio Alto', 'Mantuviste un promedio mayor a 90', '⭐', 'promedio_alto'),
('Racha Perfecta', 'Entregaste 5 tareas consecutivas a tiempo', '🔥', 'racha_perfecta'),
('Estudiante Constante', 'Completaste todas las actividades del mes', '💪', 'constancia');