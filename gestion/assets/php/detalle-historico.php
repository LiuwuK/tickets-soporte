<?php
if(isset($_GET['tipo'])){
    $tipo = $_GET['tipo'];
    $id = $_GET['id'];

    if($tipo == 'traslado'){
        $query = "SELECT tr.nombre_colaborador AS colaborador,
                    tr.rut AS rutC,
                    tr.fecha_inicio_turno AS fecha_turno,
                    tr.observacion AS obs,
                    us.name AS soliN, -- Nombre del solicitante
                    su_origen.nombre AS suOrigen, -- Sucursal de origen
                    jo_origen.tipo_jornada AS joOrigen, -- Jornada de origen
                    su_destino.nombre AS suDestino, -- Sucursal de destino
                    jo_destino.tipo_jornada AS joDestino, -- Jornada de destino
                    sup_origen.nombre_supervisor AS supOrigen, -- Supervisor de origen
                    sup_destino.nombre_supervisor AS supDestino, -- Supervisor destino
                    mg.motivo AS motivoN, -- Motivo traslado
                    rol_origen.nombre_rol AS rolOrigen, -- rol origen
                    rol_destino.nombre_rol AS rolDestino, -- rol destino
                    tr.estado,
                    tr.obs_rrhh
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
                WHERE tr.id = $id
            ";
        $tr = mysqli_query($con, $query);

    }else{
        $query = "SELECT de.*,
                        de.instalacion AS instalacion_id,
                        su.nombre AS instalacion,
                        us.name AS soliN,
                        sup.nombre_supervisor AS supervisor,
                        mo.motivo AS motivoEgreso,
                        doc.url AS url,
                        de.id AS idDesv
                    FROM desvinculaciones de
                    JOIN user us ON(de.solicitante = us.id)
                    JOIN sucursales su ON(de.instalacion = su.id)
                    JOIN supervisores sup ON(de.supervisor_origen = sup.id)
                    JOIN motivos_gestion mo ON(de.motivo = mo.id)
                    LEFT JOIN desvinculaciones_docs doc ON(de.id = doc.desvinculacion_id)
                    WHERE de.id = $id
                    ";
        $info = mysqli_query($con, $query);
        $dv = mysqli_fetch_assoc($info);
        $motivo = $dv['motivo'];    
        if($motivo == 8){
            $query = " SELECT *
                        FROM desvinculaciones_fechas 
                        WHERE desvinculacion_id = $id
                    ";
            $infoAusencia = mysqli_query($con, $query);
        }
    }
}

if(isset($_POST['newDoc'])){
    if(isset($_FILES['desvDocs']) && $_FILES['desvDocs']['error'] === UPLOAD_ERR_OK) {
        $desvinculacion_id = $_POST['desv'];
        // Configuración de Supabase
        $supabase_url = 'https://zessdkphohirwcsbqnif.supabase.co';
        $supabase_key = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Inplc3Nka3Bob2hpcndjc2JxbmlmIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NDAwNjMxOTMsImV4cCI6MjA1NTYzOTE5M30.iTUsOH7OxO49h62FQCmXV05-DZKUwQ1RFLGdC_FEEWE';
        $bucket_name = "safeteck.uploads";
        
        // Define la subcarpeta (puedes hacerla dinámica según tus necesidades)
        $subcarpeta = "desvinculaciones/".date('Y-m')."/"; 
        
        // Prepara el nombre del archivo
        $file_name = str_replace(" ", "_", $_FILES["desvDocs"]["name"]);
        $file_path = $_FILES["desvDocs"]["tmp_name"];
        $file_type = $_FILES["desvDocs"]["type"];
        
        // URL con subcarpeta
        $url = "$supabase_url/storage/v1/object/$bucket_name/$subcarpeta$file_name";
        
        // Inicializar cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $supabase_key",
            "Content-Type: $file_type",
            "x-upsert: true"
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents($file_path));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($response === false) {
            echo "Error en cURL: $error";
        } else {
            $filePath = $url;
            $query = "INSERT INTO desvinculaciones_docs(desvinculacion_id, url)
            VALUES (?,?)";
            $stmt = $con->prepare($query);
            $stmt->bind_param("is",$desvinculacion_id, $filePath);
            $stmt->execute();
            $stmt->close();
            echo "✅ Archivo subido correctamente a $subcarpeta: $file_name";
        }
    }
}

if(isset($_POST['rrhh'])){
    $obs = $_POST['descRRHH'];

    if($tipo == 'traslado'){
        $query = "UPDATE traslados 
                    SET obs_rrhh = ?
                    WHERE id = ? ";
        $stmt = $con->prepare($query);
        $stmt->bind_param("si", $obs, $id);
        $stmt->execute();
    }else{
        $query = "UPDATE desvinculaciones 
                    SET obs_rrhh = ?
                    WHERE id = ? ";
        $stmt = $con->prepare($query);
        $stmt->bind_param("si", $obs, $id);
        $stmt->execute();
    }
    echo "<script>alert('Observacion agregada correctamente.'); location.href='detalle-historico.php?id=$id&tipo=$tipo';</script>";
}
?>