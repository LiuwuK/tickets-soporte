<?php


// Función única para obtener datos
function obtenerDatos($con) {
    $datos = [];
    
    // Tablas a obtener
    $tablas = [
        'supervisores' => 'nombre_supervisor as nombre',
        'roles' => 'nombre_rol as nombre', 
        'sucursales' => 'nombre',
        'jornadas' => 'tipo_jornada as nombre',
        'motivos_gestion' => 'motivo as nombre, tipo_motivo'
    ];
    
    foreach ($tablas as $tabla => $campos) {
        $query = "SELECT id, $campos FROM $tabla";
        if ($tabla === 'motivos_gestion') {
            $query .= " WHERE tipo_motivo IN ('traslado', 'egreso')";
        }
        
        $result = $con->query($query);
        if ($result) {
            $datos[$tabla] = $result->fetch_all(MYSQLI_ASSOC);
        }
    }
    
    return $datos;
}

// Obtener traslados
function getTraslados($con, $userId, $role) {
    $query = "SELECT tr.*, 
                us.name AS soliN,
                su_origen.nombre AS suOrigen,
                jo_origen.tipo_jornada AS joOrigen,
                su_destino.nombre AS suDestino,
                jo_destino.tipo_jornada AS joDestino,
                sup_origen.nombre_supervisor AS supOrigen,
                sup_destino.nombre_supervisor AS supDestino,
                su_origen.razon_social AS raOrigen,
                su_destino.razon_social AS raDestino,
                rol_origen.nombre_rol AS rolOrigen,
                rol_destino.nombre_rol AS rolDestino
              FROM traslados tr
              JOIN user us ON tr.solicitante = us.id
              JOIN sucursales su_origen ON tr.instalacion_origen = su_origen.id
              JOIN sucursales su_destino ON tr.instalacion_destino = su_destino.id
              JOIN jornadas jo_origen ON tr.jornada_origen = jo_origen.id
              JOIN jornadas jo_destino ON tr.jornada_destino = jo_destino.id
              JOIN supervisores sup_origen ON tr.supervisor_origen = sup_origen.id
              JOIN supervisores sup_destino ON tr.supervisor_destino = sup_destino.id
              JOIN roles rol_origen ON tr.rol_origen = rol_origen.id
              JOIN roles rol_destino ON tr.rol_destino = rol_destino.id
              WHERE (tr.fecha_registro BETWEEN 
                    DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 1 DAY), '%Y-%m-%d 16:00:00')
                    AND DATE_FORMAT(NOW(), '%Y-%m-%d 16:00:00')
                    ) OR tr.estado = 'En gestión'";
    
    if(in_array($role, [11,13])) $query .= " AND solicitante = $userId";
    $query .= " ORDER BY tr.fecha_registro ASC";
    
    return $con->query($query);
}

// Obtener desvinculaciones
function getDesvinculaciones($con, $userId, $role) {
    $query = "SELECT de.*, 
                    su.nombre AS instalacion,
                    su.razon_social AS razon,
                    us.name AS soliN,
                    sup.nombre_supervisor AS supervisor,
                    mo.motivo AS motivoEgreso,
                    rl.nombre_rol AS rolN
              FROM desvinculaciones de
              JOIN user us ON de.solicitante = us.id
              JOIN sucursales su ON de.instalacion = su.id
              JOIN supervisores sup ON de.supervisor_origen = sup.id
              JOIN motivos_gestion mo ON de.motivo = mo.id
              LEFT JOIN roles rl ON de.rol = rl.id
              WHERE (de.fecha_registro BETWEEN 
                    DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 1 DAY), '%Y-%m-%d 16:00:00')
                    AND DATE_FORMAT(NOW(), '%Y-%m-%d 16:00:00')
                    ) OR de.estado = 'En gestión'";
    
    if(in_array($role, [11,13])) $query .= " AND solicitante = $userId";
    $query .= " ORDER BY de.fecha_registro ASC";
    
    return $con->query($query);
}

// --- CARGA DE DATOS ---
$usRol = $_SESSION['cargo'];
$usID = $_SESSION['id'];

// Cargar todos los datos
$datos = obtenerDatos($con);
$sup = $datos['supervisores'] ?? [];
$rol = $datos['roles'] ?? [];
$inst = $datos['sucursales'] ?? [];
$jornada = $datos['jornadas'] ?? [];

// Filtrar motivos
$motivoT = array_filter($datos['motivos_gestion'] ?? [], function($m) {
    return $m['tipo_motivo'] === 'traslado';
});
$motivoE = array_filter($datos['motivos_gestion'] ?? [], function($m) {
    return $m['tipo_motivo'] === 'egreso';
});

$traslados = getTraslados($con, $usID, $usRol);
$num = $traslados->num_rows;
$desvinculaciones = getDesvinculaciones($con, $usID, $usRol);
$num_des = $desvinculaciones->num_rows;

// --- INSERCIONES DE FORMULARIOS ---
$solicitante = $usID;

