<?php
/**
 * Módulo del Cliente: Catálogo de Servicios Fotográficos y Formulario de Reserva
 * Desarrollado por: Elkin Velastegui
 */
session_start();
require_once '../config/conexion.php';

// Seguridad: Verificar que el usuario haya iniciado sesión y tenga rol de Cliente (1)
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 1) {
    header("Location: ../index.php");
    exit();
}

$mensaje = "";
$tipo_alerta = "";

// Procesar el formulario de reserva cuando se envía por POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['servicio_id'])) {
    $cliente_id = $_SESSION['id_usuario'];
    $servicio_id = intval($_POST['servicio_id']);
    $fecha = trim($_POST['fecha']);
    $hora = trim($_POST['hora']);
    $estado = "Pendiente";

    // Validar que no se reserve en una fecha pasada
    if ($fecha < date("Y-m-d")) {
        $mensaje = "No puedes agendar una sesión en una fecha anterior a hoy.";
        $tipo_alerta = "danger";
    } else {
        // Verificar si ya existe una reserva confirmada o pendiente en esa misma fecha y hora
        $check = $conexion->prepare("SELECT id FROM reservas WHERE fecha = ? AND hora = ? AND estado != 'Cancelada'");
        $check->bind_param("ss", $fecha, $hora);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $mensaje = "Ese horario ya está ocupado. Por favor selecciona otra hora o fecha.";
            $tipo_alerta = "warning";
        } else {
            // Insertar la nueva reserva en la base de datos
            $stmt = $conexion->prepare("INSERT INTO reservas (cliente_id, servicio_fotografico_id, fecha, hora, estado) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iisss", $cliente_id, $servicio_id, $fecha, $hora, $estado);

            if ($stmt->execute()) {
                $mensaje = "¡Tu sesión fotográfica ha sido reservada con éxito! Estado actual: Pendiente.";
                $tipo_alerta = "success";
            } else {
                $mensaje = "Error al guardar la reserva: " . $conexion->error;
                $tipo_alerta = "danger";
            }
            $stmt->close();
        }
        $check->close();
    }
}

// Consultar todos los servicios fotográficos disponibles
$servicios = $conexion->query("SELECT * FROM servicios_fotograficos ORDER BY id ASC");

include '../config/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0"><i class="bi bi-camera2 text-warning me-2"></i>Catálogo de Sesiones</h2>
        <p class="text-muted mb-0">Elige el paquete fotográfico ideal y agenda tu fecha en el estudio.</p>
    </div>
    <a href="mis_reservas.php" class="btn btn-outline-dark">
        <i class="bi bi-calendar-check me-1"></i>Ver Mis Reservas
    </a>
</div>

<?php if (!empty($mensaje)): ?>
    <div class="alert alert-<?php echo $tipo_alerta; ?> alert-dismissible fade show" role="alert">
        <?php echo $mensaje; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Tarjetas de Servicios Fotográficos -->
<div class="row g-4">
    <?php while ($fila = $servicios->fetch_assoc()): ?>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0 card-servicio">
                <div class="card-header bg-dark text-white py-3">
                    <h5 class="card-title mb-0"><?php echo htmlspecialchars($fila['nombre']); ?></h5>
                </div>
                <div class="card-body d-flex flex-column">
                    <p class="card-text text-muted flex-grow-1"><?php echo htmlspecialchars($fila['descripcion']); ?></p>
                    <h3 class="fw-bold text-dark my-3">$<?php echo number_format($fila['precio'], 2); ?></h3>
                    <!-- Botón que abre el Modal y pasa los datos del servicio con JavaScript -->
                    <button type="button" 
                            class="btn btn-warning fw-bold w-100 btn-reservar" 
                            data-bs-toggle="modal" 
                            data-bs-target="#modalReserva"
                            data-id="<?php echo $fila['id']; ?>"
                            data-nombre="<?php echo htmlspecialchars($fila['nombre']); ?>"
                            data-precio="<?php echo number_format($fila['precio'], 2); ?>">
                        <i class="bi bi-calendar-plus me-1"></i>Reservar Sesión
                    </button>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<!-- Modal de Reserva de Sesión -->
<div class="modal fade" id="modalReserva" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title"><i class="bi bi-calendar-event text-warning me-2"></i>Agendar Sesión</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="catalogo.php" method="POST" id="formReserva">
                <div class="modal-body p-4">
                    <input type="hidden" name="servicio_id" id="modal_servicio_id">
                    
                    <div class="alert alert-secondary mb-3">
                        <strong>Paquete seleccionado:</strong> <span id="modal_servicio_nombre"></span><br>
                        <strong>Valor estimado:</strong> $<span id="modal_servicio_precio"></span>
                    </div>

                    <div class="mb-3">
                        <label for="fecha" class="form-label fw-semibold">Fecha de la sesión</label>
                        <input type="date" class="form-control" id="fecha" name="fecha" min="<?php echo date('Y-m-d'); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="hora" class="form-label fw-semibold">Hora preferida (09:00 a 18:00)</label>
                        <input type="time" class="form-control" id="hora" name="hora" min="09:00" max="18:00" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning fw-bold">Confirmar Reserva</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../config/footer.php'; ?>