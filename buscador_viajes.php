<?php
// Iniciar la sesión para gestionar la autenticación de usuarios
session_start();
$pdo = new PDO('mysql:host=dbmopa.cxzeapfroqne.us-east-1.rds.amazonaws.com;dbname=mopa', 'adminMopa', 'adminsmopa');
// Conexión a la base de datos
$servername = "dbmopa.cxzeapfroqne.us-east-1.rds.amazonaws.com";
$username = "adminMopa";
$password = "adminsmopa";
$dbname = "mopa";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8mb4");

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Variables para almacenar datos del usuario y filtros de búsqueda
$userId = isset($_SESSION['idusuario']) ? $_SESSION['idusuario'] : null;
$origen = isset($_POST['origen']) ? $_POST['origen'] : (isset($_SESSION['origen']) ? $_SESSION['origen'] : '');
$destino = isset($_POST['destino']) ? $_POST['destino'] : (isset($_SESSION['destino']) ? $_SESSION['destino'] : '');
$fecha = isset($_POST['fecha']) ? $_POST['fecha'] : (isset($_SESSION['fecha']) ? $_SESSION['fecha'] : '');
$pasajeros = isset($_POST['pasajeros']) ? (int) $_POST['pasajeros'] : (isset($_SESSION['pasajeros']) ? (int) $_SESSION['pasajeros'] : 1);
$ordenar = isset($_POST['ordenar']) ? $_POST['ordenar'] : (isset($_SESSION['ordenar']) ? $_SESSION['ordenar'] : '');
$discapacidad = isset($_POST['discapacidad']) ? $_POST['discapacidad'] : (isset($_SESSION['discapacidad']) ? $_SESSION['discapacidad'] : []);

// Procesar el formulario de búsqueda
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_SESSION['origen'] = $origen;
    $_SESSION['destino'] = $destino;
    $_SESSION['fecha'] = $fecha;
    $_SESSION['pasajeros'] = $pasajeros;
    $_SESSION['ordenar'] = $ordenar;
    $_SESSION['discapacidad'] = $discapacidad;
}

// Consulta para buscar viajes pendientes
$query = "SELECT v.*, u.Nombre, u.Apellidos, u.Foto, u.Rating, 
                  c.LicenciaConduccion, 
                  ve.Modelo, ve.Marca 
          FROM viaje v
          JOIN conductor c ON v.IdConductor = c.IdConductor
          JOIN usuarios u ON c.IdUsuario = u.IdUsuario
          JOIN vehiculo ve ON ve.IdConductor = c.IdConductor
          WHERE v.Estado = 'Pendiente'";
$params = [];
$types = '';

// Filtrar resultados según los criterios de búsqueda
if (!empty($origen)) {
    $query .= " AND v.Origen = ?";
    $params[] = $origen;
    $types .= 's';
}
if (!empty($destino)) {
    $query .= " AND v.Destino = ?";
    $params[] = $destino;
    $types .= 's';
}
if ($pasajeros > 0) {
    $query .= " AND v.PlazasDisponibles >= ?";
    $params[] = $pasajeros;
    $types .= 'i';
}
if (!empty($fecha)) {
    $query .= " AND DATE(v.FechaInicio) >= ?";
    $params[] = $fecha;
    $types .= 's';
}
if (!empty($discapacidad)) {
    foreach ($discapacidad as $dis) {
        $query .= " AND v.TipoDiscapacidad LIKE ?";
        $discapacidadParams[] = '%' . $dis . '%';
        $types .= 's';
    }
    $params = array_merge($params, $discapacidadParams);
}

// Preparar y ejecutar la consulta
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Error en la preparación de la consulta: " . $conn->error);
}
if (count($params) > 0) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Procesar resultados
$viajes = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $viajes[] = $row;
    }
}

// Ordenar resultados según la selección del usuario
if (!empty($ordenar)) {
    switch ($ordenar) {
        case 'salidaTemprana':
            usort($viajes, function ($a, $b) {
                return strtotime($a['HoraSalida']) - strtotime($b['HoraSalida']);
            });
            break;
        case 'precioBajo':
            usort($viajes, function ($a, $b) {
                return $a['TotalAPagar'] - $b['TotalAPagar'];
            });
            break;
        case 'viajeCorto':
            usort($viajes, function ($a, $b) {
                $duracionA = strtotime($a['HoraVuelta']) - strtotime($a['HoraSalida']);
                $duracionB = strtotime($b['HoraVuelta']) - strtotime($b['HoraSalida']);
                return $duracionA - $duracionB;
            });
            break;
        case 'mejorValorado':
            usort($viajes, function ($a, $b) {
                return $b['Rating'] - $a['Rating'];
            });
            break;
    }
}

