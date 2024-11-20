<?php
session_start();


// Conexión a la base de datos
$servername = "dbmopa.cxzeapfroqne.us-east-1.rds.amazonaws.com";
$username = "adminMopa";
$password = "adminsmopa";
$dbname = "mopa";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Comprobar la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Comprobar si el usuario existe en la tabla 'conductor'
$user_id = $_SESSION['idusuario']; // Suponiendo que el idusuario se almacena en la sesión
$sql = "SELECT * FROM conductor WHERE IdUsuario = ?";
$stmt = $conn->prepare($sql); // Preparar la consulta
$stmt->bind_param("i", $user_id); // Vincular parámetros
$stmt->execute(); // Ejecutar la consulta
$result = $stmt->get_result(); // Obtener resultados

// Verificar si no se encuentra el usuario
if ($result->num_rows == 0) {
    echo "<script>alert('Erabiltzailea ez da aurkitu.'); window.location.href = 'index.php';</script>";
    exit(); // Salir si el usuario no se encuentra
}

$stmt->close(); // Cerrar la sentencia
$conn->close(); // Cerrar la conexión
?>
<link rel="stylesheet" href="Styles/iniciopub.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />

<?php include 'Includes/header.php'; ?>

<title>Bidai bat argitaratu</title>
<div class="estructura_publicar_viaje">
    <div class="columna1">
        <?php include 'Includes/mapa.html'; // Incluir el mapa ?>
    </div>

    <div class="columna2">
        <div class="cuando_viajas">
            <?php include 'Includes/calendario.html'; // Incluir el calendario ?>
            <h2>Zein ordutan jasoko dituzu zure bidaiariak?</h2>
            <input type="time" id="timeInput" value="10:00"> <!-- Campo para seleccionar la hora -->
        </div>

        <div class="pasajeros_precio">
            <div class="pasajeros">
                <h2>Zenbat bidaiari eraman ditzakezu bidaia honetan?</h2>
                <div class="icon-container">
                    <div class="icon circle" id="decrease">-</div>
                    <div>
                        <span class="number" id="number">2</span> <!-- Mostrar número de pasajeros -->
                    </div>
                    <div class="icon circle" id="increase">+</div>
                </div>
            </div>
            <div class="precio">
                <h2>Jarri eserleku bakoitzeko prezioa</h2>
                <div class="icon-container">
                    <div class="icon circle" id="precio_decrease">-</div>
                    <div>
                        <span class="number" id="precio_number">2</span>
                        <a class="number">€</a> <!-- Mostrar precio por asiento -->
                    </div>
                    <div class="icon circle" id="precio_increase">+</div>
                </div>
            </div>
        </div>
    </div>

    <div class="columna3">
        <div class="tipo_discapacidad">
            <h2>Zer eskaintzen duzu:</h2>
            <div class="filtro-opcion">
                <label>
                    <span>
                        <input type="checkbox" name="discapacidad" value="Fisica" class="radio-grande" />
                        Fisikoa
                    </span>
                    <span class="material-symbols-outlined">accessible</span>
                </label>
            </div>
            <div class="filtro-opcion">
                <label>
                    <span>
                        <input type="checkbox" name="discapacidad" value="Mental" class="radio-grande" />
                        Mentala
                    </span>
                    <span class="material-symbols-outlined">psychology</span>
                </label>
            </div>
            <div class="filtro-opcion">
                <label>
                    <span>
                        <input type="checkbox" name="discapacidad" value="Auditiva" class="radio-grande" />
                        Entzumena
                    </span>
                    <span class="material-symbols-outlined">volume_up</span>
                </label>
            </div>
            <div class="filtro-opcion">
                <label>
                    <span>
                        <input type="checkbox" name="discapacidad" value="Visual" class="radio-grande" />
                        Ikusmena
                    </span>
                    <span class="material-symbols-outlined">visibility</span>
                </label>
            </div>
        </div>
        <div class="tambien_vuelves">
            <h2>Itzulerako bidaia egin nahi duzu?</h2>
            <input class="tambien_vuelves_input" type="radio" name="vuelves" value="si"> Bai<br>
            <h3 id="h3aquehoravuelves">¿A que hora vuelves?</h3>
            <input class="si_vuelve_none" type="time" id="timeVuelta" value="10:00"><br>
            <input class="tambien_vuelves_input" type="radio" name="vuelves" value="no"> Ez<br>
            <script src="Scripts/viaje.js"></script>
            <div>
                <form id="formulario_viaje" action="ManejoDatos/enviar_viaje.php" method="POST" onsubmit="return actualizarCampoOculto()">
                    <!-- Campos ocultos para enviar datos -->
                    <input type="hidden" name="hidden_origen" id="hidden_origen">
                    <input type="hidden" name="hidden_destino" id="hidden_destino">
                    <input type="hidden" name="fechaSalida" id="FechaInicio">
                    <input type="hidden" name="fechaFin" id="FechaFin">
                    <input type="hidden" name="horaSalida" id="hidden_horaSalida" value="10:00:00">
                    <input type="hidden" name="numPasajeros" id="numPasajeros" value="2">
                    <input type="hidden" name="precioAsiento" id="precioAsiento" value="2">
                    <input type="hidden" name="discapacidades" id="discapacidades">
                    <input type="hidden" name="horaVuelta" id="hidden_horaVuelta" value="10:00:00">

                    <!-- Botón para enviar el formulario -->
                    <button class="boton_guardar_datos" type="submit">Datuak gorde</button>
                </form>
            </div>
        </div>
    </div>

</div>

<?php include 'Includes/footer.html'; ?>

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function () {
        // Escuchar el cambio en el campo de hora de salida
        document.getElementById('timeInput').addEventListener('change', function (e) {
            let horaSeleccionada = e.target.value;  // Obtener la hora seleccionada
            document.getElementById('hidden_horaSalida').value = horaSeleccionada;  // Asignarla al campo oculto
            console.log(horaSeleccionada);
        });

        // Escuchar el cambio en el campo de hora de vuelta
        document.getElementById('timeVuelta').addEventListener('change', function (es) {
            let horaVueltaSeleccionada = es.target.value;  // Obtener la hora seleccionada
            document.getElementById('hidden_horaVuelta').value = horaVueltaSeleccionada;  // Asignarla al campo oculto
            console.log(horaVueltaSeleccionada);
        });
    });
</script>
