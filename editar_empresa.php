// editar_empresa.php
include 'conectar_db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $razon_social = $_POST['razon_social'];
    $nit = $_POST['nit'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];
    $representante_legal = $_POST['representante_legal'];

    // Actualizar los datos de la empresa en la base de datos
    $update_sql = "UPDATE empresas SET razon_social='$razon_social', nit='$nit', correo='$correo', telefono='$telefono', representante_legal='$representante_legal' WHERE id=$id";
    mysqli_query($conn, $update_sql);
    header('Location: consultar_empresas.php?msg=Actualización exitosa');
    exit();
}

$id = $_GET['id'];
$sql = "SELECT * FROM empresas WHERE id = $id";
$result = mysqli_query($conn, $sql);
$empresa = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Empresa</title>
</head>
<body>
    <h2>Editar Empresa</h2>
    <form method="post" action="editar_empresa.php">
        <input type="hidden" name="id" value="<?php echo $empresa['id']; ?>">
        Razón Social: <input type="text" name="razon_social" value="<?php echo $empresa['razon_social']; ?>" required><br>
        NIT: <input type="text" name="nit" value="<?php echo $empresa['nit']; ?>" required><br>
        Correo: <input type="email" name="correo" value="<?php echo $empresa['correo']; ?>" required><br>
        Teléfono: <input type="text" name="telefono" value="<?php echo $empresa['telefono']; ?>" required><br>
        Representante Legal: <input type="text" name="representante_legal" value="<?php echo $empresa['representante_legal']; ?>" required><br>
        <button type="submit">Actualizar</button>
    </form>
    <a href="consultar_empresas.php">Volver al listado</a>
</body>
</html>
