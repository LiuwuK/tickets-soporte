<?php
// Función para obtener datos
function fetchAll($con, $query){
    $res = mysqli_query($con, $query);
    return $res ? mysqli_fetch_all($res, MYSQLI_ASSOC) : [];
}

// Catálogos
$catalogs = [
    'status'       => fetchAll($con, "SELECT * FROM estados WHERE type='project'"),
    'cities'       => fetchAll($con, "SELECT * FROM ciudades ORDER BY nombre_ciudad ASC"),
    'types'        => fetchAll($con, "SELECT * FROM tipo_proyecto"),
    'class'        => fetchAll($con, "SELECT * FROM clasificacion_proyecto"),
    'ingenieros'   => fetchAll($con, "SELECT id,name FROM user WHERE cargo='1'"),
    'verticales'   => fetchAll($con, "SELECT * FROM verticales"),
    'distribuidores'=> fetchAll($con, "SELECT * FROM distribuidores"),
    'cargos'       => fetchAll($con, "SELECT * FROM cargos"),
    'portales'     => fetchAll($con, "SELECT * FROM portales"),
    'clientes'     => fetchAll($con, "SELECT * FROM clientes"),
    'competidores' => fetchAll($con, "SELECT * FROM competidores"),
];

// Función para insertar múltiples registros
function insertMultiple($con, $table, $fields, $data){
    $placeholders = implode(',', array_fill(0, count($fields), '?'));
    $types = str_repeat('s', count($fields));
    $stmt = mysqli_prepare($con, "INSERT INTO $table (".implode(',',$fields).") VALUES ($placeholders)");
    foreach($data as $row){
        mysqli_stmt_bind_param($stmt, $types, ...array_values($row));
        mysqli_stmt_execute($stmt);
    }
    mysqli_stmt_close($stmt);
}

// Crear proyecto
if(isset($_POST['newProject'])){
    $pData = [
        'nombre' => $_POST['name'],
        'cliente' => $_POST['client'],
        'ciudad' => $_POST['city'],
        'estado_id' => 19,
        'costo_software' => $_POST['software-input'] ?? 0,
        'costo_hardware' => $_POST['hardware-input'] ?? 0,
        'resumen' => $_POST['desc'],
        'fecha_creacion' => date('Y-m-d'),
        'comercial_responsable' => $_SESSION['id'],
        'monto' => $_POST['montoP'],
        'tipo' => $_POST['pType'],
        'clasificacion' => $_POST['pClass'],
        'vertical' => $_POST['vertical'],
        'fecha_cierre_documental' => $_POST['cierreDoc'] ?: null,
        'fecha_adjudicacion' => $_POST['fAdj'] ?: null,
        'fecha_fin_contrato' => $_POST['finContrato'] ?: null
    ];

    $fields = array_keys($pData);
    $placeholders = implode(',', array_fill(0,count($fields),'?'));
    $stmt = mysqli_prepare($con, "INSERT INTO proyectos (".implode(',',$fields).") VALUES ($placeholders)");
    mysqli_stmt_bind_param($stmt, str_repeat('s', count($fields)), ...array_values($pData));

    if(mysqli_stmt_execute($stmt)){
        $pId = mysqli_insert_id($con);
        mysqli_stmt_close($stmt);

        // Actividades
        if(!empty($_POST['actividades'])){
            $actData = [];
            foreach($_POST['actividades']['nombre'] as $k => $name){
                $actData[] = [
                    'nombre' => $name,
                    'fecha_inicio' => $_POST['actividades']['fechaInicio'][$k],
                    'fecha_termino' => $_POST['actividades']['fechaTermino'][$k],
                    'proyecto_id' => $pId,
                    'descripcion' => $_POST['actividades']['descripcion'][$k],
                    'area' => $_POST['actividades']['area'][$k],
                ];
            }
            insertMultiple($con,'actividades', ['nombre','fecha_inicio','fecha_termino','proyecto_id','descripcion','area'], $actData);
        }

        // Contactos (si tipo 2)
        if($_POST['pType']==2 && !empty($_POST['contacto'])){
            $contactData = [];
            foreach($_POST['contacto']['nombre'] as $k => $name){
                $contactData[] = [
                    'nombre' => $name,
                    'correo' => $_POST['contacto']['email'][$k],
                    'cargo' => $_POST['contacto']['cargo'][$k],
                    'numero' => $_POST['contacto']['contacto'][$k],
                    'proyecto_id' => $pId
                ];
            }
            insertMultiple($con,'contactos_proyecto', ['nombre','correo','cargo','numero','proyecto_id'], $contactData);
        }

        // Licitación (si tipo 1)
        if($_POST['pType']==1){
            $portal = !empty($_POST['portal']) ? $_POST['portal'] : NULL;
            $licID = $_POST['licID'];
            $stmt = mysqli_prepare($con,"INSERT INTO licitacion_proyecto (licitacion_id, proyecto_id, portal) VALUES (?,?,?)");
            mysqli_stmt_bind_param($stmt,'sii',$licID,$pId,$portal);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

        // Notificación
        $user = $_SESSION['name'];
        Notificaciones::crearTicketMail($pId,'project',$user);

        echo "<script>alert('Proyecto Registrado Correctamente'); location.replace(document.referrer)</script>";
        exit;
    } else {
        echo "<script>alert('Error al registrar el proyecto');</script>";
    }
}
?>
