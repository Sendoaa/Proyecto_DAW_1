//////////////////VALIDACIONES REGISTRO//////////////////

document.addEventListener('DOMContentLoaded', function() {
    const formulario = document.querySelector('.formulario');

    formulario.addEventListener('submit', function(event) {
        // Validar todos los campos
        if (!validarNombre() || !validarApellidos() || !validarTelefono() || !validarCorreo()) {
            event.preventDefault(); // Prevenir el envío del formulario si alguna validación falla
        }
    });

    function validarNombre() {
        const nombre = document.getElementById('nombre').value;
        if (nombre.length > 10) {
            alert('Izenak ez du 10 karaktere baino gehiago izan behar.');
            return false;
        }
        return true;
    }

    function validarApellidos() {
        const apellidos = document.getElementById('apellidos').value;
        if (apellidos.length > 25) {
            alert('Abizenak ez du 25 karaktere baino gehiago izan behar.');
            return false;
        }
        return true;
    }

    function validarTelefono() {
        const telefono = document.getElementById('telefono').value;
        const telefonoRegex = /^[0-9]{9}$/;
        if (!telefonoRegex.test(telefono)) {
            alert('Telefonoak 9 zenbaki izan behar ditu.');
            return false;
        }
        return true;
    }

    function validarCorreo() {
        const correo = document.getElementById('correo').value;
        const correoRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!correoRegex.test(correo)) {
            alert('Posta elektronikoak example@example.com formatua izan behar du.');
            return false;
        }
        return true;
    }
}); 



function validarEditarPerfil() {
    let nombre = document.getElementById("nuevo-nombre").value.trim();
    let apellidos = document.getElementById("nuevos-apellidos").value.trim();
    let email = document.getElementById("nuevo-email").value.trim();
    let telefono = document.getElementById("nuevo-telefono").value.trim();

    if (!/^[A-Za-z\s]{2,}$/.test(nombre)) {
        alert("Izenak gutxienez 2 letra izan behar ditu eta zenbakirik ez eduki.");
        return false;
    }

    if (!/^[A-Za-z\s]{1,20}$/.test(apellidos)) {
        alert("Abizenak gehienez 20 karaktere izan behar ditu eta zenbakirik ez eduki.");
        return false;
    }

    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        alert("Mesedez, baliozko email bat sartu.");
        return false;
    }

    if (!/^\d{9}$/.test(telefono)) {
        alert("Telefonoak 9 digitu izan behar ditu.");
        return false;
    }

    return true;
}



function validarAñadirCoche() {
    let marca = document.getElementById("marca").value.trim();
    let modelo = document.getElementById("modelo").value.trim();
    let matricula = document.getElementById("matricula").value.trim();
    let asientos = document.getElementById("asientos").value.trim();

    // Validar marca (solo letras, mínimo 2 caracteres)
    if (!/^[A-Za-z\s]{2,}$/.test(marca)) {
        alert("Markak gutxienez 2 letra izan behar ditu eta zenbakirik ez eduki.");
        return false;
    }

    // Validar modelo (solo letras y números, mínimo 2 caracteres)
    if (!/^[A-Za-z0-9\s]{2,}$/.test(modelo)) {
        alert("Modeloak gutxienez 2 karaktere izan behar ditu eta letrak eta zenbakiak eduki ditzake.");
        return false;
    }

    // Validar matrícula (formato específico, 4 números seguidos de 3 letras, ejemplo: 1234ABC)
    if (!/^\d{4}[A-Z]{3}$/.test(matricula)) {
        alert("Matrikulak 1234ABC formatua izan behar du.");
        return false;
    }

    // Validar asientos (solo números entre 1 y 9)
    if (!/^[1-9]$/.test(asientos)) {
        alert("Aulkien kopurua 1 eta 9 artean egon behar da.");
        return false;
    }

    return true;
}
