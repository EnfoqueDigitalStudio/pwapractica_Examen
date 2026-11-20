<?php
/**
 * Formulario de registro para nuevos clientes (Rol = 1)
 */
session_start();
require_once 'config/conexion.php';

$mensaje = "";
$tipo_alerta = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $telefono = trim($_POST['telefono']);
    $contrasena = trim($_POST['contrasena']);
    $id_rol = 1; // Por defecto todo registro público es Cliente

    // Verificar si el correo ya existe
    $verificar = $conexion->prepare("SELECT id FROM clientes WHERE correo = ?");
    $verificar->bind_param("s", $correo);
    $verificar->execute();
    $verificar->store_result();

    if ($verificar->num_rows > 0) {
        $mensaje = "Este correo electrónico ya se encuentra registrado.";
        $tipo_alerta = "warning";
    } else {
        // Encriptar contraseña para seguridad
        $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);

        $stmt = $conexion->prepare("INSERT INTO clientes (nombre, correo, contrasena, telefono, id_rol) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $nombre, $correo, $contrasena_hash, $telefono, $id_rol);

        if ($stmt->execute()) {
            $mensaje = "¡Registro exitoso! Ya puedes iniciar sesión con tu cuenta.";
            $tipo_alerta = "success";
        } else {
            $mensaje = "Error al registrar el usuario: " . $conexion->error;
            $tipo_alerta = "danger";
        }
        $stmt->close();
    }
    $verificar->close();
}

include 'config/header.php';
?>

<div class="row justify-content-center py-3">
    <div class="col-md-6">
        <div class="card shadow border-0">
            <div class="card-header bg-dark text-white text-center py-3">
                <h4 class="mb-0"><i class="bi bi-person-plus text-warning me-2"></i>Crear Cuenta de Cliente</h4>
            </div>
            <div class="card-body p-4">
                <?php if (!empty($mensaje)): ?>
                    <div class="alert alert-<?php echo $tipo_alerta; ?> alert-dismissible fade show" role="alert">
                        <?php echo $mensaje; ?>
                        <?php if ($tipo_alerta === "success"): ?>
                            <a href="index.php" class="alert-link ms-2">Ir al Login</a>
                        <?php endif; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form action="registro.php" method="POST" id="formRegistro" novalidate>
                    <div class="mb-3">
                        <label for="nombre" class="form-label fw-semibold">Nombre completo</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" maxlength="50" required>
                    </div>
                    <div class="mb-3">
                        <label for="correo" class="form-label fw-semibold">Correo electrónico</label>
                        <input type="email" class="form-control" id="correo" name="correo" maxlength="100" required>
                    </div>
                    <div class="mb-3">
                        <label for="telefono" class="form-label fw-semibold">Teléfono / Celular</label>
                        <input type="text" class="form-control" id="telefono" name="telefono" maxlength="10" placeholder="0999999999" required>
                    </div>
                    <div class="mb-3">
                        <label for="contrasena" class="form-label fw-semibold">Contraseña</label>
                        <input type="password" class="form-control" id="contrasena" name="contrasena" minlength="5" required>
                    </div>
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-warning fw-bold">Registrarme</button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center bg-white py-3">
                <span class="text-muted">¿Ya tienes cuenta?</span>
                <a href="index.php" class="text-decoration-none fw-bold text-dark">Inicia sesión</a>
            </div>
        </div>
    </div>
</div>

<?php include 'config/footer.php'; ?>