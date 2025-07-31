<?php
require '../../../../vendor/autoload.php'; 
require_once '../../../../dbconnection.php';

$formato = $_GET['formato'] ?? 'pdf';

$sucursal_id = (int)($_GET['sucursal_id'] ?? 0);
$colaborador_id = isset($_GET['colaborador_id']) ? (int)$_GET['colaborador_id'] : null;
$mes = (int)($_GET['mes'] ?? date('n'));
$anio = (int)($_GET['anio'] ?? date('Y'));

$datos = obtenerDatosCalendario($con, $sucursal_id, $colaborador_id, $mes, $anio);
$allD = alldata($con, $mes, $anio);

if ($formato === 'excel') {
    generarExcel($datos, $mes, $anio);
} else if ($formato ===  'all') {
    generarExcelMultiSucursal($allD, $mes, $anio);
} else {
    generarPdfCalendario($datos, $mes, $anio);
}


function obtenerDatosCalendario($con, $sucursal_id, $colaborador_id, $mes, $anio) {
    $query = "SELECT 
                hc.id AS horario_id,
                hc.fecha, 
                hc.tipo, 
                hc.hora_entrada, 
                hc.hora_salida, 
                ti.codigo,
                c.id AS colaborador_id,
                CONCAT(c.name, ' ', c.fname) AS nombre_colaborador
              FROM horarios_sucursal hc
              JOIN turnos_instalacion ti ON hc.turno_id = ti.id
              LEFT JOIN colaborador_turno ct ON hc.turno_id = ct.turno_id 
                AND hc.fecha BETWEEN ct.fecha_inicio AND ct.fecha_fin
              LEFT JOIN colaboradores c ON ct.colaborador_id = c.id
              WHERE hc.sucursal_id = ? 
                AND MONTH(hc.fecha) = ? 
                AND YEAR(hc.fecha) = ?";

    if ($colaborador_id) {
        $query .= " AND c.id = ?";
    }

    $query .= " ORDER BY hc.fecha, hc.hora_entrada, ti.codigo";

    $stmt = $con->prepare($query);
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['error' => 'Error en la preparación de la consulta: ' . $con->error]);
        exit;
    }

    if ($colaborador_id) {
        $stmt->bind_param("iiii", $sucursal_id, $mes, $anio, $colaborador_id);
    } else {
        $stmt->bind_param("iii", $sucursal_id, $mes, $anio);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $datos = [];
    while ($row = $result->fetch_assoc()) {
        $datos[] = $row;
    }

    return $datos;
}

function allData($con, $mes, $anio) {
   $query = "SELECT 
            hc.id AS horario_id,
            hc.fecha, 
            hc.tipo, 
            hc.hora_entrada, 
            hc.hora_salida, 
            ti.codigo,
            c.id AS colaborador_id,
            CONCAT(c.name, ' ', c.fname) AS nombre_colaborador,
            hc.sucursal_id,
            s.nombre AS nombre_sucursal
          FROM horarios_sucursal hc
          JOIN turnos_instalacion ti ON hc.turno_id = ti.id
          LEFT JOIN colaborador_turno ct ON hc.turno_id = ct.turno_id 
            AND hc.fecha BETWEEN ct.fecha_inicio AND ct.fecha_fin
          LEFT JOIN colaboradores c ON ct.colaborador_id = c.id
          JOIN sucursales s ON hc.sucursal_id = s.id
          WHERE MONTH(hc.fecha) = ? 
            AND YEAR(hc.fecha) = ?";

    $query .= " ORDER BY hc.fecha, hc.hora_entrada, ti.codigo";
    $stmt = $con->prepare($query);
    
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['error' => 'Error en la preparación de la consulta: ' . $con->error]);
        exit;
    }
    
    $stmt->bind_param("ii", $mes, $anio);


    $stmt->execute();
    $result = $stmt->get_result();

    $datos = [];
    while ($row = $result->fetch_assoc()) {
        $datos[] = $row;
    }
    // Agrupar datos por sucursal
    $datosPorSucursal = [];
    foreach ($datos as $dato) {
        $sucursalId = $dato['sucursal_id'];
        if (!isset($datosPorSucursal[$sucursalId])) {
            $datosPorSucursal[$sucursalId] = [];
        }
        $datosPorSucursal[$sucursalId][] = $dato;
    }

    return $datosPorSucursal;
}

