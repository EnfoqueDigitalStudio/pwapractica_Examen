<?php
/**
 * Módulo del Cliente: Historial de Reservas y Cancelación
 * Desarrollado por: Elkin Velastegui
 */
session_start();
require_once '../config/conexion.php';

// Seguridad: Solo accesible para Clientes autenticados
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 1) {
    header("Location: ../index.php");
    exit();
}

$cliente_id = $_SESSION['id_usuario'];
$mensaje = "";

// Procesar cancelación de reserva si el cliente lo solicita y sigue 'Pendiente'
if (isset($_GET['cancelar_id'])) {
    $reserva_id = intval($_GET['cancelar_id']);
    $stmt_cancel = $conexion->prepare("UPDATE reservas SET estado = 'Cancelada' WHERE id = ? AND cliente_id = ? AND estado = 'Pendiente'");
    $stmt_cancel->bind_param("ii", $reserva_id, $cliente_id);
    if ($stmt_cancel->execute() && $stmt_cancel->affected_rows > 0) {
        $mensaje = "La reserva #$reserva_id ha sido cancelada correctamente.";
    }
    $stmt_cancel->close();
}

// Consultar las reservas del cliente uniendo con la tabla de servicios fotográficos
$sql = "SELECT r.id, s.nombre AS servicio, s.precio, r.fecha, r.hora, r.estado 
        FROM reservas r 
        INNER JOIN servicios_fotograficos s ON r.servicio_fotografico_id = s.id 
        WHERE r.cliente_id = ? 
        ORDER BY r.fecha DESC, r.hora DESC";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $cliente_id);
$stmt->execute();
$resultado = $stmt->get_result();

include '../config/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0"><i class="bi bi-journal-bookmark text-warning me-2"></i>Mis Reservas</h2>
        <p class="text-muted mb-0">Consulta el estado de tus sesiones agendadas en el estudio.</p>
    </div>
    <a href="catalogo.php" class="btn btn-warning fw-bold">
        <i class="bi bi-plus-circle me-1"></i>Nueva Reserva
    </a>
</div>

<?php if (!empty($mensaje)): ?>
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <?php echo $mensaje; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Servicio Fotográfico</th>
                        <th>Precio</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Estado</th>
                        <th class="text-center">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($resultado->num_rows > 0): ?>
                        <?php while ($row = $resultado->fetch_assoc()): ?>
                            <tr>
                                <td class="fw-bold"><?php echo $row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['servicio']); ?></td>
                                <td>$<?php echo number_format($row['precio'], 2); ?></td>
                                <td><?php echo date("d/m/Y", strtotime($row['fecha'])); ?></td>
                                <td><?php echo date("H:i", strtotime($row['hora'])); ?></td>
                                <td>
                                    <?php
                                    // Etiqueta de color según el estado
                                    $badge = "bg-secondary";
                                    if ($row['estado'] == 'Pendiente') $badge = "bg-warning text-dark";
                                    if ($row['estado'] == 'Confirmada') $badge = "bg-success";
                                    if ($row['estado'] == 'Cancelada') $badge = "bg-danger";
                                    ?>
                                    <span class="badge <?php echo $badge; ?>"><?php echo $row['estado']; ?></span>
                                </td>
                                <td class="text-center">
                                    <?php if ($row['estado'] == 'Pendiente'): ?>
                                        <a href="mis_reservas.php?cancelar_id=<?php echo $row['id']; ?>" 
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('¿Seguro que deseas cancelar esta reserva?');">
                                            Cancelar
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small">Sin acciones</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                Aún no tienes sesiones reservadas. ¡Explora nuestro catálogo y agenda la primera!
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$stmt->close();
include '../config/footer.php';
?>