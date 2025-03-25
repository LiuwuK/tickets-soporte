<?php
session_start();
include("../../../dbconnection.php");
require '../../../vendor/autoload.php'; // PHPSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Definir estilos para los encabezados
$styleColor1 = [
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'FFD9E1F2'] //(azul claro)
    ],
    'font' => [
        'bold' => true,
        'color' => ['rgb' => '000000'] // Texto en negro
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER
    ]
];

$styleColor2 = [
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'FFF2D9D9'] //(rojo claro)
    ],
    'font' => [
        'bold' => true,
        'color' => ['rgb' => '000000'] // Texto en negro
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER
    ]
];

$styleColor3 = [
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'FFE14E'] //(verde claro)
    ],
    'font' => [
        'bold' => true,
        'color' => ['rgb' => '000000'] // Texto en negro
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER
    ]
];

// Encabezados de la tabla
$headers = [
    'Nº Cuenta de Cargo', 'Nº Cuenta de Destino', 'Banco Destino', 
    'Rut beneficiario', 'Dig. Verif. Beneficiario','Monto Transferencia',
    'Nº Factura Boleta (1)', 'Nº Orden de Compra(1)',
    'Tipo de Pago(2)',
    'Mensaje Destinatario (3)', 'Email Destinatario(3)',
    'Cuenta Destino inscrita como(4)',
];

// Aplicar estilos a los encabezados
$sheet->fromArray([$headers], NULL, 'A1');
$sheet->getStyle('A1:G1')->applyFromArray($styleColor2);
$sheet->getStyle('J1')->applyFromArray($styleColor1);

$sheet->getStyle('H1:I1')->applyFromArray($styleColor1);
$sheet->getStyle('K1:L1')->applyFromArray($styleColor1);

$sheet->getStyle('m1')->applyFromArray($styleColor3);

// Capturar filtros
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : '';
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : '';
$estado = isset($_GET['estado']) ? $_GET['estado'] : '';

// Filtros para consulta
$filtros = [];

// Filtro por fecha
if (!empty($fecha_inicio) && !empty($fecha_fin)) {
    $filtros[] = "te.created_at BETWEEN '$fecha_inicio' AND '$fecha_fin'";
}
// Filtro para dpto 10 (Remuneraciones, solo registros con estado "aprobado")
if (array_intersect([10], $_SESSION['deptos'])) {
    $filtros[] = "(te.created_at >= DATE_SUB(DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY), INTERVAL 1 WEEK)
                   AND te.created_at < DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY))";
    $filtros[] = "te.estado = 'aprobado'";
} elseif (!empty($estado)) {
    $filtros[] = "te.estado = '$estado'";
}

// Generar condiciones WHERE para consulta
$where = !empty($filtros) ? "WHERE " . implode(" AND ", $filtros) : "";

$query = "
    SELECT
        '63320975' AS cuenta_cargo,
        dp.numero_cuenta AS numCuenta,
        dp.banco AS banco, 
        dp.rut_cta AS rut_cuenta,
        dp.digito_verificador AS rut_dv,
        te.monto AS monto,
        '' AS NroFactura,
        '' AS NroOrden,
        'otr' AS tipo_pago,
        '' AS mensajeDest,
        '' AS emailDest,
        te.nombre_colaborador AS CuentaDestino
    FROM turnos_extra te
    JOIN sucursales su ON te.sucursal_id = su.id
    JOIN datos_pago dp ON te.datos_bancarios_id = dp.id
    JOIN `user` us ON te.autorizado_por = us.id
    $where
    ORDER BY te.created_at DESC
";

echo $query;


$result = mysqli_query($con, $query);
if (!$result) {
    die("Error en la consulta SQL: " . mysqli_error($con));
}
$rowIndex = 2;
// Agregar datos a la hoja
while ($row = mysqli_fetch_assoc($result)) {
    // Convertir array asociativo a indexado
    $rowData = array_values($row);
    $sheet->fromArray([$rowData], NULL, 'A' . $rowIndex);
    $rowIndex++;
}

// Definir anchos de columna
$columnWidths = [
    'A' => 15, 'B' => 20, 'C' => 25, 'D' => 20, 'E' => 30, 'F' => 20,
    'G' => 15, 'H' => 15, 'I' => 25, 'J' => 15, 'K' => 15, 'L' => 20,
    'M' => 20
];

// Aplicar anchos
foreach ($columnWidths as $col => $width) {
    $sheet->getColumnDimension($col)->setWidth($width);
}
$fecha = date('dmY');
// Generar el archivo
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="Nomina_de_pago_'.$fecha.'.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');

exit();
?>