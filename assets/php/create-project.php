<?php
//Obtener estados del proyecto
$query = "SELECT * 
            FROM estados
            WHERE type = 'project'";
$status = mysqli_query($con, $query);

//obtener ciudades
$query = "SELECT * FROM ciudades";
$cities = mysqli_query($con, $query);

//obtener tipos de proyecto
$query = "SELECT * FROM tipo_proyecto";
$types = mysqli_query($con, $query);

//obtener clasificaciones de proyecto
$query = "SELECT * FROM clasificacion_proyecto";
$class = mysqli_query($con, $query);
 

//Insertar proyectos nuevos
if(isset($_POST['newProject'])){
    $nameP     = $_POST['name'];
    $client    = $_POST['client'];
    $city      = $_POST['city']; //id de la ciudad
    $status    = $_POST['status']; //id del estado
    $ingeniero = $_POST['ingeniero']; //id del ingeniero
    //$bom       = $_POST['bom']; //id de la bom
    $dist      = $_POST['dist'];
    //$software  = $_POST['software'];
    //$hardware  = $_POST['hardware'];
    $resumen   = $_POST['desc'];
    $date      = date('Y-m-d'); 
    $comercial = $_SESSION['user_id']; //id del usuario

    $query = "INSERT INTO proyectos (nombre, cliente, ciudad, estado_id, 
                comercial_responsable, ingeniero_responsable, bom, distribuidor,
                costo_software, costo_hardware, resumen, fecha_creacion) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
    if ($stmt = mysqli_prepare($con, $query)) {
        mysqli_stmt_bind_param($stmt, "", ); 
        if (mysqli_stmt_execute($stmt)) {
            $pId = mysqli_insert_id($con);
            //Si tiene actividades, se registran
            if(isset($_POST['actividades'])){
                foreach ($_POST['actividades']['nombre'] as $key => $name) {
                    $fecha = $_POST['actividades']['fecha'][$key];
                    $desc = $_POST['actividades']['descripcion'][$key];
            
                    $query = 'INSERT INTO actividades (nombre, fecha, proyecto_id, descripcion)
                    VALUES (?, ?, ?, ?)';
                    $stmt = mysqli_prepare($con, $query);
                    mysqli_stmt_bind_param($stmt, "ssis", $name, $fecha, $pId, $desc); 
                    mysqli_stmt_execute($stmt);
                    echo "<script>alert('Proyecto Registrado Correctamente'); location.replace(document.referrer)</script>";
                }       
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