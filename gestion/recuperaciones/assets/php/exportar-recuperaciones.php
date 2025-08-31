<?php
session_start();
require_once '../../../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (!isset($_SESSION['recuperaciones_export'])) {
    die('No hay datos para exportar.');
}

$data = $_SESSION['recuperaciones_export']['events'];

// Crear nuevo Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Cabecera
$sheet->setCellValue('A1', 'SUCURSAL');
$sheet->setCellValue('B1', 'FECHA');
$sheet->setCellValue('C1', 'MONTO');

$fila = 2;
foreach ($data as $evento) {
    $sheet->setCellValue('A'.$fila, $evento['extendedProps']['sucursal']);
    $sheet->setCellValue('B'.$fila, $evento['start']);
    $sheet->setCellValue('C'.$fila, $evento['extendedProps']['monto']);
    
    // Formato moneda
    $sheet->getStyle('C'.$fila)->getNumberFormat()->setFormatCode('"$"#,##0');

    $fila++;
}

// Autoajustar columnas
foreach (range('A','C') as $col) {
    $sheet->getColumnDimension($col)->setWidth(20);
}

// Descargar archivo
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="reporte_recuperaciones.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
