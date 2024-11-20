<?php


$servername = "dbmopa.cxzeapfroqne.us-east-1.rds.amazonaws.com";
$username = "adminMopa";
$password = "adminsmopa";
$dbname = "mopa";

$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


session_start();


if (isset($_SESSION['idusuario'])) {
    $id_usuario = intval($_SESSION['idusuario']);

    // Paso 1: Obtener el IdConductor asociado al IdUsuario
    $stmt_get_conductor = $conn->prepare("SELECT IdConductor FROM conductor WHERE IdUsuario = ?");
    $stmt_get_conductor->bind_param("i", $id_usuario);
    $stmt_get_conductor->execute();
    $stmt_get_conductor->bind_result($id_conductor);
    $stmt_get_conductor->fetch();
    $stmt_get_conductor->close();

    if ($id_conductor) {
        // Paso 2: Eliminar el vehiculo asociado al IdConductor
        $stmt_vehiculo = $conn->prepare("DELETE FROM vehiculo WHERE IdConductor = ?");
        $stmt_vehiculo->bind_param("i", $id_conductor);

        if ($stmt_vehiculo->execute()) {
            $stmt_vehiculo->close();

            // Eliminar de 'conductor'
            $stmt_conductor = $conn->prepare("DELETE FROM conductor WHERE IdUsuario = ?");
            $stmt_conductor->bind_param("i", $id_usuario);
            if ($stmt_conductor->execute()) {
                $stmt_conductor->close();

                // Actualizar el rol en 'usuarios'
                $stmt_update_role = $conn->prepare("UPDATE usuarios SET rol = 'pasajero' WHERE idusuario = ?");
                $stmt_update_role->bind_param("i", $id_usuario);
                if ($stmt_update_role->execute()) {
                    $stmt_update_role->close();

                    echo "<script>alert('Autoa arrakastaz kendu da.');";
                    echo "window.location.href='../perfil.php';</script>";
                } else {
                    echo "Error actualizando el rol: " . $conn->error;
                }
            } else {
                echo "Error borrando el conductor: " . $conn->error;
            }
        } else {
            echo "Error borrando el coche: " . $conn->error;
        }
    } else {
        echo "No se encontró un conductor asociado al usuario.";
    }
} else {
    echo "No user ID found in session.";
}




$conn->close();
?>
