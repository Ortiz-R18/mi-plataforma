<?php
// Conectar a la base de datos
$conexion = new mysqli('localhost', 'root', '', 'certificados_db');

// Comprobar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

if (isset($_GET['id'])) {
    $id_certificado = intval($_GET['id']);

    // Obtener la ruta del certificado
    $stmt = $conexion->prepare("SELECT ruta_certificado FROM certificados WHERE id = ?");
    $stmt->bind_param("i", $id_certificado);
    $stmt->execute();
    $stmt->bind_result($ruta_certificado);
    $stmt->fetch();
    $stmt->close();

    // Debug: Imprimir el ID y la ruta recuperada
    echo "ID: $id_certificado, Ruta: $ruta_certificado<br>"; // Para depuración

    // Verificar si la ruta del certificado no está vacía
    if (!empty($ruta_certificado)) {
        // Crear la ruta completa al archivo PDF
        $ruta_completa = __DIR__ . '/../mi-plataforma/' . $ruta_certificado;

        // Verificar si el archivo existe
        if (file_exists($ruta_completa)) {
            // Configurar las cabeceras para la descarga
            header('Content-Description: File Transfer');
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . basename($ruta_completa) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($ruta_completa));

            // Limpiar el buffer de salida
            ob_clean();
            flush();

            // Leer el archivo y enviarlo al navegador
            readfile($ruta_completa);
            exit;
        } else {
            echo "El certificado no existe.";
        }
    } else {
        echo "No se encontró la ruta del certificado.";
    }
} else {
    echo "No se ha especificado un certificado.";
}

// Cerrar conexión
$conexion->close();
?>