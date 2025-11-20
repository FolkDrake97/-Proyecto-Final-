<?php
// Credenciales de base de datos
define('DB_HOST', 'fdb1032.awardspace.net');
define('DB_NAME', '4681910_plataforma');
define('DB_USER', '4681910_plataforma');
define('DB_PASS', 'isaac2002');
define('DB_CHARSET', 'utf8mb4');
// Configuración de la aplicación
define('APP_NAME', 'Plataforma Académica');
define('APP_VERSION', '1.0.0');
define('SITE_NAME', 'Plataforma Académica');
define('PASSWORD_MIN_LENGTH', 6);
// URL Base (detección automática)
if (!defined('BASE_URL')) {
    protocol=isset(protocol = isset(
protocol=isset(_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    path=dirname(path = dirname(
path=dirname(script);
    $path = str_replace('\\', '/', $path);
    path=trim(path = trim(
path=trim(path, '/');
    path = preg_replace('#/(api|views|includes|config).*
#', '', $path);
    define('BASE_URL', $protocol . '://' . $host . '/' . $path);
}

// Zona horaria
date_default_timezone_set('America/Mexico_City');
// Manejo de errores (cambiar a 0 en producción)
error_reporting(E_ALL);
ini_set('display_errors', 1);
// ==========================================
// CLASE DATABASE (SINGLETON)
// ==========================================
class Database {
private static $instance = null;
private $conn;
private function __construct() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
        ];
        $this->conn = new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch(PDOException $e) {
        error_log("Error de conexión: " . $e->getMessage());
        die("Error al conectar con la base de datos.");
    }
}

public static function getInstance() {
    if (self::$instance === null) {
        self::$instance = new self();
    }
    return self::$instance;
}

public function getConnection() {
    return $this->conn;
}

private function __clone() {}

public function __wakeup() {
    throw new Exception("No se puede deserializar un singleton");
}
?>