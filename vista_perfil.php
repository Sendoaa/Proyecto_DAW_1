<?php
session_start();

// Verificar si se ha recibido el ID del conductor mediante POST
if (isset($_POST['idConductor'])) {
    $idConductor = $_POST['idConductor'];

    // Intentamos conectarnos a la base de datos
    try {
        // Conexión a la base de datos
        $pdo = new PDO('mysql:host=dbmopa.cxzeapfroqne.us-east-1.rds.amazonaws.com;dbname=mopa', 'adminMopa', 'adminsmopa');        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec("set names utf8mb4");
        // Consulta para obtener los datos del usuario a partir del IdConductor
        $stmt = $pdo->prepare("
            SELECT u.Nombre, u.Apellidos, u.Telefono, u.FechaNac, u.Correo, u.Foto, u.Rating, u.CantidadViajes, u.Rol
            FROM usuarios u 
            JOIN conductor c ON u.IdUsuario = c.IdUsuario 
            WHERE c.IdConductor = :idConductor
        ");
        $stmt->execute(['idConductor' => $idConductor]);
        $usuarioSeleccionado = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuarioSeleccionado) {
            $nombreUser = $usuarioSeleccionado['Nombre'];
            $apellidos = $usuarioSeleccionado['Apellidos'];
            $telefono = $usuarioSeleccionado['Telefono'];
            $fechaNac = $usuarioSeleccionado['FechaNac'];
            $correo = $usuarioSeleccionado['Correo'];
            $foto = $usuarioSeleccionado['Foto'];
            $rating = $usuarioSeleccionado['Rating'];
            $cantidadViajes = $usuarioSeleccionado['CantidadViajes'];
            $conduce = $usuarioSeleccionado['Rol'];

            // Consulta para verificar si el usuario tiene un coche
            $stmt = $pdo->prepare("
                SELECT * 
                FROM vehiculo 
                WHERE IdConductor = :idConductor
            ");
            $stmt->execute(['idConductor' => $idConductor]);
            $vehiculo = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            echo "No se encontró el conductor seleccionado.";
        }
    } catch (PDOException $e) {
        echo "No se ha podido conectar a la base de datos: " . $e->getMessage();
        exit;
    }
} else {
    echo "No se ha recibido el ID del conductor.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="eu">
    

<head>
    <link rel="stylesheet" href="Styles/perfil.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="Shortcut icon" href="img/mopa.ico" type="image/x-icon">
    <title>Profila</title>
</head>

<header>
    <?php include 'Includes/header.php'; ?>
</header>

<body>

    <div class="todo">

        <!-- Parte de la izquierda -->
        <div class="parte-izda">
            <?php
            if ($foto) {
                echo '<img src="' . htmlspecialchars($foto) . '" alt="Foto perfil" class="img-perfil" style="width: 100%; height: auto; max-width: 150px; max-height: 150px;">';
            } else {
                echo '<img src="img/foto-por-defecto.jpg" alt="Foto perfil por defecto" class="img-perfil" style="width: 100%; height: auto; max-width: 100px; max-height: 100px;">';
            }

            echo "<br><br>";
            echo "<h2>" . htmlspecialchars($nombreUser) . "</h2>";
            echo "<h2 style='margin-top:-15px;'>" . htmlspecialchars($apellidos) . "</h2>";

            if ($conduce == 'Conductor') {
                echo "<h3>Gidaria</h3>";
            } else {
                echo "<h3>Bidaiaria</h3>";
            }

            echo "<div class='rating'>";
            echo "<span class='material-symbols-outlined' style='font-size: 38px;'>star</span>";
            echo "<h2>" . htmlspecialchars($rating) . "</h2>";
            echo "</div>";

            echo "<p>Profilaren sortze-data:</p>";
            echo "<h3 style='margin-top:-15px;'>" . htmlspecialchars($fechaNac) . "</h3>";
            echo "<p>Egin dituzu <strong>" . htmlspecialchars($cantidadViajes) . "</strong> bidai</p>";
            ?>
        </div>

        <span style="border-left: 2px solid rgba(0, 0, 0, 0.5); height: 76%; display: inline-block; margin-left:-30px; opacity: 13%; position: absolute; top: 50%; transform: translateY(-50%);"></span>

        <!-- Parte central -->
        <div class="parte-central">
            <h1>Informazio pertsonala</h1>
            <hr>

            <div id="perfil-datos">
                <div class="datos">
                    <p>Izena: &nbsp;</p>
                    <h3><?php echo htmlspecialchars($nombreUser); ?></h3>
                </div>

                <div class="datos">
                    <p>Abizenak:&nbsp;</p>
                    <h3><?php echo htmlspecialchars($apellidos); ?></h3>
                </div>

                <div class="datos">
                    <p>Telefono zenbakia:&nbsp;</p>
                    <h3><?php echo htmlspecialchars($telefono); ?></h3>
                </div>

                <div class="datos-email">
                    <p>Posta elektronikoa:&nbsp;</p>
                    <h3><?php echo htmlspecialchars($correo); ?></h3>
                </div>

                <br>
            </div>
        </div>

        <!-- Parte derecha -->
        <div class="parte-dcha">
            <h1>Autoak</h1>
            <span class="span-coche"></span>
            <div class="coche-añadido">
                <h2>Autoaren datuak</h2>
                <?php if ($vehiculo): ?>
                    <p><strong style="color: #0A8754;">Matrikula:</strong> <?= htmlspecialchars($vehiculo['Matricula']) ?></p>
                    <p><strong style="color: #0A8754;">Modeloa:</strong> <?= htmlspecialchars($vehiculo['Modelo']) ?></p>
                    <p><strong style="color: #0A8754;">Marka:</strong> <?= htmlspecialchars($vehiculo['Marca']) ?></p>
                    <p><strong style="color: #0A8754;">Kolorea:</strong> <?= htmlspecialchars($vehiculo['Color']) ?></p>
                    <p><strong style="color: #0A8754;">Eserlekuak:</strong> <?= htmlspecialchars($vehiculo['Plazas']) ?></p>
                <?php else: ?>
                    <p>No hay vehículos registrados para este usuario.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

<footer>
    <?php include 'Includes/footer.html'; ?>
</footer>

</html>
