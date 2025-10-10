<?php
session_start();
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

include("../conexion/bd.php");

// Solo admin
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    die("Acceso no autorizado");
}

// Obtener los mismos datos
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

// Crear el documento Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Estudiantes");

// Encabezados
$headers = ["ID", "Nombre", "Apellido", "Grado/Curso", "Email", "Teléfono", "Tutor"];
$col = 'A';
foreach ($headers as $header) {
    $sheet->setCellValue($col . '1', $header);
    $sheet->getStyle($col . '1')->getFont()->setBold(true);
    $sheet->getColumnDimension($col)->setAutoSize(true);
    $col++;
}

// Datos
$row = 2;
foreach ($estudiantes as $e) {
    $sheet->setCellValue('A' . $row, 'EST-' . $e['id']);
    $sheet->setCellValue('B' . $row, $e['nombre']);
    $sheet->setCellValue('C' . $row, $e['apellido']);
    $sheet->setCellValue('D' . $row, $e['curso'] . '°');
    $sheet->setCellValue('E' . $row, $e['email']);
    $sheet->setCellValue('F' . $row, $e['telefono']);
    $sheet->setCellValue('G' . $row, $e['tutor'] ?? 'Sin Tutor');
    $row++;
}

// Descargar archivo
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="reporte_estudiantes.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
