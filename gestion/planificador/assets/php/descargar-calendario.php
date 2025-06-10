<?php
require '../../../../vendor/autoload.php'; 
require_once '../../../../dbconnection.php';

$formato = $_GET['formato'] ?? 'pdf';
$sucursal_id = (int)($_GET['sucursal_id'] ?? 0);
$colaborador_id = isset($_GET['colaborador_id']) ? (int)$_GET['colaborador_id'] : null;
$mes = (int)($_GET['mes'] ?? date('n'));
$anio = (int)($_GET['anio'] ?? date('Y'));

$datos = obtenerDatosCalendario($con, $sucursal_id, $colaborador_id, $mes, $anio);

if ($formato === 'excel') {
    generarExcel($datos, $mes, $anio);
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

function generarPdfCalendario($datos, $mes, $anio) {
    if (ob_get_length()) {
        ob_end_clean();
    }

    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="calendario.pdf"');
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Pragma: public');

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

    // Datos para el mes
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

        // Armar contenido de la celda
        $contenido = "$fechaActual\n";

        foreach ($datos as $turno) {
            if ($turno['fecha'] === $fechaActual) {
                $colaborador = $turno['nombre_colaborador'] ?? 'Sin asignar';
                $contenido .= "{$turno['hora_entrada']} - {$turno['hora_salida']}\n$colaborador\n";
            }
        }

        $pdf->MultiCell($anchoCelda, $altoCelda, $contenido, 1, 'L', false, 0, '', '', true, 0, false, true, $altoCelda, 'M');

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

function generarExcel($datos, $mes, $anio) {
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle("Calendario $mes-$anio");

    $sheet->fromArray(
        [['Fecha', 'Entrada', 'Salida', 'Tipo', 'Código', 'Colaborador']],
        NULL, 'A1'
    );

    $row = 2;
    foreach ($datos as $d) {
        $colaborador = $d['nombre_colaborador'] ?? 'Sin asignar';
        $sheet->setCellValue("A$row", $d['fecha']);
        $sheet->setCellValue("B$row", $d['hora_entrada']);
        $sheet->setCellValue("C$row", $d['hora_salida']);
        $sheet->setCellValue("D$row", $d['tipo']);
        $sheet->setCellValue("E$row", $d['codigo']);
        $sheet->setCellValue("F$row", $colaborador);
        $row++;
    }

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="calendario.xlsx"');

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save("php://output");
    exit;
}
