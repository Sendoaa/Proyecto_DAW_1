<?php
session_start();

$servername = "dbmopa.cxzeapfroqne.us-east-1.rds.amazonaws.com";
$username = "adminMopa";
$password = "adminsmopa";
$dbname = "mopa";
// Conexión a la base de datos
$conexion = new mysqli("dbmopa.cxzeapfroqne.us-east-1.rds.amazonaws.com", "adminMopa", "adminsmopa", "mopa");
if ($conexion->connect_error) {
    die("Conexión fallida: {$conexion->connect_error}");
}

// Función para obtener todos los viajes
function obtenerViajes($conexion, $usuarioId = null, $estado = null)
{
    $query = "SELECT v.*, u.Nombre AS nombre_conductor, c.IdConductor 
              FROM viaje v 
              JOIN conductor c ON v.IdConductor = c.IdConductor 
              JOIN usuarios u ON c.IdUsuario = u.IdUsuario 
              WHERE v.Estado != 'Cancelada'"; // Excluir viajes cancelados

    if ($usuarioId) {
        $query .= " AND c.IdConductor = ?";
    }

    if ($estado) {
        $query .= " AND v.Estado = ?";
    }

    $stmt = $conexion->prepare($query);

    if ($usuarioId && $estado) {
        $stmt->bind_param('is', $usuarioId, $estado);
    } elseif ($usuarioId) {
        $stmt->bind_param('i', $usuarioId);
    } elseif ($estado) {
        $stmt->bind_param('s', $estado);
    }

    $stmt->execute();
    return $stmt->get_result();
}

// Función para cancelar un viaje (actualizar el estado a 'Cancelada')
function cancelarViaje($conexion, $idViaje)
{
    $query = "UPDATE viaje SET Estado = 'Cancelada' WHERE IdViaje = ? AND Estado != 'Terminada'";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param('i', $idViaje);
    return $stmt->execute();
}

// Función para borrar un viaje (eliminar registro)
function borrarViaje($conexion, $idViaje)
{
    $query = "DELETE FROM viaje WHERE IdViaje = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param('i', $idViaje);
    return $stmt->execute();
}

// Manejo de las acciones de cancelar o borrar viaje
if (isset($_GET['accion']) && isset($_GET['id'])) {
    $idViaje = intval($_GET['id']);
    if ($_GET['accion'] === 'cancelar') {
        cancelarViaje($conexion, $idViaje);
    } elseif ($_GET['accion'] === 'borrar') {
        borrarViaje($conexion, $idViaje);
    }
    header('Location: viajes_admin.php'); // Redirige después de la acción
    exit();
}

// Obtiene todos los viajes
$usuarioId = isset($_GET['usuario']) ? intval($_GET['usuario']) : null;
$estado = isset($_GET['estado']) ? $_GET['estado'] : null;
$viajes = obtenerViajes($conexion, $usuarioId, $estado);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bidaiak Administratzea</title>
    <link rel="stylesheet" href="Styles/viajes-admin.css">
    <link rel="Shortcut icon" href="img/mopa.ico" type="image/x-icon">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible:ital,wght@0,400;0,700;1,400;1,700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" />
    <script>
        function enviarFormulario(idConductor) {
            document.getElementById('conductorIdForm' + idConductor).submit();
        }
    </script>
</head>

<header>
    <?php include 'Includes/header.php'; ?>
</header>

