<?php

define('ROOT_PATH', __DIR__);
require_once ROOT_PATH . '/includes/helpers.php';
require_once ROOT_PATH . '/includes/conexion.php';

iniciarSesionSegura();

// Si ya está autenticado, redirigir
if (estaAutenticado()) {
    $rol = obtenerRol();
    redirigir("views/dashboard_$rol.php");
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizar($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Por favor, completa todos los campos';
    } else {
        try {
            $db = Conexion::getInstance()->getConexion();
            
            $stmt = $db->prepare("
                SELECT id_usuario, nombre, apellido, email, password, rol, activo
                FROM usuarios 
                WHERE email = ? AND activo = 1
                LIMIT 1
            ");
            $stmt->execute([$email]);
            $usuario = $stmt->fetch();
            
            if ($usuario) {
                $passwordValida = false;
                
                // VERIFICACIÓN MÚLTIPLE (por compatibilidad con datos de prueba)
                if (password_verify($password, $usuario['password'])) {
                    $passwordValida = true;
                } elseif ($password === $usuario['password']) {
                    // Para datos de prueba con contraseñas en texto plano
                    $passwordValida = true;
                    
                    // ⚠️ ACTUALIZAR a hash en primera oportunidad
                    $nuevoHash = password_hash($password, PASSWORD_BCRYPT);
                    $updateStmt = $db->prepare("UPDATE usuarios SET password = ? WHERE id_usuario = ?");
                    $updateStmt->execute([$nuevoHash, $usuario['id_usuario']]);
                }
                
                if ($passwordValida) {
                    // Regenerar ID de sesión (previene session fixation)
                    session_regenerate_id(true);
                    
                    $_SESSION['user_id'] = $usuario['id_usuario'];
                    $_SESSION['user_name'] = $usuario['nombre'] . ' ' . $usuario['apellido'];
                    $_SESSION['user_email'] = $usuario['email'];
                    $_SESSION['user_role'] = $usuario['rol'];
                    
                    // Registrar último acceso
                    $stmt = $db->prepare("UPDATE usuarios SET ultimo_acceso = NOW() WHERE id_usuario = ?");
                    $stmt->execute([$usuario['id_usuario']]);
                    
                    redirigir("views/dashboard_{$usuario['rol']}.php");
                } else {
                    $error = 'Contraseña incorrecta';
                }
            } else {
                $error = 'Usuario no encontrado o inactivo';
            }
            
        } catch (PDOException $e) {
            error_log("Error en login: " . $e->getMessage());
            $error = 'Error del servidor. Intenta más tarde.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo SITE_NAME; ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="login-screen">
        <div class="login-card">
            <div class="login-header">
                <div class="login-logo">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h1 class="login-title"><?php echo SITE_NAME; ?></h1>
                <p class="login-subtitle">Iniciar Sesión en tu Cuenta</p>
            </div>

            <div class="login-body">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?php echo htmlspecialchars($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope me-1"></i>Email
                        </label>
                        <input type="email" class="form-control" id="email" name="email" required 
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                               placeholder="tu@email.com">
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock me-1"></i>Contraseña
                        </label>
                        <input type="password" class="form-control" id="password" name="password" required 
                               placeholder="••••••">
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-sign-in-alt me-2"></i>Ingresar al Sistema
                    </button>
                </form>

                <div class="login-links">
                    <a href="<?php echo BASE_URL; ?>/signup_page.php" class="login-link">
                        <i class="fas fa-user-plus me-1"></i>¿No tienes cuenta? Regístrate
                    </a>
                    <br>
                    <a href="<?php echo BASE_URL; ?>/forgot_password.php" class="login-link">
                        <i class="fas fa-key me-1"></i>¿Olvidaste tu contraseña?
                    </a>
                </div>

                <!-- Cuentas de demostración -->
                <div class="demo-accounts">
                    <h6 class="demo-title">🔑 Cuentas de Prueba:</h6>
                    
                    <div class="demo-account" onclick="llenarLogin('admin@plataforma.com', '123456')">
                        <span class="demo-role">👨‍💼 Administrador</span>
                        <span class="demo-credentials">admin@plataforma.com / 123456</span>
                    </div>
                    
                    <div class="demo-account" onclick="llenarLogin('maria.gonzalez@plataforma.com', '123456')">
                        <span class="demo-role">👨‍🏫 Maestro</span>
                        <span class="demo-credentials">maria.gonzalez@plataforma.com / 123456</span>
                    </div>
                    
                    <div class="demo-account" onclick="llenarLogin('pedro.lopez@plataforma.com', '123456')">
                        <span class="demo-role">👨‍🎓 Estudiante</span>
                        <span class="demo-credentials">pedro.lopez@plataforma.com / 123456</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function llenarLogin(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
            document.getElementById('email').classList.add('is-valid');
            document.getElementById('password').classList.add('is-valid');
        }
    </script>
</body>
</html>