<?php
include('../../dbconnection.php');
$table = $_GET['table'];
$field = $_GET['field'];



if ($table == 'user') {
    
    $sql = "SELECT id, $field 
            FROM $table
            WHERE cargo = '1'";
    $result = $con->query($sql);

    $options = [];
    while ($row = $result->fetch_assoc()) {
        $options[] = $row;
    }

 } else{
    $sql = "SELECT id, $field FROM $table";
    $result = $con->query($sql);

    $options = [];
    while ($row = $result->fetch_assoc()) {
        $options[] = $row;
    }

}

header('Content-Type: application/json');
echo json_encode($options);
?>