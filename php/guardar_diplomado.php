<?php
// Conectar a la base de datos
$conexion = new mysqli('localhost', 'root', '', 'certificados_db');

// Comprobar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Verificar si se recibió un POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener los datos del formulario
    $nombre_diplomado = strtoupper(mysqli_real_escape_string($conexion, $_POST['nombre_diplomado']));
    $prefijo = strtoupper(mysqli_real_escape_string($conexion, $_POST['prefijo']));
    $descripcion = strtoupper(mysqli_real_escape_string($conexion, $_POST['descripcion']));

    // Preparar la declaración para insertar un nuevo diplomado
    $stmt = $conexion->prepare("INSERT INTO diplomados (nombre_diplomado, prefijo, descripcion) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nombre_diplomado, $prefijo, $descripcion);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        // Redirigir a la página de ver diplomados
        header('Location: ver_diplomados.php');
        exit;
    } else {
        // Manejar el error de la base de datos
        echo "Error al guardar el diplomado: " . $stmt->error;
    }

    // Cerrar la declaración
    $stmt->close();
} else {
    echo "Método de solicitud no válido.";
}

// Cerrar conexión
$conexion->close();
?>
