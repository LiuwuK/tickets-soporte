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
    'Turno ID', 'Fecha de Subida', 'Estado', 'Autorizado Por', 'Instalación','Centro de Costo', 'Supervisor',
    'Fecha del Turno (DIA-MES-AÑO)','Hora Entrada', 'Hora Salida', 'Horas cubiertas', 'Monto',
    'Nombre y Apellido', 'RUT', 'DV', 'Nacionalidad',
    'Banco', 'RUT Cuenta', 'DV Cuenta', 'Número de cuenta',
    'Motivo', 'Persona del Motivo', 'Motivo Rechazo', 'Justificación',
    'Contratado'
];

// Aplicar estilos a los encabezados
$sheet->fromArray([$headers], NULL, 'A1'); 
$sheet->getStyle('A1:L1')->applyFromArray($styleColor1);
$sheet->getStyle('M1:P1')->applyFromArray($styleColor2);
$sheet->getStyle('Q1:T1')->applyFromArray($styleColor3); 
$sheet->getStyle('U1:Y1')->applyFromArray($styleColor1);

// Capturar filtros
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : '';
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : '';
$estado = isset($_GET['estado']) ? $_GET['estado'] : '';
$supervisor = isset($_GET['supervisor']) ? $_GET['supervisor'] : '';

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

if (!empty($supervisor)){
    $filtros[] = "te.autorizado_por = '$supervisor'";
}

// Generar condiciones WHERE para consulta
$where = !empty($filtros) ? "WHERE " . implode(" AND ", $filtros) : "";

$query = "
    SELECT
        te.id AS idTurno,       
        te.created_at AS fechaCreacion,          
        te.estado AS estado, 
        us.name AS autorizadoPor,
        CASE WHEN su.id IS NULL THEN 'SPOT' ELSE su.nombre END AS instalacion,
        dep.nombre_departamento,
        sup.nombre_supervisor AS supervisor,
        te.fecha_turno AS fechaTurno,
        TIME_FORMAT(te.hora_inicio, '%H:%i') AS hora_entrada, 
        TIME_FORMAT(te.hora_termino, '%H:%i') AS hora_salida,
        te.horas_cubiertas AS horas, 
        te.monto AS monto,
        te.nombre_colaborador AS colaborador, 
        REGEXP_REPLACE(
        IF(te.rut LIKE '%-%', 
            SUBSTRING_INDEX(te.rut, '-', 1), 
            LEFT(te.rut, LENGTH(te.rut) - 1)
            ),
        '[^0-9]', '') AS rut,
        RIGHT(te.rut, 1)  AS digito_verificador,
        te.nacionalidad AS nacionalidad,  
        bc.nombre_banco AS banco, 
        IFNULL(dp.rut_cta, '') AS rutcta, 
        IFNULL(dp.digito_verificador, '') AS dvcuenta, 
        dp.numero_cuenta AS numCuenta,
        mg.motivo AS motivo,
        te.persona_motivo AS persona_motivo,
        te.motivo_rechazo AS motivoN,
        te.justificacion AS justificacion,
        te.contratado AS contratado
    FROM turnos_extra te
    LEFT JOIN sucursales su ON te.sucursal_id = su.id
    LEFT JOIN supervisores sup ON su.supervisor_id = sup.id
    LEFT JOIN datos_pago dp ON te.datos_bancarios_id = dp.id
    LEFT JOIN bancos bc ON dp.banco = bc.id
    LEFT JOIN motivos_gestion mg ON te.motivo_turno_id = mg.id
    LEFT JOIN `user` us ON te.autorizado_por = us.id
    LEFT JOIN departamentos dep ON dep.id = su.departamento_id 
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
    $fechaCreacion = DateTime::createFromFormat('Y-m-d H:i:s', $row['fechaCreacion']);
    $row['fechaCreacion'] = $fechaCreacion ? $fechaCreacion->format('d-m-Y H:i:s') : '';

    // Formatear fechas
    $fechaTurno = DateTime::createFromFormat('Y-m-d', $row['fechaTurno']);
    $row['fechaTurno'] = $fechaTurno ? $fechaTurno->format('d-m-Y') : '';
    
    // Formatear "contratado"
    $row['contratado'] = ($row['contratado'] == 1) ? "SI" : "NO";

    // Convertir array asociativo a indexado
    $rowData = array_values($row);
    $sheet->fromArray([$rowData], NULL, 'A' . $rowIndex);
    $rowIndex++;
}

//Aplicar formato pesos
$lastRow = $rowIndex - 1;
$sheet->getStyle("L2:L{$lastRow}")
    ->getNumberFormat()
    ->setFormatCode('"$"#,##0');

// Definir anchos de columna
$columnWidths = [
    'A' => 15, 'B' => 20, 'C' => 25, 'D' => 20, 'E' => 30, 'F' => 20,
    'G' => 15, 'H' => 15, 'I' => 15, 'J' => 15, 'K' => 15, 'L' => 20,
    'M' => 20, 'N' => 20, 'O' => 25, 'P' => 30, 'Q' => 20, 'R' => 30,
    'S' => 20, 'T' => 20, 'U' => 20, 'V' => 20, 'W' => 20, 
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