<?php
session_start();

// Conexión a la base de datos
$servername = "dbmopa.cxzeapfroqne.us-east-1.rds.amazonaws.com";
$username = "adminMopa";
$password = "adminsmopa";
$dbname = "mopa";
$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verificar si se envía el ID del viaje y el ID del usuario
if (isset($_POST['id']) && isset($_POST['userId'])) {
    $viajeId = $_POST['id'];
    $userId = $_POST['userId'];

    // Consultar el IdConductor asignado al viaje en la tabla Conductor
    $queryConductor = "
        SELECT c.IdConductor 
        FROM conductor c
        JOIN viaje v ON c.IdConductor = v.IdConductor
        WHERE v.IdViaje = ? AND c.IdUsuario = ?";
        
    $stmtConductor = $conn->prepare($queryConductor);
    if ($stmtConductor) {
        $stmtConductor->bind_param('ii', $viajeId, $userId);
        $stmtConductor->execute();
        $stmtConductor->store_result();

        // Si el usuario es el conductor asignado, no permitir la compra
        if ($stmtConductor->num_rows > 0) {
            echo "El conductor no puede comprar su propio viaje.";
            exit;
        }
        $stmtConductor->close();

        // Preparar la consulta para cambiar el estado del viaje
        $query = "UPDATE viaje SET Estado = 'Confirmada' WHERE IdViaje = ?";
        $stmt = $conn->prepare($query);
        if ($stmt) {
            $stmt->bind_param('i', $viajeId);
            if ($stmt->execute()) {
                echo "Viaje confirmado correctamente.";

                // Inserción en la tabla Contrata (IdUsuario, IdViaje)
                $queryInsert = "INSERT INTO contrata (IdUsuario, IdViaje) VALUES (?, ?)";
                $stmtInsert = $conn->prepare($queryInsert);
                if ($stmtInsert) {
                    $stmtInsert->bind_param('ii', $userId, $viajeId);
                    if ($stmtInsert->execute()) {
                        echo "Compra registrada correctamente en la tabla Contrata.";
                    } else {
                        echo "Error al registrar la compra en la tabla Contrata.";
                    }
                    $stmtInsert->close();
                } else {
                    echo "Error en la preparación de la consulta de inserción.";
                }
            } else {
                echo "Error al confirmar el viaje.";
            }
            $stmt->close();
        } else {
            echo "Error en la preparación de la consulta.";
        }
    } else {
        echo "Error al consultar los datos del conductor.";
    }
} else {
    echo "No se recibieron los datos necesarios.";
}

$conn->close();
?>
