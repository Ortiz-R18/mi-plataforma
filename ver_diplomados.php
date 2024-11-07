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
        $idsEliminar = implode(',', array_map('intval', $_POST['certificados']));
        $eliminarQuery = "DELETE FROM certificados_diplomados WHERE id IN ($idsEliminar)";
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
$registrosPorPagina = 10;
$paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($paginaActual - 1) * $registrosPorPagina;

// Calcular el número total de registros
$totalRegistrosQuery = "SELECT COUNT(*) as total FROM certificados_diplomados";
$resultTotalRegistros = $conexion->query($totalRegistrosQuery);
$totalRegistros = $resultTotalRegistros->fetch_assoc()['total'];
$totalPaginas = ceil($totalRegistros / $registrosPorPagina);

// Obtener los registros para la página actual
$query_certificados = "
    SELECT * 
    FROM certificados_diplomados 
    ORDER BY fecha_certificacion ASC
    LIMIT $registrosPorPagina OFFSET $offset
";
$result_certificados = $conexion->query($query_certificados);
$certificados = [];
if ($result_certificados->num_rows > 0) {
    while ($row = $result_certificados->fetch_assoc()) {
        $certificados[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Certificados de Diplomados</title>
    <link rel="stylesheet" href="css/estilos.css">
    <style>
        /* Estilos para la paginación */
        .pagination {
            margin-top: 15px;
            text-align: center;
        }
        .pagination button {
            padding: 5px 10px;
            margin: 0 2px;
        }
            /* Estilo para alinear los botones a la derecha */
    .button-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }
    .right-buttons {
        display: flex;
        gap: 10px;
    }
        /* Estilo para el cuadro de búsqueda */
        .search-container {
        width: 100%; /* Ancho completo */
        margin-bottom: 15px; /* Espacio debajo del cuadro de búsqueda */
    }
    .search-input {
        width: 100%; /* Ancho completo del input */
        padding: 10px; /* Espaciado interno */
        box-sizing: border-box; /* Para incluir padding en el ancho total */
        border: 1px solid #ccc; /* Borde del cuadro */
        border-radius: 5px; /* Esquinas redondeadas */
        font-size: 12px; /* Tamaño de fuente */
    }
    </style>
</head>
<body>

    <?php include 'header_nav.php'; ?>

    <div class="container">
        <h1>Lista de Certificados de Diplomados</h1>

        <?php if (isset($mensaje)): ?>
            <p class="mensaje"><?php echo htmlspecialchars($mensaje); ?></p>
        <?php endif; ?>

        <form method="POST" action="">
    <div class="button-container">
        <!-- Botón "Volver al Menú Anterior" a la izquierda -->
        <a href="index.php" class="button">
            <button type="button">Volver al Inicio</button>
        </a>
        <!-- Botones alineados a la derecha -->
        <div class="right-buttons">
            <a href="certificados_diplomados.php" class="button">
                <button type="button">Generar nuevo certificado</button>
            </a>
            <button type="submit" name="eliminar_seleccionados" 
                    onclick="return confirm('¿Estás seguro de que quieres eliminar los certificados seleccionados?');" 
                    class="button">
                Eliminar Seleccionados
            </button>
        </div>
    </div>

            <div class="search-container">
                <input type="text" id="searchInput" class="search-input" placeholder="Buscar...">
            </div>

            <table id="certificadosTable">
                <tr>
                    <th>N° Certificado</th>
                    <th>Tipo ID</th>
                    <th>Documento</th>
                    <th>Nombre Completo</th>
                    <th>Nombre Curso</th>
                    <th>Descripción</th>
                    <th>Fecha Certificación</th>
                    <th>Fecha Emisión</th>
                    <th>Acción</th>
                    <th>Descargar</th>
                    <th><input type="checkbox" id="selectAllCheckbox"></th>
                </tr>
                <?php foreach ($certificados as $certificado): ?>
                    <tr class="certificadoRow">
                        <td><?php echo $certificado['numero_certificado']; ?></td>
                        <td><?php echo $certificado['tipo_identificacion']; ?></td>
                        <td><?php echo $certificado['numero_identificacion']; ?></td>
                        <td><?php echo $certificado['nombre_completo']; ?></td>
                        <td><?php echo $certificado['nombre_curso']; ?></td>
                        <td><?php echo $certificado['descripcion']; ?></td>
                        <td><?php echo $certificado['fecha_certificacion']; ?></td>
                        <td><?php echo $certificado['fecha_emision']; ?></td>
                        <td><a href="editar_diplomado.php?id=<?php echo $certificado['id']; ?>">Editar</a></td>
                        <td><a href="php/descargar_certificado_diplomado.php?id=<?php echo $certificado['id']; ?>">Descargar</a></td>
                        <td><input type="checkbox" name="certificados[]" value="<?php echo $certificado['id']; ?>"></td>
                    </tr>
                <?php endforeach; ?>
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
        const rows = Array.from(document.querySelectorAll('.certificadoRow'));
        
        document.getElementById('selectAllCheckbox').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('input[name="certificados[]"]');
            checkboxes.forEach(checkbox => checkbox.checked = this.checked);
        });

        // Filtro en tiempo real
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                row.style.display = Array.from(cells).some(cell => cell.textContent.toLowerCase().includes(filter)) ? '' : 'none';
            });
        });
    </script>

</body>
</html>

<?php
// Cerrar conexión
$conexion->close();
?>
