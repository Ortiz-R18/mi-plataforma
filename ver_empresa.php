<?php 
session_start(); // Iniciar la sesión para usar mensajes
include 'conectar_db.php';

// Verificar si se ha pasado el ID
if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']); // Escapar el ID
    $sql = "SELECT * FROM empresas WHERE id = '$id'";
    $result = mysqli_query($conn, $sql);
    
    // Verificar si se encontró la empresa
    if ($result && mysqli_num_rows($result) > 0) {
        $empresa = mysqli_fetch_assoc($result);
    } else {
        $empresa = null; // Asignar null si no se encontró
    }
} else {
    $empresa = null; // Asignar null si no se pasó el ID
}

// Manejo de actualización del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $razon_social = mysqli_real_escape_string($conn, $_POST['razon_social']);
    $nit = mysqli_real_escape_string($conn, $_POST['nit']);
    $correo = mysqli_real_escape_string($conn, $_POST['correo']);
    $telefono = mysqli_real_escape_string($conn, $_POST['telefono']);
    $representante_legal = mysqli_real_escape_string($conn, $_POST['representante_legal']);
    
    // Obtener y escapar los datos de los enlaces
    $pagina_web = mysqli_real_escape_string($conn, $_POST['pagina_web']);
    $intranet = mysqli_real_escape_string($conn, $_POST['intranet']);
    $carpeta_sig = mysqli_real_escape_string($conn, $_POST['carpeta_sig']);

    // Actualizar datos de la empresa, incluyendo los enlaces
    $sql_update = "UPDATE empresas SET 
        razon_social = '$razon_social', 
        nit = '$nit', 
        correo = '$correo', 
        telefono = '$telefono', 
        representante_legal = '$representante_legal',
        pagina_web = '$pagina_web', 
        intranet = '$intranet', 
        carpeta_sig = '$carpeta_sig' 
        WHERE id = '$id'";

