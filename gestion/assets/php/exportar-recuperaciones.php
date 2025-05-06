<?php
require '../../../vendor/autoload.php';
require '../../../dbconnection.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Crear nuevo documento Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$query = "SELECT id, nombre 
            FROM sucursales 
            ORDER BY nombre";
// 1. Obtener todas las sucursales
$sucursales = $con->query($query);

// 2. Obtener todos los días del mes actual
$dias_mes = range(1, date('t'));
$mes_actual = date('m');
$anio_actual = date('Y');

// 3. Configurar cabeceras
$sheet->setCellValue('A1', 'SUCURSAL');
$columna = 'B';
foreach ($dias_mes as $dia) {
    $fecha_formato = str_pad($dia, 2, '0', STR_PAD_LEFT).'/'.$mes_actual.'/'.$anio_actual;
    $sheet->setCellValue($columna.'1', $fecha_formato);
    $columna++;
}

// 4. Llenar datos
$fila = 2;
while ($sucursal = $sucursales->fetch_assoc()) {
    $sheet->setCellValue('A'.$fila, $sucursal['nombre']);
    
    $columna = 'B';
    foreach ($dias_mes as $dia) {
        $fecha = $anio_actual.'-'.$mes_actual.'-'.str_pad($dia, 2, '0', STR_PAD_LEFT);
        
        $query = $con->query("
            SELECT IFNULL(SUM(monto), 0) AS total 
            FROM recuperaciones 
            WHERE sucursal_id = ".$sucursal['id']." 
            AND DATE_FORMAT(fecha, '%Y-%m-%d') = '$fecha'");
        
        $monto = $query->fetch_assoc()['total'];
        $sheet->setCellValue($columna.$fila, $monto);
        
        // Formato de moneda
        $sheet->getStyle($columna.$fila)
              ->getNumberFormat()
              ->setFormatCode('"$"#,##0');
              
        $columna++;
    }
    $fila++;
}

// 5. Autoajustar columnas
foreach (range('A', $columna) as $col) {
    $sheet->getColumnDimension($col)->setWidth(30);
}

// 6. Descargar el archivo
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="reporte_mensual_'.$mes_actual.'_'.$anio_actual.'.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>