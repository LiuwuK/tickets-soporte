<?php
// Asegurarse de que projectId es un entero
$pID = isset($_GET['projectId']) ? intval($_GET['projectId']) : 0;

if($pID > 0){
    // Proyecto
    $stmt = $con->prepare("SELECT * FROM proyectos WHERE id = ?");
    $stmt->bind_param("i", $pID);
    $stmt->execute();
    $projectData = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // Actividades
    $stmt = $con->prepare("SELECT * FROM actividades WHERE proyecto_id = ?");
    $stmt->bind_param("i", $pID);
    $stmt->execute();
    $actividades = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Licitación o Contacto
    if($projectData['tipo'] == 1){
        $stmt = $con->prepare("SELECT * FROM licitacion_proyecto WHERE proyecto_id = ?");
        $stmt->bind_param("i", $pID);
        $stmt->execute();
        $licData = $stmt->get_result()->fetch_assoc();
        $stmt->close();
    } elseif($projectData['tipo'] == 2){
        $stmt = $con->prepare("SELECT * FROM contactos_proyecto WHERE proyecto_id = ?");
        $stmt->bind_param("i", $pID);
        $stmt->execute();
        $ctData = $stmt->get_result()->fetch_assoc();
        $stmt->close();
    }

    // BOM
    if($projectData['bom'] == 1){
        $stmt = $con->prepare("SELECT * FROM bom WHERE proyecto_id = ?");
        $stmt->bind_param("i", $pID);
        $stmt->execute();
        $materiales = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }

    // Etapas
    if($projectData['estado'] == 19){
        $etData = $con->query("SELECT * FROM etapas_proyecto")->fetch_all(MYSQLI_ASSOC);
    }

    // Cargar listas para selects
    function fetchAllAssoc($con, $table){
        return $con->query("SELECT * FROM $table")->fetch_all(MYSQLI_ASSOC);
    }

    $clientsArray       = fetchAllAssoc($con, "clientes");
    $typesArray         = fetchAllAssoc($con, "tipo_proyecto");
    $classArray         = fetchAllAssoc($con, "clasificacion_proyecto");
    $citiesArray        = fetchAllAssoc($con, "ciudades");
    $verticalArray      = fetchAllAssoc($con, "verticales");
    $ingeArray          = fetchAllAssoc($con, "usuarios"); // ingenieros
    $distribuidorArray  = fetchAllAssoc($con, "distribuidores");
    $competidoresArray  = fetchAllAssoc($con, "competidores");
    $statusArray        = fetchAllAssoc($con, "estado_proyecto");
    $cargosArray        = fetchAllAssoc($con, "cargos"); // para actividades
}

// Función helper para renderizar selects
function renderOptions($data, $selectedId, $valueField, $textField){
    foreach($data as $row){
        $sel = ($row[$valueField] == $selectedId) ? 'selected' : '';
        echo "<option value='{$row[$valueField]}' $sel>{$row[$textField]}</option>";
    }
}

