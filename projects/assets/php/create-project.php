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
//obtener verticales
$query = "SELECT * FROM verticales";
$vertical = mysqli_query($con, $query);

//obtener distribuidores
$query = "SELECT * FROM distribuidores";
$distribuidor = mysqli_query($con, $query);

//obtener cargos
$query = "SELECT * FROM cargos";
$cargos = mysqli_query($con, $query);

//obtener portales 
$query = "SELECT * FROM portales";
$portal = mysqli_query($con, $query);
//obtener clientes
$query = "SELECT * FROM clientes";
$clients = mysqli_query($con,$query);
//obtener competidores
$query = "SELECT * FROM competidores";
$competidores = mysqli_query($con,$query);

// cargar proyecto que va a ser actualizado
if(isset($_GET['projectId']) ){
    //obtener todos los datos del proyecto
    $pID = $_GET['projectId'];
    $query =  "SELECT *
                FROM proyectos
                WHERE id = $pID";
    $project = mysqli_query($con, $query);
    $projectData = mysqli_fetch_assoc($project);
    //obtener actividades asociadas al proyecto
    $query = "SELECT * 
                FROM actividades
                WHERE proyecto_id = $pID";
    $act = mysqli_query($con, $query);
    $actividades = mysqli_fetch_all($act, MYSQLI_ASSOC);
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
    //obtener lista de materiales
    if($projectData['bom'] = 1){
        $query = "SELECT * 
                    FROM bom
                    WHERE proyecto_id = $pID";
        $bom = mysqli_query($con, $query);
        $materiales = mysqli_fetch_all($bom, MYSQLI_ASSOC);
    }
    //obtener etapa del proyecto si es que este esta en evaluacion
    if($projectData['estado'] = 19){
        $query =  "SELECT * FROM etapas_proyecto";
        $etData = mysqli_query($con, $query);
    }

}
  
