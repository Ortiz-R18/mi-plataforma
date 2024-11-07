<?php  
// Conectar a la base de datos
$conexion = new mysqli('localhost', 'root', '', 'certificados_db');

// Comprobar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Manejar la búsqueda
$busqueda = '';
if (isset($_POST['buscar'])) {
    $busqueda = $_POST['busqueda'];
}

// Obtener todos los certificados según la búsqueda
$query_certificados = "
    SELECT certificados.*, cursos.nombre_curso 
    FROM certificados 
    JOIN cursos ON certificados.curso_id = cursos.id
    WHERE certificados.nombre_completo LIKE ? OR certificados.numero_identificacion LIKE ?
    ORDER BY fecha_certificacion ASC
";

// Preparar la consulta
$stmt = $conexion->prepare($query_certificados);
$like_busqueda = "%$busqueda%";
$stmt->bind_param("ss", $like_busqueda, $like_busqueda);
$stmt->execute();
$result_certificados = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Certificados</title>
    <link rel="stylesheet" href="css/estilos.css">
    <style>
        .button-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }
        .button-container .left-buttons {
            flex: 1;
        }
        .button-container .right-buttons {
            display: flex;
            gap: 10px; /* Espaciado entre los botones */
        }
    </style>
</head>
<body>

    <?php include 'header_nav.php'; ?>

    <div class="container">
        <h1>Buscar Certificados</h1>

        <form method="POST" action="">
            <input type="text" name="busqueda" placeholder="Buscar por nombre o documento" value="<?php echo htmlspecialchars($busqueda); ?>" required>
            <button type="submit" name="buscar" class="button">Buscar</button>
        </form>

        <div class="button-container">
            <div class="left-buttons">
                <a href="index.php" class="button">
                    <button type="button">Volver al Menú Anterior</button>
                </a>
            </div>
            <div class="right-buttons">
                <a href="certificados.php" class="button">
                    <button type="button">Generar nuevo certificado</button>
                </a>
            </div>
        </div>

        <table>
            <tr>
                <th>N° Certificado</th>
                <th>Tipo ID</th>
                <th>Documento</th>
                <th>Nombres Completos</th>
                <th>Nombre curso</th>
                <th>Horas</th>
                <th>Fecha</th>
                <th>Formador</th>
                <th>Acción</th>
                <th>Descargar</th>
                <th>ㅤ</th>
            </tr>
            <?php if ($result_certificados->num_rows > 0): ?>
                <?php while($certificado = $result_certificados->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $certificado['numero_certificado']; ?></td>
                        <td><?php echo $certificado['tipo_identificacion']; ?></td>
                        <td><?php echo $certificado['numero_identificacion']; ?></td>
                        <td><?php echo $certificado['nombre_completo']; ?></td>
                        <td><?php echo $certificado['nombre_curso']; ?></td>
                        <td><?php echo $certificado['numero_horas']; ?></td>
                        <td><?php echo $certificado['fecha_certificacion']; ?></td>
                        <td><?php echo $certificado['formador']; ?></td>
                        <td>
                            <a href="editar_certificado.php?id=<?php echo $certificado['id']; ?>">Editar</a>
                        </td>
                        <td>
                            <a href="php/descargar_certificado.php?id=<?php echo $certificado['id']; ?>">Descargar</a>
                        </td>
                        <td>
                            <input type="checkbox" name="certificados[]" value="<?php echo $certificado['id']; ?>">
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="11">No se encontraron certificados.</td></tr>
            <?php endif; ?>
        </table>
    </div>

</body>
</html>

<?php
// Cerrar conexión
$conexion->close();
?>