// Actualizar proyecto
if(isset($_POST['updtProject'])){
    $pId = $pID;
    $pClass = $_POST['pClass'];
    $bom = isset($_POST['material']) ? 1 : 0;
    $distribuidor = !empty($_POST['dist']) ? $_POST['dist'] : null;
    $ingeniero = !empty($_POST['ingeniero']) ? $_POST['ingeniero'] : null;
    $software  = ($pClass == 1 && isset($_POST['software-input'])) ? $_POST['software-input'] : 0;
    $hardware  = ($pClass == 1 && isset($_POST['hardware-input'])) ? $_POST['hardware-input'] : 0;

    // Fechas
    $cierre    = !empty($_POST['cierreDoc']) ? $_POST['cierreDoc'] : null;
    $fAdj      = !empty($_POST['fAdj']) ? $_POST['fAdj'] : null;
    $finCt     = !empty($_POST['finContrato']) ? $_POST['finContrato'] : null;
    $etapa     = !empty($_POST['etapaEst']) ? $_POST['etapaEst'] : null;

    $competidor = !empty($_POST['competidor']) ? $_POST['competidor'] : null;

    $newData = [
        'id'                        => $pId,
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
        'costo_real'                => $projectData['costo_real'],
        'distribuidor'              => $distribuidor,
        'vertical'                  => $_POST['vertical'],
        'fecha_cierre_documental'   => $cierre,
        'fecha_adjudicacion'        => $fAdj,
        'fecha_fin_contrato'        => $finCt,
        'competidor'                => $competidor
    ];

    $currentJson = json_encode($projectData);
    $newJson     = json_encode($newData);

    // Actualizar licitación
    if($projectData["tipo"] == 1){
        $newLic = [
            'id'            => $licData["id"],
            'licitacion_id' => $_POST['licID'],
            'proyecto_id'   => $pId,
            'portal'        => $_POST['portal']
        ];
        if(json_encode($newLic) !== json_encode($licData)){
            $stmt = $con->prepare("UPDATE licitacion_proyecto SET licitacion_id=?, proyecto_id=?, portal=? WHERE id=?");
            $stmt->bind_param("siii", $newLic['licitacion_id'], $newLic['proyecto_id'], $newLic['portal'], $newLic['id']);
            $stmt->execute();
            $stmt->close();
        }
    }

    // Insertar actividades nuevas
    if(isset($_POST['actividades'])){
        $stmtAct = $con->prepare("INSERT INTO actividades (nombre, fecha_inicio, fecha_termino, proyecto_id, descripcion, area) VALUES (?, ?, ?, ?, ?, ?)");
        foreach($_POST['actividades']['nombre'] as $key => $name){
            $stmtAct->bind_param(
                "sssiss",
                $name,
                $_POST['actividades']['fechaInicio'][$key],
                $_POST['actividades']['fechaTermino'][$key],
                $pId,
                $_POST['actividades']['descripcion'][$key],
                $_POST['actividades']['area'][$key]
            );
            $stmtAct->execute();
        }
        $stmtAct->close();
    }

    // Insertar materiales nuevos
    if(isset($_POST['material'])){
        $stmtMat = $con->prepare("INSERT INTO bom (nombre, cantidad, total, distribuidor_id, proyecto_id) VALUES (?, ?, ?, ?, ?)");
        foreach($_POST['material']['nombre'] as $key => $name){
            $stmtMat->bind_param(
                "siiii",
                $name,
                $_POST['material']['cantidad'][$key],
                $_POST['material']['total'][$key],
                $distribuidor,
                $pId
            );
            $stmtMat->execute();
        }
        $stmtMat->close();
    }

    // Actualizar proyecto si hay cambios
    if($newJson !== $currentJson){
        $stmt = $con->prepare("UPDATE proyectos 
            SET nombre=?, cliente=?, ciudad=?, estado_id=?, estado_etapa=?, ingeniero_responsable=?, distribuidor=?, vertical=?, monto=?,
                costo_software=?, costo_hardware=?, costo_real=?, resumen=?, bom=?, fecha_cierre_documental=?, fecha_adjudicacion=?, fecha_fin_contrato=?, competidor=?, clasificacion=?
            WHERE id=?");
        $stmt->bind_param(
            "ssiiiiiiiiiisisssiii",
            $newData['nombre'], $newData['cliente'], $newData['ciudad'], $newData['estado_id'], $newData['estado_etapa'], $newData['ingeniero_responsable'],
            $newData['distribuidor'], $newData['vertical'], $newData['monto'], $newData['costo_software'], $newData['costo_hardware'], 
            $newData['costo_real'], $newData['resumen'], $newData['bom'], $newData['fecha_cierre_documental'], $newData['fecha_adjudicacion'], 
            $newData['fecha_fin_contrato'], $newData['competidor'], $newData['clasificacion'], $newData['id']
        );
        $stmt->execute();
        $stmt->close();

        echo "<script>alert('Su proyecto ha sido actualizado correctamente');location.replace(document.referrer)</script>";
    } else {
        echo "<script>alert('No se ha cambiado ningún dato');location.replace(document.referrer)</script>";
    }
}
