<?php   
// Conectar a la base de datos
$conexion = new mysqli('localhost', 'root', '', 'certificados_db');

// Comprobar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener datos de las personas
$query_personas = "SELECT numero_identificacion, tipo_identificacion, nombre_completo FROM personas";
$result_personas = $conexion->query($query_personas);

// Obtener datos de los diplomados
$query_diplomados = "SELECT id, nombre_diplomado, descripcion FROM diplomados"; // Cambia 'nombre_curso' a 'nombre_diplomado'
$result_diplomados = $conexion->query($query_diplomados);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generar Certificados de Diplomados</title>
    <link rel="stylesheet" href="css/estilos.css"> <!-- Enlace a estilos.css -->
    <style>
        /* Estilos para el contenedor de datos de la persona */
        .data-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px; /* Espacio inferior para separación */
        }
        .data-container label {
            flex-basis: 30%; /* Ancho de las etiquetas */
        }
        .data-container input {
            flex-basis: 32%; /* Ancho de los campos de texto aumentado */
        }
        /* Ajustar el ancho de los inputs */
        input[type="text"],
        input[type="number"],
        input[type="date"],
        select {
            width: 100%; /* Campos al ancho completo */
            padding: 8px; /* Espaciado interno */
            box-sizing: border-box; /* Incluye padding en el ancho total */
        }
        /* Estilo para el contenedor de detalles del certificado */
        .detalles-container {
            display: flex; /* Flexbox para alinear campos */
            flex-wrap: wrap; /* Permitir que los elementos se envuelvan */
            margin-bottom: 20px; /* Margen inferior */
        }
        /* Estilo para los checkboxes de los diplomados */
        .curso-checkbox {
            display: flex; /* Usar flexbox para alinear checkbox y label */
            align-items: center; /* Alinear verticalmente */
            margin-bottom: 10px; /* Espacio entre los checkboxes */
        }
        /* Estilo para el contenedor de diplomados y descripciones */
        .diplomados-container {
            display: grid; /* Usar grid para disposición en columnas */
            grid-template-columns: 1fr 1fr; /* Dos columnas de igual tamaño */
            gap: 10px; /* Espacio entre columnas */
        }
        /* Ocultar campos de identificación */
        .hidden {
            display: none; /* Ocultar elementos */
        }
        /* Estilo para los cuadros de descripción */
        .descripcion-input {
            width: 100%; /* Ancho completo */
            padding: 8px; /* Espaciado interno */
            margin-left: 10px; /* Espacio a la izquierda del cuadro de texto */
            box-sizing: border-box; /* Incluye padding en el ancho total */
        }
    </style>
    <script>
        function seleccionarCliente() {
            var select = document.getElementById('seleccionar_cliente');
            var selectedOption = select.options[select.selectedIndex];

            // Obtener los datos de la opción seleccionada
            var tipoIdentificacion = selectedOption.getAttribute('data-tipo');
            var numeroIdentificacion = selectedOption.value;
            var nombreCompleto = selectedOption.getAttribute('data-nombre');

            // Rellenar los campos con los datos correspondientes
            document.getElementById('tipo_identificacion').value = tipoIdentificacion;
            document.getElementById('numero_identificacion').value = numeroIdentificacion;
            document.getElementById('nombre_completo').value = nombreCompleto;

            // Mostrar los campos de identificación si están ocultos
            document.querySelector('.data-container').classList.remove('hidden');
        }

        function mostrarDescripcion(id) {
            const descripcion = document.getElementById('descripcion_' + id);
            descripcion.classList.toggle('hidden');
        }
    </script>
</head>
<body>

    <!-- Incluir header_nav.php -->
    <?php include 'header_nav.php'; ?>

    <!-- Contenedor principal -->
    <div class="container">
        <h1>Generar Certificados de Diplomados</h1>

        <div class="button-container">
            <button type="button" onclick="window.location.href='ver_diplomados.php'" class="back-button">Volver al Menú Anterior</button>
        </div>

        <form method="POST" action="php/generar_certificado_diplomado.php">
            <h2>Datos de la Persona</h2>
            <label for="seleccionar_cliente">Seleccionar cliente:</label>
            <select id="seleccionar_cliente" name="seleccionar_cliente" required onchange="seleccionarCliente()">
                <option value="">Seleccione un cliente</option>
                <?php if ($result_personas->num_rows > 0): ?>
                    <?php while($persona = $result_personas->fetch_assoc()): ?>
                        <option value="<?php echo $persona['numero_identificacion']; ?>" 
                                data-tipo="<?php echo $persona['tipo_identificacion']; ?>" 
                                data-nombre="<?php echo $persona['nombre_completo']; ?>">
                            <?php echo $persona['tipo_identificacion'] . ' - ' . $persona['numero_identificacion'] . ' - ' . $persona['nombre_completo']; ?>
                        </option>
                    <?php endwhile; ?>
                <?php else: ?>
                    <option value="">No hay personas registradas.</option>
                <?php endif; ?>
            </select>

            <div class="data-container hidden">
                <div>
                    <label for="tipo_identificacion">Tipo de Identificación:</label>
                    <input type="text" id="tipo_identificacion" name="tipo_identificacion" required readonly>
                </div>
                <div>
                    <label for="numero_identificacion">Número de Identificación:</label>
                    <input type="text" id="numero_identificacion" name="numero_identificacion" required readonly>
                </div>
                <div>
                    <label for="nombre_completo">Nombre Completo:</label>
                    <input type="text" id="nombre_completo" name="nombre_completo" required readonly>
                </div>
            </div>

            <h2>Diplomados</h2>
            <div class="diplomados-container">
                <?php if ($result_diplomados->num_rows > 0): ?>
                    <?php while($diplomado = $result_diplomados->fetch_assoc()): ?>
                        <div class="curso-checkbox">
                            <input type="checkbox" id="diplomado<?php echo $diplomado['id']; ?>" name="diplomados[]" value="<?php echo $diplomado['id']; ?>" onchange="mostrarDescripcion(<?php echo $diplomado['id']; ?>)">
                            <label for="diplomado<?php echo $diplomado['id']; ?>"><?php echo $diplomado['nombre_diplomado']; ?></label>
                        </div>
                        <div>
                            <input type="text" class="descripcion-input hidden" id="descripcion_<?php echo $diplomado['id']; ?>" value="<?php echo $diplomado['descripcion']; ?>" readonly>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No hay diplomados registrados.</p>
                <?php endif; ?>
            </div>

            <h2>Fechas</h2>
            <div class="detalles-container">
                <div>
                    <label for="fecha_certificacion">Fecha de Certificación:</label>
                    <input type="date" id="fecha_certificacion" name="fecha_certificacion" required>
                </div>
                <div>
                    <label for="fecha_emision">Fecha de Emisión:</label>
                    <input type="date" id="fecha_emision" name="fecha_emision" required>
                </div>
            </div>

            <div class="button-container">
                <button type="submit" class="submit-button">Generar Certificado</button>
           </div>
        </form>
    </div>
</body>
</html>
