<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once "../../../dbconnection.php";

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido"]);
    exit;
}

try {
    $sucursalId = isset($_GET['sucursal_id']) ? intval($_GET['sucursal_id']) : null;
    
    $sql = "SELECT r.fecha, s.nombre AS sucursal, r.monto 
            FROM recuperaciones r
            JOIN sucursales s ON r.sucursal_id = s.id
            WHERE YEAR(r.fecha) = YEAR(CURDATE()) 
            AND MONTH(r.fecha) = MONTH(CURDATE())";
    
    if ($sucursalId) {
        $sql .= " AND r.sucursal_id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("i", $sucursalId);
    } else {
        $stmt = $con->prepare($sql);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $datos = [];
    while ($row = $result->fetch_assoc()) {
        $datos[] = [
            'fecha' => $row['fecha'],
            'sucursal' => $row['sucursal'],
            'monto' => (float)$row['monto']
        ];
    }
    
    echo json_encode($datos);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => "Error en la base de datos",
        'detalle' => $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) $stmt->close();
    $con->close();
}
?>