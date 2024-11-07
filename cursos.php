<?php 
// Conectar a la base de datos
$conexion = new mysqli('localhost', 'root', '', 'certificados_db');

// Comprobar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Variable para el mensaje
$mensaje = "";

// Eliminar curso
if (isset($_GET['eliminar'])) {
    $id_curso = intval($_GET['eliminar']); // Convertir a entero para mayor seguridad
    $sql_eliminar = "DELETE FROM cursos WHERE id = $id_curso";

    if ($conexion->query($sql_eliminar) === TRUE) {
        header("Location: cursos.php?mensaje=Curso eliminado correctamente.");
        exit();
    } else {
        $mensaje = "Error al eliminar: " . $conexion->error;
    }
}

// Guardar curso
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_curso = strtoupper(mysqli_real_escape_string($conexion, $_POST['nombre_curso']));
    $prefijo = strtoupper(mysqli_real_escape_string($conexion, $_POST['prefijo']));

    // Verificar si el prefijo ya existe
    $check_prefijo = "SELECT * FROM cursos WHERE prefijo = '$prefijo'";
    $result = $conexion->query($check_prefijo);

    if ($result->num_rows > 0) {
        $mensaje = "El prefijo '$prefijo' ya está en uso. Por favor, elige otro.";
    } else {
        // Solo insertamos el nombre del curso y el prefijo
        $sql = "INSERT INTO cursos (nombre_curso, prefijo) VALUES ('$nombre_curso', '$prefijo')";
        if ($conexion->query($sql) === TRUE) {
            header("Location: cursos.php?mensaje=" . urlencode("$prefijo $nombre_curso se ha guardado correctamente."));
            exit();
        } else {
            $mensaje = "Error: " . $sql . "<br>" . $conexion->error;
        }
    }
}

// Obtener datos de cursos existentes
$query = "SELECT id, nombre_curso, prefijo FROM cursos"; // Asegúrate de incluir el campo 'id'
$result = $conexion->query($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Cursos</title>
    <link rel="stylesheet" href="css/estilos.css">
    <style>
        /* Estilo general para los campos de entrada */
        input[type="text"] {
            width: 98%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        .button-container {
            display: flex;
            justify-content: space-between;
            width: 100%;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .button-link {
            text-decoration: none;
        }
    </style>
</head>
<body>

    <?php include 'header_nav.php'; ?>

    <div class="container">
        <h1>Gestionar Cursos</h1>

        <div class="button-container">
            <a href="index.php" class="button-link"><button type="button">Volver al Inicio</button></a>
            <button type="submit" form="cursoForm">Guardar Curso</button>
        </div>

        <form id="cursoForm" method="POST" action="cursos.php">
            <label for="nombre_curso">Nombre del Curso:</label>
            <input type="text" id="nombre_curso" name="nombre_curso" required>

            <label for="prefijo">Prefijo:</label>
            <input type="text" id="prefijo" name="prefijo" required>
        </form>

        <h2>Cursos Existentes</h2>
        <table>
            <thead>
                <tr>
                    <th>Nombre del Curso</th>
                    <th>Prefijo</th>
                    <th>Acciones</th> <!-- Columna para acciones -->
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['nombre_curso']; ?></td>
                            <td><?php echo $row['prefijo']; ?></td>
                            <td>
                                <div style="display: flex; justify-content: space-between; width: 50%;">
                                    <a href="editar_curso.php?id=<?php echo $row['id']; ?>">
                                        <button>Editar</button>
                                    </a>
                                    <a href="cursos.php?eliminar=<?php echo $row['id']; ?>" onclick="return confirm('¿Estás seguro de que quieres eliminar este curso?');">
                                        <button class="eliminar">Eliminar</button>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="3">No hay cursos disponibles.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Mostrar mensaje de confirmación o error
        window.onload = function() {
            const mensaje = "<?php echo isset($_GET['mensaje']) ? $_GET['mensaje'] : ''; ?>";
            if (mensaje) {
                alert(mensaje);
            }
        }
    </script>

</body>
</html>

<?php
// Cerrar conexión
$conexion->close();
?>
