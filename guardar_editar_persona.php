<?php
// Conectar a la base de datos
$conexion = new mysqli('localhost', 'root', '', 'certificados_db');

// Comprobar conexión
if ($conexion->connect_error) {
    die(json_encode(['success' => false, 'error' => 'Error de conexión: ' . $conexion->connect_error]));
}

// Verificar que se reciban los datos del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numero_identificacion_original = mysqli_real_escape_string($conexion, $_POST['numero_identificacion_original']);
    $tipo_identificacion = mysqli_real_escape_string($conexion, $_POST['tipo_identificacion']);
    $numero_identificacion = mysqli_real_escape_string($conexion, $_POST['numero_identificacion']);
    $nombre_completo = mysqli_real_escape_string($conexion, $_POST['nombre_completo']);
    $correo_electronico = mysqli_real_escape_string($conexion, $_POST['correo_electronico']);

    // Convertir los valores a mayúsculas antes de guardarlos
    $tipo_identificacion = strtoupper(mysqli_real_escape_string($conexion, $_POST['tipo_identificacion']));
    $numero_identificacion = strtoupper(mysqli_real_escape_string($conexion, $_POST['numero_identificacion']));
    $nombre_completo = strtoupper(mysqli_real_escape_string($conexion, $_POST['nombre_completo']));

    // Actualizar los datos en la base de datos
    $query = "UPDATE personas SET tipo_identificacion='$tipo_identificacion', numero_identificacion='$numero_identificacion', nombre_completo='$nombre_completo', correo_electronico='$correo_electronico' WHERE numero_identificacion='$numero_identificacion_original'";

    if ($conexion->query($query) === TRUE) {
        // Si la actualización fue exitosa, mostrar mensaje y redirigir
        echo "<script>
                alert('Los datos han sido actualizados correctamente.');
                window.location.href = 'ver_personas.php';
              </script>";
    } else {
        // En caso de error en la actualización
        echo "<script>
                alert('Error al actualizar los datos: " . $conexion->error . "');
                window.location.href = 'ver_personas.php';
              </script>";
    }
} else {
    echo "<script>
            alert('No se recibieron datos para actualizar.');
            window.location.href = 'ver_personas.php';
          </script>";
}

// Cerrar la conexión
$conexion->close();
?>