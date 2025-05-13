<?php
//obtener todos los colaboradores 
$query = "SELECT co.*, su.nombre AS sucursalID
          FROM colaboradores co
          JOIN sucursales su ON(co.facility = su.id)";
if(isset($_GET['colabID'])){
    $id = $_GET['colabID'];
    $query .= "WHERE co.id = $id";
}
$colaborador = $con->prepare($query);
$colaborador->execute();
$colab = $colaborador->get_result();
$num_cl = $colab->num_rows;

function calcularAntiguedad($fechaIngreso) {
    $entryDate = new DateTime($fechaIngreso);
    $currentDate = new DateTime();
    $interval = $currentDate->diff($entryDate);
    
    return $interval->format('%y años, %m meses');
}


//obtener info del colaborador 
if(isset($_GET['colabID'])){
    $id = $_GET['colabID'];

}


?>