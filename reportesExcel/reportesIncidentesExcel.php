<?php
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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

// Crear nuevo documento
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Incidentes");

// Encabezado
$sheet->setCellValue('A1', 'Reporte de Incidentes');
$sheet->mergeCells('A1:I1');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
$sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

// Fecha
$fechaHoy = date("d/m/Y H:i");
$sheet->setCellValue('A2', 'Generado el: ' . $fechaHoy);
$sheet->mergeCells('A2:I2');
$sheet->getStyle('A2')->getAlignment()->setHorizontal('center');

// Encabezados de tabla
$encabezados = [
    "ID", "Descripción", "Tipo", "Estudiante",
    "Reportado por", "Fecha", "Hora", "Lugar", "Estado"
];
$col = 'A';
foreach ($encabezados as $encabezado) {
    $sheet->setCellValue($col . '4', $encabezado);
    $sheet->getStyle($col . '4')->getFont()->setBold(true);
    $sheet->getStyle($col . '4')->getFill()
          ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
          ->getStartColor()->setARGB('FFD9D9D9');
    $col++;
}

// Contenido de la tabla
$fila = 5;
foreach ($listaIncidentes as $item) {
    $sheet->setCellValue('A' . $fila, 'INC-' . $item->id_incidente);
    $sheet->setCellValue('B' . $fila, $item->descripcion);
    $sheet->setCellValue('C' . $fila, $item->tipo);
    $sheet->setCellValue('D' . $fila, $item->nombre_estudiante);
    $sheet->setCellValue('E' . $fila, $item->usuario_reporta);
    $sheet->setCellValue('F' . $fila, $item->fecha_incidente);
    $sheet->setCellValue('G' . $fila, $item->hora_incidente);
    $sheet->setCellValue('H' . $fila, $item->lugar);
    $sheet->setCellValue('I' . $fila, $item->estado);
    $fila++;
}

// Ajustar ancho automático
foreach (range('A', 'I') as $columna) {
    $sheet->getColumnDimension($columna)->setAutoSize(true);
}

// Descargar Excel
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: inline; filename="reporte_incidentes.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
