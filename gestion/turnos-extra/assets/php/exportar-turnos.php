<?php
session_start();
include("../../../../dbconnection.php");
require '../../../../vendor/autoload.php'; // PHPSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

// Función para construir filtros
function construirFiltros($input) {
    $where = [];
    $params = [];
    $types = '';

    // Supervisor
    if ($_SESSION['cargo'] == 11) {
        $where[] = "te.autorizado_por = ?";
        $params[] = $_SESSION['id'];
        $types .= 'i';
    } elseif (!empty($input['supervisor'])) {
        $where[] = "te.autorizado_por = ?";
        $params[] = $input['supervisor'];
        $types .= 'i';
    }

    // Estado
    if (!empty($input['estado'])) {
        $where[] = "te.estado = ?";
        $params[] = $input['estado'];
        $types .= 's';
    }

    // Fechas
    if (!empty($input['fecha_inicio']) && !empty($input['fecha_fin'])) {
        $where[] = "te.fecha_turno BETWEEN ? AND ?";
        $params[] = $input['fecha_inicio'];
        $params[] = $input['fecha_fin'];
        $types .= 'ss';
    }

    // Texto búsqueda
    if (!empty($input['texto'])) {
        $search = '%' . $input['texto'] . '%';
        $where[] = "(te.nombre_colaborador LIKE ? OR te.rut LIKE ? OR us.name LIKE ? OR mg.motivo LIKE ? OR su.nombre LIKE ? OR te.estado LIKE ?)";
        for ($i = 0; $i < 6; $i++) $params[] = $search;
        $types .= str_repeat('s', 6);
    }

    // Dpto 10: solo últimos turnos aprobados
    if (array_intersect([10], $_SESSION['deptos'])) {
        $where[] = "(te.fecha_turno >= DATE_SUB(DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY), INTERVAL 1 WEEK)
                     AND te.fecha_turno < DATE_SUB(CURDATE(), INTERVAL WEEKDAY(CURDATE()) DAY))";
        $where[] = "te.estado = 'aprobado'";
    }

    $whereSQL = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

    return [$whereSQL, $params, $types];
}

// Capturar filtros de GET
$input = [
    'fecha_inicio' => $_GET['fecha_inicio'] ?? '',
    'fecha_fin' => $_GET['fecha_fin'] ?? '',
    'estado' => $_GET['estado'] ?? '',
    'supervisor' => $_GET['supervisor'] ?? '',
    'texto' => $_GET['texto'] ?? ''
];

list($whereSQL, $params, $types) = construirFiltros($input);

// Query principal 
$query = "
    SELECT
        te.id AS idTurno,
        DATE_FORMAT(te.created_at, '%d-%m-%Y %H:%i:%s') AS fechaCreacion,
        te.estado,
        us.name AS autorizadoPor,
        COALESCE(su.nombre, 'SPOT') AS instalacion,
        dep.nombre_departamento,
        sup.nombre_supervisor AS supervisor,
        DATE_FORMAT(te.fecha_turno, '%d-%m-%Y') AS fechaTurno,
        TIME_FORMAT(te.hora_inicio, '%H:%i') AS hora_entrada,
        TIME_FORMAT(te.hora_termino, '%H:%i') AS hora_salida,
        te.horas_cubiertas AS horas,
        te.monto,
        te.nombre_colaborador AS colaborador,
        REGEXP_REPLACE(
            IF(te.rut LIKE '%-%', SUBSTRING_INDEX(te.rut, '-', 1), LEFT(te.rut, LENGTH(te.rut)-1)),
            '[^0-9]', ''
        ) AS rut,
        RIGHT(te.rut, 1) AS digito_verificador,
        te.nacionalidad,
        bc.nombre_banco AS banco,
        IFNULL(dp.rut_cta, '') AS rutcta,
        IFNULL(dp.digito_verificador, '') AS dvcuenta,
        dp.numero_cuenta AS numCuenta,
        mg.motivo,
        te.persona_motivo,
        CASE WHEN COUNT(ht.turno_id) > 0 OR te.estado='rechazado' THEN 'Sí' ELSE 'No' END AS tiene_historico,
        te.motivo_rechazo,
        te.justificacion,
        CASE WHEN te.contratado=1 THEN 'SI' ELSE 'NO' END AS contratado
    FROM turnos_extra te
    LEFT JOIN sucursales su ON te.sucursal_id = su.id
    LEFT JOIN supervisores sup ON su.supervisor_id = sup.id
    LEFT JOIN datos_pago dp ON te.datos_bancarios_id = dp.id
    LEFT JOIN bancos bc ON dp.banco = bc.id
    LEFT JOIN motivos_gestion mg ON te.motivo_turno_id = mg.id
    LEFT JOIN `user` us ON te.autorizado_por = us.id
    LEFT JOIN departamentos dep ON dep.id = su.departamento_id
    LEFT JOIN historico_turnos ht ON ht.turno_id = te.id
    $whereSQL
    GROUP BY te.id
    ORDER BY te.created_at DESC
";

// Preparar y ejecutar
$stmt = $con->prepare($query);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

// Generar Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Encabezados
$headers = [
    'Turno ID', 'Fecha de Subida', 'Estado', 'Autorizado Por', 'Instalación','Centro de Costo', 'Supervisor',
    'Fecha del Turno','Hora Entrada', 'Hora Salida', 'Horas cubiertas', 'Monto',
    'Nombre y Apellido', 'RUT', 'DV', 'Nacionalidad',
    'Banco', 'RUT Cuenta', 'DV Cuenta', 'Número de cuenta',
    'Motivo', 'Persona del Motivo', 'Fue Rechazado?','Motivo Rechazo','Justificación','Contratado'
];
$sheet->fromArray([$headers], NULL, 'A1');

// Estilo encabezados
$styleHeader = [
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb'=>'FFD9E1F2']],
    'font' => ['bold'=>true], 
    'alignment'=>['horizontal'=>Alignment::HORIZONTAL_CENTER]
];
$sheet->getStyle('A1:Z1')->applyFromArray($styleHeader);

// Llenar datos
$rowIndex = 2;
while ($row = $result->fetch_assoc()) {
    $sheet->fromArray(array_values($row), NULL, 'A'.$rowIndex);
    $rowIndex++;
}

// Formato pesos
$sheet->getStyle("L2:L{$rowIndex}")
    ->getNumberFormat()
    ->setFormatCode('"$"#,##0');

// Anchos fijos
$widths = [
    'A'=>15,'B'=>20,'C'=>25,'D'=>20,'E'=>30,'F'=>20,'G'=>15,'H'=>15,'I'=>15,'J'=>15,'K'=>15,'L'=>20,
    'M'=>20,'N'=>20,'O'=>25,'P'=>30,'Q'=>20,'R'=>30,'S'=>20,'T'=>20,'U'=>20,'V'=>20,'W'=>20,'X'=>30,'Y'=>40,'Z'=>20
];
foreach($widths as $col=>$w) $sheet->getColumnDimension($col)->setWidth($w);

// Exportar
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="turnos.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit();
?>
