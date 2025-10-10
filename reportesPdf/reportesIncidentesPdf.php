<?php
session_start();
require '../vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

// Solo admin
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    die("Acceso no autorizado");
}


include("../conexion/bd.php");

// Obtener los incidentes
$sql = $conexion->prepare("SELECT  
    usuarios.nombre AS usuario_reporta,
    incidentes.id as id_incidente,
    incidentes.descripcion,
    estudiantes.nombre AS nombre_estudiante,
    incidentes.fecha_incidente,
    incidentes.hora_incidente,
    incidentes.lugar,
    incidentes.estado,
    incidentes.tipo
FROM incidentes 
INNER JOIN estudiantes ON incidentes.estudiante_id = estudiantes.id 
INNER JOIN usuarios ON incidentes.usuario_reporta_id = usuarios.id 
ORDER BY incidentes.id DESC");
$sql->execute();
$listaIncidentes = $sql->fetchAll(PDO::FETCH_OBJ);

// Opciones de Dompdf
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);

// Fecha actual
$fechaHoy = date("d/m/Y H:i");

// HTML para el PDF
$html = '
<style>
body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
.header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
.header img { width: 70px; float: left; }
.header h2 { margin: 0; color: #2c3e50; font-size: 20px; }
.header p { margin: 5px 0 0; font-size: 12px; color: #555; }
.clearfix { clear: both; }
table { width: 100%; border-collapse: collapse; margin-top: 20px; }
th, td { border: 1px solid #ccc; padding: 6px; text-align: left; font-size: 11px; }
th { background: #f2f2f2; font-weight: bold; }
.incident-id { font-weight: bold; color: #2c3e50; }
.footer { position: fixed; bottom: 10px; left: 0; right: 0; text-align: center; font-size: 10px; color: #666; }
</style>

<div class="header">
    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/6b/Logo_UNESCO.svg/1200px-Logo_UNESCO.svg.png">
    <h2>Reporte de Incidentes</h2>
    <p>Generado el ' . $fechaHoy . '</p>
    <div class="clearfix"></div>
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Descripción</th>
            <th>Tipo</th>
            <th>Estudiante</th>
            <th>Reportado por</th>
            <th>Fecha</th>
            <th>Hora</th>
            <th>Lugar</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>';

foreach ($listaIncidentes as $item) {
    $html .= '
    <tr>
        <td class="incident-id">INC-' . $item->id_incidente . '</td>
        <td>' . htmlspecialchars($item->descripcion) . '</td>
        <td>' . htmlspecialchars($item->tipo) . '</td>
        <td>' . htmlspecialchars($item->nombre_estudiante) . '</td>
        <td>' . htmlspecialchars($item->usuario_reporta) . '</td>
        <td>' . htmlspecialchars($item->fecha_incidente) . '</td>
        <td>' . htmlspecialchars($item->hora_incidente) . '</td>
        <td>' . htmlspecialchars($item->lugar) . '</td>
        <td>' . htmlspecialchars($item->estado) . '</td>
    </tr>';
}

$html .= '</tbody></table>

<div class="footer">
    Sistema de Gestión de Incidentes - Página {PAGE_NUM} de {PAGE_COUNT}
</div>';

// Cargar HTML en Dompdf
$dompdf->loadHtml($html);

// Configurar tamaño de hoja y orientación
$dompdf->setPaper('A4', 'landscape');

// Renderizar PDF
$dompdf->render();

// Mostrar en navegador (no descarga)
$dompdf->stream("reporte_incidentes.pdf", ["Attachment" => false]);