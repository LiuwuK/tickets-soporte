<?php
require_once '../../../../dbconnection.php';

$turnoId = (int)$_GET['turno_id'];
$fechaInicio = $_GET['fecha_inicio'];
$fechaFin = $_GET['fecha_fin'];

try {
    $stmt = $con->prepare("SELECT COUNT(*) as total 
                          FROM horarios_sucursal 
                          WHERE turno_id = ? 
                          AND fecha BETWEEN ? AND ?");
    $stmt->bind_param("iss", $turnoId, $fechaInicio, $fechaFin);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $count = $result->fetch_assoc()['total'];
    
    echo json_encode([
        'success' => true,
        'disponible' => $count > 0,
        'total_dias' => $count
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}