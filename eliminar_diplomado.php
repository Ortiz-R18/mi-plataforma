<?php
// Conectar a la base de datos
$conexion = new mysqli('localhost', 'root', '', 'certificados_db');

// Comprobar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Comprobar si se han enviado certificados a eliminar
if (isset($_POST['eliminar_seleccionados']) && isset($_POST['certificados'])) {
    $certificados_a_eliminar = $_POST['certificados'];

    // Iniciar transacción
    $conexion->begin_transaction();

    try {
        // Preparar la consulta para eliminar
        $stmt = $conexion->prepare("DELETE FROM certificados_diplomados WHERE id = ?");
        
        // Recorrer los certificados seleccionados y eliminarlos
        foreach ($certificados_a_eliminar as $certificado_id) {
            $stmt->bind_param("i", $certificado_id); // "i" significa que el parámetro es un entero
            $stmt->execute();
        }

        // Confirmar transacción
        $conexion->commit();

        // Redireccionar con mensaje de éxito
        header("Location: ver_diplomados.php?mensaje=Certificados eliminados correctamente.");
        exit();
    } catch (Exception $e) {
        // En caso de error, deshacer la transacción
        $conexion->rollback();
        header("Location: ver_diplomados.php?mensaje=Error al eliminar certificados: " . $e->getMessage());
        exit();
    } finally {
        // Cerrar la declaración
        $stmt->close();
    }
} else {
    // Redireccionar si no se seleccionaron certificados
    header("Location: ver_diplomados.php?mensaje=No se seleccionaron certificados para eliminar.");
    exit();
}

// Cerrar conexión
$conexion->close();
?>
