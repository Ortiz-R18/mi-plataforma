<?php
// Conectar a la base de datos
$conexion = new mysqli('localhost', 'root', '', 'certificados_db');

// Comprobar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener el diplomado a editar
$diplomado = null;
if (isset($_GET['id'])) {
    $id_diplomado = intval($_GET['id']);
    $query_diplomado = "SELECT * FROM certificados_diplomados WHERE id = $id_diplomado";
    $result_diplomado = $conexion->query($query_diplomado);
    $diplomado = $result_diplomado->fetch_assoc();
}

// Actualizar diplomado si se envía el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo_identificacion = mysqli_real_escape_string($conexion, $_POST['tipo_identificacion']);
    $numero_identificacion = mysqli_real_escape_string($conexion, $_POST['numero_identificacion']);
    $nombre_completo = mysqli_real_escape_string($conexion, $_POST['nombre_completo']);
    $nombre_curso = mysqli_real_escape_string($conexion, $_POST['nombre_curso']);
    $descripcion = mysqli_real_escape_string($conexion, $_POST['descripcion']);
    $fecha_certificacion = mysqli_real_escape_string($conexion, $_POST['fecha_certificacion']);
    $fecha_emision = mysqli_real_escape_string($conexion, $_POST['fecha_emision']);

    // Actualizar la base de datos
    $conexion->query("UPDATE certificados_diplomados SET 
        tipo_identificacion='$tipo_identificacion', 
        numero_identificacion='$numero_identificacion', 
        nombre_completo='$nombre_completo', 
        nombre_curso='$nombre_curso', 
        descripcion='$descripcion', 
        fecha_certificacion='$fecha_certificacion', 
        fecha_emision='$fecha_emision' 
        WHERE id = $id_diplomado");

    // Obtener el número de certificado o generar uno nuevo si es necesario
    $numero_certificado = $diplomado['numero_certificado']; // O generar uno nuevo

    // Generar el PDF actualizado
    require('php/fpdf/fpdf.php');

    // Clase personalizada para el PDF de Certificado
    class PDF_Certificado extends FPDF
    {
        function Header()
        {
            // Configuración de las imágenes y el encabezado
            $this->Image('imagenes/CV1.png', 85, 5, 43);
            $this->Image('../imagenes/SELLO.png', 5, 5, 30);
            $this->Image('../imagenes/QR.png', 179, 5, 27);
            $this->Ln(25);
            $this->SetFont('Arial', '', 12);
            $this->Cell(0, 10, 'NIT. 901.339.075-7', 0, 1, 'C');
            $this->Ln(7);
            $this->SetFont('Arial', 'B', 20);
            $this->Cell(0, 10, 'HACE CONSTAR QUE:', 0, 1, 'C');
            $this->Ln(10);
        }

        function Footer()
        {
            $this->SetY(-15);
            $this->SetFont('Arial', 'IB', 8);
            $this->Cell(0, 10, utf8_decode('Verifique este certificado escaneando el código QR o ingresando a nuestra página web www.cultvial.com/certificados | Página ') . $this->PageNo(), 0, 0, 'C');
        }

        function CertificadoBody($data)
        {
            $this->SetFont('Arial', 'B', 30);
            $this->Ln(-3);
            $this->SetAlpha(0.1);
            $this->Image('imagenes/FONDO.png', 0, 0, 220);
            $this->SetAlpha(1);
            $this->MultiCell(0, 14, strtoupper(utf8_decode($data['nombre_completo'])), 0, 'C');
            $this->Ln(3);
            $this->SetFont('Arial', 'B', 15);
            $this->Cell(0, 10, 'CON DOCUMENTO DE IDENTIDAD NO:', 0, 1, 'C');
            $this->Ln(4);
            $this->SetFont('Arial', 'B', 25);
            $this->Cell(0, 10, "{$data['tipo_identificacion']} {$data['numero_identificacion']}", 0, 1, 'C');
            $this->Ln(6);
            
            // Texto de introducción del curso
            $texto = utf8_decode("PARTICIPÓ Y APROBÓ EL DIPLOMADO DE:");
            $this->SetFont('Arial', '', 14);
            $this->MultiCell(0, 8, $texto, 0, 'C');
            $this->Ln(5);

            // Nombre del curso en su propia fila con margen
            $this->SetFont('Arial', 'B', 28);
            $this->SetLeftMargin(20); // Margen izquierdo para nombre_curso
            $this->MultiCell(0, 10, utf8_decode($data['nombre_curso']), 0, 'C');
            $this->SetLeftMargin(10); // Restaurar el margen izquierdo
            $this->Ln(2);
            
            // Mostrar la descripción del diplomado debajo de nombre_curso
            $this->SetFont('Arial', 'B', 12);
            $this->MultiCell(0, 10, utf8_decode($data['descripcion']), 0, 'C');
            $this->Ln(3);
            
            // Fecha de certificación en formato personalizado y en mayúsculas
            $fecha_certificacion = strtoupper($this->formatearFecha($data['fecha_certificacion']));
            $fecha_formateada = "EN CONSTANCIA DE LO ANTERIOR SE FIRMA EN LA CIUDAD DE BOGOTÁ DC, COLOMBIA A LOS " . $fecha_certificacion;
            $this->SetFont('Arial', '', 12);
            $this->MultiCell(0, 5, utf8_decode($fecha_formateada), 0, 'C');
            $this->Ln(4);
            
            // Información adicional
            $this->Image('../imagenes/FIRMA.png', 80, $this->GetY() - 10, 50);
            $this->Ln(19);
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(0, 10, "ERVIN RICARDO BARAJAS", 0, 1, 'C');
            $this->Ln(-5);
            $this->SetFont('Arial', 'B', 10);
            $this->Cell(0, 10, "GERENTE", 0, 1, 'C');
            $this->Ln(0);
            $this->SetFont('Arial', 'B', 11);
            $this->SetTextColor(218, 143, 16); // RGB para amarillo
            $this->Cell(0, 10, utf8_decode("Fecha de emisión:"), 0, 1, 'L');
            $this->Ln(-2);
            $this->Cell(0, 10, utf8_decode("No. certificado:"), 0, 1, 'L');
            $this->Ln(-18);
            $this->SetFont('Arial', '', 11);
            $this->SetTextColor(0, 0, 0); // RGB para negro
            $this->Cell(0, 10, utf8_decode("                                 {$data['fecha_emision']}"), 0, 1, 'L');
            $this->Ln(-2);
            $this->SetTextColor(0, 0, 0); // RGB para negro
            $this->Cell(0, 10, utf8_decode("                            {$data['numero_certificado']}"), 0, 1, 'L');
            $this->Ln(-11);

            // Cambiar fuente a negrita y tamaño de letra
            $this->SetFont('Arial', 'B', 20);
            $this->SetTextColor(255, 0, 0); // RGB para rojo

            // Centrar y mostrar el número de certificado en negrita y rojo
            $this->Cell(0, 10, utf8_decode("{$data['numero_certificado']}"), 0, 1, 'R');
            $this->Ln(7);

            // Restablecer el color del texto a negro para el resto del documento
            $this->SetTextColor(0, 0, 0);

            $this->SetFont('Arial', '', 8);
            $nota = "El presente documento certifica la competencia, la actualización de conocimientos y las habilidades técnicas y prácticas, de acuerdo con lo establecido en el Decreto 1075 de 2015 sobre educación informal. La copia o plagio de esta certificación viola las leyes colombianas; por tal razón, las modificaciones, enmendaduras o adulteraciones posteriores a la entrega son de entera y única responsabilidad del titular de dicha información. CVC&S se reserva el derecho de emitir nuevas comunicaciones o documentos adicionales al presente, salvo orden judicial o mandato de autoridad competente.";
            $this->MultiCell(0, 3, utf8_decode($nota), 0, 'J');
        }

        function SetAlpha($alpha)
        {
            $this->_out(sprintf('q %.2F gs', $alpha));
        }

        function formatearFecha($fecha)
        {
            $date = new DateTime($fecha);
            $meses = [
                1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
                5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
                9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
            ];
            return $date->format('d') . ' días del mes de ' . $meses[(int)$date->format('m')] . ' de ' . $date->format('Y');
        }
    }

    // Crear un nuevo PDF
    $pdf = new PDF_Certificado();
    $pdf->AddPage('P');
    $pdf->CertificadoBody([
        'nombre_completo' => $nombre_completo,
        'tipo_identificacion' => $tipo_identificacion,
        'numero_identificacion' => $numero_identificacion,
        'nombre_curso' => $nombre_curso,
        'descripcion' => $descripcion,
        'fecha_certificacion' => $fecha_certificacion,
        'fecha_emision' => $fecha_emision,
        'numero_certificado' => $numero_certificado
    ]);

    // Guardar el PDF
    $pdf_file = "certificados/$numero_certificado.pdf"; // Guardar con el número de certificado
    if ($pdf->Output('F', $pdf_file)) {
        echo "El PDF se ha actualizado correctamente.";
    } else {
        echo "Error al generar el PDF.";
    }         

    header('Location: ver_diplomados.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Diplomado</title>
    <link rel="stylesheet" href="css/estilos.css">
    <style>
        h1 { text-align: left; margin-bottom: 20px; }
        label { display: block; margin: 10px 0 5px; font-weight: bold; }
        input[type="text"], input[type="date"] { width: 90%; padding: 10px; border: 1px solid #ccc; border-radius: 2px; margin-bottom: 10px; }
        .form-row { margin-bottom: 20px; }
    </style>
</head>
<body>
    <?php include 'header_nav.php'; ?>

    <div class="container">
        <h1>Editar Diplomado</h1>
        <div class="botones">
            <a href="ver_diplomados.php"><button>Volver al Menú Anterior</button></a>
        </div>

        <form method="POST">
            <div class="form-row">
                <label for="numero_identificacion">Número de Identificación:</label>
                <input type="text" id="numero_identificacion" name="numero_identificacion" value="<?php echo $diplomado['numero_identificacion']; ?>" required>
            </div>
            <div class="form-row">
                <label for="tipo_identificacion">Tipo de Identificación:</label>
                <select name="tipo_identificacion" id="tipo_identificacion" required>
                    <option value="CC" <?php if($diplomado['tipo_identificacion'] == 'CC') echo 'selected'; ?>>CC</option>
                    <option value="CE" <?php if($diplomado['tipo_identificacion'] == 'CE') echo 'selected'; ?>>CE</option>
                    <option value="PA" <?php if($diplomado['tipo_identificacion'] == 'PA') echo 'selected'; ?>>PA</option>
                    <option value="PPT" <?php if($diplomado['tipo_identificacion'] == 'PPT') echo 'selected'; ?>>PPT</option>
                </select>
            </div>
            <div class="form-row">
                <label for="nombre_completo">Nombre Completo:</label>
                <input type="text" id="nombre_completo" name="nombre_completo" value="<?php echo $diplomado['nombre_completo']; ?>" required>
            </div>
            <div class="form-row">
                <label for="nombre_curso">Nombre del Curso:</label>
                <input type="text" id="nombre_curso" name="nombre_curso" value="<?php echo $diplomado['nombre_curso']; ?>" required>
            </div>
            <div class="form-row">
                <label for="descripcion">Descripción:</label>
                <input type="text" id="descripcion" name="descripcion" value="<?php echo $diplomado['descripcion']; ?>" required>
            </div>
            <div class="form-row">
                <label for="fecha_certificacion">Fecha de Certificación:</label>
                <input type="date" id="fecha_certificacion" name="fecha_certificacion" value="<?php echo $diplomado['fecha_certificacion']; ?>" required>
            </div>
            <div class="form-row">
                <label for="fecha_emision">Fecha de Emisión:</label>
                <input type="date" id="fecha_emision" name="fecha_emision" value="<?php echo $diplomado['fecha_emision']; ?>" required>
            </div>
            <button type="submit">Actualizar Diplomado</button>
        </form>
    </div>
</body>
</html>

<?php
// Cerrar conexión
$conexion->close();
?>
