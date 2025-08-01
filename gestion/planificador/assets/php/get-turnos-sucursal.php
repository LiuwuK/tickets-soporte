<?php
header('Content-Type: application/json');
require_once '../../../../dbconnection.php';

try {
    if (!isset($_GET['sucursal_id'])) {
        throw new Exception("El parámetro sucursal_id es requerido", 400);
    }

    $sucursalId = (int)$_GET['sucursal_id'];
    if ($sucursalId <= 0) {
        throw new Exception("ID de sucursal inválido", 400);
    }


    $stmt = $con->prepare("
        SELECT 
            t.id AS turno_id,
            t.codigo,
            t.nombre_turno,
            j.tipo_jornada,
            hs.bloque_id
        FROM turnos_instalacion t
        JOIN horarios_sucursal hs ON t.id = hs.turno_id
        JOIN jornadas j ON j.id = t.jornada_id
        WHERE hs.sucursal_id = ?
        GROUP BY t.id, hs.bloque_id
        ORDER BY t.codigo, hs.bloque_id
    ");
    $stmt->bind_param("i", $sucursalId);
    $stmt->execute();

    $result = $stmt->get_result();
    $turnos = [];

    while ($row = $result->fetch_assoc()) {
        $turnos[] = $row;
    }

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
