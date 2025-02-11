<?php
require '../../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Conexión a la base de datos
require_once '../../../dbconnection.php';

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Definir las cabeceras de la tabla en la fila 1
$sheet->setCellValue('A1', 'Supervisor Origen');
$sheet->setCellValue('B1', 'Nombre Colaborador');
$sheet->setCellValue('C1', 'RUT');
$sheet->setCellValue('D1', 'Instalación Origen');
$sheet->setCellValue('E1', 'Motivo');
$sheet->setCellValue('F1', 'Observación');
$sheet->setCellValue('G1', 'Solicitante');

// Realizar la consulta a la tabla "desvinculaciones"
$query = "SELECT * 
            FROM desvinculaciones";
$result = mysqli_query($con, $query);

// Comenzar a escribir los datos a partir de la fila 2
$rowNumber = 2;
while ($row = mysqli_fetch_assoc($result)) {
    $sheet->setCellValue('A' . $rowNumber, $row['supervisor_origen']);
    $sheet->setCellValue('B' . $rowNumber, $row['colaborador']);
    $sheet->setCellValue('C' . $rowNumber, $row['rut']);
    $sheet->setCellValue('D' . $rowNumber, $row['instalacion']);
    $sheet->setCellValue('E' . $rowNumber, $row['motivo']);
    $sheet->setCellValue('F' . $rowNumber, $row['observacion']);
    $sheet->setCellValue('G' . $rowNumber, $row['solicitante']);
    $rowNumber++;
}

// Crear el escritor para Excel (Xlsx)
$writer = new Xlsx($spreadsheet);

// Configurar las cabeceras para forzar la descarga del archivo Excel
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="desvinculaciones.xlsx"');
header('Cache-Control: max-age=0');

// Escribir el archivo en la salida
$writer->save('php://output');
exit;
?>
