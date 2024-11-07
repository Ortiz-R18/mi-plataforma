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

// Obtener datos de los cursos
$query_cursos = "SELECT id, nombre_curso FROM cursos";
$result_cursos = $conexion->query($query_cursos);

// Obtener datos de las empresas
$query_empresas = "SELECT razon_social, logo_path FROM empresas";
$result_empresas = $conexion->query($query_empresas);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generar Certificados</title>
    <link rel="stylesheet" href="css/estilos.css">
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
        .detalles-container > div {
            flex-basis: 30%; /* Cada campo ocupa el 30% del ancho */
            margin-right: 20px; /* Espacio a la derecha entre campos */
        }
        .detalles-container > div:last-child {
            margin-right: 0; /* Eliminar margen derecho en el último campo */
        }
        /* Estilo para los checkboxes de los cursos */
        .curso-checkbox {
            display: flex; /* Usar flexbox para alinear checkbox y label */
            align-items: center; /* Alinear verticalmente */
            margin-bottom: 10px; /* Espacio entre los checkboxes */
        }
        /* Ocultar campos de identificación */
        .hidden {
            display: none !important; /* Ocultar elementos */
        }
        .empresa-logo {
            margin-top: 10px;
            max-width: 200px; /* Ajustar el tamaño del logo */
        }
    </style>
    <script>
        function seleccionarCliente() {
            var select = document.getElementById('seleccionar_cliente');
            var selectedOption = select.options[select.selectedIndex];
            document.getElementById('tipo_identificacion').value = selectedOption.getAttribute('data-tipo');
            document.getElementById('numero_identificacion').value = selectedOption.value;
            document.getElementById('nombre_completo').value = selectedOption.getAttribute('data-nombre');
            document.querySelector('.data-container').classList('hidden');
        }

        function mostrarLogo() {
    var select = document.getElementById("seleccionar_empresa");
    var logoPath = select.options[select.selectedIndex].dataset.logo;
    document.getElementById("razon_social").value = select.value;
    document.getElementById("logo_path").value = logoPath;

    // Ocultar el logo siempre
    document.getElementById("logo_container").classList.add("hidden");
}
    </script>
</head>
<body>
    <?php include 'header_nav.php'; ?>
    <div class="container">
        <h1>Generar Certificados</h1>
        <div class="button-container">
            <button type="button" onclick="window.location.href='ver_certificados.php'" class="back-button">Volver al Menú Anterior</button>
        </div>

        <form method="POST" action="php/generar_certificado.php">
            <!-- Datos de la Persona -->
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
                    <input type="text" id="tipo_identificacion" name="tipo_identificacion" readonly>
                </div>
                <div>
                    <label for="numero_identificacion">Número de Identificación:</label>
                    <input type="text" id="numero_identificacion" name="numero_identificacion" readonly>
                </div>
                <div>
                    <label for="nombre_completo">Nombre Completo:</label>
                    <input type="text" id="nombre_completo" name="nombre_completo" readonly>
                </div>
            </div>

            <!-- Selección de Empresa -->
            <h2>Seleccionar Empresa</h2>
            <label for="seleccionar_empresa">Seleccionar empresa:</label>
            <select id="seleccionar_empresa" name="seleccionar_empresa" onchange="mostrarLogo()">
                <option value="">No aplica</option>
                <?php if ($result_empresas->num_rows > 0): ?>
                    <?php while($empresa = $result_empresas->fetch_assoc()): ?>
                        <option value="<?php echo $empresa['razon_social']; ?>" data-logo="<?php echo $empresa['logo_path']; ?>">
                            <?php echo $empresa['razon_social']; ?>
                        </option>
                    <?php endwhile; ?>
                <?php else: ?>
                    <option value="">No hay empresas registradas.</option>
                <?php endif; ?>
            </select>
            <div id="logo_container" class="empresa-logo"></div>
            <input type="hidden" id="razon_social" name="razon_social">
            <input type="hidden" id="logo_path" name="logo_path">

            <!-- Selección de Cursos -->
            <h2>Seleccionar Cursos</h2>
            <?php if ($result_cursos->num_rows > 0): ?>
                <?php while($curso = $result_cursos->fetch_assoc()): ?>
                    <div class="curso-checkbox">
                        <input type="checkbox" name="cursos[]" value="<?php echo $curso['id']; ?>">
                        <label><?php echo $curso['nombre_curso']; ?></label>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No hay cursos disponibles.</p>
            <?php endif; ?>

            <!-- Detalles del Certificado -->
            <h2>Detalles del Certificado</h2>
            <div class="detalles-container">
                <div>
                    <label for="tipo_formacion">Tipo de Formación:</label>
                    <select name="tipo_formacion" id="tipo_formacion" required>
                        <option value="">Seleccione el tipo de formación</option>
                        <option value="presencial">Presencial</option>
                        <option value="virtual">Virtual</option>
                    </select>
                </div>
                <div>
                    <label for="numero_horas">Número de Horas:</label>
                    <input type="number" id="numero_horas" name="numero_horas" required>
                </div>
                <div>
                    <label for="fecha_certificacion">Fecha de Certificación:</label>
                    <input type="date" id="fecha_certificacion" name="fecha_certificacion" required>
                </div>
                <div>
                    <label for="fecha_emision">Fecha de Emisión:</label>
                    <input type="date" id="fecha_emision" name="fecha_emision" required>
                </div>
                <div>
                    <label for="formador">Formador:</label>
                    <select id="formador" name="formador" required>
                        <option value="">Selecciona una opción</option>
                        <option value="Marisol Fajardo Pedraza">Marisol Fajardo Pedraza</option>
                        <option value="Ervin Ricardo Barajas">Ervin Ricardo Barajas</option>
                        <option value="Oscar Bermúdez">Oscar Bermúdez</option>
                    </select>
                </div>
            </div>

            <!-- Botón para enviar el formulario -->
            <div class="button-container">
                <button type="submit" class="submit-button">Generar Certificado</button>
            </div>
        </form>
    </div>
</body>
</html>
