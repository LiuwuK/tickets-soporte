<?php
//obtener info sobre los clientes
$query = "SELECT cl.*, vt.nombre AS verticalN
          FROM clientes cl
          JOIN verticales vt ON(cl.vertical = vt.id)";
if(isset($_GET['clientID'])){
    $id = $_GET['clientID'];
    $query .= "WHERE cl.id = $id";
}
$clientsData = $con->prepare($query);
$clientsData->execute();
$clientes = $clientsData->get_result();
$num_cl = $clientes->num_rows;



?>