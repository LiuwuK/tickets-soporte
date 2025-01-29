<?php
setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES', 'esp');
require __DIR__ . '/../../vendor/autoload.php'; 
include('../../dbconnection.php');
use Fpdf\Fpdf; 

$dia = strftime('%A %d-%m-%Y'); 

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, utf8_decode('Bitácora Día ').$dia, 0, 1, 'C');

// Obtiene actividades del día
$user = $_GET['userId'];
$hoy = date('Y-m-d');
$query = "SELECT wl.titulo, wl.descripcion, ta.nombre_actividad AS tAct, 
                 DATE_FORMAT(wl.fecha, '%h:%i %p') AS hora
          FROM work_log wl
          JOIN tipo_actividades ta ON (wl.tipo_actividad = ta.id)
          WHERE DATE(fecha) = ? AND wl.user_id = ?
          ORDER BY wl.fecha ASC";
$stmt = $con->prepare($query);
$stmt->bind_param('si', $hoy, $user);
$stmt->execute();
$result = $stmt->get_result();

$pdf->SetFont('Arial', '', 12);
$pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
while ($actividad = $result->fetch_assoc()) {
    $pdf->Ln(2);
    $pdf->Cell(0, 7, 'Titulo: ' . utf8_decode($actividad['titulo']), 0, 1);
    $pdf->Cell(0, 7, 'Tipo: ' . utf8_decode($actividad['tAct']), 0, 1);
    $pdf->Cell(0, 7, 'Hora: ' . $actividad['hora'], 0, 1);
    $pdf->MultiCell(0, 7, 'Descripcion: ' . utf8_decode($actividad['descripcion']), 0, 1);
    $pdf->Ln(2);
    $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
}

// Descargar el PDF
$pdf->Output('D', 'Bitacora_' . $hoy . '.pdf');
?>