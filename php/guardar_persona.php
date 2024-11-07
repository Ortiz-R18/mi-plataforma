<?php  
// Conectar a la base de datos
$conexion = new mysqli('localhost', 'root', '', 'certificados_db');

// Comprobar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Inicializar mensaje
$mensaje = "";

// Verificar si se han recibido los datos del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del formulario
    $tipo_identificacion = $_POST['tipo_identificacion'];
    $numero_identificacion = $_POST['numero_identificacion'];
    $nombre_completo = $_POST['nombre_completo'];

    // Actualizar la persona en la base de datos
    $sql = "UPDATE personas SET tipo_identificacion='$tipo_identificacion', nombre_completo='$nombre_completo' WHERE numero_identificacion='$numero_identificacion'";

    if ($conexion->query($sql) === TRUE) {
        $mensaje = "'$tipo_identificacion' '$numero_identificacion' '$nombre_completo' se ha actualizado correctamente.";
    } else {
        $mensaje = "Error: " . $sql . "<br>" . $conexion->error;
    }
} else {
    $mensaje = "No se han recibido datos para guardar.";
}

// Cerrar la conexión
$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado de Edición de Persona</title>
    <link rel="stylesheet" href="css/estilos.css"> <!-- Acceso a estilos.css -->
</head>
<body>

<div class="modal" id="myModal">
    <div class="modal-content">
        <span class="close" id="closeModal">&times;</span>
        <p><?php echo $mensaje; ?></p>
        <a href="ver_personas.php" class="btn">Volver a Consultar Personas</a>
    </div>
</div>

<script>
    // Mostrar la modal
    window.onload = function() {
        var modal = document.getElementById("myModal");
        modal.style.display = "block";
        
        // Cerrar la modal cuando se hace clic en la X
        var span = document.getElementById("closeModal");
        span.onclick = function() {
            modal.style.display = "none";
            window.location.href = 'ver_personas.php'; // Redirigir después de cerrar
        }
        
        // Cerrar la modal si se hace clic fuera de ella
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
                window.location.href = 'ver_personas.php'; // Redirigir después de cerrar
            }
        }
    }
</script>

</body>
</html>
