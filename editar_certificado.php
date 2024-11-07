<?php
session_start();  // Añadir esto al inicio del archivo PHP

// Conectar a la base de datos
$conexion = new mysqli('localhost', 'root', '', 'certificados_db');

// Comprobar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener el certificado a editar
$certificado = null;
if (isset($_GET['id'])) {
    $id_certificado = intval($_GET['id']);
    $query_certificado = "SELECT * FROM certificados WHERE id = $id_certificado";
    $result_certificado = $conexion->query($query_certificado);
    $certificado = $result_certificado->fetch_assoc();
}

// Verificar si se obtuvo el certificado
if (!$certificado) {
    die("Certificado no encontrado.");
}

// Obtener el nombre del curso basado en curso_id
$curso_id = $certificado['curso_id'];
$query_curso = "SELECT nombre_curso FROM cursos WHERE id = $curso_id";
$result_curso = $conexion->query($query_curso);
$curso = $result_curso->fetch_assoc();
$nombre_curso = $curso['nombre_curso'];  // Asignar el nombre del curso

// Obtener los cursos disponibles para el formulario
$query_cursos = "SELECT * FROM cursos";
$result_cursos = $conexion->query($query_cursos);

// Actualizar certificado si se envía el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo_identificacion = mysqli_real_escape_string($conexion, $_POST['tipo_identificacion']);
    $numero_identificacion = mysqli_real_escape_string($conexion, $_POST['numero_identificacion']);
    $nombre_completo = mysqli_real_escape_string($conexion, $_POST['nombre_completo']);
    $curso_id = mysqli_real_escape_string($conexion, $_POST['curso_id']);
    $tipo_formacion = mysqli_real_escape_string($conexion, $_POST['tipo_formacion']);
    $numero_horas = mysqli_real_escape_string($conexion, $_POST['numero_horas']);
    $fecha_certificacion = mysqli_real_escape_string($conexion, $_POST['fecha_certificacion']);
    $fecha_emision = mysqli_real_escape_string($conexion, $_POST['fecha_emision']);
    $formador = mysqli_real_escape_string($conexion, $_POST['formador']);

    // Actualizar certificado
    $result = $conexion->query("UPDATE certificados SET 
                                tipo_identificacion='$tipo_identificacion', 
                                numero_identificacion='$numero_identificacion', 
                                nombre_completo='$nombre_completo', 
                                curso_id='$curso_id', 
                                tipo_formacion='$tipo_formacion',
                                numero_horas='$numero_horas',
                                fecha_certificacion='$fecha_certificacion',
                                fecha_emision='$fecha_emision',
                                formador='$formador'
                            WHERE id='$id_certificado'");

    // Verificar si la actualización fue exitosa
    if ($result) {
        $_SESSION['mensaje'] = 'Certificado actualizado correctamente.';
        $_SESSION['mensaje_tipo'] = 'success';
    } else {
        $_SESSION['mensaje'] = 'Error al actualizar el certificado.';
        $_SESSION['mensaje_tipo'] = 'error';
    }

    // Redirigir después de la actualización para evitar reenviar el formulario al hacer refresh
    header("Location: editar_certificado.php?id=$id_certificado");
    exit; // Asegúrate de llamar a exit después de header para que el script no siga ejecutándose
}

// Obtener el número de certificado o generar uno nuevo si es necesario
$numero_certificado = $certificado['numero_certificado']; // O generar uno nuevo
?>

<!-- HTML Formulario -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Certificado</title>
    <link rel="stylesheet" href="css/estilos.css">
    <style>
        h1 { text-align: left; margin-bottom: 20px; }
        label { display: block; margin: 10px 0 5px; font-weight: bold; }
        input[type="text"], input[type="number"], input[type="date"] { width: 90%; padding: 10px; border: 1px solid #ccc; border-radius: 2px; margin-bottom: 10px; }
        .form-row { margin-bottom: 20px; }
    </style>
</head>
<body>
    <?php include 'header_nav.php'; ?>

    <div class="container">
        <h1>Editar Certificado</h1>
        <div class="botones">
            <a href="ver_certificados.php"><button>Volver al Menú Anterior</button></a>
        </div>

        <form method="POST">
            <label for="tipo_identificacion">Tipo de Identificación:</label>
            <select name="tipo_identificacion" id="tipo_identificacion" required>
                <option value="CC" <?php if ($certificado['tipo_identificacion'] == 'CC') echo 'selected'; ?>>CC</option>
                <option value="CE" <?php if ($certificado['tipo_identificacion'] == 'CE') echo 'selected'; ?>>CE</option>
                <option value="PA" <?php if ($certificado['tipo_identificacion'] == 'PA') echo 'selected'; ?>>PA</option>
                <option value="PPT" <?php if ($certificado['tipo_identificacion'] == 'PPT') echo 'selected'; ?>>PPT</option>
            </select>

            <label for="numero_identificacion">Número de Identificación:</label>
            <input type="text" id="numero_identificacion" name="numero_identificacion" value="<?php echo $certificado['numero_identificacion']; ?>" required>

            <label for="nombre_completo">Nombre Completo:</label>
            <input type="text" id="nombre_completo" name="nombre_completo" value="<?php echo $certificado['nombre_completo']; ?>" required>

            <label for="curso_id">Curso:</label>
            <select name="curso_id" id="curso_id" required>
                <?php while ($curso = $result_cursos->fetch_assoc()): ?>
                    <option value="<?php echo $curso['id']; ?>" <?php if ($certificado['curso_id'] == $curso['id']) echo 'selected'; ?>>
                        <?php echo $curso['nombre_curso']; ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="tipo_formacion">Tipo de Formación:</label>
            <select name="tipo_formacion" id="tipo_formacion" required>
                <option value="presencial" <?php if ($certificado['tipo_formacion'] == 'presencial') echo 'selected'; ?>>Presencial</option>
                <option value="virtual" <?php if ($certificado['tipo_formacion'] == 'virtual') echo 'selected'; ?>>Virtual</option>
            </select>

            <label for="numero_horas">Número de Horas:</label>
            <input type="number" id="numero_horas" name="numero_horas" value="<?php echo $certificado['numero_horas']; ?>" required>

            <label for="fecha_certificacion">Fecha de Certificación:</label>
            <input type="date" id="fecha_certificacion" name="fecha_certificacion" value="<?php echo $certificado['fecha_certificacion']; ?>" required>

            <label for="fecha_emision">Fecha de Emisión:</label>
            <input type="date" id="fecha_emision" name="fecha_emision" value="<?php echo $certificado['fecha_emision']; ?>" required>

            <label for="formador">Formador:</label>
            <input type="text" id="formador" name="formador" value="<?php echo $certificado['formador']; ?>" required>
            <button type="submit">Actualizar Certificado</button>
        </form>
        <a href="ver_certificados.php">Cancelar</a>
    </div>
</body>
</html>
