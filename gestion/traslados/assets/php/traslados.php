<?php
// Datos de usuario
$usRol = $_SESSION['cargo'];
$usID = $_SESSION['id'];

// --- Función datos de referencia ---
function obtenerDatos($con){
    $datos = [];

    // Traer roles
    $res = $con->query("SELECT id, nombre_rol AS nombre FROM roles");
    $datos['roles'] = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

    // Traer jornadas
    $res = $con->query("SELECT id, tipo_jornada AS nombre FROM jornadas");
    $datos['jornadas'] = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

    // Traer motivos de gestión (traslado y egreso)
    $res = $con->query("SELECT id, motivo AS nombre, tipo_motivo FROM motivos_gestion WHERE tipo_motivo IN ('traslado','egreso')");
    $datos['motivos_gestion'] = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

    // Traer sucursales con supervisor
    $res = $con->query("SELECT s.id, s.nombre, s.supervisor_id AS supervisorID, sup.nombre_supervisor AS supervisor_nombre 
                        FROM sucursales s 
                        LEFT JOIN supervisores sup ON s.supervisor_id = sup.id");
    $datos['sucursales'] = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

    return $datos;
}

$datos = obtenerDatos($con);

// --- Función genérica para listar ---
function listar($con, $tabla){
    if($tabla==='traslados'){
        $q="SELECT tr.*, us.name AS soliN,
            su_origen.nombre AS suOrigen, jo_origen.tipo_jornada AS joOrigen,
            su_destino.nombre AS suDestino, jo_destino.tipo_jornada AS joDestino,
            sup_origen.nombre_supervisor AS supOrigen, sup_destino.nombre_supervisor AS supDestino,
            rol_origen.nombre_rol AS rolOrigen, rol_destino.nombre_rol AS rolDestino
            FROM traslados tr
            JOIN user us ON tr.solicitante=us.id
            JOIN sucursales su_origen ON tr.instalacion_origen=su_origen.id
            JOIN sucursales su_destino ON tr.instalacion_destino=su_destino.id
            JOIN jornadas jo_origen ON tr.jornada_origen=jo_origen.id
            JOIN jornadas jo_destino ON tr.jornada_destino=jo_destino.id
            JOIN supervisores sup_origen ON tr.supervisor_origen=sup_origen.id
            JOIN supervisores sup_destino ON tr.supervisor_destino=sup_destino.id
            JOIN roles rol_origen ON tr.rol_origen=rol_origen.id
            JOIN roles rol_destino ON tr.rol_destino=rol_destino.id
            WHERE tr.estado='En gestión'
            ORDER BY tr.fecha_registro DESC";
    } elseif($tabla==='desvinculaciones'){
        $q="SELECT d.*, us.name AS soliN, s.nombre AS sucN, r.nombre_rol AS rolN, sup.nombre_supervisor AS supN, mo.motivo AS motivoEgreso
            FROM desvinculaciones d
            JOIN user us ON d.solicitante=us.id
            JOIN sucursales s ON d.instalacion=s.id
            JOIN roles r ON d.rol=r.id
            JOIN supervisores sup ON d.supervisor_origen=sup.id
            JOIN motivos_gestion mo ON d.motivo = mo.id
            WHERE d.estado='En gestión'
            ORDER BY d.fecha_registro DESC";
    }
    return $con->query($q);
}

// Obtener listas
$traslados = listar($con,'traslados');
$desvinculaciones = listar($con,'desvinculaciones');

$num = $traslados ? $traslados->num_rows : 0;
$num_des = $desvinculaciones ? $desvinculaciones->num_rows : 0;

// --- Inserciones ---
if($_SERVER['REQUEST_METHOD']==='POST'){
    if(isset($_POST['tipo']) && $_POST['tipo']==='traslado'){
        $stmt=$con->prepare("INSERT INTO traslados(nombre_colaborador,rut,solicitante,instalacion_origen,instalacion_destino,jornada_origen,jornada_destino,rol_origen,rol_destino,supervisor_origen,supervisor_destino,motivo_traslado,observacion,fecha_inicio_turno) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("sssiiiiiiissss",
            $_POST['colaborador'],
            $_POST['rut'],
            $usID,
            $_POST['inst_origen'],
            $_POST['inst_destino'],
            $_POST['jor_origen'],
            $_POST['jor_destino'],
            $_POST['rol_origen'],
            $_POST['rol_destino'],
            $_POST['sup_origen_ID'],
            $_POST['sup_destino_ID'],
            $_POST['motivo'],
            $_POST['observacion'],
            $_POST['fecha_inicio']
        );
        if($stmt->execute()) $_SESSION['swal'] = ['tipo'=>'success','msg'=>'Traslado registrado correctamente'];
        else $_SESSION['swal'] = ['tipo'=>'error','msg'=>'Error al registrar traslado'];
    }

    if(isset($_POST['tipo']) && $_POST['tipo']==='desvinculacion'){
        $stmt=$con->prepare("INSERT INTO desvinculaciones(colaborador,rut,solicitante,instalacion,rol,supervisor_origen,motivo,observacion) VALUES(?,?,?,?,?,?,?,?)");
        $stmt->bind_param("sssiiiss",
            $_POST['colaborador'],
            $_POST['rut'],
            $usID,
            $_POST['instalacion'],
            $_POST['rol'],
            $_POST['supervisorID'],
            $_POST['motivo'],
            $_POST['observacion']
        );
        if($stmt->execute()) $_SESSION['swal'] = ['tipo'=>'success','msg'=>'Desvinculación registrada correctamente'];
        else $_SESSION['swal'] = ['tipo'=>'error','msg'=>'Error al registrar desvinculación'];
    }
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

