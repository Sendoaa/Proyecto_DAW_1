<!DOCTYPE html>
<html lang="eus">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nire Bidaiak</title>
    <link rel="stylesheet" href="Styles/mis-viajes.css">
    <link rel="Shortcut icon" href="img/mopa.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />

</head>

<?php
session_start();

if (!isset($_SESSION['idusuario'])) {
    header("Location: index.php");
    exit;
}

function obtenerIconosDiscapacidad($tipoDiscapacidad)
{
    $iconos = [];
    $tipos = explode("-", $tipoDiscapacidad);

    foreach ($tipos as $tipo) {
        switch ($tipo) {
            case 'Fisica':
                $iconos[] = "<span class='material-symbols-outlined' title='Física'>accessible</span>";
                break;
            case 'Mental':
                $iconos[] = "<span class='material-symbols-outlined' title='Mental'>psychology</span>";
                break;
            case 'Auditiva':
                $iconos[] = "<span class='material-symbols-outlined' title='Auditiva'>volume_up</span>";
                break;
            case 'Visual':
                $iconos[] = "<span class='material-symbols-outlined' title='Visual'>visibility</span>";
                break;
        }
    }

    return implode(" ", $iconos);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cancelar'])) {
    $idViaje = $_POST['idViaje'];
    $servername = "dbmopa.cxzeapfroqne.us-east-1.rds.amazonaws.com";
    $username = "adminMopa";
    $password = "adminsmopa";
    $dbname = "mopa";
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    $sql = "UPDATE viaje SET Estado = 'Cancelado' WHERE IdViaje = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $idViaje);
        $stmt->execute();
        $stmt->close();
    }

    $conn->close();
}
?>

<?php include 'Includes/header.php'; ?>

