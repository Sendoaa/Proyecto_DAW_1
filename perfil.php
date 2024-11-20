<!-- Por defecto pondremos una variable de inicio de sesión mediante id usuario para cargar los datos -->
<?php
session_start();



if (!isset($_SESSION['idusuario'])) {
    header("Location: index.php");
    exit();
}





// Intentamos conectarnos a la base de datos
try {
    // Conexión a la bd
    $pdo = new PDO('mysql:host=dbmopa.cxzeapfroqne.us-east-1.rds.amazonaws.com;dbname=mopa', 'adminMopa', 'adminsmopa');

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta para obtener los datos del usuario
    $stmt = $pdo->prepare("SELECT Nombre, Apellidos, Telefono, FechaNac, Correo, FechaRegistro, Foto, Rating, CantidadViajes, Rol FROM usuarios WHERE IdUsuario = :idusuario");

    $stmt->execute(['idusuario' => $_SESSION['idusuario']]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    $nombre = $usuario['Nombre'];
    $apellidos = $usuario['Apellidos'];
    $telefono = $usuario['Telefono'];
    $fechaNac = $usuario['FechaNac'];
    $correo = $usuario['Correo'];
    $fechaRegistro = $usuario['FechaRegistro'];
    $foto = $usuario['Foto'];
    $rating = $usuario['Rating'];
    $cantidadViajes = $usuario['CantidadViajes'];
    $conduce = $usuario['Rol'] === 'Conductor' ? true : false;
    $admin = $usuario['Rol'] === 'Admin' ? true : false;

    /* Muestra los datos del usuario para verificar que se han obtenido correctamente
    echo "<pre>";
    print_r($usuario);
    echo "</pre>";*/
    if ($conduce === true) {
        // Consulta para verificar si el usuario tiene un coche
        $stmt = $pdo->prepare("SELECT * FROM vehiculo WHERE IdConductor IN (SELECT IdConductor FROM conductor WHERE IdUsuario = :idusuario)");
        $stmt->execute(['idusuario' => $_SESSION['idusuario']]);
        $vehiculo = $stmt->fetch(PDO::FETCH_ASSOC);

    } else {
        $vehiculo = null;

    }

} catch (PDOException $e) {
    echo "No se ha podido conectar a la base de datos: " . $e->getMessage();
    exit;
}



?>

<!DOCTYPE html>
<html lang="eu">

<head>
    <link rel="stylesheet" href="Styles/perfil.css">
    <script src="Scripts/validaciones.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="Shortcut icon" href="img/mopa.ico" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profila</title>
</head>

<header>
    <?php include 'Includes/header.php'; ?>
</header>





<body>

    <div class="todo">



        <!----------------------------------------------------------PARTE IZQUIERDA-------------------------------------------------------->
        <div class="parte-izda">
            <?php
            if ($foto) {
                echo '<img src="' . htmlspecialchars($foto) . '" alt="Foto perfil" class="img-perfil" style="width: 100%; height: auto; max-width: 150px; max-height: 150px;">';
            } else {
                echo '<img src="img/avatar.png" alt="Foto perfil por defecto" class="img-perfil" style="width: 100%; height: auto; max-width: 100px; max-height: 100px;">';
            }

            echo "<br>";
            echo "<br>";

            echo "<h2> " . $nombre . "</h2>";
            echo "<h2 style='margin-top:-15px;'>" . $apellidos . "</h2>" ?>

            <?php
            if ($conduce) {
                echo "<h3>Gidaria</h3>";

               }   else if ($admin) {
                    echo "<h3>Administratzailea</h3>";
                }
             else {
                echo "<h3>Bidaiaria</h3>";
            } 
            ?>

            <div class="rating">
                <span class="material-symbols-outlined" style="font-size: 38px;">
                    star
                </span>

                <?php echo "<h2> " . $rating . "</h2>"; ?>

            </div>

            <p>Profilaren sortze-data:</p>

            <?php echo "<h3 style='margin-top:-15px;'>" . $fechaRegistro . "</h3>";

            echo "<p>Egin dituzu <strong>" . $cantidadViajes . "</strong> bidai</p>"
                ?>
        </div>

        <span
            style="border-left: 2px solid rgba(0, 0, 0, 0.5); height: 76%; display: inline-block; margin-left:-30px; opacity: 13%; position: absolute; top: 50%; transform: translateY(-50%);">
        </span>



        <!--------------------------------------------------------PARTE CENTRAL-------------------------------------------------------->
        <div class="parte-central">
            <h1>Informazio pertsonala</h1>
            <hr>

            <!-- Perfil por defecto -->
            <div id="perfil-datos">
                <div class="datos">
                    <p>Izena: &nbsp;</p>
                    <h3><?php echo $nombre; ?></h3>
                </div>

                <div class="datos">
                    <p>Abizenak:&nbsp;</p>
                    <h3><?php echo $apellidos; ?></h3>
                </div>

                <div class="datos">
                    <p>Telefono zenbakia:&nbsp;</p>
                    <h3><?php echo $telefono; ?></h3>
                </div>

                <div class="datos">
                    <p>Jaiotze data:&nbsp;</p>
                    <h3><?php echo $fechaNac; ?></h3>
                </div>

                <div class="datos-email">
                    <p>Posta elektronikoa:&nbsp;</p>
                    <h3><?php echo $correo; ?></h3>

                </div>



                <div class="datos">

                </div>

                <br>

                <div class="btn-editar">
                    <button type="button" id="editar-perfil" class="editar-perfil">
                        <span class="material-symbols-outlined" style="background-color:#04344B">
                            edit
                        </span>
                    </button>
                </div>
            </div>


            <!--------------------------------------------------------PERFIL EDITABLE-------------------------------------------------------->
            <div id="perfil-datos-editable" style="display: none; flex-direction: column; align-items: flex-start;">
            <form id="perfil-datos-form" method="POST" action="ManejoDatos/actualizar-perfil.php" onsubmit="return validarEditarPerfil()" enctype="multipart/form-data">
                    <div class="datos">
                        <p>Izena: &nbsp;</p>
                        <input type="text" name="nuevo-nombre" id="nuevo-nombre" value="<?php echo $nombre; ?>">
                    </div>

                    <div class="datos">
                        <p>Abizenak:&nbsp;</p>
                        <input type="text" name="nuevos-apellidos" id="nuevos-apellidos"
                            value="<?php echo $apellidos; ?>">
                    </div>

                    <div class="datos">
                        <p>Telefono zenbakia:&nbsp;</p>
                        <input type="text" name="nuevo-telefono" id="nuevo-telefono" value="<?php echo $telefono; ?>">
                    </div>

                    <div class="datos">
                        <p>Jaiotze data:&nbsp;</p>
                        <input type="date" name="nueva-fecha-nacimiento" id="nueva-fecha-nacimiento"
                            value="<?php echo $fechaNac; ?>">
                    </div>

                    <div class="datos">
                        <p>Posta elektronikoa:&nbsp;</p>
                        <input type="email" name="nuevo-email" id="nuevo-email" style="width: 250px;"
                            value="<?php echo $correo; ?> ">
                    </div>

                    <div class="datos">
                        <p>Profileko argazkia:&nbsp;</p>
                        <input type="file" name="nueva-foto" id="nueva-foto" accept="image/*">
                    </div>

                    <br>

                    <div class="btn-cancelar-guardar">
                        <input type="submit" id="guardar-cambios" class="guardar-cambios"
                            style="display: none; background-color: #04344B; color:white; border: 0; border-radius:5px; margin-top: 10px; margin-right:55px;"
                            value="Gorde">

                        <input type="button" id="cancelar-cambios" class="cancelar-cambios"
                            style="display: none; background-color: #FF0000; color:white; border: 0; margin-top: 10px; border-radius:5px;"
                            value="Utzi">
                    </div>
                </form>
            </div>

            <span class="span-izquierda" style="display:block;">
            </span>
        </div>



        <!----------------------------------------------------------PARTE DERECHA-------------------------------------------------------->
        <div class="parte-dcha">

        

            <h1>Autoak</h1>
            <span class="span-coche"></span>

            <?php if ($vehiculo): ?>


                <div class="coche-añadido">
                    <h2>Autoaren datuak</h2>
                    <p><strong style="color: #0A8754;">Matrikula:</strong> <?= htmlspecialchars($vehiculo['Matricula']) ?>
                    </p>
                    <p><strong style="color: #0A8754;">Modeloa:</strong> <?= htmlspecialchars($vehiculo['Modelo']) ?></p>
                    <p><strong style="color: #0A8754;">Marka:</strong> <?= htmlspecialchars($vehiculo['Marca']) ?></p>
                    <p><strong style="color: #0A8754;">Kolorea:</strong> <?= htmlspecialchars($vehiculo['Color']) ?></p>
                    <p><strong style="color: #0A8754;">Eserlekuak:</strong> <?= htmlspecialchars($vehiculo['Plazas']) ?></p>
                    <p><strong style="color: #0A8754;">Urtea:</strong> <?= htmlspecialchars($vehiculo['Año']) ?></p>
                </div>

                <form action="ManejoDatos/borrar-coche.php" method="POST"
                    onsubmit="return confirm('Zihur zaude autoa ezabatu nahi duzula?');">


                    <input type="submit" name="borrar-coche" class="boton-aceptar-cancelar"
                        style="background-color: #FF0000; color:white; border: 0; border-radius:5px;" value="Ezabatu">
                </form>
            </div>

        </div>

    <?php else: ?>
        <!-- Añadir coche -->
        <div class="form-añadir-coche">
            <div class="añadir-coche" id="añadir-coche">
                <span class="material-symbols-outlined" style="margin-right: 5px;">add_circle</span>
                <strong>Gehitu autoa</strong>
            </div>

            <div id="inputs-coche" style="display: none;">
                <form action="ManejoDatos/añadir-coche.php" method="POST" onsubmit="return validarAñadirCoche()">
                    <div class="añadir-coche">
                        <!-- Editor de coches -->
                        <div class="datos-vehiculo">
                            <p>NAN zenbakia:&nbsp;</p>
                            <input type="text" name="licencia-conduccion" value="" required>
                        </div>
                    </div>
                    <div class="datos-vehiculo">
                        <p>Gidabaimenaren iraungipena:&nbsp;</p>
                        <input type="date" name="caducidad-licencia" value="" required>
                    </div>

                    <div class="datos-vehiculo">
                        <p>Gideatzen urteak:&nbsp;</p>
                        <input type="number" name="anos-conduciendo" value="" required>
                    </div>

                    <div class="datos-vehiculo">
                        <p>Matrikula:&nbsp;</p>
                        <input type="text" name="matricula" value="" required>
                    </div>

                    <div class="datos-vehiculo">
                        <p>Modeloa:&nbsp;</p>
                        <input type="text" name="modelo" value="" required>
                    </div>

                    <div class="datos-vehiculo">
                        <p>Marka:&nbsp;</p>
                        <input type="text" name="marca" value="" required>
                    </div>

                    <div class="datos-vehiculo">
                        <p>Kolorea:&nbsp;</p>
                        <input type="text" name="color" value="" required>
                    </div>

                    <div class="datos-vehiculo">
                        <p>Plazak:&nbsp;</p>
                        <input type="number" name="plazas" value="" required>
                    </div>

                    <div class="datos-vehiculo">
                        <p>Urtea:&nbsp;</p>
                        <input type="number" name="ano" value="" required>
                    </div>

                    <div class="aceptar-cancelar">
                        <input type="submit" value="Gorde" class="boton-aceptar-cancelar"
                            style="background-color: #04344B; margin-right: 10px; color:white; border: 0; border-radius:5px;">
                        <input type="reset" value="Utzi" class="boton-aceptar-cancelar"
                            style="background-color: #FF0000; color:white; border: 0; border-radius:5px;">
                    </div>
                </form>
            </div>
        </div>

    <?php endif; ?>

    </div>

    </div>

</body>


<footer>
    <?php include 'Includes/footer.html'; ?>
</footer>



</html>


<script>

    // Editar perfil
    document.getElementById("editar-perfil").addEventListener("click", function () {
        document.getElementById("perfil-datos").style.display = "none";
        document.getElementById("perfil-datos-editable").style.display = "block";
        this.style.display = "none"; // Oculta el botón de editar
        document.getElementById("guardar-cambios").style.display = "block"; // Muestra el botón de guardar
        document.getElementById("cancelar-cambios").style.display = "block"; // Muestra el botón de cancelar
    });

    // Cancelar cambios
    document.getElementById("cancelar-cambios").addEventListener("click", function () {
        document.getElementById("perfil-datos").style.display = "block";
        document.getElementById("perfil-datos-editable").style.display = "none";
        document.getElementById("editar-perfil").style.display = "block"; // Muestra el botón de editar
        this.style.display = "none"; // Oculta el botón de cancelar
        document.getElementById("guardar-cambios").style.display = "none"; // Oculta el botón de guardar
    });

    // Guardar cambios (puedes agregar la lógica para guardar cambios aquí)
    document.getElementById("guardar-cambios").addEventListener("click", function () {
        document.getElementById("perfil-datos").style.display = "block";
        document.getElementById("perfil-datos-editable").style.display = "none";
        document.getElementById("editar-perfil").style.display = "block"; // Muestra el botón de editar
        this.style.display = "none"; // Oculta el botón de guardar
        document.getElementById("cancelar-cambios").style.display = "none"; // Oculta el botón de cancelar
    });

    // Añadir coche
    document.getElementById("añadir-coche").addEventListener("click", function () {
        document.getElementById("inputs-coche").style.display = "block";
        document.getElementById("añadir-coche").style.display = "none";
    });

    document.getElementsByClassName("boton-aceptar-cancelar")[1].addEventListener("click", function () {
        document.getElementById("inputs-coche").style.display = "none";
        document.getElementById("añadir-coche").style.display = "block";
    });

    // Cancelar la edición del coche
    document.getElementById("cancelar-edicion-coche").addEventListener("click", function () {
        document.getElementById("form-editar-coche").style.display = "none";
        document.getElementById("editar-coche").style.display = "block"; // Muestra el botón de editar
    });


</script>