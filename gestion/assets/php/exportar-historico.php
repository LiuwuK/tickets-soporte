<?php
session_start();
include("../../../dbconnection.php");
require '../../../vendor/autoload.php'; // PHPSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();


// Capturar filtros desde la URL
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : '';
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : '';
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : '';
$estado = isset($_GET['estado']) ? $_GET['estado'] : '';
// Filtros para cada consulta
$filtrosTraslados = [];
$filtrosDesvinculaciones = [];

// Filtro por fecha
if (!empty($fecha_inicio) && !empty($fecha_fin)) {
    $filtrosTraslados[] = "tr.fecha_registro BETWEEN '$fecha_inicio' AND '$fecha_fin'";
    $filtrosDesvinculaciones[] = "de.fecha_registro BETWEEN '$fecha_inicio' AND '$fecha_fin'";
}

// Filtro por tipo (Traslado o Desvinculación)
if ($tipo === "traslado") {
    $filtrosDesvinculaciones[] = "1=0"; // No incluir desvinculaciones
}
if ($tipo === "desvinculación") {
    $filtrosTraslados[] = "1=0"; // No incluir traslados
}

// Filtro para cargo 13 (Solo registros con estado "realizado")
if ($_SESSION['cargo'] == 13) {
    $filtrosTraslados[] = "tr.estado = 'realizado'";
    $filtrosDesvinculaciones[] = "de.estado = 'realizado'";
}else{
    if(!empty($estado)){
        $filtrosTraslados[] = "tr.estado = '$estado'";
        $filtrosDesvinculaciones[] = "de.estado = '$estado'";
    }
}

// Generar condiciones WHERE para cada consulta
$whereTraslados = !empty($filtrosTraslados) ? "WHERE " . implode(" AND ", $filtrosTraslados) : "";
$whereDesvinculaciones = !empty($filtrosDesvinculaciones) ? "WHERE " . implode(" AND ", $filtrosDesvinculaciones) : "";

// Encabezados de la tabla
$headers = [
    'Estado','Tipo', 'Fecha de Registro', 'Hora de registro', 'Solicitante', 'Supervisor Origen', 'Colaborador', 'RUT', 
    'Sucursal Origen', 'Jornada Origen', 'Rol Origen', 'Motivo', 
    'Sucursal Destino', 'Jornada Destino','Rol Destino', 'Fecha de Inicio Turno', 'Supervisor Destino', 
    'Observacion', 'Observacion RRHH'
];
$sheet->fromArray([$headers], NULL, 'A1');

// Consulta de Traslados
$queryTraslados = "
    SELECT  tr.estado AS estadoN,
            'Traslado' AS tipo,
            DATE(tr.fecha_registro) AS fecha_registro, 
            TIME(tr.fecha_registro) AS hora_registro,
            us.name AS soliN,
            sup_origen.nombre_supervisor AS supOrigen, 
            tr.nombre_colaborador AS colaborador, 
            tr.rut AS rutC,
            su_origen.nombre AS suOrigen,
            jo_origen.tipo_jornada AS joOrigen,
            rol_origen.nombre_rol AS rolOrigen, 
            mg.motivo AS motivoN, 
            su_destino.nombre AS suDestino, 
            jo_destino.tipo_jornada AS joDestino,
            rol_destino.nombre_rol AS rolDestino,
            tr.fecha_inicio_turno AS fecha_turno,
            sup_destino.nombre_supervisor AS supDestino,
            tr.observacion AS observacion,
            tr.obs_rrhh AS obs_rrhh
    FROM traslados tr
    JOIN user us ON tr.solicitante = us.id
    JOIN sucursales su_origen ON tr.instalacion_origen = su_origen.id
    JOIN sucursales su_destino ON tr.instalacion_destino = su_destino.id
    JOIN jornadas jo_origen ON tr.jornada_origen = jo_origen.id
    JOIN jornadas jo_destino ON tr.jornada_destino = jo_destino.id
    JOIN supervisores sup_origen ON tr.supervisor_origen = sup_origen.id
    JOIN supervisores sup_destino ON tr.supervisor_destino = sup_destino.id
    JOIN motivos_gestion mg ON tr.motivo_traslado = mg.id
    JOIN roles rol_origen ON tr.rol_origen = rol_origen.id
    JOIN roles rol_destino ON tr.rol_destino = rol_destino.id
    $whereTraslados
";
$queryTraslados .= "ORDER BY tr.fecha_registro DESC";

$resultTraslados = mysqli_query($con, $queryTraslados);

// Consulta de Desvinculaciones
$queryDesvinculaciones = "
    SELECT  de.estado AS estadoN,
            'Desvinculación' AS tipo,
            DATE(de.fecha_registro) AS fecha_registro, 
            TIME(de.fecha_registro) AS hora_registro,
            us.name AS soliN, 
            sup.nombre_supervisor AS supOrigen, 
            de.colaborador AS colaborador, 
            de.rut AS rutC,  
            su.nombre AS suOrigen, 
            '' AS joOrigen,
            '' AS rolOrigen, 
            mo.motivo AS motivoN,
            '' AS suDestino, 
            '' AS joDestino,
            '' AS rolDestino, 
            '' AS fecha_turno, 
            '' AS supDestino, 
            de.observacion AS observacion,
            de.obs_rrhh AS obs_rrhh
    FROM desvinculaciones de
    JOIN user us ON de.solicitante = us.id
    JOIN sucursales su ON de.instalacion = su.id
    JOIN supervisores sup ON de.supervisor_origen = sup.id
    JOIN motivos_gestion mo ON de.motivo = mo.id
    $whereDesvinculaciones
";
$queryTraslados .= "ORDER BY de.fecha_registro DESC";

$resultDesvinculaciones = mysqli_query($con, $queryDesvinculaciones);

$rowIndex = 2; // Comenzamos desde la fila 2

// Agregar Traslados
while ($row = mysqli_fetch_assoc($resultTraslados)) {
    $sheet->fromArray([$row], NULL, 'A' . $rowIndex);
    $rowIndex++;
}

// Agregar Desvinculaciones
while ($row = mysqli_fetch_assoc($resultDesvinculaciones)) {
    $sheet->fromArray([$row], NULL, 'A' . $rowIndex);
    $rowIndex++;
}

// Definir anchos de columna
$columnWidths = [
    'A' => 25, 'B' => 15, 'C' => 18, 'D' => 30, 'E' => 20, 'F' => 30,
    'G' => 20, 'H' => 20, 'I' => 25, 'J' => 25, 'K' => 30, 'L' => 30,
    'M' => 30, 'N' => 18, 'O' => 25, 'P' => 25, 'Q' => 30, 'R' => 35,
    'S' => 35,
];

// Aplicar anchos
foreach ($columnWidths as $col => $width) {
    $sheet->getColumnDimension($col)->setWidth($width);
}

// Generar el archivo
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="historico.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');

exit();
?>
