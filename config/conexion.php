<?php
/**
 * Archivo de conexión a la base de datos MySQL usando MySQLi
 * Proyecto: Enfoque Digital Studio (Sistema de reservas para estudio de fotografía)
 */

// Parámetros de configuración del servidor local (Laragon)
$servidor = "localhost";
$usuario = "root";
$contrasena = ""; // En Laragon la contraseña por defecto es vacía
$base_datos = "enfoque_digital_studio";

// Crear la instancia de conexión
$conexion = new mysqli($servidor, $usuario, $contrasena, $base_datos);

// Verificar si ocurrió algún error al conectar
if ($conexion->connect_error) {
    die("Error crítico de conexión a la base de datos: " . $conexion->connect_error);
}

// Configurar la codificación de caracteres a UTF-8 para soportar tildes y caracteres especiales
$conexion->set_charset("utf8mb4");
?>