function generarPdfCalendario($datos, $mes, $anio) {
    if (ob_get_length()) {
        ob_end_clean();
    }

    $pdf = new \TCPDF('P', 'mm', 'A4');
    $pdf->SetMargins(10, 10, 10);
    $pdf->AddPage();

    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Cell(0, 10, "Calendario $mes/$anio", 0, 1, 'C');

    $diasSemana = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
    $pdf->SetFont('helvetica', 'B', 10);

    $anchoCelda = 27;
    $altoCelda = 30;

    // Encabezado con días de la semana
    foreach ($diasSemana as $dia) {
        $pdf->Cell($anchoCelda, 8, $dia, 1, 0, 'C');
    }
    $pdf->Ln();

    $primerDia = mktime(0, 0, 0, $mes, 1, $anio);
    $diaSemana = (int)date('N', $primerDia);
    $diasMes = (int)date('t', $primerDia);

    $pdf->SetFont('helvetica', '', 8);

    $diaActual = 1;
    $columna = 1;

    while ($diaActual <= $diasMes) {
        if ($diaActual == 1 && $columna < $diaSemana) {
            $pdf->Cell($anchoCelda, $altoCelda, '', 1);
            $columna++;
            if ($columna > 7) {
                $columna = 1;
                $pdf->Ln();
            }
            continue;
        }

        $fechaActual = sprintf('%04d-%02d-%02d', $anio, $mes, $diaActual);

        // Buscar turnos para este día
        $contenido = "$diaActual\n";
        $tieneHorarios = false;

        foreach ($datos as $turno) {
            if ($turno['fecha'] === $fechaActual) {
                $colaborador = $turno['nombre_colaborador'] ?? 'Sin asignar';
                $contenido .= "{$turno['hora_entrada']} - {$turno['hora_salida']}\n$colaborador\n";
                $tieneHorarios = true;
            }
        }

        if (!$tieneHorarios) {
            $contenido .= "Libre\n";
            $pdf->SetFillColor(230, 230, 230);
            $relleno = true;
        } else {
            $relleno = false;
        }

        $pdf->MultiCell($anchoCelda, $altoCelda, $contenido, 1, 'L', $relleno, 0, '', '', true, 0, false, true, $altoCelda, 'M');

        $columna++;
        if ($columna > 7) {
            $columna = 1;
            $pdf->Ln();
        }
        $diaActual++;
    }

    if ($columna != 1) {
        while ($columna <= 7) {
            $pdf->Cell($anchoCelda, $altoCelda, '', 1);
            $columna++;
        }
    }

    $pdf->Output('calendario.pdf', 'D');
    exit;
}


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

