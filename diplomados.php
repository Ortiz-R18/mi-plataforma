<?php 
// Conectar a la base de datos
$conexion = new mysqli('localhost', 'root', '', 'certificados_db');

// Comprobar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Variable para el mensaje
$mensaje = "";

// Guardar diplomado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_diplomado = strtoupper(mysqli_real_escape_string($conexion, $_POST['nombre_diplomado']));
    $prefijo = strtoupper(mysqli_real_escape_string($conexion, $_POST['prefijo']));
    $descripcion = mysqli_real_escape_string($conexion, $_POST['descripcion']);

    // Insertamos el diplomado
    $sql = "INSERT INTO diplomados (nombre_diplomado, prefijo, descripcion) VALUES ('$nombre_diplomado', '$prefijo', '$descripcion')";
    if ($conexion->query($sql) === TRUE) {
        header("Location: diplomados.php?mensaje=" . urlencode("$prefijo $nombre_diplomado se ha guardado correctamente."));
        exit();
    } else {
        $mensaje = "Error: " . $sql . "<br>" . $conexion->error;
    }
}

// Obtener datos de diplomados existentes
$query = "SELECT id, nombre_diplomado, prefijo, descripcion FROM diplomados"; // Asegúrate de incluir 'id' en la consulta
$result = $conexion->query($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar nuevo Diplomado</title>
    <link rel="stylesheet" href="css/estilos.css">
    <style>
        /* Estilo general para los campos de entrada */
        input[type="text"], select {
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
        <h1>Gestionar Diplomados</h1>

        <div class="button-container">
            <a href="index.php" class="button-link"><button type="button">Volver al Inicio</button></a>
            <button type="submit" form="diplomadoForm">Guardar Diplomado</button>
        </div>

        <form id="diplomadoForm" method="POST" action="diplomados.php">
            <label for="nombre_diplomado">Nombre del Diplomado:</label>
            <input type="text" id="nombre_diplomado" name="nombre_diplomado" required>

            <label for="prefijo">Prefijo:</label>
            <input type="text" id="prefijo" name="prefijo" required>

            <label for="descripcion">Descripción:</label>
            <select id="descripcion" name="descripcion" required>
                <option value="">Seleccione una opción</option>
                <option value="BAJO LOS CRITERIOS DEFINIDOS EN LA RESOLUCIÓN 20223040040595 DE 2022">BAJO LOS CRITERIOS DEFINIDOS EN LA RESOLUCIÓN 20223040040595 DE 2022</option>
                <option value="BAJO LOS LINEAMIENTOS DE LA ISO 19011:2018">BAJO LOS LINEAMIENTOS DE LA ISO 19011:2018</option>
            </select>
        </form>

        <h2>Diplomados Existentes</h2>
        <table>
            <thead>
                <tr>
                    <th>Nombre del Diplomado</th>
                    <th>Prefijo</th>
                    <th>Descripción</th>
                    <th>Acciones</th> <!-- Añadir columna para acciones -->
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['nombre_diplomado']; ?></td>
                        <td><?php echo $row['prefijo']; ?></td>
                        <td><?php echo $row['descripcion']; ?></td>
                        <td> 
    <div style="display: flex; justify-content: space-between; width: 50%;">
        <a href="editar_diplomado.php?id_diplomado=<?php echo $row['id']; ?>">
            <button>Editar</button>
        </a>
        <a href="eliminar_diplomado.php?id=<?php echo $row['id']; ?>" onclick="return confirm('¿Estás seguro de que quieres eliminar este diplomado?');">
            <button class="eliminar">Eliminar</button>
        </a>
    </div>
</td>

                    </tr>
                <?php endwhile; ?>
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
