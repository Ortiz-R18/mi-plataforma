<?php 
// Conectar a la base de datos
$conexion = new mysqli('localhost', 'root', '', 'certificados_db');

// Comprobar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Verificar si se ha pasado el número de identificación
if (isset($_GET['id'])) {
    $numero_identificacion = mysqli_real_escape_string($conexion, $_GET['id']);

    // Obtener datos de la persona
    $query = "SELECT tipo_identificacion, nombre_completo, correo_electronico FROM personas WHERE numero_identificacion='$numero_identificacion'";
    $result = $conexion->query($query);

    if ($result->num_rows > 0) {
        $persona = $result->fetch_assoc();
    } else {
        echo "<script>alert('Persona no encontrada.'); window.location.href='ver_personas.php';</script>";
        exit;
    }
} else {
    echo "<script>alert('Número de identificación no proporcionado.'); window.location.href='ver_personas.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Persona</title>
    <link rel="stylesheet" href="css/estilos.css">
    <style>
        .form-group {
            margin-bottom: 15px; /* Espacio entre campos */
        }

        label {
            display: block; /* Hace que el label ocupe una línea completa */
            margin-bottom: 5px; /* Espacio debajo del label */
        }

        input[type="text"], select {
            width: 100%; /* Ancho completo */
            padding: 10px; /* Espacio interno */
            border: 1px solid #ccc; /* Borde */
            border-radius: 4px; /* Bordes redondeados */
            box-sizing: border-box; /* Para incluir padding y borde en el ancho total */
        }

        button.btn {
            padding: 10px 15px; /* Espaciado interno */
            color: white; /* Color del texto */
            border: none; /* Sin borde */
            border-radius: 4px; /* Bordes redondeados */
            cursor: pointer; /* Cambia el cursor al pasar el mouse */
        }
    </style>
</head>
<body>
    <!-- Incluyendo el menú de navegación -->
    <?php include 'header_nav.php'; ?>

    <div class="container" style="margin-left: 220px; padding: 20px; max-width: 800px; margin-right: auto;">
        <h1>Editar Persona</h1>

        <!-- Botones en la parte superior izquierda -->
        <div class="button-group">
            <form method="POST" action="ver_personas.php" style="display: inline;">
                <button type="submit" class="btn">Volver al menú anterior</button>
            </form>
        </div>

        <!-- Formulario para editar la persona -->
        <form id="editarPersonaForm" method="POST" action="guardar_editar_persona.php">
            <input type="hidden" name="numero_identificacion_original" value="<?php echo htmlspecialchars($numero_identificacion); ?>">

            <div class="form-group">
                <label for="tipo_identificacion">Tipo de Identificación:</label>
                <select name="tipo_identificacion" id="tipo_identificacion" required>
                    <option value="TI" <?php echo ($persona['tipo_identificacion'] == 'TI') ? 'selected' : ''; ?>>TI</option>
                    <option value="CC" <?php echo ($persona['tipo_identificacion'] == 'CC') ? 'selected' : ''; ?>>CC</option>
                    <option value="CE" <?php echo ($persona['tipo_identificacion'] == 'CE') ? 'selected' : ''; ?>>CE</option>
                    <option value="PA" <?php echo ($persona['tipo_identificacion'] == 'PA') ? 'selected' : ''; ?>>PA</option>
                    <option value="PPT" <?php echo ($persona['tipo_identificacion'] == 'PPT') ? 'selected' : ''; ?>>PPT</option>
                </select>
            </div>

            <div class="form-group">
                <label for="numero_identificacion">Número de Identificación:</label>
                <input type="text" id="numero_identificacion" name="numero_identificacion" value="<?php echo htmlspecialchars($numero_identificacion); ?>" required>
            </div>

            <div class="form-group">
                <label for="nombre_completo">Nombre Completo:</label>
                <input type="text" id="nombre_completo" name="nombre_completo" value="<?php echo htmlspecialchars($persona['nombre_completo']); ?>" required>
            </div>

            <div class="form-group">
                <label for="correo_electronico">Correo Electrónico:</label>
                <input type="text" id="correo_electronico" name="correo_electronico" value="<?php echo isset($persona['correo_electronico']) ? htmlspecialchars($persona['correo_electronico']) : ''; ?>" required>
            </div>

            <button type="submit" class="btn">Guardar Cambios</button>
        </form>
    </div>
</body>
</html>
