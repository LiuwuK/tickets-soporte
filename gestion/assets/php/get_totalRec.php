<?php
header('Content-Type: application/json'); 
require_once '../../../dbconnection.php'; 

$sucursalId = isset($_GET['sucursal_id']) && $_GET['sucursal_id'] !== '' ? intval($_GET['sucursal_id']) : null;

$query = "SELECT s.nombre AS sucursal, SUM(r.monto) 
          FROM recuperaciones r
          JOIN sucursales s ON r.sucursal_id = s.id
          WHERE YEAR(r.fecha) = YEAR(CURDATE()) 
          AND MONTH(r.fecha) = MONTH(CURDATE())".
          ($sucursalId !== null ? " AND r.sucursal_id = $sucursalId" : "").
          " GROUP BY s.nombre";

$result = $con->query($query);

$data = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

$con->close();
echo json_encode($data);
?>