<?php session_start(); ?>

<!DOCTYPE html>
<html lang="eu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MOPA - Hasiera</title>
    <link rel="stylesheet" href="Styles/iniciopub.css">
    <link rel="Shortcut icon" href="img/mopa.ico" type="image/x-icon">
</head>

<body>
    <?php include 'Includes/header.php'; ?>

    <?php
    //-------------------------------------------------------COOKIES-------------------------------------------------------//
    // Verifica si el usuario no ha aceptado las cookies y no tiene una sesión activa
    if (!isset($_SESSION['idusuario']) && !isset($_SESSION['cookies_aceptadas'])) {
        echo '
        <div id="cookie-overlay" style="
            position: fixed; 
            top: 0; 
            left: 0; 
            width: 100%; 
            height: 100%; 
            background-color: rgba(0, 0, 0, 0.7); 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            z-index: 1000;">
            <div id="cookie-banner" style="
                background-color: #fff; 
                color: #333; 
                padding: 20px; 
                width: 90%; 
                max-width: 500px; 
                text-align: center; 
                border-radius: 10px; 
                box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);">
                <p>Gure webguneak cookie-ak erabiltzen ditu zure nabigazio esperientzia hobetzeko. <a href="politica_cookies.php">Informazio gehiago</a>.</p>
                <button id="aceptar-cookies" style="
                    background-color: #4CAF50; 
                    color: #fff; 
                    border: none; 
                    padding: 10px 20px; 
                    margin-top: 15px; 
                    border-radius: 5px; 
                    cursor: pointer;">
                    Onartu
                </button>
            </div>
        </div>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                document.getElementById("aceptar-cookies").addEventListener("click", function() {
                    document.getElementById("cookie-overlay").style.display = "none";
                    fetch("ManejoDatos/aceptar_cookies.php")
                        .then(response => {
                            if (!response.ok) {
                                throw new Error("Network response was not ok");
                            }
                            return response.text();
                        })
                        .then(data => console.log(data))
                        .catch(error => console.error("There was a problem with the fetch operation:", error));
                });
            });
        </script>
        ';
    }
    ?>

    <div class="banner-container">
        <img class="banner" src="img/banner.jpeg" alt="Banner">
        <span class="nav"><?php include 'Includes/nav_busqueda.html'; ?></span>
    </div>

    <div class="Bienvenido">
        <h1>Ongi etorri MOPAra</h1>
        <h3>(Mugikortasun Optimoa Pertsonen Atzigarritasunarentzat)</h3>
    </div>

    <div class="tarjetas">
        <div class="traj1">
            <p>MOPA-n gure helburua mugikortasun-arazoak dituzten pertsonentzako garraio inklusiboa erraztea da. Gure plataforma erabiltzaileei aukera ematen die ibilbideak antolatzeko eta partekatzeko, betiere irisgarritasuna eta eraginkortasuna kontuan hartuta. Gure sistema adimendunak ibilbide eraginkorrenak eta azkarrenak aurkitzen ditu, denbora eta baliabideak aurrezteko. Gainera, gure komunitateak elkarri laguntzea eta segurtasuna lehenesten ditu, erabiltzaileen arteko konexioak eta laguntza sustatuz. MOPA-n, mugikortasun inklusiboa eta irisgarria eskaintzea da gure helburua, pertsona guztien beharrei erantzuteko.</p>
        </div>
        <div class="traj1">
            <p>Gure plataformaren bidez, ibilbideak antolatzea sinplifikatzen dugu, erabiltzaileen beharrak kontuan hartuta...</p>
            <p>Gainera, gure sistema adimendunak ibilbide eraginkorrenak eta azkarrenak aurkitzen ditu, denbora eta baliabideak aurrezteko.</p>
            <p>Plataformak erabiltzaileei aukera ematen die ibilbideak partekatzeko eta komunitate bat sortzeko, non elkarri laguntzea eta segurtasuna lehenesten diren.</p>            <p>Gure helburua da mugikortasun inklusiboa eta irisgarria eskaintzea, pertsona guztien beharrei erantzuteko.</p>
        </div>
        <div class="traj1">
            <h3>Zergatik MOPA?</h3>
            <ul>
                <li>Irisgarritasuna bermatuta: Plataforma honetako ibilgailu eta ibilbide guztiak irisgarritasun baldintzak betetzen dituzte.</li>
                <li>Errepikatzen diren bidaiak: Lana, unibertsitatea edo eguneroko jarduerak bezalako ibilbide erregularrak egiten dituztenentzat aproposa.</li>
                <li>Giza konexioak: Ez da soilik mugikortasuna, baizik eta laguntza, errespetua eta segurtasuna sustatzen dituen ingurune batean egitea, elkarri laguntzea bultzatuz.</li>
            </ul>
        </div>
    </div>
    
    <?php include 'Includes/footer.html'; ?>
</body>

</html>