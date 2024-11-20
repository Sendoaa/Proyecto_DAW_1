<?php

session_start();

// Verifica si los datos fueron enviados por el método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger datos del formulario
    $_licenciaConduccion = $_POST['licencia-conduccion'];
    $_caducidadLicencia = $_POST['caducidad-licencia'];
    $_anosConduciendo = $_POST['anos-conduciendo'];
    $_matricula = $_POST['matricula'];
    $_modelo = $_POST['modelo'];
    $_marca = $_POST['marca'];
    $_color = $_POST['color'];
    $_plazas = $_POST['plazas'];
    $_ano = $_POST['ano'];
    $_idUsuario = $_SESSION['idusuario'];

    try {
        // Conexión a la bd
        $pdo = new PDO('mysql:host=dbmopa.cxzeapfroqne.us-east-1.rds.amazonaws.com;dbname=mopa', 'adminMopa', 'adminsmopa');        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Inserta el perfil del conductor
        $stmtConductor = $pdo->prepare("INSERT INTO conductor (IdUsuario, LicenciaConduccion, CaducidadLicencia, AñosConduciendo) VALUES (:idUsuario, :licenciaConduccion, :caducidadLicencia, :anosConduciendo)");
        $stmtConductor->execute([
            ':idUsuario' => $_idUsuario,
            ':licenciaConduccion' => $_licenciaConduccion,
            ':caducidadLicencia' => $_caducidadLicencia,
            ':anosConduciendo' => $_anosConduciendo
        ]);

        // Le pone al usuario como conductor
        $stmtUsuario = $pdo->prepare("UPDATE usuarios SET Rol = 'Conductor' WHERE IdUsuario = :idUsuario");
        $stmtUsuario->execute([':idUsuario' => $_idUsuario]);

     
        // Consulta para verificar si el conductor ya existe
        $stmtCheckConductor = $pdo->prepare("SELECT IdConductor FROM conductor WHERE IdUsuario = :idUsuario");
        $stmtCheckConductor->execute([':idUsuario' => $_idUsuario]);
        $existingConductor = $stmtCheckConductor->fetch(PDO::FETCH_ASSOC);

        if ($existingConductor) {
            $idConductor = $existingConductor['IdConductor'];
        } else {
            // Obtén el IdConductor recién creado
            $idConductor = $pdo->lastInsertId();
        }

        // Inserta el vehículo
        $stmtVehiculo = $pdo->prepare("INSERT INTO vehiculo (IdConductor, Matricula, Modelo, Marca, Color, Plazas, Año) VALUES (:idConductor, :matricula, :modelo, :marca, :color, :plazas, :ano)");
        $stmtVehiculo->execute([
            ':idConductor' => $idConductor,
            ':matricula' => $_matricula,
            ':modelo' => $_modelo,
            ':marca' => $_marca,
            ':color' => $_color,
            ':plazas' => $_plazas,
            ':ano' => $_ano
        ]);

     
        echo "<script>alert('Autoa arrakastaz gehitu da');</script>";
        echo "<script>window.location = '../perfil.php';</script>";

    } catch (PDOException $e) {
        echo "No se ha podido conectar a la base de datos: " . $e->getMessage();
        exit;
    }
} else {
    echo "No se han enviado datos";
}
?>
