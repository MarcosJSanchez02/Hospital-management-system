
<?php
//conexión
$servername = "localhost";
$username = "root";
$password = "";
$database = "salita_db";

//crear la conexión
$conn = new mysqli($servername, $username, $password, $database);

//revisamos la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

//establecemos un charset UTF8 para no tener problemas con los acentos
$conn->set_charset("utf8");
?>