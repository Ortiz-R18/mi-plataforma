<?php
// Conectar a la base de datos
$conexion = new mysqli('localhost', 'root', '', 'certificados_db');

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$query = $_POST['query'];

// Consulta para filtrar en todas las columnas
$sql = "
    SELECT * FROM certificados_diplomados 
    WHERE numero_certificado LIKE '%$query%' 
    OR tipo_identificacion LIKE '%$query%' 
    OR numero_identificacion LIKE '%$query%' 
    OR nombre_completo LIKE '%$query%' 
    OR nombre_curso LIKE '%$query%' 
    OR descripcion LIKE '%$query%' 
    OR fecha_certificacion LIKE '%$query%' 
    OR fecha_emision LIKE '%$query%' 
    ORDER BY fecha_certificacion ASC
";

$result = $conexion->query($sql);

if ($result->num_rows > 0) {
    while($certificado = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$certificado['numero_certificado']}</td>
                <td>{$certificado['tipo_identificacion']}</td>
                <td>{$certificado['numero_identificacion']}</td>
                <td>{$certificado['nombre_completo']}</td>
                <td>{$certificado['nombre_curso']}</td>
                <td>{$certificado['descripcion']}</td>
                <td>{$certificado['fecha_certificacion']}</td>
                <td>{$certificado['fecha_emision']}</td>
                <td><a href='editar_certificado.php?id={$certificado['id']}'>Editar</a></td>
                <td><a href='php/descargar_certificado_diplomado.php?id={$certificado['id']}'>Descargar</a></td>
                <td><input type='checkbox' name='certificados[]' value='{$certificado['id']}'></td>
            </tr>";
    }
} else {
    echo "<tr><td colspan='11'>No hay certificados disponibles.</td></tr>";
}

$conexion->close();
?>
