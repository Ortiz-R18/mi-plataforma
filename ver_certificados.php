<?php 
// Conectar a la base de datos
$conexion = new mysqli('localhost', 'root', '', 'certificados_db');

// Comprobar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Manejar la eliminación de certificados
if (isset($_POST['eliminar_seleccionados'])) {
    if (isset($_POST['certificados']) && is_array($_POST['certificados'])) {
        $idsEliminar = implode(',', array_map('intval', $_POST['certificados'])); // Asegurar que sean enteros
        $eliminarQuery = "DELETE FROM certificados WHERE id IN ($idsEliminar)";
        if ($conexion->query($eliminarQuery) === TRUE) {
            $mensaje = "Certificados eliminados con éxito.";
        } else {
            $mensaje = "Error al eliminar certificados: " . $conexion->error;
        }
    } else {
        $mensaje = "No se seleccionaron certificados para eliminar.";
    }
}

// Configuración de la paginación
$registrosPorPagina = 10; // Cambiar a 10 para cumplir con tu requerimiento
$paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($paginaActual - 1) * $registrosPorPagina;

// Calcular el número total de registros
$totalRegistrosQuery = "SELECT COUNT(*) as total FROM certificados";
$resultTotalRegistros = $conexion->query($totalRegistrosQuery);
$totalRegistros = $resultTotalRegistros->fetch_assoc()['total'];
$totalPaginas = ceil($totalRegistros / $registrosPorPagina);

// Obtener todos los certificados
$query_certificados = "SELECT * FROM certificados";
$result_certificados = $conexion->query($query_certificados);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Certificados</title>
    <link rel="stylesheet" href="css/estilos.css">
    <style>
        .button-container {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
        }
        .right-buttons {
            display: flex;
            gap: 10px;
        }
        .pagination {
            margin-top: 15px;
            text-align: center;
        }
        .pagination button {
            padding: 5px 10px;
            margin: 0 2px;
        }
    </style>
</head>
<body>

<?php include 'header_nav.php'; ?>

<div class="container">
    <h1>Lista de Certificados Cursos</h1>

    <?php if (isset($mensaje)): ?>
        <p class="mensaje"><?php echo htmlspecialchars($mensaje); ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="button-container">
            <div>
                <a href="index.php" class="button">
                    <button type="button">Volver al Menú Anterior</button>
                </a>
            </div>
            <div class="right-buttons">
                <a href="certificados.php" class="button">
                    <button type="button">Generar nuevo certificado</button>
                </a>
                <button type="submit" name="eliminar_seleccionados" 
                        onclick="return confirm('¿Estás seguro de que quieres eliminar los certificados seleccionados?');" 
                        class="button">
                    Eliminar Seleccionados
                </button>
            </div>
        </div>

        <table id="certificadosTable">
            <tr>
                <th>N° Certificado</th>
                <th>Tipo ID</th>
                <th>Documento</th>
                <th>Nombres Completos</th>
                <th>Nombre curso</th>
                <th>Formación</th>
                <th>Horas</th>
                <th>Certificación</th>
                <th>Fecha</th>
                <th>Formador</th>
                <th>Empresa</th>
                <th>Logo(Opc)</th>
                <th>Acción</th>
                <th><input type="checkbox" id="selectAllCheckbox"></th>
            </tr>
            <?php if ($result_certificados->num_rows > 0): ?>
                <?php while($certificado = $result_certificados->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $certificado['numero_certificado']; ?></td>
                            <td><?php echo $certificado['tipo_identificacion']; ?></td>
                            <td><?php echo $certificado['numero_identificacion']; ?></td>
                            <td><?php echo $certificado['nombre_completo']; ?></td>
                            <td><?php echo $certificado['nombre_curso']; ?></td>
                            <td><?php echo $certificado['tipo_formacion']; ?></td>
                            <td><?php echo $certificado['numero_horas']; ?></td>
                            <td><?php echo $certificado['fecha_certificacion']; ?></td>
                            <td><?php echo $certificado['fecha_emision']; ?></td>
                            <td><?php echo $certificado['formador']; ?></td>
                            <td><?php echo $certificado['razon_social']; ?></td>
                            <td><?php echo $certificado['logo_path']; ?></td>
                        <td><a href="php/descargar_certificado.php?id=<?php echo $certificado['id']; ?>">Descargar</a></td>
                        <td><input type="checkbox" name="certificados[]" value="<?php echo $certificado['id']; ?>"></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="13">No hay certificados disponibles.</td></tr>
            <?php endif; ?>
        </table>
    </form>

    <!-- Paginación -->
    <div class="pagination">
        <span>Página <?php echo $paginaActual; ?> de <?php echo $totalPaginas; ?></span>
        <?php if ($paginaActual > 1): ?>
            <a href="?pagina=<?php echo $paginaActual - 1; ?>">Anterior</a>
        <?php endif; ?>
        <?php if ($paginaActual < $totalPaginas): ?>
            <a href="?pagina=<?php echo $paginaActual + 1; ?>">Siguiente</a>
        <?php endif; ?>
    </div>

</div>

<script>
    // Selección de todos los checkboxes
    document.getElementById('selectAllCheckbox').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('input[name="certificados[]"]');
        checkboxes.forEach(checkbox => checkbox.checked = this.checked);
    });
</script>

</body>
</html>

<?php
// Cerrar conexión
$conexion->close();
?>
