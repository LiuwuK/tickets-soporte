<?php
$id = $_GET['id'] ?? null;
$tipo = $_GET['tipo'] ?? null;

// Normalizamos tipo recibido
$tipo = strtolower($tipo);

if($tipo == 'traslado'){
    $tr = fetchTraslado($con,$id);
} else if($tipo == 'desvinculacion'){
    $dv = fetchDesvinculacion($con,$id);
    if(isset($dv['motivo']) && $dv['motivo'] == 8){
        $infoAusencia = $con->prepare("SELECT fecha FROM desvinculaciones_fechas WHERE desvinculacion_id = ?");
        $infoAusencia->bind_param("i",$id);
        $infoAusencia->execute();
        $infoAusencia = $infoAusencia->get_result();
    }
}

function fetchTraslado($con, $id){
    $sql = "SELECT tr.nombre_colaborador AS colaborador, tr.rut AS rutC, tr.fecha_inicio_turno AS fecha_turno,
            tr.observacion AS obs, tr.inOrigen_nombre AS nombre_origen, tr.inDestino_nombre AS nombre_destino,
            us.name AS soliN, su_origen.nombre AS suOrigen, jo_origen.tipo_jornada AS joOrigen,
            su_destino.nombre AS suDestino, jo_destino.tipo_jornada AS joDestino,
            sup_origen.nombre_supervisor AS supOrigen, sup_destino.nombre_supervisor AS supDestino,
            su_origen.razon_social AS raOrigen, su_destino.razon_social AS raDestino,
            mg.motivo AS motivoN, rol_origen.nombre_rol AS rolOrigen, rol_destino.nombre_rol AS rolDestino,
            tr.estado, tr.obs_rrhh
            FROM traslados tr
            JOIN user us ON tr.solicitante = us.id
            JOIN sucursales su_origen ON tr.instalacion_origen = su_origen.id
            JOIN sucursales su_destino ON tr.instalacion_destino = su_destino.id
            JOIN jornadas jo_origen ON tr.jornada_origen = jo_origen.id
            JOIN jornadas jo_destino ON tr.jornada_destino = jo_destino.id
            JOIN supervisores sup_origen ON tr.supervisor_origen = sup_origen.id
            JOIN supervisores sup_destino ON tr.supervisor_destino = sup_destino.id
            JOIN motivos_gestion mg ON tr.motivo_traslado = mg.id
            JOIN roles rol_origen ON tr.rol_origen = rol_origen.id
            JOIN roles rol_destino ON tr.rol_destino = rol_destino.id
            WHERE tr.id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i",$id);
    $stmt->execute();
    return $stmt->get_result();
}

function fetchDesvinculacion($con, $id){
    $sql = "SELECT de.*, su.nombre AS instalacion, us.name AS soliN,
            sup.nombre_supervisor AS supervisor, mo.motivo AS motivoEgreso,
            doc.url AS url, de.id AS idDesv, su.razon_social AS razon
            FROM desvinculaciones de
            JOIN user us ON de.solicitante = us.id
            JOIN sucursales su ON de.instalacion = su.id
            JOIN supervisores sup ON de.supervisor_origen = sup.id
            JOIN motivos_gestion mo ON de.motivo = mo.id
            LEFT JOIN desvinculaciones_docs doc ON de.id = doc.desvinculacion_id
            WHERE de.id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i",$id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

if($tipo == 'traslado'){
    $tr = fetchTraslado($con,$id);
}else{
    $dv = fetchDesvinculacion($con,$id);
    if($dv['motivo'] == 8){
        $infoAusencia = $con->prepare("SELECT fecha FROM desvinculaciones_fechas WHERE desvinculacion_id = ?");
        $infoAusencia->bind_param("i",$id);
        $infoAusencia->execute();
        $infoAusencia = $infoAusencia->get_result();
    }
}

// Función para actualizar RRHH
if(isset($_POST['rrhh'])){
    $obs = $_POST['descRRHH'] ?? '';
    $table = ($tipo == 'traslado') ? 'traslados' : 'desvinculaciones';
    $stmt = $con->prepare("UPDATE $table SET obs_rrhh=? WHERE id=?");
    $stmt->bind_param("si",$obs,$id);
    $stmt->execute();
    echo "<script>alert('Observación agregada correctamente'); location.href='detalle-historico.php?id=$id&tipo=$tipo';</script>";
}

// Función de subida de archivo
function uploadDesvFile($file, $desv_id){
    $supabase_url = 'https://zessdkphohirwcsbqnif.supabase.co';
    $supabase_key = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Inplc3Nka3Bob2hpcndjc2JxbmlmIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NDAwNjMxOTMsImV4cCI6MjA1NTYzOTE5M30.iTUsOH7OxO49h62FQCmXV05-DZKUwQ1RFLGdC_FEEWE';
    $bucket_name = "safeteck.uploads";
        
    $subcarpeta = "desvinculaciones/".date('Y-m')."/";
    $file_name = str_replace(" ","_",$file["name"]);
    $file_path = $file["tmp_name"];
    $file_type = $file["type"];
    $url = "$supabase_url/storage/v1/object/$bucket_name/$subcarpeta$file_name";

    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"PUT");
    curl_setopt($ch,CURLOPT_HTTPHEADER,[
        "Authorization: Bearer $supabase_key",
        "Content-Type: $file_type",
        "x-upsert: true"
    ]);
    curl_setopt($ch,CURLOPT_POSTFIELDS,file_get_contents($file_path));
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    $response = curl_exec($ch);
    curl_close($ch);

    return $response ? $url : false;
}

if(isset($_POST['newDoc']) && isset($_FILES['desvDocs'])){
    $url = uploadDesvFile($_FILES['desvDocs'], $_POST['desv']);
    if($url){
        $stmt = $con->prepare("INSERT INTO desvinculaciones_docs(desvinculacion_id,url) VALUES (?,?)");
        $stmt->bind_param("is",$_POST['desv'],$url);
        $stmt->execute();
    }
}
?>
