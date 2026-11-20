/**
 * Lógica del lado del cliente (JavaScript)
 * Proyecto: Enfoque Digital Studio
 */

document.addEventListener("DOMContentLoaded", function () {
    // 1. Cargar dinámicamente los datos del servicio seleccionado en el Modal de Reserva
    const botonesReservar = document.querySelectorAll(".btn-reservar");
    botonesReservar.forEach(function (boton) {
        boton.addEventListener("click", function () {
            const idServicio = this.getAttribute("data-id");
            const nombreServicio = this.getAttribute("data-nombre");
            const precioServicio = this.getAttribute("data-precio");

            document.getElementById("modal_servicio_id").value = idServicio;
            document.getElementById("modal_servicio_nombre").textContent = nombreServicio;
            document.getElementById("modal_servicio_precio").textContent = precioServicio;
        });
    });

    // 2. Validación de formularios con estilos visuales de Bootstrap 5
    const formularios = document.querySelectorAll("#formLogin, #formRegistro, #formReserva");
    formularios.forEach(function (form) {
        form.addEventListener("submit", function (evento) {
            if (!form.checkValidity()) {
                evento.preventDefault();
                evento.stopPropagation();
            }
            form.classList.add("was-validated");
        });
    });

    // 3. Validar que el campo de teléfono acepte solo números (en el registro)
    const inputTelefono = document.getElementById("telefono");
    if (inputTelefono) {
        inputTelefono.addEventListener("input", function () {
            this.value = this.value.replace(/[^0-9]/g, "");
        });
    }
});