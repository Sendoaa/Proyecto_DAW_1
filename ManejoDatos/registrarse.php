<?php
// Intentamos conectarnos a la base de datos
try {
    // Conexión a la bd
    $pdo = new PDO('mysql:host=dbmopa.cxzeapfroqne.us-east-1.rds.amazonaws.com;dbname=mopa', 'adminMopa', 'adminsmopa', );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Datos del usuario a registrar
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $rating = mt_rand(0, 100) / 10;

    // Comprobar si el correo ya existe
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE Correo = :correo");
    $stmt->execute(['correo' => $correo]);
    $correoExiste = $stmt->fetchColumn();

    if ($correoExiste > 0) {
        echo "<form id='hiddenForm' method='POST' action='../inicio_registro.php' style='display: none;'>
        <input type='hidden' name='registrarse'>
        <input type='hidden' name='bienMal' value='mal'>
      </form>
      <script>
        document.getElementById('hiddenForm').submit();
      </script>";
        exit;
    }

    // Obtener el último IdUsuario
    $stmt = $pdo->query("SELECT MAX(IdUsuario) AS max_id FROM usuarios");
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    $nuevoIdUsuario = ($resultado['max_id'] !== null) ? $resultado['max_id'] + 1 : 1; // Incrementar el ID o iniciar en 1 si no hay usuarios

    // Manejo de la carga de la imagen
    $nuevaFoto = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        // Definir la ruta donde se guardará la imagen
        $directorioDestino = '/var/www/html/Fotos-perfil/';
        $extensionArchivo = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        // Usar el nuevo IdUsuario para el nombre del archivo
        $nombreArchivo = 'img_perfil_' . $nuevoIdUsuario . '.' . $extensionArchivo;
        $rutaArchivo = $directorioDestino . $nombreArchivo;

        // Mover el archivo subido a la carpeta destino
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $rutaArchivo)) {
            // Guardar la ruta relativa para la base de datos
            $nuevaFoto = "Fotos-perfil/$nombreArchivo";
        } else {
            echo "Error al mover el archivo subido.";
            exit;
        }
    }

    // Encriptar la contraseña antes de almacenarla
    $contrasenaEncriptada = password_hash($contrasena, PASSWORD_DEFAULT);

    // Consulta para insertar un nuevo usuario con la ruta de la imagen
    $stmt = $pdo->prepare("INSERT INTO usuarios (Nombre, Apellidos, Telefono, FechaNac, Correo, Contrasena, Foto, Rating, FechaRegistro) VALUES (:nombre, :apellidos, :telefono, :fecha_nacimiento, :correo, :contrasena, :foto, :rating, CURDATE())");
    $stmt->execute([
        'nombre' => $nombre,
        'apellidos' => $apellidos,
        'telefono' => $telefono,
        'fecha_nacimiento' => $fecha_nacimiento,
        'correo' => $correo,
        'contrasena' => $contrasenaEncriptada, // Usamos la contraseña encriptada aquí
        'foto' => $nuevaFoto, // Usamos la ruta de la foto aquí
        'rating' => $rating
    ]);

    // Obtener el IdUsuario del nuevo registro
    $idusuario = $pdo->lastInsertId();

    // Iniciar sesión automáticamente
    session_start();
    $_SESSION['idusuario'] = $idusuario;
    $_SESSION['nombre'] = $nombre;

    echo "<script>
        window.location.href = '../index.php';
    </script>";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

?>

<script>
    // Mostrar u ocultar contraseña
    document.getElementById('toggle-password').addEventListener('click', function () {
        var passwordDisplay = document.getElementById('password-display');
        if (passwordDisplay.textContent.includes('*')) {
            passwordDisplay.textContent = '<?php echo $contrasena; ?>';
            this.textContent = 'visibility_off';
        } else {
            passwordDisplay.textContent = '<?php echo str_repeat('*', strlen($contrasena)); ?>';
            this.textContent = 'visibility';
        }
    });
</script>