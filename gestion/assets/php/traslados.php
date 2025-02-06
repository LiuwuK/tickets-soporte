<?php
//usuarios
$query = "SELECT * FROM user";
$userData = $con->prepare($query);
$userData->execute();
$result = $userData->get_result();
//obtener supervisores/departamentos/roles/ciudades/instalaciones
$query = "SELECT * FROM supervisores";
$supervisorData = $con->prepare($query);
$supervisorData->execute();
$supData = $supervisorData->get_result();
while ($row = mysqli_fetch_assoc($supData)) {
    $sup[] = $row; 
}
$query = "SELECT * FROM roles";
$rolesData = $con->prepare($query);
$rolesData->execute();
$rolData = $rolesData->get_result();
while ($row = mysqli_fetch_assoc($rolData)) {
    $rol[] = $row; 
}
$query = "SELECT * FROM departamentos";
$deptosData = $con->prepare($query);
$deptosData->execute();
$deptoData = $deptosData->get_result();
while ($row = mysqli_fetch_assoc($deptoData)) {
    $depto[] = $row; 
}

$query = "SELECT * FROM sucursales";
$sucursalesData = $con->prepare($query);
$sucursalesData->execute();
$sucursalData = $sucursalesData->get_result();
while ($row = mysqli_fetch_assoc($sucursalData)) {
    $inst[] = $row; 
}

$query = "SELECT * FROM jornadas";
$jornadasData = $con->prepare($query);
$jornadasData->execute();
$jornadaData = $jornadasData->get_result();
while ($row = mysqli_fetch_assoc($jornadaData)) {
    $jornada[] = $row; 
}
?>