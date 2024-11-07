<?php
// Incluir el archivo de conexión a la base de datos
include 'conectar_db.php';

// Verificar si se ha recibido el ID
if (isset($_GET['id'])) {
    $id = (int)$_GET['id']; // Asegurarse de que sea un entero

    // Consulta para eliminar la empresa
    $sql_delete = "DELETE FROM empresas WHERE id = ?";
    $stmt = $conn->prepare($sql_delete);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Redirigir a consultar_empresas.php después de eliminar
        header("Location: consultar_empresas.php?message=Empresa eliminada exitosamente.");
        exit();
    } else {
        echo "Error al eliminar la empresa: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>
