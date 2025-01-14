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

//obtener ingenieros
$query =  "SELECT id, name 
            FROM user
            WHERE cargo = '1'";
$inge = mysqli_query($con, $query);

if(isset($_GET['projectId']) ){
    //obtener todos los proyectos
    $pID = $_GET['projectId'];
    $query =  "SELECT *
                FROM proyectos
                WHERE id = $pID";
    $project = mysqli_query($con, $query);
    $projectData = mysqli_fetch_assoc($project);
    
    //obtener la ciudad correspondiente
    $cityID = $projectData["ciudad"];
    $queryC = "SELECT *
                FROM ciudades
                WHERE id = $cityID";
    $ciudad = mysqli_query($con, $queryC);
    $cityData = mysqli_fetch_assoc($ciudad);            


    //obtener datos de licitacion/contacto
    if ($projectData['tipo'] == 1) {
        $query =  "SELECT *
                    FROM licitacion_proyecto
                    WHERE proyecto_id = $pID";
        $lic = mysqli_query($con, $query);
        $licData = mysqli_fetch_assoc($lic);  
    
    } else if ($projectData['tipo'] == 2){
        $query =  "SELECT *
                    FROM contactos_proyecto
                    WHERE proyecto_id = $pID";
        $ct = mysqli_query($con, $query);
        $ctData = mysqli_fetch_assoc($ct);
    }
    
}
  
//Insertar proyectos nuevos
if(isset($_POST['newProject'])){
    $nameP     = $_POST['name']; 
    $client    = $_POST['client']; 
    $city      = $_POST['city']; 
    $status    = $_POST['status'];
    $pType     = $_POST['pType'];  
    $pClass    = $_POST['pClass']; //id clase
    $ingeniero = '11';
    $bom       = isset($_POST['bom']) ? $_POST['bom'] : 0;
    $dist      = $_POST['dist'];
    $software  = ($pClass == 1 && isset($_POST['software-input'])) ? $_POST['software-input'] : 0;
    $hardware  = ($pClass == 1 && isset($_POST['hardware-input'])) ? $_POST['hardware-input'] : 0; 
    $resumen   = $_POST['desc'];
    $comercial = $_SESSION['user_id']; //id del usuario
    $monto     = $_POST['monto'];
    $pdate     = date('Y-m-d'); 
  

    print_r($_POST);
    echo $ingeniero;
    echo $software;
    echo $hardware;
    echo $bom;
    echo $comercial;

    $query = "INSERT INTO proyectos (nombre, cliente, ciudad, estado_id, 
                ingeniero_responsable, costo_software, costo_hardware, resumen, 
                fecha_creacion, comercial_responsable, tipo, clasificacion) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
    if ($stmt = mysqli_prepare($con, $query)) {
        mysqli_stmt_bind_param($stmt, "ssiiiiissiii", $nameP, $client, $city, $status, 
                                $ingeniero,$software, $hardware, $resumen, $pdate, $comercial, $pType, $pClass ); 
        if (mysqli_stmt_execute($stmt)) {
            $pId = mysqli_insert_id($con);

            //Si tiene actividades, se registran
            if(isset($_POST['actividades'])){
                foreach ($_POST['actividades']['nombre'] as $key => $name) {
                    $fecha = $_POST['actividades']['fecha'][$key];
                    $desc = $_POST['actividades']['descripcion'][$key];
            
                    $query = 'INSERT INTO actividades (nombre, fecha, proyecto_id, descripcion)
                    VALUES (?, ?, ?, ?)';
                    $act_stmt = mysqli_prepare($con, $query);
                    mysqli_stmt_bind_param($act_stmt, "ssis", $name, $fecha, $pId, $desc); 
                    mysqli_stmt_execute($act_stmt);
                    mysqli_stmt_close($act_stmt);
                }       
            }
            //Si el tipo es licitacion (ID 1) o contacto (ID 2)
            if($pType == 1){
                $portal = $_POST['portal'];
                $licID = $_POST['licID'];
                $query = 'INSERT INTO licitacion_proyecto (licitacion_id, proyecto_id, portal) VALUES(?, ?, ?)'; 
                $lc_stmt = mysqli_prepare($con, $query);
                mysqli_stmt_bind_param($lc_stmt, "sis", $licID, $pId, $portal); 
                mysqli_stmt_execute($lc_stmt);
                mysqli_stmt_close($lc_stmt);

            } else if ($pType == 2) {
                $cname = $_POST['cName'];
                $email = $_POST['cEmail'];
                $cargo = $_POST['cargo'];
                $numero = $_POST['cNumero'];

                $query = 'INSERT INTO contactos_proyecto (nombre, correo, cargo, numero, proyecto_id) VALUES(?, ?, ?, ?, ?)'; 
                $ct_stmt = mysqli_prepare($con, $query);
                mysqli_stmt_bind_param($ct_stmt, "ssssi", $cname, $email, $cargo, $numero, $pId); 
                mysqli_stmt_execute($ct_stmt);
                mysqli_stmt_close($ct_stmt);
            } else{
                echo "<script>alert('Error al registrar el proyecto');</script>";
            }
            
            echo "<script>alert('Proyecto Registrado Correctamente'); location.replace(document.referrer)</script>";
        } else {
            echo "<script>alert('Error al registrar el proyecto');</script>";
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "<script>alert('Error al preparar la consulta');</script>";
    }
} 
//Actualizar proyectos
else if(isset($_POST['updtProject'])) {
    echo "hola";
}

?>