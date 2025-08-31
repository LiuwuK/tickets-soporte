<?php
session_start();
require '../../../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

require_once '../../../../dbconnection.php';
require_once './traslados.php';

$usRol = $_SESSION['cargo'];
$usID = $_SESSION['id'];

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Cabeceras
$headers = ['Supervisor Origen','Nombre Colaborador','RUT','Instalación Origen','Motivo','Observación','Solicitante'];
$sheet->fromArray($headers, null, 'A1');

// Estilo cabecera
$sheet->getStyle('A1:G1')->applyFromArray([
    'font'=>['bold'=>true,'size'=>14],
    'alignment'=>['horizontal'=>Alignment::HORIZONTAL_CENTER,'vertical'=>Alignment::VERTICAL_CENTER],
    'fill'=>['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>'CCCCCC']]
]);
$sheet->getRowDimension(1)->setRowHeight(30);

// Obtener datos filtrados "En gestión"
$lista = listar($con, 'desvinculaciones', $usID, $usRol);

$rowNumber = 2;
while($row = $lista->fetch_assoc()){
    $sheet->setCellValue('A'.$rowNumber, $row['supN'] ?? $row['supervisor'] ?? '');
    $sheet->setCellValue('B'.$rowNumber, $row['colaborador'] ?? '');
    $sheet->setCellValue('C'.$rowNumber, $row['rut'] ?? '');
    $sheet->setCellValue('D'.$rowNumber, $row['sucN'] ?? $row['instalacion'] ?? '');
    $sheet->setCellValue('E'.$rowNumber, $row['motivoEgreso'] ?? $row['motivo'] ?? '');
    $sheet->setCellValue('F'.$rowNumber, $row['observacion'] ?? '');
    $sheet->setCellValue('G'.$rowNumber, $row['soliN'] ?? '');
    $rowNumber++;
}

// Ajustar ancho automático
foreach(range('A','G') as $col) $sheet->getColumnDimension($col)->setAutoSize(true);

// Exportar
$writer = new Xlsx($spreadsheet);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="desvinculaciones.xlsx"');
header('Cache-Control: max-age=0');
$writer->save('php://output');
exit;
