// validar_registroEmpresa.js

window.onload = function() {
    var form = document.querySelector('form');

    form.addEventListener('submit', function(event) {
        // Validación del teléfono
        var telefonoInput = document.getElementById('telefono');
        var telefonoValue = telefonoInput.value.trim();
        if (!/^([6-7])\d{8}$/.test(telefonoValue)) {
            alert('El teléfono debe empezar por 6 o 7 y tener 9 dígitos.');
            event.preventDefault();
            return;
        }

        // Validación del email
        var emailInput = document.getElementById('email');
        var emailValue = emailInput.value.trim();
        if (!isValidEmail(emailValue)) {
            alert('Ingrese un email válido.');
            event.preventDefault();
            return;
        }

        // Validación de la contraseña
        var passwordInput = document.getElementById('password');
        var passwordValue = passwordInput.value.trim();
        if (passwordValue.length < 6) {
            alert('La contraseña debe tener al menos 6 caracteres.');
            event.preventDefault();
            return;
        }
    });

    function isValidEmail(email) {
        // Expresión regular para validar el email
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
};
