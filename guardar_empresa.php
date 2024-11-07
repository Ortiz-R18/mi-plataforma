<?php
include 'conectar_db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger los datos del formulario
    $razon_social = mysqli_real_escape_string($conn, $_POST['razon_social']);
    $nit = mysqli_real_escape_string($conn, $_POST['nit']);
    $correo = mysqli_real_escape_string($conn, $_POST['correo']);
    $telefono = mysqli_real_escape_string($conn, $_POST['telefono']);
    $representante_legal = mysqli_real_escape_string($conn, $_POST['representante_legal']);
    $pagina_web = mysqli_real_escape_string($conn, $_POST['pagina_web']);
    $intranet = mysqli_real_escape_string($conn, $_POST['intranet']);
    $carpeta_sig = mysqli_real_escape_string($conn, $_POST['carpeta_sig']);

    // Rutas de subida
    $base_upload_dir = "uploads/";
    $camara_comercio_dir = $base_upload_dir . "camara/";
    $logo_dir = $base_upload_dir . "logos/";
    $rut_dir = $base_upload_dir . "rut/";
    $otros_dir = $base_upload_dir . "otros/";

    // Crear directorios si no existen
    foreach ([$camara_comercio_dir, $logo_dir, $rut_dir, $otros_dir] as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true); // Crear el directorio
        }
    }

    // Inicializar las variables de nombre
    $nombre_camara_comercio = '';
    $nombre_logo = ''; // Para el logo
    $nombre_rut = '';
    $nombre_otros_documentos = '';

    // Función para generar un nombre aleatorio de 8 dígitos
    function generarNombreAleatorio() {
        return random_int(10000000, 99999999);
    }

    // Subida de archivos
    if (isset($_FILES['camara_comercio']) && $_FILES['camara_comercio']['error'] == 0) {
        $extension = pathinfo($_FILES['camara_comercio']['name'], PATHINFO_EXTENSION);
        $nuevo_nombre_camara = generarNombreAleatorio() . '.' . $extension;
        move_uploaded_file($_FILES['camara_comercio']['tmp_name'], $camara_comercio_dir . $nuevo_nombre_camara);
        $nombre_camara_comercio = $nuevo_nombre_camara; // Solo guardar el nombre
    }

    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $extension = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
        $nuevo_nombre_logo = generarNombreAleatorio() . '.' . $extension;
        move_uploaded_file($_FILES['logo']['tmp_name'], $logo_dir . $nuevo_nombre_logo);
        $nombre_logo = $nuevo_nombre_logo; // Solo guardar el nombre
    }

    if (isset($_FILES['rut']) && $_FILES['rut']['error'] == 0) {
        $extension = pathinfo($_FILES['rut']['name'], PATHINFO_EXTENSION);
        $nuevo_nombre_rut = generarNombreAleatorio() . '.' . $extension;
        move_uploaded_file($_FILES['rut']['tmp_name'], $rut_dir . $nuevo_nombre_rut);
        $nombre_rut = $nuevo_nombre_rut; // Solo guardar el nombre
    }

    if (isset($_FILES['otros_documentos']) && $_FILES['otros_documentos']['error'] == 0) {
        $extension = pathinfo($_FILES['otros_documentos']['name'], PATHINFO_EXTENSION);
        $nuevo_nombre_otros = generarNombreAleatorio() . '.' . $extension;
        move_uploaded_file($_FILES['otros_documentos']['tmp_name'], $otros_dir . $nuevo_nombre_otros);
        $nombre_otros_documentos = $nuevo_nombre_otros; // Solo guardar el nombre
    }

    // Insertar los datos en la base de datos
    $sql = "INSERT INTO empresas (razon_social, nit, correo, telefono, representante_legal, 
        logo_path, camara_comercio_path, rut_path, otros_documentos_path, pagina_web, 
        intranet, carpeta_sig) VALUES (
        '$razon_social', '$nit', '$correo', '$telefono', '$representante_legal', 
        '$nombre_logo', '$nombre_camara_comercio', '$nombre_rut', '$nombre_otros_documentos', 
        '$pagina_web', '$intranet', '$carpeta_sig')";

    if (mysqli_query($conn, $sql)) {
        // Registro insertado correctamente, redirigir
        header("Location: consultar_empresas.php?message=Registro insertado correctamente.");
        exit(); // Importante: detener la ejecución después de redirigir
    } else {
        echo "Error: " . mysqli_error($conn);
    }

    mysqli_close($conn);
}
?>
