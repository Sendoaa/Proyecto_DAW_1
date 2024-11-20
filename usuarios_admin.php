<?php
session_start();

//----------------------------------------------------------------------------------CONEXIÓN A LA BASE DE DATOS-----------------------------------------------------------------------//
try {
    $pdo = new PDO('mysql:host=dbmopa.cxzeapfroqne.us-east-1.rds.amazonaws.com;dbname=mopa', 'adminMopa', 'adminsmopa');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error al conectar a la base de datos: " . $e->getMessage());
}

//----------------------------------------------------------SI NO HAY UNA SESIÓN INICIADA Y NO ERES ADMIN, REDIRIGE A INDEX.PHP-------------------------------------------------------//
if (!isset($_SESSION['idusuario'])) {
    echo "<script>alert('Ez duzu orri honi sartzeko baimena'); window.location.href = 'index.php';</script>";
    exit();
}

$idUsuario = $_SESSION['idusuario'];
$query = "SELECT Rol FROM usuarios WHERE IdUsuario = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$idUsuario]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario || $usuario['Rol'] !== 'Admin') {
    echo "<script>alert('Ez duzu orri honi sartzeko baimena'); window.location.href = 'index.php';</script>";
    exit();
}

//---------------------------------------------------------------------FUNCIÓN PARA OBTENER TODOS LOS USUARIOS, CON OPCIONES DE FILTRADO-----------------------------------------------//
function obtenerUsuarios($pdo, $rol = null)
{
    $query = "SELECT * FROM usuarios WHERE 1";
    if (!is_null($rol) && $rol !== '') {
        $query .= " AND Rol = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$rol]);
    } else {
        $stmt = $pdo->prepare($query);
        $stmt->execute();
    }
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

//---------------------------------------------------------------------FUNCIÓN PARA ELIMINAR UN USUARIO--------------------------------------------------------------------------------//
function eliminarUsuario($pdo, $idUsuario)
{
    $query = "DELETE FROM usuarios WHERE IdUsuario = ?";
    $stmt = $pdo->prepare($query);
    return $stmt->execute([$idUsuario]);
}

// Manejo de la acción de eliminar usuario
if (isset($_GET['accion']) && isset($_GET['id'])) {
    $idUsuario = intval($_GET['id']);
    if ($_GET['accion'] === 'eliminar') {
        eliminarUsuario($pdo, $idUsuario);
    }
    header('Location: usuarios_admin.php');
    exit();
}


//---------------------------------------------------------------------FUNCIÓN PARA AÑADIR NUEVO ADMINISTRADOR------------------------------------------------------------------------//
function agregarAdministrador($pdo, $nombre, $apellidos, $telefono, $fechaNac, $correo, $foto, $contrasena)
{
    $query = "INSERT INTO usuarios (Nombre, Apellidos, Telefono, FechaNac, Correo, Contrasena, Foto, Rol) VALUES (?, ?, ?, ?, ?, ?, 'Admin')";
    $stmt = $pdo->prepare($query);
    return $stmt->execute([$nombre, $apellidos, $telefono, $fechaNac, $correo, $contrasena, $foto]);
}


// Manejo del formulario para agregar un nuevo administrador
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar_admin'])) {
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $telefono = $_POST['telefono'];
    $fechaNac = $_POST['fechaNac'];
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];
    $foto = 'img/avatar.png';

    if (agregarAdministrador($pdo, $nombre, $apellidos, $telefono, $fechaNac, $correo, $foto, $contrasena)) {
        echo "<script>alert('Administratzaile berria arrakastaz gehitu da.'); window.location.href = 'usuarios_admin.php';</script>";

    } else {
        echo "<script>alert('Administratzailea gehitzean errorea gertatu da.')</script>";
    }
}

//---------------------------------------------------------------------FUNCIÓN PARA OBTENER TODOS LOS USUARIOS------------------------------------------------------------------------//

$rol = $_GET['rol'] ?? null;
$usuarios = obtenerUsuarios($pdo, $rol);
?>




<!DOCTYPE html>
<html lang="eu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erabiltzaileen kudeaketa</title>
    <link rel="stylesheet" href="Styles/panel_usuarios.css">
    <link rel="Shortcut icon" href="img/mopa.ico" type="image/x-icon">
    <script>
        function toggleForm() {
            var form = document.getElementById('adminForm');
            if (form.style.display === 'none' || form.style.display === '') {
                form.style.display = 'block';
            } else {
                form.style.display = 'none';
            }
        }
    </script>
