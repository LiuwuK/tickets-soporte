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
        'startColor' => ['rgb' => 'FFE2EFDA'] //(verde claro)
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
    'Turno ID', 'Fecha de Subida', 'Estado', 'Autorizado Por', 'Instalación','Supervisor',
    'Fecha del Turno (DIA-MES-AÑO)', 'Horas cubiertas', 'Monto',
    'Nombre y Apellido', 'RUT', 'Nacionalidad',
    'Banco', 'RUT Cuenta', 'Número de cuenta',
    'Motivo', 'Persona del Motivo', 'Motivo Rechazo', 'Justificación',
    'Contratado'
];

// Aplicar estilos a los encabezados
$sheet->fromArray([$headers], NULL, 'A1'); 
$sheet->getStyle('A1:I1')->applyFromArray($styleColor1);
$sheet->getStyle('J1:L1')->applyFromArray($styleColor2);
$sheet->getStyle('M1:O1')->applyFromArray($styleColor3); 
$sheet->getStyle('P1:T1')->applyFromArray($styleColor1);

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
// Filtro para dpto 10 (Solo registros con estado "aprobado")
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
        te.id AS idTurno,       
        te.created_at AS fechaCreacion,          
        te.estado AS estado, 
        us.name AS autorizadoPor,
        su.nombre AS instalacion,
        sup.nombre_supervisor AS supervisor,
        te.fecha_turno AS fechaTurno, 
        te.horas_cubiertas AS horas, 
        te.monto AS monto,
        te.nombre_colaborador AS colaborador, 
        te.rut AS rut,
        te.nacionalidad AS nacionalidad,  
        bc.nombre_banco AS banco, 
        CONCAT(IFNULL(dp.rut_cta, ''), '-', IFNULL(dp.digito_verificador, '')) AS RUTcta, 
        dp.numero_cuenta AS numCuenta,
        mg.motivo AS motivo,
        te.persona_motivo AS persona_motivo,
        te.motivo_rechazo AS motivoN,
        te.justificacion AS justificacion,
        te.contratado AS contratado
    FROM turnos_extra te
    JOIN sucursales su ON te.sucursal_id = su.id
    JOIN supervisores sup ON su.supervisor_id = sup.id
    JOIN datos_pago dp ON te.datos_bancarios_id = dp.id
    JOIN bancos bc ON dp.banco = bc.id
    JOIN motivos_gestion mg ON te.motivo_turno_id = mg.id
    JOIN `user` us ON te.autorizado_por = us.id
    $where
    ORDER BY te.created_at DESC
";

$result = mysqli_query($con, $query);
if (!$result) {
    die("Error en la consulta SQL: " . mysqli_error($con));
}
$rowIndex = 2;
// Agregar datos a la hoja
while ($row = mysqli_fetch_assoc($result)) {
    // Formatear fechas
    $fechaTurno = DateTime::createFromFormat('Y-m-d', $row['fechaTurno']);
    $row['fechaTurno'] = $fechaTurno ? $fechaTurno->format('d-m-Y') : '';

    $fechaCreacion = DateTime::createFromFormat('Y-m-d H:i:s', $row['fechaCreacion']);
    $row['fechaCreacion'] = $fechaCreacion ? $fechaCreacion->format('d-m-Y H:i:s') : '';

    // Formatear "contratado"
    $row['contratado'] = ($row['contratado'] == 1) ? "SI" : "NO";

    // Convertir array asociativo a indexado
    $rowData = array_values($row);
    $sheet->fromArray([$rowData], NULL, 'A' . $rowIndex);
    $rowIndex++;
}

// Definir anchos de columna
$columnWidths = [
    'A' => 15, 'B' => 20, 'C' => 25, 'D' => 20, 'E' => 30, 'F' => 20,
    'G' => 15, 'H' => 15, 'I' => 25, 'J' => 15, 'K' => 15, 'L' => 20,
    'M' => 20, 'N' => 20, 'O' => 25, 'P' => 30, 'Q' => 20, 'R' => 30,
    'S' => 20, 'T' => 15
];

// Aplicar anchos
foreach ($columnWidths as $col => $width) {
    $sheet->getColumnDimension($col)->setWidth($width);
}

// Generar el archivo
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="turnos.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');

exit();
?>