<?php   
// Conectar a la base de datos
$conexion = new mysqli('localhost', 'root', '', 'certificados_db');

// Comprobar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Variable para almacenar el mensaje de confirmación
$mensaje_confirmacion = '';

// Obtener datos de las personas, incluyendo el correo electrónico
$query_personas = "SELECT numero_identificacion, tipo_identificacion, nombre_completo, correo_electronico FROM personas";
$result_personas = $conexion->query($query_personas);
$personas = []; // Arreglo para almacenar las personas

if ($result_personas->num_rows > 0) {
    while($persona = $result_personas->fetch_assoc()) {
        $personas[] = $persona; // Agregar cada persona al arreglo
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Personas</title>
    <link rel="stylesheet" href="css/estilos.css">
    <script>
        function filtrarResultados() {
            const input = document.getElementById('busqueda');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('tabla-personas');
            const tr = table.getElementsByTagName('tr');

            for (let i = 1; i < tr.length; i++) {
                const td = tr[i].getElementsByTagName('td');
                let mostrar = false;
                for (let j = 0; j < td.length - 1; j++) { // Excluyendo el checkbox
                    if (td[j].textContent.toLowerCase().indexOf(filter) > -1) {
                        mostrar = true;
                        break;
                    }
                }
                tr[i].style.display = mostrar ? '' : 'none';
            }
        }

        function seleccionarTodos(source) {
            const checkboxes = document.getElementsByName('personas[]');
            for (let i = 0; i < checkboxes.length; i++) {
                checkboxes[i].checked = source.checked;
            }
        }

        function validarEliminar() {
            const checkboxes = document.getElementsByName('personas[]');
            let seleccionados = false;
            for (let i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].checked) {
                    seleccionados = true;
                    break;
                }
            }
            
            if (seleccionados) {
                // Usar prompt para confirmar la eliminación
                const confirmacion = prompt('Escriba "ELIMINAR" para confirmar la eliminación de los registros seleccionados. Esta acción no se puede recuperar.');
                if (confirmacion === 'ELIMINAR') {
                    document.getElementById('form-personas').submit();
                } else {
                    alert('Debe escribir "ELIMINAR" para confirmar.');
                }
            } else {
                alert('Seleccione al menos una persona para eliminar.');
            }
        }
    </script>
</head>
<body>

    <!-- Incluyendo el menú de navegación -->
    <?php include 'header_nav.php'; ?>

    <div class="container">
        <h1>Consultar Personas</h1>

        <!-- Botones para volver al menú anterior, agregar persona y eliminar seleccionados -->
        <div class="botones">
            <div style="display: flex; justify-content: space-between; width: 100%;">
                <a href="index.php"><button>Volver al Inicio</button></a>
                <div>
                    <a href="personas.php" class="boton-agregar"><button>Agregar Persona</button></a>
                    <a href="#" onclick="validarEliminar()"><button>Eliminar Seleccionados</button></a>
                </div>
            </div>
        </div>

        <!-- Campo de búsqueda -->
        <input type="text" id="busqueda" class="busqueda" placeholder="Buscar..." onkeyup="filtrarResultados()">

        <form id="form-personas" method="post" action="eliminar_persona.php">
            <table id="tabla-personas" border="1">
                <tr>
                    <th>Tipo de Identificación</th>
                    <th>Número de Identificación</th>
                    <th>Nombre Completo</th>
                    <th>Correo Electrónico</th> <!-- Nuevo encabezado para el correo -->
                    <th>Acciones</th> <!-- Columna de acciones -->
                    <th><input type="checkbox" onclick="seleccionarTodos(this)">Seleccionar</th> <!-- Checkbox al final -->
                </tr>
                <?php foreach ($personas as $persona): ?>
                <tr>
                    <td><?php echo $persona['tipo_identificacion']; ?></td>
                    <td><?php echo $persona['numero_identificacion']; ?></td>
                    <td><?php echo $persona['nombre_completo']; ?></td>
                    <td><?php echo $persona['correo_electronico']; ?></td> <!-- Mostrar el correo electrónico -->
                    <td>
                        <a href="editar_persona.php?id=<?php echo $persona['numero_identificacion']; ?>">
                            <button type="button" class="boton boton-editar">Editar</button>
                        </a>
                        <a href="eliminar_persona.php?id=<?php echo $persona['numero_identificacion']; ?>" onclick="return confirm('¿Está seguro de que desea eliminar esta persona?');">
                            <button type="button" class="boton boton-eliminar">Eliminar</button>
                        </a>
                    </td>
                    <td class="checkbox-label">
                        <input type="checkbox" name="personas[]" value="<?php echo $persona['numero_identificacion']; ?>">
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </form>

        <?php if ($mensaje_confirmacion): ?>
            <p><?php echo $mensaje_confirmacion; ?></p>
        <?php endif; ?>
    </div>

</body>
</html>
