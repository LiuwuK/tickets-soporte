<?php
//Obtener estados del proyecto
$query = "SELECT * 
            FROM estados
            WHERE type = 'project'";
$status = mysqli_query($con, $query);

//obtener ciudades
$query = "SELECT * 
            FROM ciudades
            ORDER BY nombre_ciudad ASC";
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

// cargar proyecto que va a ser actualizado
if(isset($_GET['projectId']) ){
    //obtener todos los datos del proyecto
    $pID = $_GET['projectId'];
    $query =  "SELECT *
                FROM proyectos
                WHERE id = $pID";
    $project = mysqli_query($con, $query);
    $projectData = mysqli_fetch_assoc($project);
    
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
    $status    = '19';
    $pType     = $_POST['pType'];  
    $pClass    = $_POST['pClass']; //id clase
    $bom       = isset($_POST['bom']) ? $_POST['bom'] : 0;
    $dist      = 'Sin asignar';
    $software  = ($pClass == 1 && isset($_POST['software-input'])) ? $_POST['software-input'] : 0;
    $hardware  = ($pClass == 1 && isset($_POST['hardware-input'])) ? $_POST['hardware-input'] : 0; 
    $resumen   = $_POST['desc'];
    $comercial = $_SESSION['id']; //id del usuario
    $pdate     = date('Y-m-d'); 

    $query = "INSERT INTO proyectos (nombre, cliente, ciudad, estado_id, 
                costo_software, costo_hardware, resumen, 
                fecha_creacion, comercial_responsable, tipo, clasificacion) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
    if ($stmt = mysqli_prepare($con, $query)) {
        mysqli_stmt_bind_param($stmt, "ssiiiissiii", $nameP, $client, $city, $status, $software,
                                 $hardware, $resumen, $pdate, $comercial, $pType, $pClass ); 
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
    //ACTUALIZAR DATOS DEL PROTECTO
    $pClass = $projectData['clasificacion'];
    $bom =  isset($_POST['bom']) ? $_POST['bom'] : NULL; 
    $software  = ($pClass == 1 && isset($_POST['software-input'])) ? $_POST['software-input'] : 0;
    $hardware  = ($pClass == 1 && isset($_POST['hardware-input'])) ? $_POST['hardware-input'] : 0; 
    //datos del formulario
    $newData = [
        'id'                    => $_GET['projectId'],
        'nombre'                => $_POST['name'],
        'cliente'               => $_POST['client'],
        'ciudad'                => $_POST['city'],
        'estado_id'             => $_POST['status'],
        'ingeniero_responsable' => $_POST['ingeniero'],
        'bom'                   => $bom,
        'distribuidor'          => $_POST['dist'],
        'costo_software'        => $software,
        'costo_hardware'        => $hardware,
        'resumen'               => $_POST['desc'],
        'fecha_creacion'        => $projectData['fecha_creacion'],
        'comercial_responsable' => $projectData['comercial_responsable'],
        'monto'                 => $_POST['monto'],
        'tipo'                  => $projectData['tipo'],
        'clasificacion'         => $pClass,
    ];
    //Datos de la db
    $currentJson = json_encode($projectData);
    //Datos nuevos (del formulario)
    $newJson     = json_encode($newData);

    //se comparan los datos de la db con los del formulario, si son distintos se actualiza el proyecto
    if($newJson !== $currentJson){
        $query =  " UPDATE proyectos 
                    SET
                        nombre = ?, cliente = ?,
                        ciudad = ?, estado_id = ?,
                        ingeniero_responsable = ?,
                        distribuidor = ?, monto = ?,
                        costo_software = ?, 
                        costo_hardware = ?,
                        resumen = ?
                    WHERE id = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("ssiiisiiisi",   
        $newData['nombre'], $newData['cliente'], $newData['ciudad'], $newData['estado_id'], $newData['ingeniero_responsable'],
        $newData['distribuidor'], $newData['monto'], $newData['costo_software'], $newData['costo_hardware'], $newData['resumen'], $newData['id'] );

        if ($stmt->execute()) {
            echo "<script>alert('Su proyecto ha sido actualizado correctamente');location.replace(document.referrer)</script>";
        } else {
            echo "<script>alert('Error al actualizar');location.replace(document.referrer)</script>";
        }

        $stmt->close();
    }else{
        echo "<script>alert('No se ha cambiado ningun dato');location.replace(document.referrer)</script>";
    }

    //ACTUALIZAR DATOS DE LICITACION(1)/CONTACTO (2)
    if($projectData["tipo"] == "1"){
        //datos de licitacion (formulario)
        $newLic = [
            'id'            => $licData["id"],
            'licitacion_id' => $_POST['licID'],
            'proyecto_id'   => $_GET['projectId'],
            'portal'        => $_POST['portal']
        ];
        $licJson = json_encode($newLic);
        //datos db
        $currentLic = json_encode($licData);
 
        if($licJson !== $currentLic){
            $query =  " UPDATE licitacion_proyecto 
                    SET
                        licitacion_id = ?,
                        proyecto_id = ?,
                        portal = ?                    
                    WHERE id = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("iisi", $newLic['licitacion_id'], $newLic['proyecto_id'], $newLic['portal'], $newLic['id'] );
            $stmt->execute();
            $stmt->close();
        }
    }else if($projectData["tipo"] == "2"){
        //datos de contacto (formulario)
        $newCt = [
            "id"            => $ctData["id"],
            "nombre"        => $_POST["cName"],
            "correo"        => $_POST["cEmail"],
            "cargo"         => $_POST["cargo"],
            "numero"        => $_POST["cNumero"],
            "proyecto_id"   => $_GET["projectId"],
        ];      
        $ctJson = json_encode($newCt);
        //datos db
        $currentCt = json_encode($ctData);
        
        if($ctJson !== $currentCt){
            $query =  " UPDATE contactos_proyecto 
                    SET
                        nombre = ?,
                        correo = ?,
                        cargo = ?,
                        numero = ?
                    WHERE id = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("sssii", $newCt['nombre'], $newCt['correo'], $newCt['cargo'], $newCt['numero'], $ctData['id']);
            $stmt->execute();
            $stmt->close();
        }
    }
}

?>