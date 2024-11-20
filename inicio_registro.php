<?php
session_start();
ob_start(); // Inicia el buffer de salida
?>

<!DOCTYPE html>
<html lang="eu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Styles/sesiones.css">
    <link rel="Shortcut icon" href="img/mopa.ico" type="image/x-icon">
    <title></title>
    <style>
        /* Estilos básicos para el modal */
        #modal {
            display: none;
            /* Oculto por defecto */
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        #modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
        }

        #close-btn {
            margin-top: 10px;
        }
    </style>
</head>

<body style="background-color:#E1E7E1">
    <?php
    include 'Includes/header.php'; // Incluye el header
    ?>
    <div style="display: flex; flex-direction: column; min-height: 100vh;">
    <section style="flex: 1">  
        <?php
        // Verifica si se ha enviado un formulario mediante POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Comprueba si el botón "Iniciar sesión" ha sido presionado
            if (isset($_POST['iniciarsesion'])) {
                include 'Includes/InicioSesion.html';
                echo "<script>document.title = 'Saioa hasi';</script>";
            } elseif (isset($_POST['registrarse'])) {
                include 'Includes/registro.html';
                echo "<script>document.title = 'Erregistratu';</script>";

                // Verificar si hubo un error de correo
                if (isset($_POST['bienMal']) && $_POST['bienMal'] === 'mal') {
                    echo "<div id='modal'>
                            <div id='modal-content'>
                                <p>El correo ya ha sido registrado anteriormente.</p>
                                <button id='close-btn' onclick='document.getElementById(\"modal\").style.display=\"none\"'>Reintentar</button>
                            </div>
                          </div>";
                }
            } elseif (isset($_POST["logout"])) {
                session_destroy();
                header("Location: index.php"); // Redirige después de destruir la sesión
                exit;
            } else {
                echo "Acción no válida.";
            }
        } else {
            echo "No se ha enviado ninguna solicitud.";
        }
        ?>
    </section>

    <footer style="flex-shrink: 0; width: 100%;">
        <?php ob_end_flush(); ?>
        <?php include 'Includes/footer.html'; ?>
    </footer>
    </div>

    <script>
        // Mostrar el modal si el div está presente
        if (document.getElementById('modal')) {
            document.getElementById('modal').style.display = 'flex'; // Mostrar el modal
        }
    </script>
</body>

</html>