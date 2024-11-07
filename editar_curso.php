<?php 
// Conectar a la base de datos
$conexion = new mysqli('localhost', 'root', '', 'certificados_db');

// Comprobar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener el curso a editar
if (isset($_GET['id'])) {
    $id_curso = intval($_GET['id']);
    $query_curso = "SELECT * FROM cursos WHERE id = $id_curso";
    $result_curso = $conexion->query($query_curso);
    $curso = $result_curso->fetch_assoc();
}

// Actualizar curso si se envía el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($id_curso)) {
    $nombre_curso = mysqli_real_escape_string($conexion, $_POST['nombre_curso']);
    $prefijo = mysqli_real_escape_string($conexion, $_POST['prefijo']);
    $conexion->query("UPDATE cursos SET nombre_curso='$nombre_curso', prefijo='$prefijo' WHERE id = $id_curso");
    
    // Redirigir a cursos.php después de la actualización
    header('Location: cursos.php?mensaje=Curso actualizado correctamente');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Curso</title>
    <link rel="stylesheet" href="css/estilos.css">
    <style>
        /* Estilos para campos de entrada y botones */
        .container {
            width: 80%;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.5);
            box-sizing: border-box;
            max-width: 1200px;
            margin: 0 auto;
        }

        .form-header {
            display: flex;
            justify-content: space-between;
            width: 100%;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
    </style>
</head>
<body>

    <!-- Incluir el archivo de encabezado y navegación -->
    <?php include 'header_nav.php'; ?>

    <div class="container">
        <h1>Editar Curso</h1>

        <!-- Formulario de edición del curso -->
        <form method="POST">
            <label for="nombre_curso">Nombre del Curso:</label>
            <input type="text" id="nombre_curso" name="nombre_curso" value="<?php echo htmlspecialchars($curso['nombre_curso']); ?>" required>

            <label for="prefijo">Prefijo:</label>
            <input type="text" id="prefijo" name="prefijo" value="<?php echo htmlspecialchars($curso['prefijo']); ?>" required>
            
            <div class="button-container">
                <a href="cursos.php"><button type="button" class="btn btn-cancelar">Volver</button></a>
                <button type="submit" class="btn btn-actualizar">Actualizar Curso</button>
            </div>
        </form>
    </div>

</body>
</html>

<?php
// Cerrar conexión
$conexion->close();
?>
