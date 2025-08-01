<?php
require_once '../../../../dbconnection.php';

$sucursalId = (int)$_GET['sucursal_id'];
$fechaInicio = $_GET['fecha_inicio'];
$fechaFin = $_GET['fecha_fin'];

try {
    if ($sucursalId <= 0 || empty($fechaInicio) || empty($fechaFin)) {
        throw new Exception("Parámetros inválidos", 400);
    }

    $stmt = $con->prepare("
        SELECT DISTINCT 
            t.id AS turno_id,
            t.codigo,
            t.nombre_turno,
            hs.bloque_id
        FROM turnos_instalacion t
        JOIN horarios_sucursal hs ON hs.turno_id = t.id
        WHERE hs.sucursal_id = ?
        AND hs.fecha BETWEEN ? AND ?
        ORDER BY t.codigo, hs.bloque_id
    ");
    $stmt->bind_param("iss", $sucursalId, $fechaInicio, $fechaFin);
    $stmt->execute();

    $result = $stmt->get_result();
    $turnos = $result->fetch_all(MYSQLI_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $turnos
    ]);
} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
