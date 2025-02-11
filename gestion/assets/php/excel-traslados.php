<?php
require '../../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

// Conexión a la base de datos
require_once '../../../dbconnection.php';

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Definir las cabeceras de la tabla en la fila 1
$sheet->setCellValue('A1', 'Nombre Colaborador');
$sheet->setCellValue('B1', 'RUT');
$sheet->setCellValue('C1', 'Instalación Origen');
$sheet->setCellValue('D1', 'Supervisor Origen');
$sheet->setCellValue('E1', 'Jornada Origen');
$sheet->setCellValue('F1', 'Instalación Destino');
$sheet->setCellValue('G1', 'Supervisor Destino');
$sheet->setCellValue('H1', 'Jornada Destino');
$sheet->setCellValue('I1', 'Motivo Traslado');
$sheet->setCellValue('J1', 'Rol');
$sheet->setCellValue('K1', 'Fecha Inicio Turno');
$sheet->setCellValue('L1', 'Solicitante');
// Aplicar estilo a la cabecera (fila 1)
$headerStyle = [
    'font' => [
        'bold' => true,
        'size' => 14,
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical'   => Alignment::VERTICAL_CENTER,
    ],
    'fill' => [
        'fillType'   => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'CCCCCC'],
    ],
];

$sheet->getStyle('A1:L1')->applyFromArray($headerStyle);
// Ajustar la altura de la fila de la cabecera para mayor espacio
$sheet->getRowDimension(1)->setRowHeight(30);

// Escribir los datos a partir de la fila 2
$query = "SELECT tr.*, 
                us.name AS soliN, -- Nombre del solicitante
                su_origen.nombre AS suOrigen, -- Sucursal de origen
                jo_origen.tipo_jornada AS joOrigen, -- Jornada de origen
                su_destino.nombre AS suDestino, -- Sucursal de destino
                jo_destino.tipo_jornada AS joDestino, -- Jornada de destino
                sup_origen.nombre_supervisor AS supOrigen, -- Supervisor de origen
                sup_destino.nombre_supervisor AS supDestino, -- Supervisor destino
                mg.motivo AS motivo, -- Motivo traslado
                ro.nombre_rol AS rolN -- ROL
            FROM traslados tr
            JOIN user us ON tr.solicitante = us.id
            JOIN sucursales su_origen ON tr.instalacion_origen = su_origen.id
            JOIN sucursales su_destino ON tr.instalacion_destino = su_destino.id
            JOIN jornadas jo_origen ON tr.jornada_origen = jo_origen.id
            JOIN jornadas jo_destino ON tr.jornada_destino = jo_destino.id
            JOIN supervisores sup_origen ON tr.supervisor_origen = sup_origen.id
            JOIN supervisores sup_destino ON tr.supervisor_destino = sup_destino.id
            JOIN motivos_gestion mg ON tr.motivo_traslado = mg.id
            JOIN roles ro ON tr.rol = ro.id 
            WHERE tr.fecha_registro BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 DAY) + INTERVAL 16 HOUR AND CURDATE() + INTERVAL 16 HOUR
            ORDER BY tr.fecha_registro ASC";
$result = mysqli_query($con, $query);

$rowNumber = 2;
while ($row = mysqli_fetch_assoc($result)) {
    $sheet->setCellValue('A' . $rowNumber, $row['colaborador']);
    $sheet->setCellValue('B' . $rowNumber, $row['rut']);
    $sheet->setCellValue('C' . $rowNumber, $row['suOrigen']);
    $sheet->setCellValue('D' . $rowNumber, $row['supOrigen']);
    $sheet->setCellValue('E' . $rowNumber, $row['joOrigen']);
    $sheet->setCellValue('F' . $rowNumber, $row['suDestino']);
    $sheet->setCellValue('G' . $rowNumber, $row['supDestino']);
    $sheet->setCellValue('H' . $rowNumber, $row['joDestino']);
    $sheet->setCellValue('I' . $rowNumber, $row['motivo']);
    $sheet->setCellValue('J' . $rowNumber, $row['rolN']);
    $sheet->setCellValue('K' . $rowNumber, $row['fecha_inicio_turno']);
    $sheet->setCellValue('L' . $rowNumber, $row['soliN']);
    $rowNumber++;
}

// Ajustar automáticamente el ancho de las columnas de A a G
foreach (range('A','L') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Crear el escritor para Excel (Xlsx)
$writer = new Xlsx($spreadsheet);

// Configurar las cabeceras para forzar la descarga del archivo Excel
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="traslados.xlsx"');
header('Cache-Control: max-age=0');

// Escribir el archivo en la salida
$writer->save('php://output');
exit;
?>
