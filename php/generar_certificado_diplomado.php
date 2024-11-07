<?php  
require('fpdf/fpdf.php'); // Incluye la biblioteca FPDF

// Conectar a la base de datos
$conexion = new mysqli('localhost', 'root', '', 'certificados_db');

// Comprobar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Establecer el conjunto de caracteres en UTF-8
$conexion->set_charset('utf8mb4');

// Clase personalizada para el PDF de Certificado
class PDF_Certificado extends FPDF
{
    function Header()
    {
        // Configuración de las imágenes y el encabezado
        $this->Image('../imagenes/CV1.png', 85, 5, 43);
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
        $this->SetY(-30); // Posiciona el texto en el pie de página
        $this->SetFont('Arial', '', 8);
        $nota = "El presente documento certifica la competencia, la actualización de conocimientos y las habilidades técnicas y prácticas, de acuerdo con lo establecido en el Decreto 1075 de 2015 sobre educación informal. La copia o plagio de esta certificación viola las leyes colombianas; por tal razón, las modificaciones, enmendaduras o adulteraciones posteriores a la entrega son de entera y única responsabilidad del titular de dicha información. CVC&S se reserva el derecho de emitir nuevas comunicaciones o documentos adicionales al presente, salvo orden judicial o mandato de autoridad competente.";
        $this->MultiCell(0, 3, utf8_decode($nota), 0, 'J');

        $this->SetY(-15);
        $this->SetFont('Arial', 'IB', 8);
        $this->Cell(0, 10, utf8_decode('Verifique este certificado escaneando el código QR o ingresando a nuestra página web www.cultvial.com/certificados | Página ') . $this->PageNo(), 0, 0, 'C');
    }

    function CertificadoBody($data)
    {
        $this->SetFont('Arial', 'B', 30);
        $this->Ln(-3);
        $this->SetAlpha(0.1);
        $this->Image('../imagenes/FONDO.png', 0, 0, 220);
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
        $this->Image('../imagenes//FIRMA.png', 80, $this->GetY() - 10, 50);
        $this->Ln(19);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 10, "ERVIN RICARDO BARAJAS", 0, 1, 'C');
        $this->Ln(-5);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 10, "GERENTE", 0, 1, 'C');
        $this->Ln(0);
        $this->SetFont('Arial', 'B', 11);
        $this->SetTextColor(218, 143, 16); // RGB para rojo
        $this->Cell(0, 10, utf8_decode("Fecha de emisión:"), 0, 1, 'L');
        $this->Ln(-2);
        $this->Cell(0, 10, utf8_decode("No. certificado:"), 0, 1, 'L');
        $this->Ln(-18);
        $this->SetFont('Arial', '', 11);
        $this->SetTextColor(0, 0, 0); // RGB para rojo
        $this->Cell(0, 10, utf8_decode("                                 {$data['fecha_emision']}"), 0, 1, 'L');
        $this->Ln(-2);
        $this->SetTextColor(0, 0, 0); // RGB para rojo
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

// Comprobar si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Escapar y obtener los datos del formulario
    $tipo_identificacion = $conexion->real_escape_string($_POST['tipo_identificacion'] ?? '');
    $numero_identificacion = $conexion->real_escape_string($_POST['numero_identificacion'] ?? '');
    $nombre_completo = $conexion->real_escape_string($_POST['nombre_completo'] ?? '');
    $fecha_certificacion = $conexion->real_escape_string($_POST['fecha_certificacion'] ?? '');
    $fecha_emision = $conexion->real_escape_string($_POST['fecha_emision'] ?? '');

    // Verifica si hay diplomados seleccionados
    if (isset($_POST['diplomados']) && is_array($_POST['diplomados'])) {
        foreach ($_POST['diplomados'] as $diplomado_id) {
            $diplomado_id = (int) $diplomado_id;

            // Consultar el nombre, prefijo y descripción del diplomado
            $query_diplomado = "SELECT nombre_diplomado, prefijo, descripcion FROM diplomados WHERE id = $diplomado_id";
            $resultado_diplomado = $conexion->query($query_diplomado);
            
            if ($resultado_diplomado->num_rows > 0) {
                $diplomado = $resultado_diplomado->fetch_assoc();
                $nombre_diplomado = $diplomado['nombre_diplomado'];
                $prefijo = $diplomado['prefijo'];
                $descripcion = $diplomado['descripcion']; // Obtén la descripción

                // Generar un número de certificado único
                $numero_aleatorio = rand(10000, 50000);
                $numero_certificado = $prefijo . $numero_aleatorio; // Cambia el prefijo según sea necesario

                // Inserta la información en la tabla certificados_diplomados
                $query_insert = "INSERT INTO certificados_diplomados (numero_certificado, tipo_identificacion, numero_identificacion, nombre_completo, nombre_curso, descripcion, fecha_certificacion, fecha_emision, ruta_certificado) 
                                VALUES ('$numero_certificado', '$tipo_identificacion', '$numero_identificacion', '$nombre_completo', '$nombre_diplomado', '$descripcion', '$fecha_certificacion', '$fecha_emision', '')";
                
                if ($conexion->query($query_insert) === TRUE) {
                    // Generación y almacenamiento del PDF
                    $pdf = new PDF_Certificado();
                    $pdf->AddPage('P');
                    $pdf->CertificadoBody([
                        'nombre_completo' => $nombre_completo,
                        'tipo_identificacion' => $tipo_identificacion,
                        'numero_identificacion' => $numero_identificacion,
                        'nombre_curso' => $nombre_diplomado,
                        'descripcion' => $descripcion, // Añadir descripción aquí
                        'fecha_certificacion' => $fecha_certificacion ?: 'Fecha no definida',
                        'fecha_emision' => $fecha_emision ?: 'Fecha no definida',
                        'numero_certificado' => $numero_certificado
                    ]);
                
                    // Guardar el archivo PDF y actualizar la ruta en la base de datos
                    $ruta_certificado = "../certificados/{$numero_certificado}.pdf";
                    $pdf->Output('F', $ruta_certificado);
                    $update_query = "UPDATE certificados_diplomados SET ruta_certificado = '$ruta_certificado' WHERE numero_certificado = '$numero_certificado'";
                    $conexion->query($update_query);
                
                    // Mostrar mensaje y redirigir usando JavaScript
                    echo "<script>
                            alert('Certificado generado correctamente para el diplomado: $nombre_diplomado. Número de Certificado: $numero_certificado');
                            setTimeout(function() {
                                window.location.href = '../ver_diplomados.php';
                            }, 200); // 3000 ms = 3 segundos de espera
                          </script>";
                } else {
                    echo "Error al insertar el certificado en la base de datos: " . $conexion->error . "<br>";
                }
                
            } else {
                echo "Diplomado no encontrado.<br>";
            }
        }
    } else {
        echo "No se seleccionó ningún diplomado.<br>";
    }
}
?>
