
# MOPA - Bidaiak Kudeatzeko Sistema

**MOPA** bidaien bilaketa eta erosketa kudeatzeko diseinatutako web orri bat da. Sistema PHP eta MySQL-en oinarritzen da, eta Apache zerbitzarian exekutatzen da. Tutorial honek XAMPP erabiliz sistema martxan jartzeko prozesua azalduko dizu.

## Aurrebaldintzak

- **XAMPP**: XAMPP [hemen](https://www.apachefriends.org/download.html) deskargatu dezakezu.
- **PHP**: 7.4 bertsioa edo handiagoa (XAMPP-en barne).
- **MySQL**: XAMPP-en barne.

## Instalazioa

### 1. Proiektua Deskargatzea

Errepositorio hau zure tokiko direktorioan klonatu edo fitxategiak deskargatu eta XAMPP-eko `htdocs` karpetan atera:

```bash
C:\xampp\htdocs\mopa
```

### 2. Datu-basea Konfiguratzea

1. Ireki **phpMyAdmin** [http://localhost/phpmyadmin](http://localhost/phpmyadmin) helbidetik.
2. Sortu `mopa` izeneko datu-base berria:
   - Joan **Datu-baseak** fitxara, sartu `mopa` izena eta hautatu `utf8_general_ci` karaktere-multzoa.
   - Egin klik **Sortu** botoian.
3. Inportatu proiektuaren `database` direktorioan dagoen SQL fitxategia:
   - `mopa` datu-basean, joan **Inportatu** fitxara eta hautatu `mopa.sql` fitxategia.
   - Egin klik **Jarraitu** botoian taulak eta datuak inportatzeko.

### 3. Datu-basearen Konexio Fitxategia Konfiguratzea

Ireki `config.php` fitxategia proiektuaren erroan eta ziurtatu parametroak zure tokiko ingurunera egokitzen direla:

```php
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mopa";
```

- **`username`**: `root` izan beharko luke (XAMPP-en lehenetsia).
- **`password`**: Utzi hutsik, MySQL-rako pasahitza ez baduzu ezarri.

### 4. Apache eta MySQL Abiaraztea

1. Ireki **XAMPP Kontrol Panela**.
2. Abiarazi **Apache** eta **MySQL** moduluak.
3. Nabigatu [http://localhost/mopa](http://localhost/mopa) helbidera zure nabigatzailean web orria atzitzeko.

## Ezaugarriak

- **Bidaien Bilaketa**: Erabiltzaileek bidaiak bila ditzakete jatorria, helmuga, data eta bidaiari kopuruaren arabera iragaziz.
- **Erabiltzaileen Kudeaketa**: Sistemak aukera ematen du hautatutako erabiltzaile baten profila kargatzeko, saioa hasi beharrik gabe.
- **Bidaien Erosketa**: Erabiltzailea bidaiaren gidaria den egiaztatzen du erosketa baimendu aurretik.
- **Euskarazko Itzulpena**: Euskararako itzulpen automatikoa inplementatuta, API kanpokoak erabili gabe.

## Proiektuaren Egitura

- **/database**: `mopa.sql` fitxategia datu-basea sortzeko.
- **/css**: Estilo fitxategi pertsonalizatuak (egutegirako estiloak barne).
- **/js**: Aplikazioaren logikarako JavaScript funtzioak.
- **/includes**: Atzeko aldeko logika eta datu-basearen konexiorako PHP fitxategiak.
- **/config.php**: Datu-basearen konfigurazio fitxategia.

## Kredituak

- **Garatzaileak**: [Lucas García | Ander Gil | Sendoa Avedillo | Patrick Deba]

---

README honek proiektua tokiko ingurune batean XAMPP erabiliz martxan jartzeko beharrezko argibideak eskaintzen ditu. Instalazio edo konfigurazioan arazoak badituzu, XAMPP Kontrol Panelean Apache eta MySQL erregistroak berrikusi.
