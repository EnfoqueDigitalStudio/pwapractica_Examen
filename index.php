<?php
/**
 * Página principal e inicio de sesión (Login)
 * Redirige automáticamente al panel correspondiente según el rol (1 = Cliente, 2 = Empleado)
 */
session_start();
require_once 'config/conexion.php';

// Si el usuario ya tiene sesión activa, enviarlo a su panel
if (isset($_SESSION['id_usuario'])) {
    if ($_SESSION['id_rol'] == 2) {
        header("Location: empleado/dashboard.php");
    } else {
        header("Location: cliente/catalogo.php");
    }
    exit();
}

$error = "";

// Procesar el formulario cuando se envía por POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = trim($_POST['correo']);
    $contrasena = trim($_POST['contrasena']);

    // Consulta preparada para prevenir inyección SQL
    $stmt = $conexion->prepare("SELECT id, nombre, contrasena, id_rol FROM clientes WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($usuario = $resultado->fetch_assoc()) {
        // Verificar contraseña (soporta texto plano de prueba o hash seguro)
        if ($contrasena === $usuario['contrasena'] || password_verify($contrasena, $usuario['contrasena'])) {
            $_SESSION['id_usuario'] = $usuario['id'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['id_rol'] = $usuario['id_rol'];

            // Redirección por rol
            if ($usuario['id_rol'] == 2) {
                header("Location: empleado/dashboard.php");
            } else {
                header("Location: cliente/catalogo.php");
            }
            exit();
        } else {
            $error = "Contraseña incorrecta. Inténtalo de nuevo.";
        }
    } else {
        $error = "No existe una cuenta registrada con ese correo.";
    }
    $stmt->close();
}

include 'config/header.php';
?>

<div class="row justify-content-center align-items-center py-4">
    <div class="col-md-5">
        <div class="card shadow border-0">
            <div class="card-header bg-dark text-white text-center py-3">
                <h4 class="mb-0"><i class="bi bi-camera text-warning me-2"></i>Iniciar Sesión</h4>
                <small class="text-secondary">Accede para reservar o administrar sesiones</small>
            </div>
            <div class="card-body p-4">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form action="index.php" method="POST" id="formLogin" novalidate>
                    <div class="mb-3">
                        <label for="correo" class="form-label fw-semibold">Correo electrónico</label>
                        <input type="email" class="form-control" id="correo" name="correo" placeholder="ejemplo@correo.com" required>
                    </div>
                    <div class="mb-3">
                        <label for="contrasena" class="form-label fw-semibold">Contraseña</label>
                        <input type="password" class="form-control" id="contrasena" name="contrasena" placeholder="••••••••" required>
                    </div>
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-dark fw-semibold">Ingresar al Sistema</button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center bg-white py-3">
                <span class="text-muted">¿Aún no tienes cuenta?</span>
                <a href="registro.php" class="text-decoration-none fw-bold text-dark">Regístrate aquí</a>
            </div>
        </div>
    </div>
</div>

<?php include 'config/footer.php'; ?>