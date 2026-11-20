<?php
// Iniciar sesión si aún no está iniciada para controlar los roles
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enfoque Digital Studio | Reservas Fotográficas</title>
    <!-- Bootstrap 5 CSS vía CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Iconos de Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="/pwapractica_Examen/assets/css/estilos.css">
</head>
<body class="bg-light d-flex flex-column min-vh-100">

<!-- Barra de navegación dinámica según el rol del usuario -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/pwapractica_Examen/index.php">
            <i class="bi bi-camera-reels text-warning me-2"></i>Enfoque Digital Studio
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menuPrincipal">
            <ul class="navbar-nav ms-auto align-items-center">
                <?php if (isset($_SESSION['id_usuario'])): ?>
                    
                    <?php if ($_SESSION['id_rol'] == 1): ?>
                        <!-- Menú exclusivo para el Rol Cliente (Parte de Elkin) -->
                        <li class="nav-item">
                            <a class="nav-link" href="/pwapractica_Examen/cliente/catalogo.php">Servicios y Reservar</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/pwapractica_Examen/cliente/mis_reservas.php">Mis Reservas</a>
                        </li>
                    <?php elseif ($_SESSION['id_rol'] == 2): ?>
                        <!-- Menú exclusivo para el Rol Empleado (Parte de Jesús) -->
                        <li class="nav-item">
                            <a class="nav-link" href="/pwapractica_Examen/empleado/dashboard.php">Panel Empleado</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/pwapractica_Examen/empleado/gestion_servicios.php">Servicios</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/pwapractica_Examen/empleado/gestion_reservas.php">Reservas</a>
                        </li>
                    <?php endif; ?>

                    <li class="nav-item ms-lg-3">
                        <span class="badge bg-warning text-dark me-2">
                            <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['nombre']); ?>
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-danger btn-sm" href="/pwapractica_Examen/logout.php">Salir</a>
                    </li>

                <?php else: ?>
                    <!-- Menú para visitantes sin sesión iniciada -->
                    <li class="nav-item">
                        <a class="nav-link" href="/pwapractica_Examen/index.php">Iniciar Sesión</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-warning btn-sm ms-lg-2 fw-semibold" href="/pwapractica_Examen/registro.php">Registrarse</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<main class="container my-4 flex-grow-1">