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
$sheet->setCellValue('A1', 'Supervisor Origen');
$sheet->setCellValue('B1', 'Nombre Colaborador');
$sheet->setCellValue('C1', 'RUT');
$sheet->setCellValue('D1', 'Instalación Origen');
$sheet->setCellValue('E1', 'Motivo');
$sheet->setCellValue('F1', 'Observación');
$sheet->setCellValue('G1', 'Solicitante');

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

$sheet->getStyle('A1:G1')->applyFromArray($headerStyle);
// Ajustar la altura de la fila de la cabecera para mayor espacio
$sheet->getRowDimension(1)->setRowHeight(30);

// Escribir los datos a partir de la fila 2
$query = "SELECT de.*, 
                su.nombre AS instalacion,
                us.name AS soliN,
                sup.nombre_supervisor AS supervisor,
                mo.motivo AS motivoEgreso
            FROM desvinculaciones de
            JOIN user us ON(de.solicitante = us.id)
            JOIN sucursales su ON(de.instalacion = su.id)
            JOIN supervisores sup ON(de.supervisor_origen = sup.id)
            JOIN motivos_gestion mo ON(de.motivo = mo.id)
            WHERE tr.fecha_registro BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 DAY) + INTERVAL 16 HOUR 
            AND CURDATE() + INTERVAL 1 DAY + INTERVAL 16 HOUR
            ORDER BY de.fecha_registro ASC";
$result = mysqli_query($con, $query);

$rowNumber = 2;
while ($row = mysqli_fetch_assoc($result)) {
    $sheet->setCellValue('A' . $rowNumber, $row['supervisor']);
    $sheet->setCellValue('B' . $rowNumber, $row['colaborador']);
    $sheet->setCellValue('C' . $rowNumber, $row['rut']);
    $sheet->setCellValue('D' . $rowNumber, $row['instalacion']);
    $sheet->setCellValue('E' . $rowNumber, $row['motivoEgreso']);
    $sheet->setCellValue('F' . $rowNumber, $row['observacion']);
    $sheet->setCellValue('G' . $rowNumber, $row['soliN']);
    $rowNumber++;
}

// Ajustar automáticamente el ancho de las columnas de A a G
foreach (range('A','G') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
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
