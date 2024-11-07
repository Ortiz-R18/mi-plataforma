<?php
// Incluir el archivo de conexión a la base de datos
include 'conectar_db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ruta donde se guardarán los archivos
    $targetDir = "../uploads/documentos/"; // Asegúrate de que esta carpeta exista y tenga permisos de escritura

    // Manejo de carga de logo
    $nombre_logo = null;
    if (isset($_FILES["logo"]) && $_FILES["logo"]["error"] == 0) {
        $extension = pathinfo($_FILES["logo"]["name"], PATHINFO_EXTENSION);
        $nuevo_nombre_logo = random_int(10000000, 99999999) . '.' . $extension;
        $targetFilePath = "../uploads/logos/" . $nuevo_nombre_logo; // Ajusta la carpeta

        // Mueve el archivo a la carpeta deseada
        if (move_uploaded_file($_FILES["logo"]["tmp_name"], $targetFilePath)) {
            echo "El logo ha sido subido con éxito.";
            $nombre_logo = $nuevo_nombre_logo; // Guardamos solo el nombre para la base de datos
        } else {
            echo "Lo siento, hubo un error al subir su logo.";
        }
    }

    // Función para manejar la carga de documentos
    function manejarDocumento($fileKey, $targetDir) {
        if (isset($_FILES[$fileKey]) && $_FILES[$fileKey]["error"] == 0) {
            $extension = pathinfo($_FILES[$fileKey]["name"], PATHINFO_EXTENSION);
            $nuevo_nombre = random_int(10000000, 99999999) . '.' . $extension;
            $targetFilePath = $targetDir . $nuevo_nombre;

            // Mueve el archivo a la carpeta deseada
            if (move_uploaded_file($_FILES[$fileKey]["tmp_name"], $targetFilePath)) {
                echo ucfirst($fileKey) . " ha sido subido con éxito: $nuevo_nombre.";
                return $nuevo_nombre; // Retorna el nombre aleatorio
            } else {
                echo "Lo siento, hubo un error al subir su archivo: $fileKey.";
                return null;
            }
        } else {
            echo "No se subió ningún archivo o hubo un error con: $fileKey.";
            return null;
        }
    }

    // Manejar carga de documentos
    $nombre_camara_comercio = manejarDocumento("camara_comercio", $targetDir);
    $nombre_rut = manejarDocumento("rut", $targetDir);
    $nombre_otros_documentos = manejarDocumento("otros_documentos", $targetDir);

    // Aquí puedes agregar el código para guardar los datos en la base de datos
    $sql = "INSERT INTO empresas (logo, camara_comercio, rut, otros_documentos, razon_social, nit, correo, telefono, representante_legal, pagina_web, intranet, carpeta_sig) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $nombre_logo,
        $nombre_camara_comercio,
        $nombre_rut,
        $nombre_otros_documentos,
        $_POST['razon_social'],
        $_POST['nit'],
        $_POST['correo'],
        $_POST['telefono'],
        $_POST['representante_legal'],
        $_POST['pagina_web'],
        $_POST['intranet'],
        $_POST['carpeta_sig']
    ]);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Empresa</title>
    <link rel="stylesheet" href="../css/estilos.css"> <!-- Ruta actualizada -->
    <style>
        .container {
            max-width: 800px;
            margin: auto;
        }
        .form-header {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        .preview-logo {
            width: 100px;
            height: 100px;
            border: 1px solid #ddd;
            margin-right: 20px;
            margin-left: 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .preview-logo img {
            max-width: 150%;
            max-height: 150%;
        }
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .form-grid label {
            display: block;
        }
        .button-container {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <header>
        <?php include 'header_nav.php'; ?>
    </header>
    
    <div class="container">
        <div class="form-header">
            <h2>Registrar Empresa</h2>
            <button onclick="window.location.href='consultar_empresas.php'" class="btn">Volver al Inicio</button>
        </div>

        <form action="guardar_empresa.php" method="POST" enctype="multipart/form-data">
            <div class="form-grid">
                <div>
                    <label for="logo">Logo de la Empresa:</label>
                    <input type="file" name="logo" accept="image/*" onchange="previewLogo(event)">
                </div>
            
                <div>
                    <label for="razon_social">Razón Social:</label>
                    <input type="text" name="razon_social" required>
                </div>

                <div>
                    <label for="nit">NIT o ID:</label>
                    <input type="text" name="nit" required>
                </div>

                <div>
                    <label for="correo">Correo:</label>
                    <input type="email" name="correo">
                </div>

                <div>
                    <label for="telefono">Teléfono:</label>
                    <input type="text" name="telefono">
                </div>

                <div>
                    <label for="representante_legal">Representante Legal:</label>
                    <input type="text" name="representante_legal">
                </div>

                <div>
                    <label for="pagina_web">Página Web:</label>
                    <input type="URL" name="pagina_web">
                </div>

                <div>
                    <label for="intranet">Intranet:</label>
                    <input type="URL" name="intranet">
                </div>

                <div>
                    <label for="carpeta_sig">Carpeta SIG:</label>
                    <input type="URL" name="carpeta_sig">
                </div>

                <div>
                    <label for="camara_comercio">Cámara de Comercio:</label>
                    <input type="file" name="camara_comercio" accept="application/pdf">
                </div>

                <div>
                    <label for="rut">RUT:</label>
                    <input type="file" name="rut" accept="application/pdf">
                </div>

                <div>
                    <label for="otros_documentos">Registro cliente:</label>
                    <input type="file" name="otros_documentos" accept="application/pdf">
                </div>
            </div>

            <div class="button-container">
                <button type="submit">Registrar Empresa</button>
            </div>
        </form>
    </div>

    <script>
        // Función para previsualizar el logo
        function previewLogo(event) {
            const file = event.target.files[0];
            const previewImage = document.getElementById('previewImage');

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                previewImage.src = ''; // Ruta actualizada
            }
        }
    </script>
</body>
</html>
