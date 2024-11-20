<?php
// Conexión a la base de datos
try {
    $pdo = new PDO('mysql:host=dbmopa.cxzeapfroqne.us-east-1.rds.amazonaws.com;dbname=mopa', 'adminMopa', 'adminsmopa'); 
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    function obtenerUsuario($pdo, $idusuario)
    {
        $stmt = $pdo->prepare("SELECT Nombre, Foto, Rol FROM usuarios WHERE IdUsuario = :idusuario");
        $stmt->execute(['idusuario' => $idusuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    $avatar = 'img/avatar.png';
    $nombre = 'invitado';
    $rol = 'invitado';

    if (isset($_SESSION['idusuario'])) {
        $usuario = obtenerUsuario($pdo, $_SESSION['idusuario']);
        if ($usuario) {
            $nombre = htmlspecialchars($usuario['Nombre']);
            $rol = htmlspecialchars($usuario['Rol']);
            $avatar = !empty($usuario['Foto']) ? htmlspecialchars($usuario['Foto']) : $avatar;
        }
    }

    $nombres = $_SESSION['nombre'] ?? 'invitado';

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<style>
    body {
        margin: 0;
        font-family: Arial, Helvetica, sans-serif;
    }

    header {
        background-color: #04344B;
        width: 100%;
    }

    .flex {
        display: flex;
        justify-content: space-between;
    }

    .logo_y_mopa {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .mopa {
        color: white;
        font-size: 1.5em;
        margin-left: 0.5em;
    }

    .logo_header {
        height: 2.5em;
        border-radius: 20px;
        margin-left: 0.5em;
    }

    .dropdown {
        display: flex;
    }

    .buscar_publicar {
        display: flex;
        align-items: center;
        margin-right: 4em;
    }

    .icono_buscar_crear {
        width: 1.5em;
        vertical-align: middle;
    }

    header a {
        color: #0A8754;
        font-size: 1.4em;
        margin-right: 1em;
        font-weight: bold;
        text-decoration: none;
    }

    .avatar {
        max-height: 4em;
        max-width: 4em;
        object-fit: contain;
        border-radius: 20%;
        margin: 1em;
        margin-right: 2em;
        cursor: pointer;
    }

    #contenido_dropdown {
        display: none;
        background-color: #04344B;
        color: white;
        width: 20em;
        position: absolute;
        right: 1em;
        top: 5em;
        border-radius: 5px;
        padding: 0.5em;
    }

    #contenido_dropdown.contenido_mostrar {
        display: block;
    }

    #contenido_dropdown h2 {
        text-align: center;
    }

    #contenido_dropdown a {
        font-size: 0.9em;
        color: #0A8754;
        text-decoration: none;
    }

    .desplegable_flex {
        display: flex;
        justify-content: space-between;
        width: 90%;
        margin: 0 5%;
    }

    .iconos_desplegable {
        width: 2em;
        vertical-align: middle;
    }

    .icono_flecha_dch {
        height: 1em;
        vertical-align: middle;
        margin-top: 0.5em;
    }

    .horizontal {
        background-color: #0A8754;
        width: 90%;
        height: 1px;
        margin: 0 5%;
        border: none;
    }

    .button_as_link {
        background: none;
        border: none;
        padding: 0;
        font: inherit;
        color: #0A8754;
        display: flex;
        align-items: center;
        cursor: pointer;
        text-decoration: none;
    }

    .button_as_link:focus {
        outline: none;
    }

    @media (max-width: 768px) {
        .logo_y_mopa {
            flex-direction: column;
            margin-top: 1em;
        }

        .mopa {
            margin-left: 0.7em;
        }

        .avatar {
            height: 6em;
            width: 6em;
        }

        .buscar_publicar {
            margin-right: 1em;
        }
    }

    @media (max-width: 570px) {
        .logo_header {
            height: 2em;
        }

        .mopa {
            font-size: 0.65em;
        }

        .dropdown a {
            font-size: 1em;
        }

        .icono_buscar_crear {
            width: 1.5em;
        }

        .buscar_publicar {
            flex-direction: column;
            margin-right: 0;
            justify-content: center;
        }

        #contenido_dropdown {
            width: 15em;
            top: 5em;
        }

        #contenido_dropdown a {
            font-size: 0.6em;
        }

        .avatar {
            height: 4em;
            width: 4em;
            margin-right: 0.5em;
        }

        .iconos_desplegable {
            width: 1.7em;
        }

        .icono_flecha_dch {
            height: 0.5em;
            margin-top: 0.2em;
        }
    }
