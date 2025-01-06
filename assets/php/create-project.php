<?php
//Obtener estados del proyecto
$query = "SELECT * 
            FROM estados
            WHERE type = 'project'";
$status = mysqli_query($con, $query);

//Insertar proyectos nuevos
if(isset($_POST['newProject'])){
    $nameP     = $_POST['name'];
    $client    = $_POST['client'];
    $city      = $_POST['city'];
    $status    = $_POST['status'];
    $comercial = $_POST['comercial'];
    $ingeniero = $_POST['ingeniero'];
    $bom       = $_POST['bom'];
    $dist      = $_POST['dist'];
    $software  = $_POST['software'];
    $hardware  = $_POST['hardware'];
    $resumen   = $_POST['desc'];
    $date      = date('Y-m-d'); 
    $userId    = $_SESSION['user_id'];

    $query = "INSERT INTO proyectos (nombre, cliente, ciudad, estado_id, 
                comercial_responsable, ingeniero_responsable, bom, distribuidor,
                costo_software, costo_hardware, resumen, fecha_creacion, user_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
    if ($stmt = mysqli_prepare($con, $query)) {
        mysqli_stmt_bind_param($stmt, "sssissssiissi",$nameP, $client, $city, $status, $comercial, $ingeniero, $bom, $dist, $software, $hardware, $resumen, $date, $userId ); 
        if (mysqli_stmt_execute($stmt)) {
            if($_POST['actividades']){

            }
            
            echo "<script>alert('Proyecto Registrado Correctamente'); location.replace(document.referrer)</script>";
        } else {
            echo "<script>alert('Error al registrar el proyecto');</script>";
        }
        
        mysqli_stmt_close($stmt);
    } else {
        echo "<script>alert('Error al preparar la consulta');</script>";
    }

    
};
?>