<body>
    <h1>Bidaiak Administratzea</h1>

    <!---------------------------------------------------------FILTRO POR USUARIO------------------------------------------------------------------>
    <form method="GET" action="viajes_admin.php">
        <label for="usuario">Erabiltzailearen arabera iragazi:</label>
        <select name="usuario" id="usuario" onchange="this.form.submit()">
            <option value="">-- Gidari guztiak --</option>
            <?php
            // Obtiene todos los conductores para el filtro
            $conductores = $conexion->query("SELECT c.IdConductor, u.Nombre, u.Apellidos 
                                             FROM conductor c 
                                             JOIN usuarios u ON c.IdUsuario = u.IdUsuario");
            while ($conductor = $conductores->fetch_assoc()) {
                $selected = (isset($_GET['usuario']) && $_GET['usuario'] == $conductor['IdConductor']) ? 'selected' : '';
                echo "<option value=\"{$conductor['IdConductor']}\" $selected>{$conductor['Nombre']} {$conductor['Apellidos']}</option>";
            }
            ?>
        </select>
    <!--------------------------------------------------------------FILTRO POR ESTADO-------------------------------------------------------------->
        <label for="estado">Egoeraren arabera iragazi:</label>
        <select name="estado" id="estado" onchange="this.form.submit()">
            <option value="">-- Egoera guztiak --</option>
            <?php
            $estados = ["Pendiente", "Confirmada", "Terminada"];
            $estadosEuskera = [
                "Pendiente" => "Baieztatzeko",
                "Confirmada" => "Baieztatuta",
                "Terminada" => "Amaituta"
            ];
            foreach ($estados as $estadoOption) {
                $selected = (isset($_GET['estado']) && $_GET['estado'] == $estadoOption) ? 'selected' : '';
                $labelEuskera = $estadosEuskera[$estadoOption];
                echo "<option value=\"$estadoOption\" $selected>$labelEuskera</option>";
            }
            ?>
        </select>
    </form>

    <table>
        <thead>
            <tr>
                <th>Bidaia ID</th>
                <th>Gidaria</th>
                <th>Jatorria</th>
                <th>Helmuga</th>
                <th>Hasiera Data</th>
                <th>Egoera</th>
                <th width="270px;">Ekintzak</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($viaje = $viajes->fetch_assoc()) {
                echo "<tr>";
                echo "<td onclick=\"enviarFormulario({$viaje['IdConductor']});\">{$viaje['IdViaje']}</td>";
                echo "<td onclick=\"enviarFormulario({$viaje['IdConductor']});\">{$viaje['nombre_conductor']}</td>";
                echo "<td onclick=\"enviarFormulario({$viaje['IdConductor']});\">{$viaje['Origen']}</td>";
                echo "<td onclick=\"enviarFormulario({$viaje['IdConductor']});\">{$viaje['Destino']}</td>";
                echo "<td onclick=\"enviarFormulario({$viaje['IdConductor']});\">{$viaje['FechaInicio']}</td>";

                // Determinar el color del estado
                $statusClass = '';
                $estadoTexto = ''; // Variable para almacenar el estado en euskera
            
                switch ($viaje["Estado"]) {
                    case 'Confirmada':
                        $statusClass = 'estado-confirmado'; // Verde
                        $estadoTexto = 'Baieztatuta'; // Confirmada en euskera
                        break;
                    case 'Pendiente':
                        $statusClass = 'estado-pendiente'; // Amarillo
                        $estadoTexto = 'Baieztatzeko'; // Pendiente en euskera
                        break;
                    case 'Terminada':
                        $statusClass = 'estado-terminado'; // Gris
                        $estadoTexto = 'Amaituta'; // Terminada en euskera
                        break;
                    default:
                        $statusClass = ''; // Sin clase por defecto
                        $estadoTexto = $viaje["Estado"]; // Mantener el valor original si no coincide
                        break;
                }

                echo "<td class='$statusClass' onclick=\"enviarFormulario({$viaje['IdConductor']});\">$estadoTexto</td>";
                echo "<td>";

                // Mostrar enlace de cancelación solo si el estado no es "Terminada"
                if ($viaje["Estado"] !== "Terminada") {
                    echo "<a href='?accion=cancelar&id={$viaje['IdViaje']}' onclick='return confirm(\"Ziur zaude bidaia hau ezeztatu nahi duzula?\");'>Bidaia ezeztatu</a> | ";
                }
                
                // Enlace para borrar el viaje
                echo "<a href='?accion=borrar&id={$viaje['IdViaje']}' onclick='return confirm(\"Ziur zaude bidaia hau ezabatu nahi duzula?\");'>Bidaia ezabatu</a>";
                echo "</td>";

                // Formulario oculto para el envío de idConductor
                echo "<form id='conductorIdForm{$viaje['IdConductor']}' action='vista_perfil.php' method='POST' style='display:none;'>
                        <input type='hidden' name='idConductor' value='{$viaje['IdConductor']}'>
                    </form>";

                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</body>

<footer>
    <?php include 'Includes/footer.html'; ?>
</footer>

</html>
