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
    };
    
    //obtener proyectos con fecha de cierre
    $userId = $_SESSION['id'];
    $query = "SELECT id, nombre, fecha_cierre
                FROM proyectos";
    if($area != 4){
        $query .= " WHERE comercial_responsable = '".$userId."'";
    };

    $cierre = $con->query($query);
    while ($row = $cierre->fetch_assoc()) {
        $eventos[] = [
            'id' => $row['id'],
            'title' => 'Cierre del proyecto #'.$row['id'].' '.$row['nombre'],
            'start' => $row['fecha_cierre'] 
        ];
    };

    echo json_encode($eventos);
?>