</head>

<header>
    <?php include 'Includes/header.php'; ?>
</header>

<body>
    <h1>Erabiltzaileak Kudeatu</h1>

    <!---------------------------------------------------------------------FILTRO POR ROL------------------------------------------------------------------------>
    <form method="GET" action="usuarios_admin.php" class="filtrar-form">
        <label for="rol">Rolaren arabera iragazi:</label>
        <select name="rol" id="rol" onchange="this.form.submit()">
            <option value="">-- Rol guztiak --</option>
            <?php
            $roles = ["Admin", "Pasajero", "Conductor"];
            $rolesEuskera = ["Admin" => "Administratzailea", "Pasajero" => "Bidaiaria", "Conductor" => "Gidaria"];
            foreach ($roles as $rolOption) {
                $selected = (isset($_GET['rol']) && $_GET['rol'] == $rolOption) ? 'selected' : '';
                echo "<option value=\"$rolOption\" $selected>{$rolesEuskera[$rolOption]}</option>";
            }
            ?>
        </select>
    </form>

    <!---------------------------------------------------------------------BOTÓN PARA AÑADIR NUEVO ADMINISTRADOR------------------------------------------------------------------------>
     <button id="toggleFormButton" class="btn-añadir-admin">↓Gehitu Administratzaile Berri Bat↓</button>
    <script>
        document.getElementById('toggleFormButton').addEventListener('click', function() {
            var form = document.getElementById('adminForm');
            if (form.style.display === 'none' || form.style.display === '') {
                form.style.display = 'block';
            } else {
                form.style.display = 'none';
            }
        });
    </script>

    <!---------------------------------------------------------------------FORMULARIO PARA AÑADIR NUEVO ADMINISTRADOR----------------------------------------------------------------------->
    <div id="adminForm" style="display: none;">
        <h2>Gehitu Administratzaile Berri Bat</h2>
        <form method="POST" action="usuarios_admin.php">
            <label for="nombre">Izena:</label>
            <input type="text" id="nombre" name="nombre" required>

            <label for="apellidos">Abizenak:</label>
            <input type="text" id="apellidos" name="apellidos" required>

            <label for="telefono">Telefonoa:</label>
            <input type="text" id="telefono" name="telefono" required>

            <label for="fechaNac">Jaiotze Data:</label>
            <input type="date" id="fechaNac" name="fechaNac" required>

            <label for="correo">Posta Elektronikoa:</label>
            <input type="email" id="correo" name="correo" required>

            <label for="contrasena">Pasahitza:</label>
            <input type="password" id="contrasena" name="contrasena" required>



            <button type="submit" name="agregar_admin">Gehitu Administratzailea</button>
        </form>
    </div>

    <!---------------------------------------------------------------------TABLA DE USUARIOS------------------------------------------------------------------------>

    <table>
        <thead>
            <tr>
                <th>Erabiltzaile ID</th>
                <th>Izena</th>
                <th>Abizenak</th>
                <th>Telefonoa</th>
                <th>Jaiotze Data</th>
                <th>Posta Elektronikoa</th>
                <th>Erregistro Data</th>
                <th>Argazkia</th>
                <th>Balorazioa</th>
                <th>Bidai Kopurua</th>
                <th>Rola</th>
                <th>Ekintzak</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($usuarios as $usuario) {
                echo "<tr>";
                echo "<td>{$usuario['IdUsuario']}</td>";
                echo "<td>{$usuario['Nombre']}</td>";
                echo "<td>{$usuario['Apellidos']}</td>";
                echo "<td>{$usuario['Telefono']}</td>";
                echo "<td>{$usuario['FechaNac']}</td>";
                echo "<td>{$usuario['Correo']}</td>";
                echo "<td>{$usuario['FechaRegistro']}</td>";
                echo "<td><img src='{$usuario['Foto']}' alt='Foto de perfil' style='width:50px;height:50px;'></td>";
                echo "<td>{$usuario['Rating']}</td>";
                echo "<td>{$usuario['CantidadViajes']}</td>";
                $rolEuskera = isset($rolesEuskera[$usuario['Rol']]) ? $rolesEuskera[$usuario['Rol']] : $usuario['Rol'];
                echo "<td>{$rolEuskera}</td>";
                echo "<td>";
                echo "<a href='?accion=eliminar&id={$usuario['IdUsuario']}' onclick='return confirm(\"¿Estás seguro de eliminar este usuario?\");' class='boton-eliminar'>Ezabatu</a>";
                echo "</td>";
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