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
        $this->Image('../imagenes/CV1.png', 124, 5, 50);
        $this->Image('../imagenes/SELLO.png', 5, 5, 30);
        $this->Image('../imagenes/QR.png', 267, 5, 27);
        $this->Ln(30);
        $this->SetFont('Arial', '', 14);
        $this->Cell(0, 10, 'NIT. 901.339.075-7', 0, 1, 'C');
        $this->Ln(0);
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, 'CERTIFICA QUE:', 0, 1, 'C');
        $this->Ln(8);
    }

    function Footer()
    {
        // Configura la fuente para el footer
        $this->SetFont('Arial', '', 9);
        $nota = "El presente documento certifica la competencia, la actualización de conocimientos y las habilidades técnicas y prácticas de acuerdo con lo establecido en el decreto 1075 de 2015 sobre educación informal. La copia o plagio de esta certificación viola las leyes colombianas; por tal razón, las modificaciones, enmendaduras o adulteraciones posteriores a la entrega son de entera y única responsabilidad del titular de dicha información. CVC&S se reserva el derecho de emitir nuevas comunicaciones o documentos validadores adicionales al presente documento, salvo orden judicial o mandato de autoridad competente.";
        
        // Configura márgenes
        $this->SetLeftMargin(10);
        $this->SetRightMargin(10);
        
        // Agrega la nota al footer
        $this->MultiCell(0, 3, utf8_decode($nota), 0, 'J');
        
        // Mensaje de verificación
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Verifique este certificado escaneando el código QR o ingresando a nuestra página web www.cultvial.com/certificados |  Página ') . $this->PageNo(), 0, 0, 'C');
    }
    

        // Cuerpo del certificado
        function CertificadoBody($data)
        {
            // Aquí usamos el logo_path que hemos recibido en los datos
            if (!empty($data['logo_path'])) {
                // Asegúrate de que la ruta completa sea correcta
                $logo_full_path = "../uploads/logos/" . $data['logo_path'];  // Concatenamos la ruta completa
                // Verificamos si el archivo existe antes de intentar insertarlo
                if (file_exists($logo_full_path)) {
                    $this->Image($logo_full_path, 220, $this->GetY() - 60, 40);  // Ajustamos la posición y tamaño del logo
                } else {
                    // Si el logo no existe, usamos el logo predeterminado (BLANCO.png)
                    $this->Image('../imagenes/BLANCO.png', 180, $this->GetY() - 0, 40);  // Logo predeterminado
                }
            } else {
                // Si no hay logo, usamos el logo predeterminado (BLANCO.png)
                $this->Image('../imagenes/BLANCO.png', 180, $this->GetY() - 0, 40);  // Logo predeterminado
            }
    
            // A continuación, seguimos con el resto del contenido, que no depende del logo.
            $this->SetFont('Arial', 'B', 30);
        $this->Ln(-5);
        $this->SetAlpha(0.1);
        $this->Image('../imagenes/CVCYSAGUA.png', 30, 26, 240);
        $this->SetAlpha(1);
        $this->Cell(0, 14, strtoupper(utf8_decode($data['nombre_completo'])), 0, 1, 'C');
        $this->SetFont('Arial', 'B', 25);
        $this->Cell(0, 10, "{$data['tipo_identificacion']} {$data['numero_identificacion']}", 0, 1, 'C');
        $this->Ln(4);
        
        $fecha_certificacion = $this->formatearFecha($data['fecha_certificacion']);
        $texto = utf8_decode("Participó en la formación presencial con una intensidad horaria de {$data['numero_horas']} horas, 
        realizada el día {$fecha_certificacion}.");
        $this->SetFont('Arial', '', 14);
        $this->MultiCell(0, 8, $texto, 0, 'C');
        $this->Ln(4);
            
        $this->SetFont('Arial', 'B', 30);
        $this->MultiCell(0, 13, utf8_decode($data['nombre_curso']), 0, 'C');
        $this->Ln(5);
        $this->SetFont('Arial', 'B', 11);
        $this->SetTextColor(218, 143, 16); // RGB para rojo
        $this->Cell(0, 5, utf8_decode("Fecha de emisión:"), 0, 1, 'L');
            
        // Insertar la firma a la izquierda
        $this->Image('../imagenes/FIRMA.png', 130, $this->GetY() - 10, 40);
            
        $this->Ln(2);
        $this->SetFont('Arial', 'B', 11);
        $this->SetTextColor(218, 143, 16); // RGB para rojo
        $this->Cell(0, 10, utf8_decode("Formador:"), 0, 1, 'L');
        $this->Cell(0, 10, utf8_decode("No. Certificado:"), 0, 1, 'L');
        $this->Ln(-27);

        $this->SetFont('Arial', '', 11);        
        $this->SetTextColor(0, 0, 0);
        $this->Cell(0, 5, utf8_decode("                                {$data['fecha_emision']}"), 0, 1, 'L');
        $this->Ln(2);
        $this->Cell(0, 10, utf8_decode("                   {$data['formador']}"), 0, 1, 'L');
        $this->Cell(0, 10, utf8_decode("                            {$data['numero_certificado']}"), 0, 1, 'L');
        $this->Ln(-7);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 10, "Gerente", 0, 1, 'C');
        $this->Ln(5);
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
        return $date->format('d') . ' de ' . $meses[(int)$date->format('m')] . ' de ' . $date->format('Y');
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Escapar y obtener los datos del formulario
    $tipo_identificacion = $conexion->real_escape_string($_POST['tipo_identificacion']);
    $numero_identificacion = $conexion->real_escape_string($_POST['numero_identificacion']);
    $nombre_completo = $conexion->real_escape_string($_POST['nombre_completo']);
    $tipo_formacion = $conexion->real_escape_string($_POST['tipo_formacion']);
    $numero_horas = (int) $_POST['numero_horas'];
    $fecha_certificacion = $conexion->real_escape_string($_POST['fecha_certificacion']);
    $fecha_emision = date('Y-m-d');
    $formador = $conexion->real_escape_string($_POST['formador']);
    $razon_social = isset($_POST['razon_social']) ? $conexion->real_escape_string($_POST['razon_social']) : '';
    $logo_path = isset($_POST['logo_path']) ? $conexion->real_escape_string($_POST['logo_path']) : '';
    $cursos = $_POST['cursos'];

    $certificados_dir = "../certificados/";
    if (!file_exists($certificados_dir)) {
        mkdir($certificados_dir, 0777, true);
    }

    foreach ($cursos as $curso_id) {
        $curso_id = (int) $curso_id;

        // Consultar los detalles del curso
        $query_curso = "SELECT nombre_curso, prefijo FROM cursos WHERE id = ?";
        if ($stmt = $conexion->prepare($query_curso)) {
            $stmt->bind_param("i", $curso_id);
            $stmt->execute();
            $resultado_curso = $stmt->get_result();

            if ($resultado_curso->num_rows > 0) {
                $curso = $resultado_curso->fetch_assoc();
                $nombre_curso = $curso['nombre_curso'];
                $prefijo_curso = $curso['prefijo'];

                $numero_aleatorio = rand(10000, 50000);
                $numero_certificado = $prefijo_curso . $numero_aleatorio;

                // Insertar los datos del certificado en la base de datos
                $query = "INSERT INTO certificados (tipo_identificacion, numero_identificacion, nombre_completo, nombre_curso, tipo_formacion, numero_horas, fecha_certificacion, fecha_emision, formador, numero_certificado, razon_social, logo_path)
                VALUES ('$tipo_identificacion', '$numero_identificacion', '$nombre_completo', '$nombre_curso', '$tipo_formacion', '$numero_horas', '$fecha_certificacion', '$fecha_emision', '$formador', '$numero_certificado', '$razon_social', '$logo_path')";

                if ($stmt_insert = $conexion->prepare($query)) {
                    if ($stmt_insert->execute()) {
                        // Crear el PDF del certificado
                        $pdf = new PDF_Certificado();
                        $pdf->AddPage('L');
                        $pdf->CertificadoBody([
                            'nombre_completo' => $nombre_completo,
                            'tipo_identificacion' => $tipo_identificacion,
                            'numero_identificacion' => $numero_identificacion,
                            'nombre_curso' => $nombre_curso,
                            'tipo_formacion' => $tipo_formacion,
                            'numero_horas' => $numero_horas,
                            'fecha_certificacion' => $fecha_certificacion,
                            'fecha_emision' => $fecha_emision,
                            'formador' => $formador,
                            'numero_certificado' => $numero_certificado,
                            'logo_path' => $logo_path
                        ]);

                        // Guardar el archivo PDF y actualizar la ruta en la base de datos
                        $ruta_certificado = "../certificados/{$numero_certificado}.pdf";
                        $pdf->Output('I', $numero_certificado);  // Guardar el archivo en el servidor
                        
                        // Actualizar la ruta del certificado en la base de datos
                        $update_query = "UPDATE certificados SET ruta_certificado = '$ruta_certificado' WHERE numero_certificado = '$numero_certificado'";
                        $conexion->query($update_query);

                        // Mostrar mensaje y redirigir usando JavaScript
                        echo "<script>
                        alert('Certificado generado correctamente para el curso: $nombre_curso. Número de Certificado: $numero_certificado');
                        setTimeout(function() {
                        window.location.href = '../ver_certificados.php';
                        }, 200); // 200 ms de espera
                        </script>";
                    } else {
                        echo "Error al insertar el certificado en la base de datos: " . $conexion->error . "<br>";
                    }
                } else {
                    echo "Error al preparar la consulta de inserción: " . $conexion->error . "<br>";
                }
            } else {
                echo "Curso no encontrado.<br>";
            }
            $stmt->close();
        } else {
            echo "Error al preparar la consulta del curso: " . $conexion->error . "<br>";
        }
    }
}
?>