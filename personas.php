<?php   
// Conectar a la base de datos
$conexion = new mysqli('localhost', 'root', '', 'certificados_db');

// Comprobar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Configurar la conexión para usar UTF-8
$conexion->set_charset("utf8mb4");

// Variable para almacenar mensajes
$mensaje = "";

// Guardar persona
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $numero_identificacion = mysqli_real_escape_string($conexion, $_POST['numero_identificacion']);
    $tipo_identificacion = mysqli_real_escape_string($conexion, $_POST['tipo_identificacion']);
    $nombre_completo = mysqli_real_escape_string($conexion, $_POST['nombre_completo']);
    $correo_electronico = !empty($_POST['correo_electronico']) ? mysqli_real_escape_string($conexion, $_POST['correo_electronico']) : null;

    // Convertir el nombre a mayúsculas usando mb_strtoupper
    $nombre_mayuscula = mb_strtoupper($nombre_completo, 'UTF-8');

    // Verificar si el número de identificación ya existe
    $check_id = "SELECT * FROM personas WHERE numero_identificacion = '$numero_identificacion'";
    $result = $conexion->query($check_id);

    if ($result->num_rows > 0) {
        $mensaje = "El número de identificación '$numero_identificacion' ya está en uso. Por favor, elige otro.";
    } else {
        // Insertar la persona en la base de datos con el correo electrónico opcional
        $sql = "INSERT INTO personas (numero_identificacion, tipo_identificacion, nombre_completo, correo_electronico) VALUES ('$numero_identificacion', '$tipo_identificacion', '$nombre_mayuscula', '$correo_electronico')";
        if ($conexion->query($sql) === TRUE) {
            // Mensaje personalizado con el nombre en mayúsculas
            $mensaje = "$tipo_identificacion $numero_identificacion $nombre_mayuscula se ha creado correctamente.";
        } else {
            $mensaje = "Error: " . $sql . "<br>" . $conexion->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Personas</title>
    <link rel="stylesheet" href="css/estilos.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        .form-control {
            width: calc(100% - 40px);
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            font-size: 16px;
        }

        .botones {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>
<body>

    <?php include 'header_nav.php'; ?>

    <?php if (!empty($mensaje)): ?>
        <script>
            alert("<?php echo $mensaje; ?>");
            window.location.href = 'ver_personas.php';
        </script>
    <?php endif; ?>

    <div class="container">
        <h1>Gestionar Personas</h1>

        <form method="POST" action="personas.php">
            <div class="botones">
                <a href="ver_personas.php">
                    <button type="button" class="btn-volver">Volver al Menú Anterior</button>
                </a>
                <button type="submit" class="btn-guardar">Guardar Persona</button>
            </div>

            <div class="form-group">
                <label for="tipo_identificacion">Tipo de Identificación:</label>
                <select name="tipo_identificacion" id="tipo_identificacion" required class="form-control">
                    <option value="TI">TI</option>    
                    <option value="CC">CC</option>
                    <option value="CE">CE</option>
                    <option value="PA">PA</option>
                    <option value="PPT">PPT</option>
                </select>
            </div>

            <div class="form-group">
                <label for="numero_identificacion">Número de Identificación:</label>
                <input type="text" id="numero_identificacion" name="numero_identificacion" required class="form-control">
            </div>

            <div class="form-group">
                <label for="nombre_completo">Nombre Completo:</label>
                <input type="text" id="nombre_completo" name="nombre_completo" required class="form-control">
            </div>

            <div class="form-group">
                <label for="correo_electronico">Correo Electrónico (Opcional):</label>
                <input type="email" id="correo_electronico" name="correo_electronico" class="form-control">
            </div>
        </form>

        <div class="info-documento">
            <h3>Tipos de Identificación:</h3>
            <table>
                <thead>
                    <tr>
                        <th>Tipo ID</th>
                        <th>Descripción</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>TI</td>
                        <td>Tarjeta de Identidad</td>
                    </tr>
                    <tr>
                        <td>CC</td>
                        <td>Cédula de Ciudadanía</td>
                    </tr>
                    <tr>
                        <td>CE</td>
                        <td>Cédula de Extranjería</td>
                    </tr>
                    <tr>
                        <td>PA</td>
                        <td>Pasaporte</td>
                    </tr>
                    <tr>
                        <td>PPT</td>
                        <td>Permiso de Protección Temporal</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <?php $conexion->close(); ?>
</body>
</html>
