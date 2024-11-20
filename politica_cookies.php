<?php session_start(); ?>
<!DOCTYPE html>
<html lang="eu"></html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cookie Politika</title>
    <link rel="stylesheet" href="Styles/politica_cookies.css">
    <link rel="Shortcut icon" href="img/mopa.ico" type="image/x-icon">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #E1E7E1;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h1 {
            color: #007BFF;
            text-align: center;
        }

        h2 {
            color: #0056b3;
            margin-top: 20px;
        }

        p, ul {
            line-height: 1.6;
        }

        ul {
            list-style-type: disc;
            padding-left: 20px;
        }

        a {
            color: #007BFF;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            color: #fff;
            background-color: #007BFF;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            }

            .btn:hover {
            background-color: #0056b3;
            }
    </style>
</head>

<body>
    <!-- Incluir el header -->
    <div style="pointer-events: none;">
        <?php include 'Includes/header.php'; ?>
    </div>
    <style>
        .logo a {
            pointer-events: auto !important;
        }
    </style>
    <div class="container">
        <h1>Cookie Politika</h1>
        <p>MOPA-n cookieak erabiltzen ditugu gure erabiltzaileen esperientzia hobetzeko. Gure webgunera sartzean, cookieak erabiltzeko politika honen baldintzak onartzen dituzu. Orrialde honek cookieak nola erabiltzen ditugun eta nola kudeatu ditzakezun azaltzen dizu.</p>

        <h2>1. Zer dira cookieak?</h2>
        <p>Cookieak testu-fitxategi txikiak dira, zure gailuan (ordenagailua, tableta, smartphone, etab.) gordetzen direnak web orri jakin batzuk bisitatzen dituzunean. Etorkizuneko bisitetan zure gailua ezagutzeko eta gure webgunean zure esperientzia hobetzeko aukera ematen digute.</p>

        <h2>2. Zer motatako cookieak erabiltzen ditugu?</h2>
        <ul>
            <li><strong>Cookie funtsezkoak:</strong> Gure webgunearen funtzionamendurako beharrezkoak dira. Gabe, zerbitzu eta funtzionalitate jakin batzuk ez lirateke eskuragarri egongo.</li>
            <li><strong>Funtzionalitate cookieak:</strong> Zure lehentasunak gogoratzeko eta webgunean zure esperientzia pertsonalizatzeko aukera ematen dute.</li>
        </ul>

        <h2>3. Nola kudeatu cookieak?</h2>
        <p>Gure webgunean cookieak onartzen ez badituzu, ezin izango dituzu gure zerbitzu guztiak erabili. Cookieak nola kudeatu jakiteko, zure nabigatzailearen laguntza atala kontsultatu dezakezu.</p>
        <div style="text-align: center; margin-top: 20px;">
            <a href="index.php" class="btn">Itzuli Hasierara</a>
        </div>
    </div>

    <!-- Incluir el footer -->
    <?php include 'Includes/footer.html'; ?>
</body>

</html>
