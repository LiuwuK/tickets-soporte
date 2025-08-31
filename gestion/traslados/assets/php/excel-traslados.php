<?php
session_start();
require '../../../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

// Conexión a la base de datos
require_once '../../../../dbconnection.php';
require_once './traslados.php';

$usRol = $_SESSION['cargo'];
$usID = $_SESSION['id'];

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Cabeceras
$headers = ['Nombre Colaborador','RUT','Instalación Origen','Supervisor Origen','Jornada Origen','Rol Origen',
            'Instalación Destino','Supervisor Destino','Jornada Destino','Rol Destino','Motivo Traslado','Fecha Inicio Turno','Solicitante'];

$sheet->fromArray($headers, null, 'A1');

// Estilo cabecera
$sheet->getStyle('A1:M1')->applyFromArray([
    'font'=>['bold'=>true,'size'=>14],
    'alignment'=>['horizontal'=>Alignment::HORIZONTAL_CENTER,'vertical'=>Alignment::VERTICAL_CENTER],
    'fill'=>['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>'CCCCCC']]
]);
$sheet->getRowDimension(1)->setRowHeight(30);

// Obtener datos filtrados "En gestión"
$lista = listar($con, 'traslados', $usID, $usRol);

$rowNumber = 2;
while($row = $lista->fetch_assoc()){
    $sheet->setCellValue('A'.$rowNumber, $row['nombre_colaborador'] ?? '');
    $sheet->setCellValue('B'.$rowNumber, $row['rut'] ?? '');
    $sheet->setCellValue('C'.$rowNumber, $row['suOrigen'] ?? $row['inOrigen_nombre'] ?? '');
    $sheet->setCellValue('D'.$rowNumber, $row['supOrigen'] ?? '');
    $sheet->setCellValue('E'.$rowNumber, $row['joOrigen'] ?? '');
    $sheet->setCellValue('F'.$rowNumber, $row['rolOrigen'] ?? '');
    $sheet->setCellValue('G'.$rowNumber, $row['suDestino'] ?? $row['inDestino_nombre'] ?? '');
    $sheet->setCellValue('H'.$rowNumber, $row['supDestino'] ?? '');
    $sheet->setCellValue('I'.$rowNumber, $row['joDestino'] ?? '');
    $sheet->setCellValue('J'.$rowNumber, $row['rolDestino'] ?? '');
    $sheet->setCellValue('K'.$rowNumber, $row['motivo'] ?? '');
    $sheet->setCellValue('L'.$rowNumber, $row['fecha_inicio_turno'] ?? '');
    $sheet->setCellValue('M'.$rowNumber, $row['soliN'] ?? '');
    $rowNumber++;
}

// Ajustar ancho automático
foreach(range('A','M') as $col) $sheet->getColumnDimension($col)->setAutoSize(true);

// Exportar
$writer = new Xlsx($spreadsheet);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="traslados.xlsx"');
header('Cache-Control: max-age=0');
$writer->save('php://output');
exit;