</style>
<link rel="Shortcut icon" href="img/mopa.ico" type="image/x-icon">
<header>
    <div class="flex">
        <div class="logo_y_mopa">
            <a href="index.php" style="display: flex; align-items: center; text-decoration: none; color: inherit;">
                <img class="logo_header" src="img/mopa.jpg" alt="Logo de MOPA">
            </a>
        </div>

        <div class="dropdown">
            <div class="buscar_publicar">
                <div>
                    <a href="./buscador_viajes.php">
                        <img class="icono_buscar_crear" src="img/icono_buscar.png"> Bilatu
                    </a>
                </div>
                <div>
                    <a href="<?= (isset($_SESSION['idusuario']) && $rol === 'Conductor') ? './publicar_viaje.php' : 'javascript:void(0);' ?>"
                        onclick="<?= (!isset($_SESSION['idusuario'])) ? 'alert(\'Ez zaude saioa hasita.\')' : (isset($_SESSION['idusuario']) && $rol !== 'Conductor' ? 'alert(\'Ez zaude gidari bezala saioa hasita. Gehitu auto bat profilean bidai bat argitaratzeko\')' : ''); ?>">
                        <img class="icono_buscar_crear" src="img/icono_sumar.png" alt="Crear viaje"> Bidai bat
                        argitaratu
                    </a>
                </div>
            </div>
            <button style="all: unset;" id="imagen">
                <img class="avatar" src="<?= $avatar; ?>" alt="Perfil">
            </button>
        </div>
    </div>
    <form action="inicio_registro.php" id="contenido_dropdown" class="contenido_dropdown" method="post">
        <h2>Aupa <?= $nombre; ?>!</h2>
        <hr>

        <?php if (isset($_SESSION['nombre'])): ?>
            <div class="desplegable_flex">
                <a href="perfil.php"><img class="iconos_desplegable" src="img/icono_perfil.png"> Profila</a>
                <img class="icono_flecha_dch" src="img/icono_flecha_dch.png">
            </div>
            <hr class="horizontal">

            <?php
            $menuItems = [
                'Admin' => ['viajes_admin.php', 'img/icono_coche.png', 'Bidaiak kudeatu'],
                'Conductor' => ['mis-viajes.php', 'img/icono_coche.png', 'Nire bidaiak']
            ];
            $paginaMenu = $menuItems[$rol] ?? $menuItems['Conductor'];
            ?>

            <div class="desplegable_flex">
                <a href="<?= $paginaMenu[0]; ?>">
                    <img class="iconos_desplegable" src="<?= $paginaMenu[1]; ?>"><?= $paginaMenu[2]; ?>
                </a>
                <img class="icono_flecha_dch" src="img/icono_flecha_dch.png">
            </div>
            <hr class="horizontal">

            <?php if ($rol === 'Admin'): ?>
                <div class="desplegable_flex">
                    <a href="usuarios_admin.php">
                        <img class="iconos_desplegable" src="img/admin_usuarios.png"> Erabiltzaileak kudeatu
                    </a>
                    <img class="icono_flecha_dch" src="img/icono_flecha_dch.png">
                </div>
                <hr class="horizontal">
            <?php endif; ?>

            <button type="submit" class="desplegable_flex button_as_link" name="logout">
                <a><img class="iconos_desplegable" src="img/icono_cerrar_sesion.png"> Saioa itxi</a>
                <img class="icono_flecha_dch" src="img/icono_flecha_dch.png">
            </button>
        <?php else: ?>
            <button type="submit" class="desplegable_flex button_as_link" name="iniciarsesion">
                <a><img class="iconos_desplegable" src="img/icono_iniciar_sesion.png"> Saioa hasi</a>
                <img class="icono_flecha_dch" src="img/icono_flecha_dch.png">
            </button>
            <hr class="horizontal">
            <button type="submit" class="desplegable_flex button_as_link" name="registrarse">
                <a><img class="iconos_desplegable" src="img/icono_registrarse.png"> Erregistratu</a>
                <img class="icono_flecha_dch" src="img/icono_flecha_dch.png">
            </button>
        <?php endif; ?>
    </form>
</header>

<script>
    document.getElementById('imagen').addEventListener('click', function () {
        document.getElementById('contenido_dropdown').classList.toggle('contenido_mostrar');
    });

    document.addEventListener('click', function (event) {
        var dropdown = document.getElementById('contenido_dropdown');
        var avatar = document.getElementById('imagen');

        if (!dropdown.contains(event.target) && !avatar.contains(event.target)) {
            dropdown.classList.remove('contenido_mostrar');
        }
    });
</script>