<?php
// Conectar a la base de datos
$conexion = new mysqli('localhost', 'root', '', 'certificados_db');

// Comprobar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Función para eliminar el curso y sus certificados
function eliminarCurso($conexion, $id_curso) {
    // Preparar declaraciones para evitar inyecciones SQL
    $stmt_certificados = $conexion->prepare("DELETE FROM certificados WHERE curso_id = ?");
    $stmt_certificados->bind_param("i", $id_curso);
    $stmt_certificados->execute();
    
    $stmt_curso = $conexion->prepare("DELETE FROM cursos WHERE id = ?");
    $stmt_curso->bind_param("i", $id_curso);
    $stmt_curso->execute();

    // Comprobar si la eliminación fue exitosa
    if ($stmt_curso->affected_rows > 0) {
        return true; // Curso eliminado
    }
    return false; // No se eliminó
}

// Verificar que se ha pasado un ID
if (isset($_GET['id'])) {
    $id_curso = intval($_GET['id']);
    if (eliminarCurso($conexion, $id_curso)) {
        echo "<script>alert('Curso y certificados eliminados correctamente'); window.location.href='cursos.php';</script>";
    } else {
        echo "<script>alert('Error al eliminar el curso. Puede que no exista.'); window.location.href='cursos.php';</script>";
    }
} else {
    echo "<script>alert('ID de curso no especificado.'); window.location.href='cursos.php';</script>";
}

// Cerrar conexión
$conexion->close();
?>
