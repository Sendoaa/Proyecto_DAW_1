
# MOPA - Sistema de Gestión de Viajes

**MOPA** es una página web diseñada para gestionar la búsqueda y compra de viajes. El sistema está basado en PHP y MySQL, y se ejecuta en un servidor Apache. Este tutorial te explica cómo poner en marcha el sistema utilizando XAMPP.

## Requisitos previos

- **XAMPP**: Puedes descargar XAMPP desde aqui --> (https://www.apachefriends.org/download.html)
- **PHP**: Versión 7.4 o superior (incluido en XAMPP).
- **MySQL**: También incluido en XAMPP.

## Instalación

### 1. Descargar el proyecto

Clona este repositorio en tu directorio local o descarga los archivos y descomprímelos en la carpeta `htdocs` de XAMPP:

```bash
C:\xampp\htdocs\mopa
```

### 2. Configuración de la base de datos

1. Abre **phpMyAdmin** en http://localhost/phpmyadmin.
2. Crea una nueva base de datos llamada `mopa`:
   - Ve a la pestaña **Bases de datos**, introduce el nombre `mopa` y selecciona el conjunto de caracteres `utf8_general_ci`.
   - Haz clic en el botón `Crear`.
3. Importa el archivo SQL del directorio `database` del proyecto:
   - En la base de datos `mopa`, ve a la pestaña Importar y selecciona el archivo `mopa.sql`.
   - Haz clic en **Continuar** para importar las tablas y los datos.

### 3. Configuración del archivo de conexión a la base de datos

Abre el archivo `config.php` en la raíz del proyecto y asegúrate de que los parámetros se ajusten a tu entorno local:

```php
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mopa";
```

- **`username`**: `root` (Por defecto en XAMPP).
- **`password`**: Vacío si no has configurado contraseña

### 4. Iniciar Apache y MySQL

1. Abre el **Panel de Control de XAMPP**.
2. Inicia los módulos **Apache** y **MySQL**.
3. Accede a http://localhost/mopa desde tu navegador para ver la página web en funcionamiento.

## Características

- **Búsqueda de viajes:** Los usuarios pueden buscar viajes filtrando por origen, destino, fecha y número de pasajeros.
- **Gestión de usuarios:** Permite cargar el perfil de un usuario seleccionado sin necesidad de iniciar sesión.
- **Compra de viajes:** Verifica si el usuario es el conductor del viaje antes de autorizar la compra.
- **Traducción al euskera:** Implementación de traducción automática al euskera sin usar APIs externas.

## Estructura del proyecto

- **/database**: Contiene el archivo `mopa.sql` para la creación de la base de datos.
- **/css**: Archivos personalizados de estilo, incluidos estilos para el calendario.
- **/js**: Funciones JavaScript para la lógica de la aplicación.
- **/includes**: Archivos PHP para la lógica del servidor y la conexión a la base de datos.
- **/config.php**: Archivo de configuración de la base de datos.

## Créditos

- **Desarrolladores**: [Lucas García | Ander Gil | Sendoa Avedillo | Patrick Deba]

---

Este README ofrece las instrucciones necesarias para poner en marcha el proyecto en un entorno local con XAMPP. Si encuentras problemas durante la instalación o configuración, revisa los registros de Apache y MySQL en el Panel de Control de XAMPP.
