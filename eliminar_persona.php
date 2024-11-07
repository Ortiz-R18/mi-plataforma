<?php
// Conectar a la base de datos
$conexion = new mysqli('localhost', 'root', '', 'certificados_db');

// Comprobar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Variable para almacenar el mensaje de confirmación
$mensaje_confirmacion = "";

// Verificar si se han pasado los números de identificación
if (isset($_POST['personas'])) {
    $ids = $_POST['personas']; // Obtener las IDs desde el formulario
    $ids_imploded = implode(',', array_map('intval', $ids)); // Asegurar que sean enteros para prevenir inyecciones SQL

    // Eliminar personas
    $sql = "DELETE FROM personas WHERE numero_identificacion IN ($ids_imploded)";
    
    if ($conexion->query($sql) === TRUE) {
        $mensaje_confirmacion = "Las personas han sido eliminadas correctamente.";
    } else {
        $mensaje_confirmacion = "Error al eliminar las personas: " . $conexion->error;
    }
} elseif (isset($_GET['id'])) {
    $numero_identificacion = mysqli_real_escape_string($conexion, $_GET['id']);
    
    // Eliminar persona individual
    $sql = "DELETE FROM personas WHERE numero_identificacion='$numero_identificacion'";
    
    if ($conexion->query($sql) === TRUE) {
        $mensaje_confirmacion = "La persona ha sido eliminada correctamente.";
    } else {
        $mensaje_confirmacion = "Error al eliminar la persona: " . $conexion->error;
    }
} else {
    $mensaje_confirmacion = "Número de identificación no proporcionado.";
}

// Cerrar conexión
$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminación de Persona</title>
</head>
<body>

<?php if ($mensaje_confirmacion): ?>
    <script>
        // Mostrar mensaje de confirmación en un alert y redirigir
        alert("<?php echo $mensaje_confirmacion; ?>");
        window.location.href = "ver_personas.php"; // Cambia a la página deseada
    </script>
<?php endif; ?>

</body>
</html>