//Crear proyectos nuevos
if(isset($_POST['newProject'])){
    $nameP     = $_POST['name']; 
    $client    = $_POST['client']; 
    $city      = $_POST['city'];
    $monto     = $_POST['montoP']; 
    $status    = '19';
    $pType     = $_POST['pType'];  
    $pClass    = $_POST['pClass']; //id clase
    $bom       = isset($_POST['bom']) ? $_POST['bom'] : 0;
    $vertical  = $_POST['vertical'];
    $software  = ($pClass == 1 && isset($_POST['software-input'])) ? $_POST['software-input'] : 0;
    $hardware  = ($pClass == 1 && isset($_POST['hardware-input'])) ? $_POST['hardware-input'] : 0; 
    $resumen   = $_POST['desc'];
    $comercial = $_SESSION['id'];
    $pdate     = date('Y-m-d');
    $cierre    = isset($_POST['cierreDoc']) && $_POST['cierreDoc'] != '' ? $_POST['cierreDoc'] : NULL;
    $fAdj      = isset($_POST['fAdj']) && $_POST['fAdj'] != '' ? $_POST['fAdj'] : NULL;
    $finCt     = isset($_POST['finContrato']) && $_POST['finContrato'] != '' ? $_POST['finContrato'] : NULL;

    $query = "INSERT INTO proyectos (nombre, cliente, ciudad, estado_id, 
                costo_software, costo_hardware, resumen, 
                fecha_creacion, comercial_responsable, monto, tipo, clasificacion, 
                vertical, fecha_cierre_documental, fecha_adjudicacion, fecha_fin_contrato) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )";
        
    if ($stmt = mysqli_prepare($con, $query)) {
        mysqli_stmt_bind_param($stmt, "ssiiiissiiiiisss", $nameP, $client, $city, $status, $software,
                                 $hardware, $resumen, $pdate, $comercial, $monto, $pType, $pClass, $vertical, $cierre, $fAdj, $finCt); 
        if (mysqli_stmt_execute($stmt)) {
            $pId = mysqli_insert_id($con);

            //Si tiene actividades, se registran
            if(isset($_POST['actividades'])){
                foreach ($_POST['actividades']['nombre'] as $key => $name) {
                    $fInicio = $_POST['actividades']['fechaInicio'][$key];
                    $fTermino = $_POST['actividades']['fechaTermino'][$key];
                    $desc = $_POST['actividades']['descripcion'][$key];
                    $area = $_POST['actividades']['area'][$key];
            
                    $query = 'INSERT INTO actividades (nombre, fecha_inicio, fecha_termino, proyecto_id, descripcion, area)
                            VALUES (?, ?, ?, ?, ?, ?)';
                    $act_stmt = mysqli_prepare($con, $query);
                    mysqli_stmt_bind_param($act_stmt, "sssiss", $name, $fInicio, $fTermino, $pId, $desc, $area);
                    mysqli_stmt_execute($act_stmt);
                    mysqli_stmt_close($act_stmt);
                }       
            }
            //Si el tipo es licitacion (ID 1) o contacto (ID 2)
            if($pType == 1){
                $portal = !empty($_POST['portal']) ? $_POST['portal'] : NULL;
                $licID = $_POST['licID'];
                $query = 'INSERT INTO licitacion_proyecto (licitacion_id, proyecto_id, portal) VALUES(?, ?, ?)'; 
                $lc_stmt = mysqli_prepare($con, $query);
                mysqli_stmt_bind_param($lc_stmt, "sii", $licID, $pId, $portal); 
                mysqli_stmt_execute($lc_stmt);
                mysqli_stmt_close($lc_stmt);

            } else if ($pType == 2) {
                foreach ($_POST['contacto']['nombre'] as $key => $name) {
                    $email = $_POST['contacto']['email'][$key];
                    $cargo = $_POST['contacto']['cargo'][$key];
                    $numero = $_POST['contacto']['contacto'][$key];
            
                    $query = 'INSERT INTO contactos_proyecto (nombre, correo, cargo, numero, proyecto_id) VALUES(?, ?, ?, ?, ?)'; 
                    $ct_stmt = mysqli_prepare($con, $query);
                    mysqli_stmt_bind_param($ct_stmt, "ssssi", $name, $email, $cargo, $numero, $pId); 
                    mysqli_stmt_execute($ct_stmt);
                    mysqli_stmt_close($ct_stmt);
                }                   
            } 
            $user = $_SESSION['name'];
            if(Notificaciones::crearTicketMail($pId, 'project', $user)){
                //echo "<script>alert('correo enviado Correctamente'); location.replace(document.referrer)</script>";
            } else {
                //echo "<script>alert('Hubo un error al enviar el correo'); location.replace(document.referrer)</script>";
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
    $pId = $_GET['projectId'];
    $pClass = $_POST['pClass'];
    $bom    = 0; 
    if(isset($_POST['material'])){
        $bom = 1; 
    }
    $distribuidor = isset($_POST['dist']) && !empty($_POST['dist']) ? $_POST['dist'] : null;
    $ingeniero = isset($_POST['ingeniero']) && !empty($_POST['ingeniero']) ? $_POST['ingeniero'] : null;
    $software  = ($pClass == 1 && isset($_POST['software-input'])) ? $_POST['software-input'] : 0;
    $hardware  = ($pClass == 1 && isset($_POST['hardware-input'])) ? $_POST['hardware-input'] : 0;
    //fechas 
    $cierre    = isset($_POST['cierreDoc']) && $_POST['cierreDoc'] != '' ? $_POST['cierreDoc'] : NULL;
    $fAdj      = isset($_POST['fAdj']) && $_POST['fAdj'] != '' ? $_POST['fAdj'] : NULL;
    $finCt     = isset($_POST['finContrato']) && $_POST['finContrato'] != '' ? $_POST['finContrato'] : NULL;
    $etapa     = isset($_POST['etapaEst']) && $_POST['etapaEst'] != '' ? $_POST['etapaEst'] : NULL;
    //datos del formulario
    $competidor = isset($_POST['competidor']) && !empty($_POST['competidor']) ? $_POST['competidor'] : null;
    $newData = [
        'id'                        => $_GET['projectId'],
        'nombre'                    => $_POST['name'],
        'cliente'                   => $_POST['client'],
        'ciudad'                    => $_POST['city'],
        'estado_id'                 => $_POST['status'],
        'estado_etapa'              => $etapa,
        'ingeniero_responsable'     => $ingeniero,
        'bom'                       => $bom,
        'costo_software'            => $software,
        'costo_hardware'            => $hardware,
        'resumen'                   => $_POST['desc'],
        'fecha_creacion'            => $projectData['fecha_creacion'],
        'comercial_responsable'     => $projectData['comercial_responsable'],
        'monto'                     => $_POST['montoP'],
        'tipo'                      => $projectData['tipo'],
        'clasificacion'             => $pClass,
        'costo_real'                => $projectData['costo_real'],//$_POST['costoR']
        'distribuidor'              => $distribuidor,
        'vertical'                  => $_POST['vertical'],
        'fecha_cierre_documental'   => $cierre,
        'fecha_adjudicacion'        => $fAdj,
        'fecha_fin_contrato'        => $finCt,
        'competidor'                => $competidor
    ];

    echo '<pre>';
    print_r($newData);
    echo '</pre>';

    
    //Datos de la db
    $currentJson = json_encode($projectData);
    //Datos nuevos (del formulario)
    $newJson     = json_encode($newData);
    
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
            $stmt->bind_param("siii", $newLic['licitacion_id'], $newLic['proyecto_id'], $newLic['portal'], $newLic['id'] );
            $stmt->execute();
            $stmt->close();
        }
    }/*else if($projectData["tipo"] == "2"){
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
    }*/
    //Si tiene actividades nuevas, estas se registran
    if(isset($_POST['actividades'])){
        foreach ($_POST['actividades']['nombre'] as $key => $name) {
            $fInicio = $_POST['actividades']['fechaInicio'][$key];
            $fTermino = $_POST['actividades']['fechaTermino'][$key];
            $desc = $_POST['actividades']['descripcion'][$key];
            $area = $_POST['actividades']['area'][$key];
    
            $query = 'INSERT INTO actividades (nombre, fecha_inicio, fecha_termino, proyecto_id, descripcion, area)
                    VALUES (?, ?, ?, ?, ?, ?)';
            $act_stmt = mysqli_prepare($con, $query);
            mysqli_stmt_bind_param($act_stmt, "sssiss", $name, $fInicio, $fTermino, $pId, $desc, $area);
            mysqli_stmt_execute($act_stmt);
            mysqli_stmt_close($act_stmt);
        }
    }
    //si hay materiales nuevos se registran
    if(isset($_POST['material'])){
        $distribuidor = $_POST['dist'];
        foreach ($_POST['material']['nombre'] as $key => $name) {
            $cantidad = $_POST['material']['cantidad'][$key];
            $total = $_POST['material']['total'][$key];

            $query = 'INSERT INTO bom (nombre, cantidad, total, distribuidor_id, proyecto_id)
                        VALUES (?, ?, ?, ?, ?)';
            $bom_stmt = mysqli_prepare($con, $query);
            mysqli_stmt_bind_param($bom_stmt, "siiii", $name, $cantidad, $total, $distribuidor, $pId); 
            mysqli_stmt_execute($bom_stmt);
            mysqli_stmt_close($bom_stmt);
        }
    }

    /*si cambia el costo 

    if($_POST['costoR'] != $projectData['costo_real']){

        $distID = $newData['distribuidor'];
        $query = "SELECT * 
                    FROM distribuidores
                    WHERE id = $distID";
        $distribuidor = mysqli_query($con, $query);
        $dist = mysqli_fetch_assoc($distribuidor);
        
        $montoInicial = $dist['monto'];        
        $montoRestante = $dist['monto_restante'];
        if($montoInicial != 0){
            $costo = floatval($_POST['costoR']);
            $nuevoMonto = $montoRestante - $costo;
            $query =  " UPDATE distribuidores 
                        SET monto_restante = ?
                        WHERE id = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("ii", $nuevoMonto, $distID); 
            $stmt->execute();
        }
    }*/
    
    //se comparan los datos de la db con los del formulario, si son distintos se actualiza el proyecto

    if($newJson !== $currentJson){

        $query =  " UPDATE proyectos 
                    SET
                        nombre = ?, cliente = ?,
                        ciudad = ?, estado_id = ?,
                        estado_etapa = ?,
                        ingeniero_responsable = ?,
                        distribuidor = ?, vertical = ?,
                        monto = ?,
                        costo_software = ?, 
                        costo_hardware = ?,
                        costo_real = ?,
                        resumen = ?, bom = ?,
                        fecha_cierre_documental = ?,
                        fecha_adjudicacion = ?,
                        fecha_fin_contrato = ?,
                        competidor = ?,
                        clasificacion = ?
                    WHERE id = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("ssiiiiiiiiiisisssiii",   
        $newData['nombre'], $newData['cliente'], $newData['ciudad'], $newData['estado_id'], $newData['estado_etapa'], $newData['ingeniero_responsable'],
        $newData['distribuidor'], $newData['vertical'], $newData['monto'], $newData['costo_software'], $newData['costo_hardware'], 
        $newData['costo_real'], $newData['resumen'], $newData['bom'], $newData['fecha_cierre_documental'], $newData['fecha_adjudicacion'], 
        $newData['fecha_fin_contrato'], $newData['competidor'], $newData['clasificacion'], $newData['id'] );
        //tabla historial actualizacion proyecto
            // 1. Comparar ambos arrays y construir un array con las diferencias.
            $diferencias = [];
            foreach ($newData as $campo => $nuevoValor) {
                // Si el valor actual es distinto al nuevo (considerando que pueden ser null o cadena vacía)
                if (!isset($projectData[$campo]) || $projectData[$campo] != $nuevoValor) {
                    $diferencias[$campo] = [
                        'antes'   => isset($projectData[$campo]) ? $projectData[$campo] : null,
                        'despues' => $nuevoValor
                    ];
                }
            }

            // 2. Si hay diferencias, guardar el historial.
            if (!empty($diferencias)) {
                // Convertir el array de diferencias a JSON.
                $jsonCambios = json_encode($diferencias);

                $accion = 'update';
                $stmtHist = $con->prepare("INSERT INTO historico_proyectos (proyecto_id, cambios, actualizado_por, accion) VALUES (?, ?, ?, ?)");
                if ($stmtHist) {
                    // Es recomendable forzar la conversión del ID a entero.
                    $stmtHist->bind_param("isis", $newData['id'], $jsonCambios, $usuario_actual, $accion);
                    $stmtHist->execute();
                    $stmtHist->close();
                } else {
                    error_log("Error preparando la inserción del histórico: " . $con->error);
                }
            }
        //---------------------------------------------------------------------------------------------------------------------------------------
        if ($stmt->execute()) {
            echo "<script>alert('Su proyecto ha sido actualizado correctamente');location.replace(document.referrer)</script>";
        } else {
            echo "<script>alert('Error al actualizar');location.replace(document.referrer)</script>";
        }   

        $stmt->close();
    }else if (isset($newLic) && $licJson !== $currentLic){
        echo "<script>alert('Su proyecto ha sido actualizado correctamente');location.replace(document.referrer)</script>";
    }else if (isset($newCt) && $ctJson !== $currentCt){
        echo "<script>alert('Su proyecto ha sido actualizado correctamente');location.replace(document.referrer)</script>";
    }else{
        echo "<script>alert('No se ha cambiado ningun dato');location.replace(document.referrer)</script>";
    }

}

?>