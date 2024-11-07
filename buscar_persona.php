<?php
// Conectar a la base de datos
$conexion = new mysqli('localhost', 'root', '', 'certificados_db');

// Comprobar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener el número de identificación desde la consulta
$numero_identificacion = isset($_GET['numero_identificacion']) ? $_GET['numero_identificacion'] : '';

// Inicializar respuesta
$response = [
    'existe' => false,
    'tipo_identificacion' => '',
    'nombre_completo' => ''
];

// Verificar si se proporcionó un número de identificación
if ($numero_identificacion) {
    $query = "SELECT tipo_identificacion, nombre_completo FROM personas WHERE numero_identificacion = '$numero_identificacion'";
    $result = $conexion->query($query);

    if ($result && $result->num_rows > 0) {
        $persona = $result->fetch_assoc();
        $response['existe'] = true;
        $response['tipo_identificacion'] = $persona['tipo_identificacion'];
        $response['nombre_completo'] = $persona['nombre_completo'];
    }
}

// Devolver respuesta en formato JSON
echo json_encode($response);

// Cerrar conexión
$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Persona</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <div class="container">
        <h1>Verificación de Persona</h1>
        <p>Este archivo devuelve información en formato JSON para la verificación de personas.</p>
    </div>
</body>
</html>