<body onload="mostrarPasajeros()">
    <div class="container">
        <div class="button-group">
            <button id="btn-pasajeros" onclick="mostrarPasajeros()">Bidaiariaren Bidaiak</button>
            <button id="btn-conductor" onclick="mostrarConductor()">Gidariaren Bidaiak</button>
        </div>

        <!-- Viajes como pasajeros -->
        <div class="main-content hidden" id="viajes-pasajeros">
            <h1>Etorkizuneko Bidaiari Bidaiak</h1>
            <hr>
            <?php
              $servername = "dbmopa.cxzeapfroqne.us-east-1.rds.amazonaws.com";
              $username = "adminMopa";
              $password = "adminsmopa";
              $dbname = "mopa";
            $conn = new mysqli($servername, $username, $password, $dbname);
            if ($conn->connect_error) {
                die("Conexión fallida: " . $conn->connect_error);
            }

            $sql = "SELECT v.IdViaje, v.Origen, v.Destino, v.FechaInicio, v.FechaFin, v.HoraSalida, v.HoraVuelta, v.PlazasDisponibles, v.Estado, v.TipoDiscapacidad 
                    FROM viaje v 
                    INNER JOIN contrata co ON v.IdViaje = co.IdViaje 
                    WHERE co.IdUsuario = ? AND v.FechaFin >= CURDATE() AND v.Estado != 'Cancelada'";

            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("i", $_SESSION['idusuario']);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<div class='viaje'>"; // Contenedor principal del viaje
            
                        // Columna izquierda con la información del origen
                        echo "<div class='viaje-info-izquierda'>";
                        echo "<h2>" . $row["Origen"] . "</h2>";
                        echo "<p><strong>Hasiera data:</strong> " . $row["FechaInicio"] . "</p>";
                        echo "<p><strong>Hasiera ordua:</strong> " . $row["HoraSalida"] . "</p>";
                        echo "</div>"; // Cierra columna izquierda
            
                        // Columna derecha con el destino, fecha y hora de vuelta
                        echo "<div class='viaje-info-derecha'>";
                        echo "<h2>" . $row["Destino"] . "</h2>";
                        echo "<p><strong>Amaiera data:</strong> " . $row["FechaFin"] . "</p>";
                        echo "<p><strong>Itzulera ordua:</strong> " . $row["HoraVuelta"] . "</p>";
                        echo "</div>"; // Cierra columna derecha
            
                        // Columna central con estado, plazas y tipo de diversidad funcional
                        echo "<div class='viaje-info-central'>";

                        // Determinar el color del estado
                        $statusClass = '';
                        $estadoTexto = ''; // Variable para almacenar el estado en euskera
            
                        switch ($row["Estado"]) {
                            case 'Cancelada':
                                $statusClass = 'estado-cancelado'; // Rojo
                                $estadoTexto = 'Baliogabetuta'; // Cancelada en euskera
                                break;
                            case 'Confirmada':
                                $statusClass = 'estado-confirmado'; // Verde
                                $estadoTexto = 'Baieztatuta'; // Confirmada en euskera
                                break;
                            case 'Pendiente':
                                $statusClass = 'estado-pendiente'; // Amarillo
                                $estadoTexto = 'Zain'; // Pendiente en euskera
                                break;
                            case 'Terminada':
                                $statusClass = 'estado-terminado'; // Gris
                                $estadoTexto = 'Amaituta'; // Terminada en euskera
                                break;
                            default:
                                $statusClass = ''; // Sin clase por defecto
                                $estadoTexto = $row["Estado"]; // Mantener el valor original si no coincide
                                break;
                        }

                        echo "<p class='$statusClass'><strong>Egoera:</strong> $estadoTexto</p>";
                        echo "<p><strong>Eserlekuak:</strong> " . $row["PlazasDisponibles"] . "</p>";
                        echo "<p><strong>Mota:</strong> <span style='vertical-align: middle;'>" . obtenerIconosDiscapacidad($row['TipoDiscapacidad']) . "</span></p>";
                        echo "</div>"; // Cierra columna central
                        echo "</div>"; // Cierra contenedor principal del viaje
                    }
                } else {
                    echo "Ez da etorkizuneko bidaiarik aurkitu.";
                }
                $stmt->close();
            } else {
                echo "Kontsulta errorea: " . $conn->error;
            }

            $conn->close();
            ?>
        </div>

        <!-- Muestra los viajes pasados del pasajero -->
        <div class="viajes-pasajeros" id="viajes-pasajeros-antiguo">
            <h1>Bidaiarien Iraganeko Bidaiak</h1>
            <hr>
            <?php
            $conn = new mysqli($servername, $username, $password, $dbname);
            if ($conn->connect_error) {
                die("Conexión fallida: " . $conn->connect_error);
            }

            $sql = "SELECT v.IdViaje, v.Origen, v.Destino, v.FechaInicio, v.FechaFin, v.HoraSalida, v.HoraVuelta, v.PlazasDisponibles, v.Estado, v.TipoDiscapacidad 
                    FROM viaje v 
                    INNER JOIN contrata co ON v.IdViaje = co.IdViaje 
                    WHERE co.IdUsuario = ? AND (v.FechaFin < CURDATE() OR v.Estado = 'Cancelada')";

            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("i", $_SESSION['idusuario']);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<div class='viaje'>"; // Contenedor principal del viaje
            
                        // Columna izquierda con la información del origen
                        echo "<div class='viaje-info-izquierda'>";
                        echo "<h2>" . $row["Origen"] . "</h2>";
                        echo "<p><strong>Hasiera data:</strong> " . $row["FechaInicio"] . "</p>";
                        echo "<p><strong>Irteera ordua:</strong> " . $row["HoraSalida"] . "</p>";
                        echo "</div>"; // Cierra columna izquierda
            
                        // Columna derecha con el destino, fecha y hora de vuelta
                        echo "<div class='viaje-info-derecha'>";
                        echo "<h2>" . $row["Destino"] . "</h2>";
                        echo "<p><strong>Amaiera data:</strong> " . $row["FechaFin"] . "</p>";
                        echo "<p><strong>Itzulera ordua:</strong> " . $row["HoraVuelta"] . "</p>";
                        echo "</div>"; // Cierra columna derecha
            
                        // Columna central con estado, plazas y tipo de diversidad funcional
                        echo "<div class='viaje-info-central'>";

                        $statusClass = '';
                        $estadoTexto = ''; // Variable para almacenar el estado en euskera
            
                        switch ($row["Estado"]) {
                            case 'Cancelada':
                                $statusClass = 'estado-cancelado'; // Rojo
                                $estadoTexto = 'Baliogabetuta'; // Cancelada en euskera
                                break;
                            case 'Confirmada':
                                $statusClass = 'estado-confirmado'; // Verde
                                $estadoTexto = 'Baieztatuta'; // Confirmada en euskera
                                break;
                            case 'Pendiente':
                                $statusClass = 'estado-pendiente'; // Amarillo
                                $estadoTexto = 'Zain'; // Pendiente en euskera
                                break;
                            case 'Terminada':
                                $statusClass = 'estado-terminado'; // Gris
                                $estadoTexto = 'Amaituta'; // Terminada en euskera
                                break;
                            default:
                                $statusClass = ''; // Sin clase por defecto
                                $estadoTexto = $row["Estado"]; // Mantener el valor original si no coincide
                                break;
                        }

                        echo "<p class='$statusClass'><strong>Egoera:</strong> $estadoTexto</p>";

                        echo "<p><strong>Eserlekuak:</strong> " . $row["PlazasDisponibles"] . "</p>";
                        echo "<p><strong>Mota:</strong> <span style='vertical-align: middle;'>" . obtenerIconosDiscapacidad($row['TipoDiscapacidad']) . "</span></p>";
                        echo "</div>"; // Cierra columna central
            
                        echo "</div>"; // Cierra contenedor principal del viaje
                    }
                } else {
                    echo "Ez da aurkitu iraganeko bidaiarik.";
                }
                $stmt->close();
            } else {
                echo "Kontsulta errorea: " . $conn->error;
            }

            $conn->close();
            ?>
        </div>

        <!-- Viajes como conductor -->
        <div class="main-content" id="viajes-conductor">
            <h1>Etorkizuneko Gidari Bidaiak</h1>
            <hr>
            <?php
              $servername = "dbmopa.cxzeapfroqne.us-east-1.rds.amazonaws.com";
              $username = "adminMopa";
              $password = "adminsmopa";
              $dbname = "mopa";
            $conn = new mysqli($servername, $username, $password, $dbname);
            if ($conn->connect_error) {
                die("Conexión fallida: " . $conn->connect_error);
            }

            // Consulta para obtener los viajes futuros del conductor
            $sql = "SELECT v.IdViaje, v.Origen, v.Destino, v.FechaInicio, v.FechaFin, v.HoraSalida, v.HoraVuelta, v.PlazasDisponibles, v.Estado, v.TipoDiscapacidad 
            FROM viaje v 
            INNER JOIN conductor c ON v.IdConductor = c.IdConductor 
            WHERE c.IdUsuario = ? AND v.FechaFin >= CURDATE() AND v.Estado != 'Cancelada'";

            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("i", $_SESSION['idusuario']);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<div class='viaje'>"; // Contenedor principal del viaje
            
                        // Información del viaje (origen, destino, fechas)
                        echo "<div class='viaje-info-izquierda'>";
                        echo "<h2>" . $row["Origen"] . "</h2>";
                        echo "<p><strong>Hasiera data:</strong> " . $row["FechaInicio"] . "</p>";
                        echo "<p><strong>Irteera ordua:</strong> " . $row["HoraSalida"] . "</p>";
                        echo "</div>";

                        echo "<div class='viaje-info-derecha'>";
                        echo "<h2>" . $row["Destino"] . "</h2>";
                        echo "<p><strong>Amaiera data:</strong> " . $row["FechaFin"] . "</p>";
                        echo "<p><strong>Itzulera ordua:</strong> " . $row["HoraVuelta"] . "</p>";
                        echo "</div>";

                        // Estado del viaje y acción de cancelar
                        echo "<div class='viaje-info-central'>";
                        $statusClass = '';
                        $estadoTexto = '';

                        switch ($row["Estado"]) {
                            case 'Cancelada':
                                $statusClass = 'estado-cancelado';
                                $estadoTexto = 'Baliogabetuta';
                                break;
                            case 'Confirmada':
                                $statusClass = 'estado-confirmado';
                                $estadoTexto = 'Baieztatuta';
                                break;
                            case 'Pendiente':
                                $statusClass = 'estado-pendiente';
                                $estadoTexto = 'Baieztatzeko';
                                break;
                            case 'Terminada':
                                $statusClass = 'estado-terminado';
                                $estadoTexto = 'Amaituta';
                                break;
                            default:
                                $statusClass = '';
                                $estadoTexto = $row["Estado"];
                                break;
                        }

                        echo "<p class='$statusClass'><strong>Egoera:</strong> $estadoTexto</p>";
                        echo "<p><strong>Eserlekuak:</strong> " . $row["PlazasDisponibles"] . "</p>";
                        echo "<p><strong>Mota:</strong> <span style='vertical-align: middle;'>" . obtenerIconosDiscapacidad($row['TipoDiscapacidad']) . "</span></p>";

                        // Botón para cancelar el viaje
                        if ($row['Estado'] != 'Cancelado') {
                            echo "<form method='post' action='ManejoDatos/cancelar-viaje.php'>";
                            echo "<input type='hidden' name='IdViaje' value='" . $row['IdViaje'] . "'>";
                            echo "<button type='submit' name='cancelar' onclick='return confirm(\"Zihur zaude bidaia ezabatu nahi duzula?\");'>Bidaia ezeztatu</button>";
                            echo "</form>";
                        }

                        echo "</div>"; // Cierra columna central
                        echo "</div>"; // Cierra contenedor principal del viaje
                    }
                } else {
                    echo "Ez da etorkizuneko bidaiarik aurkitu.";
                }
                $stmt->close();
            } else {
                echo "Kontsulta errorea: " . $conn->error;
            }

            $conn->close();
            ?>
        </div>

        <!-- Muestra los viajes pasados del conductor -->
        <div class="viajes-conductor" id="viajes-conductor-antiguo">
            <h1>Gidariaren Iraganeko Bidaiak</h1>
            <hr>
            <?php
            $conn = new mysqli($servername, $username, $password, $dbname);
            if ($conn->connect_error) {
                die("Conexión fallida: " . $conn->connect_error);
            }

            // Consulta para obtener los viajes pasados del conductor
            $sql = "SELECT v.IdViaje, v.Origen, v.Destino, v.FechaInicio, v.FechaFin, v.HoraSalida, v.HoraVuelta, v.PlazasDisponibles, v.Estado, v.TipoDiscapacidad 
            FROM viaje v 
            INNER JOIN conductor c ON v.IdConductor = c.IdConductor 
            WHERE c.IdUsuario = ? AND (v.FechaFin < CURDATE() OR v.Estado = 'Cancelada')";

            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("i", $_SESSION['idusuario']);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<div class='viaje'>"; // Contenedor principal del viaje
            
                        // Información del viaje (origen, destino, fechas)
                        echo "<div class='viaje-info-izquierda'>";
                        echo "<h2>" . $row["Origen"] . "</h2>";
                        echo "<p><strong>Hasiera data:</strong> " . $row["FechaInicio"] . "</p>";
                        echo "<p><strong>Irteera ordua:</strong> " . $row["HoraSalida"] . "</p>";
                        echo "</div>";

                        echo "<div class='viaje-info-derecha'>";
                        echo "<h2>" . $row["Destino"] . "</h2>";
                        echo "<p><strong>Amaiera data:</strong> " . $row["FechaFin"] . "</p>";
                        echo "<p><strong>Itzulera ordua:</strong> " . $row["HoraVuelta"] . "</p>";
                        echo "</div>";

                        // Estado del viaje
                        echo "<div class='viaje-info-central'>";
                        $statusClass = '';
                        $estadoTexto = '';

                        switch ($row["Estado"]) {
                            case 'Cancelada':
                                $statusClass = 'estado-cancelado';
                                $estadoTexto = 'Baliogabetuta';
                                break;
                            case 'Confirmada':
                                $statusClass = 'estado-confirmado';
                                $estadoTexto = 'Baieztatuta';
                                break;
                            case 'Pendiente':
                                $statusClass = 'estado-pendiente';
                                $estadoTexto = 'Zain';
                                break;
                            case 'Terminada':
                                $statusClass = 'estado-terminado';
                                $estadoTexto = 'Amaituta';
                                break;
                            default:
                                $statusClass = '';
                                $estadoTexto = $row["Estado"];
                                break;
                        }

                        echo "<p class='$statusClass'><strong>Egorea:</strong> $estadoTexto</p>";
                        echo "<p><strong>Eserlekuak:</strong> " . $row["PlazasDisponibles"] . "</p>";
                        echo "<p><strong>Mota:</strong> <span style='vertical-align: middle;'>" . obtenerIconosDiscapacidad($row['TipoDiscapacidad']) . "</span></p>";
                        echo "</div>"; // Cierra columna central
            
                        echo "</div>"; // Cierra contenedor principal del viaje
                    }
                } else {
                    echo "Ez da iraganeko bidaiarik aurkitu.";
                }
                $stmt->close();
            } else {
                echo "Kontsulta errorea: " . $conn->error;
            }

            $conn->close();
            ?>
        </div>
    </div>

    <script>
        function mostrarConductor() {
            document.getElementById('viajes-conductor').classList.remove('hidden');
            document.getElementById('viajes-conductor-antiguo').classList.remove('hidden');
            document.getElementById('viajes-pasajeros').classList.add('hidden');
            document.getElementById('viajes-pasajeros-antiguo').classList.add('hidden');
            document.getElementById('btn-conductor').style.backgroundColor = '#007BFF';
            document.getElementById('btn-pasajeros').style.backgroundColor = '#04344B';
          

        }

        function mostrarPasajeros() {
            document.getElementById('viajes-conductor').classList.add('hidden');
            document.getElementById('viajes-conductor-antiguo').classList.add('hidden');
            document.getElementById('viajes-pasajeros').classList.remove('hidden');
            document.getElementById('viajes-pasajeros-antiguo').classList.remove('hidden');
            document.getElementById('btn-pasajeros').style.backgroundColor = '#007BFF';
            document.getElementById('btn-conductor').style.backgroundColor = '#04344B';
        }
    </script>

    <?php include 'Includes/footer.html'; ?>
</body>

</html>