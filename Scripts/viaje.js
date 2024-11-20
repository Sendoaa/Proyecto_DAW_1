// Obtener los elementos por su ID
const decreaseButton = document.getElementById('decrease');
const increaseButton = document.getElementById('increase');
const numberDisplay = document.getElementById('number');

// Inicializar el número
let number = 2;

// Función para disminuir el número
decreaseButton.addEventListener('click', () => {
    if (number > 0) { // Asegurarse de que no baje de 0
        number--;
        numberDisplay.textContent = number;
    }
});

// Función para aumentar el número
increaseButton.addEventListener('click', () => {
    number++;
    numberDisplay.textContent = number;
});


// Obtener los elementos por su ID
const precio_decreaseButton = document.getElementById('precio_decrease');
const precio_increaseButton = document.getElementById('precio_increase');
const precio_numberDisplay = document.getElementById('precio_number');

// Inicializar el número
let precio_number = 2;

// Función para disminuir el número
precio_decreaseButton.addEventListener('click', () => {
    if (precio_number > 0) { // Asegurarse de que no baje de 0
        precio_number--;
        precio_numberDisplay.textContent = precio_number;
    }
});

// Función para aumentar el número
precio_increaseButton.addEventListener('click', () => {
    precio_number++;
    precio_numberDisplay.textContent = precio_number;
});


document.addEventListener('DOMContentLoaded', () => {

    //Actualizar num pasajeros
    let numPasajeros = 2; // Valor inicial
    const numPasajerosDisplay = document.getElementById('number');
    const numPasajerosInput = document.getElementById('numPasajeros');

    // Inicializa el campo oculto
    numPasajerosDisplay.innerText = numPasajeros;
    numPasajerosInput.value = numPasajeros;

    // Función para actualizar el número de pasajeros y el campo oculto
    function updateNumPasajeros() {
        numPasajerosDisplay.innerText = numPasajeros;
        numPasajerosInput.value = numPasajeros; // Actualiza el campo oculto
    }

    // Manejadores de eventos para los botones
    document.getElementById('decrease').addEventListener('click', () => {
        if (numPasajeros > 1) { // Limita a un mínimo de 1 pasajero
            numPasajeros--;
            updateNumPasajeros();
        }
    });

    document.getElementById('increase').addEventListener('click', () => {
        numPasajeros++; // Aumenta el número de pasajeros
        updateNumPasajeros();
    });

    // Actualizar precio
    let precioAsiento = 2; // Valor inicial en euros
    const precioDisplay = document.getElementById('precio_number');
    const precioInput = document.getElementById('precioAsiento');

    // Inicializa el campo oculto
    precioDisplay.innerText = precioAsiento;
    precioInput.value = precioAsiento;

    // Función para actualizar el precio y el campo oculto
    function updatePrecio() {
        precioDisplay.innerText = precioAsiento;
        precioInput.value = precioAsiento; // Actualiza el campo oculto
    }

    // Manejadores de eventos para los botones de precio
    document.getElementById('precio_decrease').addEventListener('click', () => {
        if (precioAsiento > 1) { // Limita a un mínimo de 1 euro
            precioAsiento--;
            updatePrecio();
        }
    });

    document.getElementById('precio_increase').addEventListener('click', () => {
        precioAsiento++; // Aumenta el precio
        updatePrecio();
    });
});

function actualizarCampoOculto() {
    // Capturar valores seleccionados de las checkboxes
    const checkboxes = document.querySelectorAll('input[name="discapacidad"]');
    let valoresSeleccionados = [];

    // Iterar sobre todas las checkboxes y añadir '0' si no está seleccionada
    checkboxes.forEach(cb => {
        if (cb.checked) {
            valoresSeleccionados.push(cb.value);
        } else {
            valoresSeleccionados.push('0');
        }
    });

    // Unir los valores seleccionados con guiones y actualizar el campo oculto
    document.getElementById('discapacidades').value = valoresSeleccionados.join('-');

    // Capturar valor seleccionado del input vuelves
    const vuelves = document.querySelector('input[name="vuelves"]:checked');
    if (vuelves) {
        document.getElementById('hidden_vuelves').value = vuelves.value;
    }

    // Validación de campos requeridos
    const hiddenOrigen = document.getElementById('hidden_origen').value;
    const hiddenDestino = document.getElementById('hidden_destino').value;
    const hiddenFechaInicio = document.getElementById('FechaInicio').value;
    const hiddenFechaFin = document.getElementById('FechaFin').value;
    const hiddenvuelves = document.getElementById('hidden_horaVuelta').value;


    if (!hiddenOrigen) {
        alert('Mesedez, bete jatorri eremua.');
        return false;
    }

    if (!hiddenDestino) {
        alert('Mesedez, bete helmuga eremua.');
        return false;
    }

    if (!hiddenFechaInicio) {
        alert('Mesedez, bete hasiera data eremua.');
        return false;
    }

    if (!hiddenFechaFin) {
        alert('Mesedez, bete amaiera data eremua.');
        return false;
    }


    if (!hiddenvuelves) {
        alert('Mesedez, bete itzultzeko eremua.');
        return false;
    }

    const tambienVuelvesSeleccionado = document.querySelector('input[name="tambien_vuelves"]:checked');
    if (!tambienVuelvesSeleccionado) {
        alert('Mesedez, aukeratu itzuli nahi duzun ala ez.');
        return false;
    }

    return true; // Permite el envío del formulario
}


// Detectar cambio en las opciones de "vuelves" (si/no)
document.querySelectorAll('input[name="vuelves"]').forEach(radio => {
    radio.addEventListener('change', (event) => {
        if (event.target.value === 'si') {
            document.getElementById('timeVuelta').style.display = 'block';
            document.getElementById('h3aquehoravuelves').style.display = 'block';
        } else {
            document.getElementById('timeVuelta').style.display = 'none';
            document.getElementById('h3aquehoravuelves').style.display = 'none';
            // Limpiar el campo oculto de hora de vuelta si no se vuelve
            document.getElementById('hidden_horaVuelta').value = '';
        }
    });
});