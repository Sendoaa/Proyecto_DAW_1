<?php
// Database connection
$servername = "dbmopa.cxzeapfroqne.us-east-1.rds.amazonaws.com";
$username = "adminMopa";
$password = "adminsmopa";
$dbname = "mopa";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the idviaje from the request
$idviaje = $_POST['IdViaje'];

// Update the viaje status to 'cancelado'
$sql = "UPDATE viaje SET Estado='Cancelada' WHERE IdViaje=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idviaje);

if ($stmt->execute()) {
    header("Location: ../mis-viajes.php");
    exit();
} else {
    echo "Error al cancelar el viaje: " . $conn->error;
}

// Close connection
$stmt->close();
$conn->close();
?>