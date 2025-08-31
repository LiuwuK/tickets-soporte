<?php
session_start();
include("../../../../dbconnection.php");
require '../../../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

// Función para construir filtros
function construirFiltrosPagos($input) {
    $where = [];
    $params = [];
    $types = '';

    if (!empty($input['fecha_inicio']) && !empty($input['fecha_fin'])) {
        $where[] = "te.created_at BETWEEN ? AND ?";
        $params[] = $input['fecha_inicio'];
        $params[] = $input['fecha_fin'];
        $types .= 'ss';
    }

    if (array_intersect([10], $_SESSION['deptos'])) {
        $where[] = "(te.created_at >= DATE_SUB(DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY), INTERVAL 1 WEEK)
                     AND te.created_at < DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY))";
        $where[] = "te.estado = 'aprobado'";
    } elseif (!empty($input['estado'])) {
        $where[] = "te.estado = ?";
        $params[] = $input['estado'];
        $types .= 's';
    }

    $whereSQL = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
    return [$whereSQL, $params, $types];
}

// Capturar filtros GET
$input = [
    'fecha_inicio' => $_GET['fecha_inicio'] ?? '',
    'fecha_fin' => $_GET['fecha_fin'] ?? '',
    'estado' => $_GET['estado'] ?? ''
];
list($whereSQL, $params, $types) = construirFiltrosPagos($input);

// Crear Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Encabezados
$headers = [
    'Nº Cuenta de Cargo', 'Nº Cuenta de Destino', 'Banco Destino', 
    'Rut beneficiario', 'Dig. Verif. Beneficiario','Monto Transferencia',
    'Nº Factura Boleta (1)', 'Nº Orden de Compra(1)',
    'Tipo de Pago(2)',
    'Mensaje Destinatario (3)', 'Email Destinatario(3)',
    'Cuenta Destino inscrita como(4)',
];
$sheet->fromArray([$headers], NULL, 'A1');

// Aplicar estilo solo a encabezados
$styleHeader = [
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb'=>'FFD9E1F2']],
    'font' => ['bold'=>true], 
    'alignment'=>['horizontal'=>Alignment::HORIZONTAL_CENTER]
];
$sheet->getStyle('A1:L1')->applyFromArray($styleHeader);

// Exportación por chunks
$rowIndex = 2;
$chunkSize = 100;
$offset = 0;

while (true) {
    $query = "
        SELECT
            '63320975' AS cuenta_cargo,
            dp.numero_cuenta AS numCuenta,
            bc.codigo AS banco, 
            dp.rut_cta AS rut_cuenta,
            dp.digito_verificador AS rut_dv,
            te.monto,
            '' AS NroFactura,
            '' AS NroOrden,
            'otr' AS tipo_pago,
            '' AS mensajeDest,
            '' AS emailDest,
            te.nombre_colaborador AS CuentaDestino
        FROM turnos_extra te
        JOIN sucursales su ON te.sucursal_id = su.id
        JOIN datos_pago dp ON te.datos_bancarios_id = dp.id
        JOIN bancos bc ON bc.id = dp.banco
        JOIN `user` us ON te.autorizado_por = us.id
        $whereSQL
        ORDER BY te.created_at DESC
        LIMIT $chunkSize OFFSET $offset
    ";

    $stmt = $con->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) break;

    while ($row = $result->fetch_assoc()) {
        $sheet->fromArray(array_values($row), NULL, 'A'.$rowIndex);
        $rowIndex++;
    }

    $offset += $chunkSize;
}

// Ajustar ancho de columnas
$columnWidths = [
    'A'=>15,'B'=>20,'C'=>25,'D'=>20,'E'=>30,'F'=>20,
    'G'=>15,'H'=>15,'I'=>25,'J'=>15,'K'=>15,'L'=>30
];
foreach ($columnWidths as $col=>$w) $sheet->getColumnDimension($col)->setWidth($w);

// Exportar archivo
$fecha = date('dmY');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="Nomina_de_pago_'.$fecha.'.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit();
?>
