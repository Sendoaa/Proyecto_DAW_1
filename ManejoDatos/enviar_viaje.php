<?php
session_start();


if ($_SERVER["REQUEST_METHOD"] == "POST") {


    // Recoger los datos del formulario
    $origen = $_POST['hidden_origen'];
    $destino = $_POST['hidden_destino'];
    $fechasalida = $_POST['fechaSalida'];
    $fechafin = $_POST['fechaFin'];
    $horasalida = $_POST['horaSalida'];
    $pasajeros = $_POST['numPasajeros'];
    $precio = $_POST['precioAsiento'];
    $discapacidades = $_POST['discapacidades'];
    $vuelta = $_POST['horaVuelta'];

     // Verificar los datos recibidos
     echo "Origen: $origen, Destino: $destino, FechaSalida: $fechasalida, FechaFin: $fechafin, HoraSalida: $horasalida, Pasajeros: $pasajeros, Precio: $precio, Discapacidades: $discapacidades, Vuelta: $vuelta";

    // Intentamos conectarnos a la base de datos
    try {
        // // Conexión a la bd
        $pdo = new PDO('mysql:host=dbmopa.cxzeapfroqne.us-east-1.rds.amazonaws.com;dbname=mopa', 'adminMopa', 'adminsmopa');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Consulta para obtener el IdConductor de la tabla conductor
        $stmt = $pdo->prepare("SELECT IdConductor FROM conductor WHERE IdUsuario = :idusuario");
        $stmt->execute(['idusuario' => $_SESSION["idusuario"]]);
        $conductor = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($conductor) {
            $idConductor = $conductor['IdConductor'];
        } else {
            echo "No se encontró un conductor con el idUsuario proporcionado.";
            exit;
        }

        // Insertar los datos del formulario en la tabla viajes
        $sql = "INSERT INTO viaje (IdConductor, Origen, Destino, FechaInicio, FechaFin, HoraSalida, HoraVuelta, PlazasDisponibles, TotalAPagar, TipoDiscapacidad)
            VALUES (:IdConductor, :Origen, :Destino, :FechaInicio, :FechaFin, :HoraSalida, :HoraVuelta, :PlazasDisponibles, :TotalAPagar, :TipoDiscapacidad)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'IdConductor' => $idConductor,
            'Origen' => $origen,
            'Destino' => $destino,
            'FechaInicio' => $fechasalida,
            'FechaFin' => $fechafin,
            'HoraSalida' => $horasalida,
            'PlazasDisponibles' => $pasajeros,
            'TotalAPagar' => ($precio * $pasajeros),
            'TipoDiscapacidad' => $discapacidades,
            'HoraVuelta' => $vuelta
        ]);

        header("Location: ../mis-viajes.php");


    } catch (PDOException $e) {
        echo "No se ha podido conectar a la base de datos: " . $e->getMessage();
        exit;
    }
}
