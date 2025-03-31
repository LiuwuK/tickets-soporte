<?php
header('Content-Type: application/json'); 

require_once '../../../dbconnection.php'; 

$sucursalId = isset($_GET['sucursal_id']) ? intval($_GET['sucursal_id']) : null;

$query = "SELECT r.fecha, s.nombre AS sucursal, r.monto 
          FROM recuperaciones r
          JOIN sucursales s ON r.sucursal_id = s.id
          WHERE YEAR(r.fecha) = YEAR(CURDATE()) 
          AND MONTH(r.fecha) = MONTH(CURDATE())" .
          ($sucursalId ? " AND r.sucursal_id = $sucursalId" : "") .
          " ORDER BY r.fecha";
$result = $con->query($query);
$events = array();

while ($row = $result->fetch_assoc()) {
    $events[] = array(
        'title' => $row['sucursal'] . ' - $' . number_format($row['monto']),
        'start' => $row['fecha'],
        'color' => '#378006'
    );
}

echo json_encode($events);
$con->close();
?>