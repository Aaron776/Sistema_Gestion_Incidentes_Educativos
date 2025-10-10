<?php
require '../vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

include("../conexion/bd.php");


// Obtener los mismos datos que en la vista
$sql = $conexion->prepare("
    SELECT 
        e.id, 
        e.nombre, 
        e.apellido, 
        e.curso, 
        e.email, 
        e.telefono, 
        u.nombre AS tutor
    FROM estudiantes e
    LEFT JOIN usuarios u ON e.tutor_id = u.id
    ORDER BY e.id DESC
");
$sql->execute();
$estudiantes = $sql->fetchAll(PDO::FETCH_ASSOC);

// Crear contenido HTML
$html = '
<h2 style="text-align:center;">Reporte de Estudiantes</h2>
<table border="1" cellspacing="0" cellpadding="6" width="100%">
    <thead style="background:#f0f0f0;">
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Grado/Curso</th>
            <th>Email</th>
            <th>Teléfono</th>
            <th>Tutor</th>
        </tr>
    </thead>
    <tbody>';

foreach ($estudiantes as $e) {
    $html .= '
    <tr>
        <td>EST-' . htmlspecialchars($e['id']) . '</td>
        <td>' . htmlspecialchars($e['nombre']) . '</td>
        <td>' . htmlspecialchars($e['apellido']) . '</td>
        <td>' . htmlspecialchars($e['curso']) . '°</td>
        <td>' . htmlspecialchars($e['email']) . '</td>
        <td>' . htmlspecialchars($e['telefono']) . '</td>
        <td>' . ($e['tutor'] ?? 'Sin Tutor') . '</td>
    </tr>';
}

$html .= '
    </tbody>
</table>';

// Opciones del PDF
$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

// Mostrar PDF en el navegador
$dompdf->stream("reporte_estudiantes.pdf", ["Attachment" => false]);
exit;
?>