// Inserción Traslado
if(isset($_POST['trasladoForm'])){
    $data = [
        "supOrigen"=>$_POST['supervisor'],
        "colaborador"=>ucwords(strtolower($_POST['colaborador'])),
        "rut"=>$_POST['rut'],
        "instOrigen"=>$_POST['instalacion'],
        "jorOrigen"=>$_POST['jornada'],
        "motivo"=>$_POST['motivo'],
        "instDestino"=>$_POST['inDestino'],
        "jorDestino"=>$_POST['joDestino'],
        "rolOrigen"=>$_POST['rolOrigen'],
        "rolDestino"=>$_POST['rolDestino'],
        "fInicio"=>$_POST['fechaInicio'],
        "supDestino"=>$_POST['supervisorDestino'],
        "observacion"=>$_POST['observacionT'],
        "inOrigen"=>$_POST['inOrigen'] ?? null,
        "inDestino"=>$_POST['iDestino'] ?? null
    ];

    // Validación duplicado
    $checkQuery = "SELECT COUNT(*), estado FROM traslados 
                WHERE supervisor_origen=? AND nombre_colaborador=? AND rut=? 
                AND instalacion_origen=? AND jornada_origen=? 
                AND instalacion_destino=? AND jornada_destino=? 
                AND rol_origen=? AND rol_destino=? 
                AND fecha_inicio_turno=? AND supervisor_destino=?";
    
    $stmt = $con->prepare($checkQuery);
    $stmt->bind_param("issiiiiiisi",
        $data['supOrigen'],$data['colaborador'],$data['rut'],$data['instOrigen'],$data['jorOrigen'],
        $data['instDestino'],$data['jorDestino'],$data['rolOrigen'],$data['rolDestino'],
        $data['fInicio'],$data['supDestino']
    );
    $stmt->execute();
    $stmt->bind_result($count,$estado);
    $stmt->fetch();
    $stmt->close();

    if($count>0 && $estado!="Anulado"){
        echo "<script>alert('Este traslado ya existe');location.replace(document.referrer)</script>";
        exit;
    }

    $insertQuery = "INSERT INTO traslados(supervisor_origen, nombre_colaborador, rut, instalacion_origen,
                        jornada_origen, motivo_traslado, instalacion_destino, jornada_destino,
                        rol_origen, rol_destino, fecha_inicio_turno, supervisor_destino, solicitante, observacion,
                        inOrigen_nombre, inDestino_nombre)
                    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    
    $stmt = $con->prepare($insertQuery);
    $stmt->bind_param("issiiiiiiisiisss",
        $data['supOrigen'],$data['colaborador'],$data['rut'],$data['instOrigen'],$data['jorOrigen'],
        $data['motivo'],$data['instDestino'],$data['jorDestino'],$data['rolOrigen'],$data['rolDestino'],
        $data['fInicio'],$data['supDestino'],$solicitante,$data['observacion'],$data['inOrigen'],$data['inDestino']
    );
    
    if($stmt->execute()){
        echo "<script>alert('Traslado registrado');location.replace(document.referrer)</script>";
    }else{
        echo "<script>alert('Error al registrar traslado');</script>";
    }
    $stmt->close();
}

// Inserción Desvinculacion
if(isset($_POST['desvForm'])){
    $data = [
        "supOrigen"=>$_POST['supervisorEncargado'],
        "colaborador"=>ucwords(strtolower($_POST['colaborador'])),
        "rut"=>$_POST['rut'],
        "instalacion"=>$_POST['instalacion'],
        "motivo"=>$_POST['motivo'],
        "rol"=>$_POST['rol'],
        "obs"=>$_POST['observacion'],
        "in_nombre"=>$_POST['inNombre'] ?? null
    ];
    $checkQuery = "SELECT COUNT(*), estado FROM desvinculaciones 
                   WHERE supervisor_origen=? AND colaborador=? AND rut=? AND instalacion=?";
    
    $stmt = $con->prepare($checkQuery);
    $stmt->bind_param("isis",$data['supOrigen'],$data['colaborador'],$data['rut'],$data['instalacion']);
    $stmt->execute();
    $stmt->bind_result($count,$estado);
    $stmt->fetch();
    $stmt->close();

    if($count>0 && $estado!="Anulado"){
        echo "<script>alert('Esta desvinculación ya existe');location.replace(document.referrer)</script>";
        exit;
    }

    $insertQuery = "INSERT INTO desvinculaciones(supervisor_origen,colaborador,rut,instalacion,
                    motivo,rol,solicitante,observacion,in_nombre) VALUES(?,?,?,?,?,?,?,?,?)";
    
    $stmt = $con->prepare($insertQuery);
    $stmt->bind_param("isisiisis",
        $data['supOrigen'],$data['colaborador'],$data['rut'],$data['instalacion'],
        $data['motivo'],$data['rol'],$solicitante,$data['obs'],$data['inNombre']
    );
    
    if($stmt->execute()){
        echo "<script>alert('Desvinculación registrada');location.replace(document.referrer)</script>";
    }else{
        echo "<script>alert('Error al registrar desvinculación');</script>";
    }
    $stmt->close();
}
?>