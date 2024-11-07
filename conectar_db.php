<?php
// Archivo de conexión a la base de datos
$servername = "localhost";
$username = "root"; // Usuario predeterminado de XAMPP
$password = "";     // Contraseña predeterminada en XAMPP (normalmente está en blanco)
$dbname = "certificados_db"; // Nombre de la base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>
