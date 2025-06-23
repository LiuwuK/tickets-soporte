<?php
require_once '../../../../dbconnection.php';

$sucursalId = (int)$_GET['sucursal_id'];
$fechaInicio = $_GET['fecha_inicio'];
$fechaFin = $_GET['fecha_fin'];

try {
    $stmt = $con->prepare("SELECT DISTINCT t.id, t.codigo, t.nombre_turno 
                          FROM turnos_instalacion t
                          JOIN horarios_sucursal h ON h.turno_id = t.id
                          WHERE t.sucursal_id = ? 
                          AND h.fecha BETWEEN ? AND ?");
    $stmt->bind_param("iss", $sucursalId, $fechaInicio, $fechaFin);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $turnos = $result->fetch_all(MYSQLI_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $turnos
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}