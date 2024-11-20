<?php
session_start(); // Inicia la sesión

// Obtiene los valores del formulario
$correosesion = $_POST['correoinicio'];
$contrasenaProporcionada = $_POST['contrasenainicio'];

try {
    // Conexión a la bd
    $pdo = new PDO('mysql:host=dbmopa.cxzeapfroqne.us-east-1.rds.amazonaws.com;dbname=mopa', 'adminMopa', 'adminsmopa');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta para obtener los datos del usuario
    $stmt = $pdo->prepare("SELECT Correo, Contrasena, IdUsuario, Nombre FROM usuarios WHERE Correo = :correo");
    $stmt->execute(['correo' => $correosesion]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        $correo = $usuario['Correo'];
        $contrasena = $usuario['Contrasena'];
        $idusuario = $usuario['IdUsuario'];
        $nombre = $usuario['Nombre'];


        // Verifica la contraseña proporcionada con la almacenada en la base de datos
        if (password_verify($contrasenaProporcionada, $contrasena)) {
            $_SESSION['idusuario'] = $idusuario;
            $_SESSION['nombre'] = $nombre;
            echo "Inicio de sesión exitoso";
            header("Location: ../index.php");
            exit;
        } else {
            $usuarioValido = false;
            echo '
            <form id="form_oculto" action="../inicio_registro.php" method="POST">
                <input type="hidden" name="iniciarsesion">
            </form>
            <script type="text/javascript">
                alert("Pasahitza okerra");
                document.getElementById("form_oculto").submit();
            </script>
            ';
            exit;
        }
    } else {
        $usuarioValido = false;
        echo '
        <form id="form_oculto" action="../inicio_registro.php" method="POST">
            <input type="hidden" name="iniciarsesion">
        </form>
        <script type="text/javascript">
            alert("Erabiltzailea okerra");
            document.getElementById("form_oculto").submit();
        </script>
        ';
        exit;
    }
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
