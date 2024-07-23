window.onload = function() {
    function mostrarError(id, mensaje) {
        var campo = document.getElementById(id);
        var error = document.getElementById(id + '_error');
        if (!error) {
            error = document.createElement('div');
            error.id = id + '_error';
            error.className = 'error';
            campo.parentNode.insertBefore(error, campo.nextSibling);
        }
        error.textContent = mensaje;
    }

    function limpiarErrores() {
        var errores = document.querySelectorAll('.error');
        errores.forEach(function(error) {
            error.textContent = '';
        });
    }

    function validarFormulario(event) {
        // Limpiar errores previos
        limpiarErrores();

        var dni = document.getElementById('dni').value.trim();
        var telefono = document.getElementById('telefono').value.trim();
        var email = document.getElementById('email').value.trim();
        var fechaNacimiento = document.getElementById('fecha_nacimiento').value.trim();
        var fechaContratacion = document.getElementById('fecha_contratacion').value.trim();

        // Expresiones regulares para DNI y NIE
        var dniRegex = /^\d{8}[a-zA-Z]$/; // DNI
        var nieRegex = /^[XYZ]\d{7}[A-Z]$/; // NIE
        var telefonoRegex = /^[6-7]\d{8}$/;
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        var errores = [];

        // Validación de DNI/NIE
        if (!(dniRegex.test(dni) || nieRegex.test(dni))) {
            errores.push("Por favor, introduce un DNI o NIE válido.");
            mostrarError('dni', "Por favor, introduce un DNI o NIE válido.");
        }

        // Validación de teléfono
        if (!telefonoRegex.test(telefono)) {
            errores.push("Por favor, introduce un número de teléfono válido (que empiece con 6 o 7 y tenga 9 dígitos).");
            mostrarError('telefono', "Por favor, introduce un número de teléfono válido (que empiece con 6 o 7 y tenga 9 dígitos).");
        }

        // Validación de email
        if (!emailRegex.test(email)) {
            errores.push("Por favor, introduce un email válido.");
            mostrarError('email', "Por favor, introduce un email válido.");
        }

        var hoy = new Date();
        var nacimiento = new Date(fechaNacimiento);
        var edad = hoy.getFullYear() - nacimiento.getFullYear();
        var m = hoy.getMonth() - nacimiento.getMonth();
        if (m < 0 || (m === 0 && hoy.getDate() < nacimiento.getDate())) {
            edad--;
        }

        // Validación de edad
        if (edad < 18) {
            errores.push("El trabajador debe tener al menos 18 años de edad.");
            mostrarError('fecha_nacimiento', "El trabajador debe tener al menos 18 años de edad.");
        }
        if (edad > 65) {
            errores.push("El trabajador no puede tener más de 65 años de edad.");
            mostrarError('fecha_nacimiento', "El trabajador no puede tener más de 65 años de edad.");
        }

        var contratacion = new Date(fechaContratacion);
        var dosMesesAtras = new Date();
        dosMesesAtras.setMonth(hoy.getMonth() - 2);

        // Validación de fecha de contratación
        if (contratacion > hoy) {
            errores.push("Imposible: la fecha de contratación no puede ser posterior al día de hoy.");
            mostrarError('fecha_contratacion', "Imposible: la fecha de contratación no puede ser posterior al día de hoy.");
        }
        if (contratacion < dosMesesAtras) {
            errores.push("Imposible: la fecha de contratación no puede ser anterior a 2 meses desde hoy.");
            mostrarError('fecha_contratacion', "Imposible: la fecha de contratación no puede ser anterior a 2 meses desde hoy.");
        }

        // Mostrar alertas de error
        if (errores.length > 0) {
            alert(errores.join("\n"));
            return false; // Cancelar el envío del formulario si hay errores
        }

        return true; // Permitir el envío del formulario si no hay errores
    }

    var form = document.querySelector('form');
    form.addEventListener('submit', validarFormulario);
};
