<?php
    header('Content-Type: application/json');
    session_start();
    include("../../dbconnection.php");
    
    //obtener actividades
    $area = $_SESSION['cargo'];
    $query = "SELECT *
                FROM actividades
                WHERE area = '$area' ";
    $result = $con->query($query);
    $eventos = [];
    while ($row = $result->fetch_assoc()) {
        $eventos[] = [
            'id' => $row['id'],
            'title' => $row['nombre'],
            'start' => $row['fecha_inicio'],
            'end' => $row['fecha_termino']    
        ];
    }
    
    echo json_encode($eventos);
?>