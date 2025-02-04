<?php
//obtener info de los supervisores
$query = "SELECT * FROM supervisores";
$supervisorData = $con->prepare($query);
$supervisorData->execute();
$result = $supervisorData->get_result();
?>