// Funcionalidad para borrar todos los filtros
if (isset($_POST['borrar_todo'])) {
    unset($_SESSION['ordenar']);
    unset($_SESSION['discapacidad']);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="eu">

<head>
    <meta charset="UTF-8" lang="EUS" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bidai Bilaketa</title>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible:ital,wght@0,400;0,700;1,400;1,700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" />
    <link rel="Shortcut icon" href="img/mopa.ico" type="image/x-icon">
    <link rel="stylesheet" href="Styles/buscador_viajes.css" />
    <script src="Scripts/busq.js"></script>

</head>

<body>
    <?php include 'Includes/header.php'; ?>
    <section class="clasesec">
        <?php include 'Includes/nav_busqueda.html'; ?>
    </section>
    <section class="section_busq">
        <form id="filtros" method="POST" action="">
            <div class="filtro-ordenar">
                <h3>Ordenatu honen arabera:</h3>
                <button type="submit" name="borrar_todo"
                    style="background-color: transparent; color: blue; border: none; cursor: pointer;">Denak
                    ezabatu</button>
            </div>
            <div class="filtro-opcion">
                <label>
                    <span>
                        <input type="radio" name="ordenar" value="salidaTemprana" class="radio-grande" <?php if (isset($_SESSION['ordenar']) && $_SESSION['ordenar'] === 'salidaTemprana')
                            echo 'checked'; ?> />
                        Irteera goiztiarrena
                    </span>
                    <span class="material-symbols-outlined">alarm</span>
                </label>
            </div>
            <div class="filtro-opcion">
                <label>
                    <span>
                        <input type="radio" name="ordenar" value="precioBajo" class="radio-grande" <?php if (isset($_SESSION['ordenar']) && $_SESSION['ordenar'] === 'precioBajo')
                            echo 'checked'; ?> />
                        Prezio baxuena
                    </span>
                    <span class="material-symbols-outlined">attach_money</span>
                </label>
            </div>
            <div class="filtro-opcion">
                <label>
                    <span>
                        <input type="radio" name="ordenar" value="viajeCorto" class="radio-grande" <?php if (isset($_SESSION['ordenar']) && $_SESSION['ordenar'] === 'viajeCorto')
                            echo 'checked'; ?> />
                        Bidaia laburrena
                    </span>
                    <span class="material-symbols-outlined">directions</span>
                </label>
            </div>
            <div class="filtro-opcion">
                <label>
                    <span>
                        <input type="radio" name="ordenar" value="mejorValorado" class="radio-grande" <?php if (isset($_SESSION['ordenar']) && $_SESSION['ordenar'] === 'mejorValorado')
                            echo 'checked'; ?> />
                        Hoberen baloratua
                    </span>
                    <span class="material-symbols-outlined">star</span>
                </label>
            </div>
            <h4>Desgaitasun mota</h4>
            <div class="filtro-opcion">
                <label>
                    <span>
                        <input type="checkbox" name="discapacidad[]" value="fisica" class="radio-grande" <?php if (isset($_SESSION['discapacidad']) && in_array('fisica', $_SESSION['discapacidad']))
                            echo 'checked'; ?> />
                        Irisgarritasun fisikoa
                    </span>
                    <span class="material-symbols-outlined">accessible</span>
                </label>
            </div>
            <div class="filtro-opcion">
                <label>
                    <span>
                        <input type="checkbox" name="discapacidad[]" value="mental" class="radio-grande" <?php if (isset($_SESSION['discapacidad']) && in_array('mental', $_SESSION['discapacidad']))
                            echo 'checked'; ?> />
                        Laguntza kognitiboa
                    </span>
                    <span class="material-symbols-outlined">psychology</span>
                </label>
            </div>
            <div class="filtro-opcion">
                <label>
                    <span>
                        <input type="checkbox" name="discapacidad[]" value="auditiva" class="radio-grande" <?php if (isset($_SESSION['discapacidad']) && in_array('auditiva', $_SESSION['discapacidad']))
                            echo 'checked'; ?> />
                        Irisgarritasun auditiboa
                    </span>
                    <span class="material-symbols-outlined">volume_up</span>
                </label>
            </div>
            <div class="filtro-opcion">
                <label>
                    <span>
                        <input type="checkbox" name="discapacidad[]" value="visual" class="radio-grande" <?php if (isset($_SESSION['discapacidad']) && in_array('visual', $_SESSION['discapacidad']))
                            echo 'checked'; ?> />
                        Ikusmen irisgarritasuna
                    </span>
                    <span class="material-symbols-outlined">visibility</span>
                </label>
            </div>
            <button type="submit"
                style="background-color: #0a8757; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">Aplikatu
                Iragazkiak</button>
        </form>
        <div class="btn_filtros">
            <section>
                <span class="material-symbols-outlined">filter_alt</span> Iragazkiak
                <span class="material-symbols-outlined">expand_more</span>
            </section>
        </div>
        <section class="columna">
            <?php
            // Obtener la fecha de hoy y de mañana en formato timestamp
            $fechaHoy = strtotime(date('Y-m-d'));
            $fechaManana = strtotime(date('Y-m-d', strtotime('+1 day')));
            // Función para obtener los iconos de discapacidad según el tipo
            function obtenerIconosDiscapacidad($tipoDiscapacidad)
            {
                $iconos = [];
                $tipos = explode("-", $tipoDiscapacidad);
                // Se asignan iconos específicos para cada tipo de discapacidad
                foreach ($tipos as $tipo) {
                    switch ($tipo) {
                        case 'Fisica':
                            $iconos[] = "<span class='material-symbols-outlined' title='Fisikoa'>accessible</span>";
                            break;
                        case 'Mental':
                            $iconos[] = "<span class='material-symbols-outlined' title='Mental'>psychology</span>";
                            break;
                        case 'Auditiva':
                            $iconos[] = "<span class='material-symbols-outlined' title='Entzumena'>volume_up</span>";
                            break;
                        case 'Visual':
                            $iconos[] = "<span class='material-symbols-outlined' title='Ikusmena'>visibility</span>";
                            break;
                    }
                }

                return implode(" ", $iconos);
            }
            // Verificar si hay viajes disponibles
            if (!empty($viajes)) {
                foreach ($viajes as $row) {
                    $fechaInicio = strtotime($row['FechaInicio']);
                    if ($fechaInicio == $fechaHoy) {
                        $TextoInicio = "Gaur";
                    } elseif ($fechaInicio == $fechaManana) {
                        $TextoInicio = "Bihar";
                    } else {
                        $TextoInicio = date('j/n/Y', $fechaInicio);
                    }
                    // Obtener la fecha de fin y formatear el texto para mostrar
                    $fechaFin = strtotime($row['FechaFin']);
                    if ($fechaFin == $fechaHoy) {
                        $TextoFin = "Gaur";
                    } elseif ($fechaFin == $fechaManana) {
                        $TextoFin = "Bihar";
                    } else {
                        $TextoFin = date('j/n/Y', $fechaFin);
                    }
                    // Mostrar información del viaje
                    echo "<div id='resultado'>
                            <div id='horarios'>
                                <div class='horario'>
                                    <span>" . date('H:i', strtotime($row['HoraSalida'])) . "</span>
                                    <span>" . htmlspecialchars($row['Origen']) . "</span>
                                    <span class='fecha-viaje'>" . $TextoInicio . "</span>
                                </div>
                                <div class='linea'></div>
                               
                                    <div class='horario'>
                                        <span>" . date('H:i', strtotime($row['HoraVuelta'])) . "</span>
                                        <span>" . htmlspecialchars($row['Destino']) . "</span>
                                        <span class='fecha-viaje'>" . $TextoFin . "</span>
                                    </div>
                                    <div class='precio-comprar'>
                                        <span class='precio'>" . htmlspecialchars($row['TotalAPagar']) . " €</span>
                                        <button class='boton-estilo' onclick='openModal(" . htmlspecialchars($row['IdViaje']) . ")'>Erosi</button>
                                    </div>
                                </div>
                                <div id='informacion'>
                                    <div class='informacion-vehiculo'>
                                        <div>
                                            <form action='vista_perfil.php' method='POST'>
                                                <input type='hidden' name='idConductor' value='" . htmlspecialchars($row['IdConductor']) . "' />
                                                <button type='submit' class='botonPerfil'>
                                                    <span><img src='" . htmlspecialchars($row['Foto']) . "' style='width: 30px; border-radius: 50%;' /></span>
                                                    <span>" . htmlspecialchars($row['Nombre']) . " " . htmlspecialchars($row['Apellidos']) . "</span>
                                                </button>
                                            </form>
                                        </div>
                                        <div>
                                            <span class='material-symbols-outlined icono'>directions_car</span>
                                            <span>" . htmlspecialchars($row['Modelo']) . " (" . htmlspecialchars($row['Marca']) . ")</span>
                                        </div>
                                        <div>
                                            <span class='material-symbols-outlined icono'>people</span>
                                            <span>" . htmlspecialchars($row['PlazasDisponibles']) . "</span>
                                        </div>
                                        <div>
                                            <span class='material-symbols-outlined icono'>star</span>
                                            <span>" . htmlspecialchars($row['Rating']) . "</span>
                                        </div>
                                        <div>" . obtenerIconosDiscapacidad($row['TipoDiscapacidad']) . "</div>
                                    </div>
                                </div>
                            </div>
                        </div>";
                }
            } else {
                // Mensaje si no hay viajes disponibles
                echo "<div id='resultado' style='color:red;'>Ez dago bidaia erabilgarririk.</div>";
            }
            ?>
        </section>
    </section>
    <a href="#top" id="subirArriba">Itzuli gora</a>
    <div class="modal" id="confirmModal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Berrespena</h2>
            <p>Ziur zaude ekintza honekin jarraitu nahi duzula?</p>
            <div class="modal-buttons">
                <button id="confirmButton">Berretsi</button>
                <button onclick="closeModal()">Utzi</button>
            </div>
        </div>
    </div>
    <div id="loadingOverlay" style="display: none;">
        <div class="spinner"></div>
        <div class="checkmark" style="display: none;">
            &#10003;
        </div>
        <div class="xmark" style="display: none;">
            <span>&#10060;</span>
        </div>
        <p id="loadingText">Erosketa egiten...</p>
    </div>
    <?php include 'Includes/footer.html'; ?>
    <script>
        const userId = <?php echo json_encode($userId); ?>;
    </script>
</body>

</html>