if (mysqli_query($conn, $sql_update)) {
    $_SESSION['message'] = "Los cambios se han guardado correctamente.";
} else {
    $_SESSION['message'] = "Error al actualizar la empresa: " . mysqli_error($conn);
}


    // Manejo de carga de logo
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $logo_path = 'uploads/logos/' . basename($_FILES['logo']['name']);

        // Mueve el archivo subido a la carpeta de destino
        if (move_uploaded_file($_FILES['logo']['tmp_name'], $logo_path)) {
            // Actualizar la ruta del logo en la base de datos
            $sql_update_logo = "UPDATE empresas SET logo_path = '" . mysqli_real_escape_string($conn, basename($_FILES['logo']['name'])) . "' WHERE id = '$id'";
            mysqli_query($conn, $sql_update_logo);
            $_SESSION['message'] = "Los cambios se han guardado correctamente.";
        } else {
            $_SESSION['message'] = "Error al mover el archivo.";
        }
    }

    // Manejo de carga de documentos
    $documentos = [
        'camara_comercio' => 'camara/',
        'rut' => 'rut/',
        'otros_documentos' => 'otros/'
    ];

    foreach ($documentos as $key => $folder) {
        if (isset($_FILES[$key]) && $_FILES[$key]['error'] == 0) {
            // Generar un número aleatorio de 8 dígitos
            $random_number = mt_rand(10000000, 99999999);
            // Obtener la extensión del archivo
            $file_extension = pathinfo($_FILES[$key]['name'], PATHINFO_EXTENSION);
            // Definir un nuevo nombre para el archivo
            $new_file_name = $random_number . '.' . $file_extension;
            // Define la ruta del documento
            $doc_path = "../mi-plataforma/uploads/$folder" . $new_file_name;
    
            // Mueve el archivo subido a la carpeta de destino
            if (move_uploaded_file($_FILES[$key]['tmp_name'], $doc_path)) {
                // Actualizar la ruta del documento en la base de datos
                $sql_update_doc = "UPDATE empresas SET {$key}_path = '" . mysqli_real_escape_string($conn, $new_file_name) . "' WHERE id = '$id'";
                mysqli_query($conn, $sql_update_doc);
                $_SESSION['message'] = "Los cambios se han guardado correctamente.";
            } else {
                $_SESSION['message'] = "Error al mover el archivo $key.";
            }
        }
    }
    

    // Redireccionar a la misma página para ver los cambios
    header("Location: ver_empresa.php?id=$id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalles de la Empresa</title>
    <link rel="stylesheet" href="../css/estilos.css"> <!-- Ruta al archivo CSS -->
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 4px; /* Ajustar la altura de las filas */
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .logo-container {
            text-align: center;
            padding: 10px;
            width: 300px; /* Ancho de la columna del logo */
        }
        img {
            max-width: 100%;
            height: auto;
        }
        input[type="text"], input[type="email"], input[type="file"] {
            width: 96.5%;
            padding: 8px;
            margin-top: 4px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .document-table {
            margin-top: 1px;
            border-collapse: collapse;
        }
        .document-table th, .document-table td {
            border: none;
        }
        .document-table td {
            padding: 5px 0;
        }
    </style>
</head>
<body>
    <?php include 'header_nav.php'; ?> <!-- Incluyendo el menú de navegación -->

    <div class="container">
        <h2>Detalles de la Empresa</h2>
        <button onclick="window.location.href='consultar_empresas.php'" class="btn">Volver al menú anterior</button> <!-- Botón de volver -->

        <?php if (isset($_SESSION['message'])): ?>
            <script>alert("<?php echo $_SESSION['message']; ?>");</script>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <?php if ($empresa): ?>
            <form method="POST" enctype="multipart/form-data">
                <table>
                    <tr>
                        <th>Razón Social</th>
                        <td><input type="text" name="razon_social" value="<?php echo htmlspecialchars($empresa['razon_social']); ?>"></td>
                        <td rowspan="6" class="logo-container">
                            <?php if (!empty($empresa['logo_path'])): ?>
                                <img src="uploads/logos/<?php echo htmlspecialchars($empresa['logo_path']); ?>" alt="Logo">
                            <?php else: ?>
                                No disponible
                            <?php endif; ?>
                            <h4>Cambiar Logo (opcional)</h4>
                            <input type="file" name="logo" class="file-input">
                        </td>
                    </tr>
                    <tr>
                        <th>NIT</th>
                        <td><input type="text" name="nit" value="<?php echo htmlspecialchars($empresa['nit']); ?>"></td>
                    </tr>
                    <tr>
                        <th>Correo</th>
                        <td><input type="email" name="correo" value="<?php echo htmlspecialchars($empresa['correo']); ?>"></td>
                    </tr>
                    <tr>
                        <th>Teléfono</th>
                        <td><input type="text" name="telefono" value="<?php echo htmlspecialchars($empresa['telefono']); ?>"></td>
                    </tr>
                    <tr>
                        <th>Representante Legal</th>
                        <td><input type="text" name="representante_legal" value="<?php echo htmlspecialchars($empresa['representante_legal']); ?>"></td>
                    </tr>
                </table>

                <h3>Enlaces</h3>
                <table class="document-table">
                    <tr>
                        <th>Página Web</th>
                        <td><input type="text" name="pagina_web" value="<?php echo htmlspecialchars($empresa['pagina_web']); ?>"></td>
                    </tr>
                    <tr>
                        <th>Intranet</th>
                        <td><input type="text" name="intranet" value="<?php echo htmlspecialchars($empresa['intranet']); ?>"></td>
                    </tr>
                    <tr>
                        <th>Carpeta SIG</th>
                        <td><input type="text" name="carpeta_sig" value="<?php echo htmlspecialchars($empresa['carpeta_sig']); ?>"></td>
                    </tr>
                </table>

                <h3>Documentos</h3>
                <table class="document-table">
                    <tr>
                        <th>Documento</th>
                        <th>Enlace</th>
                        <th>Reemplazar</th>
                    </tr>
                    <tr>
                        <td>ㅤCámara de Comercio</td>
                        <td>
                            <?php if (!empty($empresa['camara_comercio_path'])): ?>
                                <a href="uploads/camara/<?php echo htmlspecialchars($empresa['camara_comercio_path']); ?>" target="_blank">Ver</a>
                                <a href="uploads/camara/<?php echo htmlspecialchars($empresa['camara_comercio_path']); ?>" download>Descargar</a>
                            <?php else: ?>
                                No disponible
                            <?php endif; ?>
                        </td>
                        <td>
                            <input type="file" name="camara_comercio" class="file-input">
                        </td>
                    </tr>
                    <tr>
                        <td>ㅤRUT</td>
                        <td>
                            <?php if (!empty($empresa['rut_path'])): ?>
                                <a href="uploads/rut/<?php echo htmlspecialchars($empresa['rut_path']); ?>" target="_blank">Ver</a>
                                <a href="uploads/rut/<?php echo htmlspecialchars($empresa['rut_path']); ?>" download>Descargar</a>
                            <?php else: ?>
                                No disponible
                            <?php endif; ?>
                        </td>
                        <td>
                            <input type="file" name="rut" class="file-input">
                        </td>
                    </tr>
                    <tr>
                        <td>ㅤFormato registro clientes</td>
                        <td>
                            <?php if (!empty($empresa['otros_documentos_path'])): ?>
                                <a href="uploads/otros/<?php echo htmlspecialchars($empresa['otros_documentos_path']); ?>" target="_blank">Ver</a>
                                <a href="uploads/otros/<?php echo htmlspecialchars($empresa['otros_documentos_path']); ?>" download>Descargar</a>
                            <?php else: ?>
                                No disponible
                            <?php endif; ?>
                        </td>
                        <td>
                            <input type="file" name="otros_documentos" class="file-input">
                        </td>
                    </tr>
                </table>

                <button type="submit" class="edit-button">Guardar Cambios</button>
            </form>
        <?php else: ?>
            <p>No se encontraron detalles para esta empresa.</p>
        <?php endif; ?>
    </div>
</body>
</html>
