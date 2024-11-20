//Politica de contraseñas
document.getElementById('contrasena').addEventListener('input', function () {
    const contrasena = this.value;
    const errorContrasena = document.getElementById('error-contrasena');

    // Expresión regular para verificar 8 caracteres y al menos una mayúscula
    const passwordRegex = /^(?=.*[A-Z]).{8,}$/;

    if (!passwordRegex.test(contrasena)) {
        // Si no cumple, mostramos el mensaje de error
        errorContrasena.style.display = 'block';
    } else {
        // Si cumple, ocultamos el mensaje de error
        errorContrasena.style.display = 'none';
    }
});

// Validación al enviar el formulario
document.querySelector('.formulario').addEventListener('submit', function (event) {
    const contrasena = document.getElementById('contrasena').value;
    const errorContrasena = document.getElementById('error-contrasena');
    const passwordRegex = /^(?=.*[A-Z]).{8,}$/;

    if (!passwordRegex.test(contrasena)) {
        // Si la contraseña no cumple, prevenimos el envío del formulario
        event.preventDefault();
        errorContrasena.style.display = 'block';
        alert('Mesedez, ziurtatu pasahitza baldintzak betetzen dituela.');
    }
});

//Validacion para que campo contraseña y repetir contraseña sean iguales
document.querySelector('.formulario').addEventListener('submit', function(event) {
    const contrasena = document.getElementById('contrasena').value;
    const repetirContrasena = document.getElementById('repetir_contrasena').value;

    // Verificar si las contraseñas son iguales
    if (contrasena !== repetirContrasena) {
        event.preventDefault(); // Evitar que el formulario se envíe
        alert('Pasahitzak ez datoz bat. Mesedez, egiaztatu.');
    }
});