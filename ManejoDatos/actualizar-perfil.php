<?php
session_start();

// Obtener los datos del formulario
$nuevoNombre = $_POST['nuevo-nombre'];
$nuevosApellidos = $_POST['nuevos-apellidos'];
$nuevoTelefono = $_POST['nuevo-telefono'];
$nuevaFechaNac = $_POST['nueva-fecha-nacimiento'];
$nuevoCorreo = $_POST['nuevo-email'];

// Manejar la carga de la imagen
$nuevaFoto = null;
if (isset($_FILES['nueva-foto']) && $_FILES['nueva-foto']['error'] === UPLOAD_ERR_OK) {
    // Definir la ruta donde se guardará la imagen
    $directorioDestino = '../Fotos-perfil/';
    $idUsuario = $_SESSION['idusuario'];
    $extensionArchivo = pathinfo($_FILES['nueva-foto']['name'], PATHINFO_EXTENSION);
    $nombreArchivo = 'img_perfil_' . $idUsuario . '.' . $extensionArchivo;
    $rutaArchivo = $directorioDestino . $nombreArchivo;

    // Buscar y eliminar archivos con el mismo nombre pero diferente extensión
    $archivosExistentes = glob($directorioDestino . 'img_perfil_' . $idUsuario . '.*');
    foreach ($archivosExistentes as $archivo) {
        unlink($archivo); // Eliminar archivos existentes
    }

    // Mover el archivo subido a la carpeta destino
    if (move_uploaded_file($_FILES['nueva-foto']['tmp_name'], $rutaArchivo)) {
        // Guardar la ruta relativa para la base de datos
        $nuevaFoto = 'Fotos-perfil/' . $nombreArchivo;
    } else {
        echo "Error al mover el archivo subido.";
        exit;
    }
}

// Obtener los datos de la nueva contraseña si se han proporcionado
$nuevaContraseña = isset($_POST['nueva-contraseña']) ? $_POST['nueva-contraseña'] : null;


// Intentamos conectarnos a la base de datos
try {
    // Conexión a la base de datos
    $pdo = new PDO('mysql:host=dbmopa.cxzeapfroqne.us-east-1.rds.amazonaws.com;dbname=mopa', 'adminMopa', 'adminsmopa');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Preparamos la consulta para actualizar los datos
    $sql = "UPDATE usuarios 
            SET Nombre = :nombre, Apellidos = :apellidos, Telefono = :telefono, FechaNac = :fecha_nac, Correo = :correo" .
        ($nuevaFoto ? ", Foto = :foto" : "") . // Solo actualizar la foto si hay una nueva
        ($nuevaContraseña ? ", Password = :password" : "") . // Solo actualizar la contraseña si hay una nueva
        " WHERE IdUsuario = :idusuario";
    $stmt = $pdo->prepare($sql);

    // Asignar los valores
    $stmt->bindParam(':nombre', $nuevoNombre);
    $stmt->bindParam(':apellidos', $nuevosApellidos);
    $stmt->bindParam(':telefono', $nuevoTelefono);
    $stmt->bindParam(':fecha_nac', $nuevaFechaNac);
    $stmt->bindParam(':correo', $nuevoCorreo);

    if ($nuevaFoto) {
        $stmt->bindParam(':foto', $nuevaFoto); // Guardar la ruta de la imagen
    }

    if ($nuevaContraseña && $nuevaContraseña ) {
        // Encriptar la nueva contraseña antes de guardarla
        $passwordHash = password_hash($nuevaContraseña, PASSWORD_DEFAULT);
        $stmt->bindParam(':password', $passwordHash);
    }

    $stmt->bindParam(':idusuario', $_SESSION['idusuario']);

    // Ejecutamos la consulta
    $stmt->execute();

    // Actualizar los datos de la sesión
    $_SESSION['mensaje'] = 'Datuak eguneratu dira.';
    echo "<script>window.location.href='../perfil.php';</script>";
    exit;

} catch (PDOException $e) {
    echo "Error en la conexión: " . $e->getMessage();
    exit;
}
?>