<?php
header('Content-Type: application/json');
require_once '../../../../dbconnection.php';

$sucursalId = $_GET['sucursal_id'];
$fechaInicio = $_GET['fecha_inicio'];
$fechaFin = $_GET['fecha_fin'];

$stmt = $con->prepare("
  SELECT ti.codigo 
  FROM horarios_sucursal hs 
  JOIN turnos_instalacion ti ON hs.turno_id = ti.id 
  WHERE hs.sucursal_id = ? AND hs.fecha BETWEEN ? AND ?
  GROUP BY ti.codigo
");
$stmt->bind_param("iss", $sucursalId, $fechaInicio, $fechaFin);
$stmt->execute();
$result = $stmt->get_result();

$turnos = [];
while ($row = $result->fetch_assoc()) {
  $turnos[] = $row['codigo'];
}

echo json_encode($turnos);