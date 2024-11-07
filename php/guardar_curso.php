<?php 
// Conectar a la base de datos
$conexion = new mysqli('localhost', 'root', '', 'certificados_db');

// Comprobar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Guardar curso
$nombre_curso = mysqli_real_escape_string($conexion, $_POST['nombre_curso']);
$prefijo = mysqli_real_escape_string($conexion, $_POST['prefijo']);

// Verificar si el prefijo ya existe
$sql_check = "SELECT COUNT(*) as total FROM cursos WHERE prefijo = '$prefijo'";
$result_check = $conexion->query($sql_check);
$row = $result_check->fetch_assoc();

$mensaje = "";
if ($row['total'] > 0) {
    $mensaje = "Error: El prefijo '$prefijo' ya existe. No se puede guardar el curso.";
} else {
    // Solo insertamos el nombre del curso y el prefijo
    $sql = "INSERT INTO cursos (nombre_curso, prefijo) VALUES ('$nombre_curso', '$prefijo')";
    if ($conexion->query($sql) === TRUE) {
        $mensaje = "Curso '$prefijo $nombre_curso' se ha guardado correctamente.";
    } else {
        $mensaje = "Error: " . $sql . "<br>" . $conexion->error;
    }
}

// Cerrar conexión
$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado del Curso</title>
    <link rel="stylesheet" href="css/estilos.css">
    <style>
        /* Estilos para el modal */
        .modal {
            display: none; /* Oculto por defecto */
            position: fixed; /* Fijo */
            z-index: 1000; /* En la parte superior */
            left: 0;
            top: 0;
            width: 100%; /* Ancho completo */
            height: 100%; /* Alto completo */
            overflow: auto; /* Habilitar scroll si es necesario */
            background-color: rgba(0,0,0,0.5); /* Fondo negro con opacidad */
        }
        .modal-dialog {
            position: relative;
            margin: 15% auto; /* Centramos el modal */
            width: 80%; /* Ancho del modal */
            max-width: 600px; /* Ancho máximo */
        }
        .modal-content {
            background-color: #fefefe;
            border: 1px solid #888;
            border-radius: 5px; /* Bordes redondeados */
            box-shadow: 0 4px 8px rgba(0,0,0,0.2); /* Sombra */
        }
    </style>
</head>
<body>

<!-- Modal personalizado -->
<div class="modal" id="resultadoModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Resultado del Curso</h5>
        <button type="button" class="close-modal" onclick="closeModal()">×</button>
      </div>
      <div class="modal-body">
        <?php echo $mensaje; ?>
      </div>
      <div class="modal-footer">
        <a href="/mi-plataforma/cursos.php" class="btn">Volver a la Gestión de Cursos</a>
      </div>
    </div>
  </div>
</div>

<script>
// Mostrar el modal automáticamente al cargar la página
window.onload = function() {
    document.getElementById('resultadoModal').style.display = 'block';
}

// Función para cerrar el modal y redirigir a ver_cursos.php
function closeModal() {
    window.location.href = "/mi-plataforma/ver_cursos.php"; // Redirigir a ver_cursos.php
}
</script>

</body>
</html>
