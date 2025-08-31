<?php
session_start();
include("../../../../dbconnection.php");
require '../../../../vendor/autoload.php'; // PHPSpreadsheet
require_once __DIR__ . '/../helpers/historico-query.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

// Recibir filtros
$filters = [
    'fecha_inicio' => $_GET['fecha_inicio'] ?? '',
    'fecha_fin'    => $_GET['fecha_fin'] ?? '',
    'tipo'         => $_GET['tipo'] ?? '',
    'estado'       => $_GET['estado'] ?? '',
    'cargo'        => $_SESSION['cargo'] ?? null
];

// Generar query
$query = buildQueryHistorico($con, $filters, false);
$result = mysqli_query($con, $query);

// Crear hoja de Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Encabezados de columnas
$headers = [
    'Estado', 'Tipo', 'Fecha', 'Hora', 'Solicitante',
    'Supervisor Origen', 'Colaborador', 'RUT',
    'Sucursal Origen', 'Jornada Origen', 'Rol Origen',
    'Motivo', 'Sucursal Destino', 'Jornada Destino',
    'Rol Destino', 'Fecha Turno', 'Supervisor Destino',
    'Observación', 'Observación RRHH'
];

// Escribir encabezados
foreach ($headers as $col => $header) {
    $colLetter = Coordinate::stringFromColumnIndex($col + 1);
    $sheet->setCellValue($colLetter . '1', $header);
}

// Mapeo entre encabezados y campos de la BD
$map = [
    'Estado' => 'estadoN',
    'Tipo' => 'tipo',
    'Fecha' => 'fecha_registro',
    'Hora' => 'hora_registro',
    'Solicitante' => 'soliN',
    'Supervisor Origen' => 'supOrigen',
    'Colaborador' => 'colaborador',
    'RUT' => 'rutC',
    'Sucursal Origen' => 'suOrigen',
    'Jornada Origen' => 'joOrigen',
    'Rol Origen' => 'rolOrigen',
    'Motivo' => 'motivoN',
    'Sucursal Destino' => 'suDestino',
    'Jornada Destino' => 'joDestino',
    'Rol Destino' => 'rolDestino',
    'Fecha Turno' => 'fecha_turno',
    'Supervisor Destino' => 'supDestino',
    'Observación' => 'observacion',
    'Observación RRHH' => 'obs_rrhh'
];

// Escribir datos
$rowIndex = 2;
while ($data = mysqli_fetch_assoc($result)) {
    foreach ($headers as $col => $header) {
        $field = $map[$header];
        $colLetter = Coordinate::stringFromColumnIndex($col + 1);
        $sheet->setCellValue($colLetter . $rowIndex, $data[$field] ?? '');
    }
    $rowIndex++;
}

// Autoajustar ancho de columnas
foreach (range(1, count($headers)) as $colIndex) {
    $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($colIndex))->setAutoSize(true);
}

// Descargar archivo
$filename = "Historico_" . date('Y-m-d_H-i-s') . ".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment;filename=\"$filename\"");
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