function generarExcel($datos, $mes, $anio) {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle("Calendario $mes-$anio");

    $diasSemana = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];

    // Escribir encabezado (días de la semana)
    foreach ($diasSemana as $i => $dia) {
        $col = $i + 1;
        $cell = Coordinate::stringFromColumnIndex($col) . '1';
        $sheet->setCellValue($cell, $dia);
        $sheet->getStyle($cell)->getFont()->setBold(true);
    }

    $primerDia = mktime(0, 0, 0, $mes, 1, $anio);
    $diaSemana = (int)date('N', $primerDia); // 1 (Lun) a 7 (Dom)
    $diasMes = (int)date('t', $primerDia);

    $row = 2;
    $col = 1;
    $diaActual = 1;

    // Celdas vacías antes del primer día
    for ($i = 1; $i < $diaSemana; $i++) {
        $col++;
    }

    while ($diaActual <= $diasMes) {
        $fecha = sprintf('%04d-%02d-%02d', $anio, $mes, $diaActual);
        $turnosDia = array_filter($datos, fn($d) => $d['fecha'] === $fecha);

        // Contenido de la celda
        $contenido = "$diaActual\n";
        if (count($turnosDia) === 0) {
            $contenido .= "Libre";
        } else {
            foreach ($turnosDia as $turno) {
                $colaborador = $turno['nombre_colaborador'] ?? 'Sin asignar';
                $contenido .= "{$turno['hora_entrada']} - {$turno['hora_salida']}\n$colaborador\n";
            }
        }

        $cell = Coordinate::stringFromColumnIndex($col) . $row;
        $sheet->setCellValue($cell, $contenido);
        $sheet->getRowDimension($row)->setRowHeight(80);
        $sheet->getColumnDimensionByColumn($col)->setWidth(25);

        // Estilo de celda
        $style = $sheet->getStyle($cell);
        $style->getAlignment()->setWrapText(true)->setVertical('top');
        $style->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        if (count($turnosDia) === 0) {
            $style->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFEEEEEE');
        }

        // Avanzar a la siguiente columna / fila
        $col++;
        if ($col > 7) {
            $col = 1;
            $row++;
        }

        $diaActual++;
    }

    // Encabezados de descarga
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="calendario.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}

function generarExcelMultiSucursal($datosPorSucursal, $mes, $anio) {
    $spreadsheet = new Spreadsheet();

    $spreadsheet->removeSheetByIndex(0);
    
    $diasSemana = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
    
    foreach ($datosPorSucursal as $sucursalId => $datos) {
        $ns = $datos['nombre_sucursal'];
        // nueva hoja para cada sucursal
        $sheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, "Sucursal $ns");
        $spreadsheet->addSheet($sheet);
        $sheet->setTitle("Sucursal $ns"); 
        
        //  (días de la semana)
        foreach ($diasSemana as $i => $dia) {
            $col = $i + 1;
            $cell = Coordinate::stringFromColumnIndex($col) . '1';
            $sheet->setCellValue($cell, $dia);
            $sheet->getStyle($cell)->getFont()->setBold(true);
        }
        
        $primerDia = mktime(0, 0, 0, $mes, 1, $anio);
        $diaSemana = (int)date('N', $primerDia); 
        $diasMes = (int)date('t', $primerDia);
        
        $row = 2;
        $col = 1;
        $diaActual = 1;
        
        for ($i = 1; $i < $diaSemana; $i++) {
            $col++;
        }
        
        while ($diaActual <= $diasMes) {
            $fecha = sprintf('%04d-%02d-%02d', $anio, $mes, $diaActual);
            $turnosDia = array_filter($datos, fn($d) => $d['fecha'] === $fecha);
            
            // Contenido de la celda
            $contenido = "$diaActual\n";
            if (count($turnosDia) === 0) {
                $contenido .= "Libre";
            } else {
                foreach ($turnosDia as $turno) {
                    $colaborador = $turno['nombre_colaborador'] ?? 'Sin asignar';
                    $contenido .= "{$turno['hora_entrada']} - {$turno['hora_salida']}\n$colaborador\n";
                }
            }
            
            $cell = Coordinate::stringFromColumnIndex($col) . $row;
            $sheet->setCellValue($cell, $contenido);
            $sheet->getRowDimension($row)->setRowHeight(80);
            $sheet->getColumnDimensionByColumn($col)->setWidth(25);
            
            // Estilo de celda
            $style = $sheet->getStyle($cell);
            $style->getAlignment()->setWrapText(true)->setVertical('top');
            $style->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            
            if (count($turnosDia) === 0) {
                $style->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFEEEEEE');
            }
            
            // Avanzar a la siguiente columna / fila
            $col++;
            if ($col > 7) {
                $col = 1;
                $row++;
            }
            
            $diaActual++;
        }
    }
    
    // Encabezados de descarga
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="calendario_sucursales.xlsx"');
    header('Cache-Control: max-age=0');
    
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}
