<?php
//usuarios
$query = "SELECT * FROM user";
$userData = $con->prepare($query);
$userData->execute();
$result = $userData->get_result();
//obtener supervisores/departamentos/roles/ciudades/instalaciones
$query = "SELECT * FROM supervisores";
$supervisorData = $con->prepare($query);
$supervisorData->execute();
$supData = $supervisorData->get_result();
while ($row = mysqli_fetch_assoc($supData)) {
    $sup[] = $row; 
}
$query = "SELECT * FROM roles";
$rolesData = $con->prepare($query);
$rolesData->execute();
$rolData = $rolesData->get_result();
while ($row = mysqli_fetch_assoc($rolData)) {
    $rol[] = $row; 
}
$query = "SELECT * FROM departamentos";
$deptosData = $con->prepare($query);
$deptosData->execute();
$deptoData = $deptosData->get_result();
while ($row = mysqli_fetch_assoc($deptoData)) {
    $depto[] = $row; 
}

$query = "SELECT * FROM sucursales";
$sucursalesData = $con->prepare($query);
$sucursalesData->execute();
$sucursalData = $sucursalesData->get_result();
while ($row = mysqli_fetch_assoc($sucursalData)) {
    $inst[] = $row; 
}

$query = "SELECT * FROM jornadas";
$jornadasData = $con->prepare($query);
$jornadasData->execute();
$jornadaData = $jornadasData->get_result();
while ($row = mysqli_fetch_assoc($jornadaData)) {
    $jornada[] = $row; 
}


//motivos traslados
$query = "SELECT * 
            FROM motivos_gestion
            WHERE tipo_motivo = 'traslado'";
$motivosData = $con->prepare($query);
$motivosData->execute();
$motivoData = $motivosData->get_result();
while ($row = mysqli_fetch_assoc($motivoData)) {
    $motivoT[] = $row; 
}

//motivos egreso
$query = "SELECT * 
            FROM motivos_gestion
            WHERE tipo_motivo = 'egreso'";
$motivosData = $con->prepare($query);
$motivosData->execute();
$motivoData = $motivosData->get_result();
while ($row = mysqli_fetch_assoc($motivoData)) {
    $motivoE[] = $row; 
}

$solicitante = $_SESSION['id'];
if(isset($_POST['trasladoForm'])){
    $supOrigen = $_POST['supervisor'];
    $colaborador = ucwords(strtolower($_POST['colaborador']));
    $rut = $_POST['rut'];
    $instOrigen = $_POST['instalacion'];
    $jorOrigen = $_POST['jornada'];
    $motivo = $_POST['motivo'];
    $instDestino = $_POST['inDestino'];
    $jorDestino = $_POST['joDestino'];
    $rolOrigen = $_POST['rolOrigen'];
    $rolDestino = $_POST['rolDestino'];
    $fInicio = $_POST['fechaInicio'];
    $supDestino = $_POST['supervisorDestino'];
    $observacionT = $_POST['observacionT'];
    $inOrigen = $_POST['inOrigen'] ?? null;
    $inDestino = $_POST['iDestino'] ?? null;

    $checkQuery = "SELECT COUNT(*), estado FROM traslados 
                WHERE supervisor_origen = ? AND nombre_colaborador = ? AND rut = ? 
                AND instalacion_origen = ? AND jornada_origen = ? 
                AND instalacion_destino = ? AND jornada_destino = ? 
                AND rol_origen = ? AND rol_destino = ? 
                AND fecha_inicio_turno = ? AND supervisor_destino = ?";
    $checkStmt = $con->prepare($checkQuery);
    $checkStmt->bind_param("issiiiiiisi", $supOrigen, $colaborador, $rut, $instOrigen, 
                                        $jorOrigen, $instDestino, $jorDestino, 
                                        $rolOrigen, $rolDestino, $fInicio, $supDestino);
    $checkStmt->execute();
    $checkStmt->bind_result($count, $estado);
    $checkStmt->fetch();
    $checkStmt->close();

    if ($count > 0 && $estado != 'Anulado' ) {
        echo "<script>alert('Este traslado ya existe en la base de datos'); location.replace(document.referrer);</script>";
    } else {
        $query = "INSERT INTO traslados(supervisor_origen, nombre_colaborador, rut, instalacion_origen, 
                                        jornada_origen, motivo_traslado, instalacion_destino, jornada_destino, 
                                        rol_origen, rol_destino, fecha_inicio_turno, supervisor_destino, solicitante, observacion,
                                        inOrigen_nombre, inDestino_nombre) 
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $stmt = $con->prepare($query);
        $stmt->bind_param("issiiiiiiisiisss", $supOrigen, $colaborador, $rut, $instOrigen, $jorOrigen, 
                                        $motivo, $instDestino, $jorDestino, $rolOrigen, $rolDestino, 
                                        $fInicio, $supDestino, $solicitante,$observacionT,
                                    $inOrigen, $inDestino);
        if ($stmt->execute()) {
            echo "<script>alert('Traslado registrado correctamente'); location.replace(document.referrer);</script>";
        } else {
            echo "<script>alert('Error al registrar el traslado');</script>";
        }
        $stmt->close();
    }
}

if(isset($_POST['desvForm'])){
    $supOrigen = $_POST['supervisorEncargado'];
    $colaborador = ucwords(strtolower($_POST['colaborador']));
    $rut = $_POST['rut'];
    $instalacion = $_POST['instalacion'];
    $motivo = $_POST['motivo'];
    $rol = $_POST['rol'];
    $obs = $_POST['observacion'];
    $inNombre = $_POST['inNombre'] ?? null;

    $checkQuery = "SELECT COUNT(*), estado FROM desvinculaciones 
               WHERE supervisor_origen = ? AND colaborador = ? AND rut = ? 
               AND instalacion = ?";
    $checkStmt = $con->prepare($checkQuery);
    $checkStmt->bind_param("issi", $supOrigen, $colaborador, $rut, $instalacion);
    $checkStmt->execute();
    $checkStmt->bind_result($count, $estado);
    $checkStmt->fetch();
    $checkStmt->close();

    if ($count > 0 && $estado != 'Anulado' OR $count > 2 ) {
        echo "<script>alert('Esta desvinculacion ya existe en la base de datos'); location.replace(document.referrer);</script>";
    }else{
        $query = "INSERT INTO desvinculaciones(supervisor_origen, colaborador, rut, instalacion, motivo, observacion, solicitante, rol, in_nombre)
                VALUES (?,?,?,?,?,?,?,?,?)";
        $stmt = $con->prepare($query);
        $stmt->bind_param("issiisiis", $supOrigen, $colaborador, $rut, $instalacion, $motivo, $obs, $solicitante, $rol, $inNombre);
        
        if($stmt->execute()){
            $desvinculacion_id = $stmt->insert_id;
            if($motivo == 8 && !empty($_POST['fechasAusencia'])){
                $fechas = explode(", ", $_POST['fechasAusencia']);
                // Insertar cada fecha
                $queryFechas = "INSERT INTO desvinculaciones_fechas (desvinculacion_id, fecha) VALUES (?, ?)";
                $stmtFechas = $con->prepare($queryFechas);

                foreach ($fechas as $fecha) {
                    $stmtFechas->bind_param("is", $desvinculacion_id, $fecha);
                    $stmtFechas->execute();
                }
                echo "Registro insertado correctamente con múltiples fechas.";
                $stmt->close();
                $stmtFechas->close();
            }
            //Subida de archivos-------------------------------------------------------------------------------------------------------

            if(isset($_FILES['desvDocs']) && $_FILES['desvDocs']['error'] === UPLOAD_ERR_OK) {
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
            echo "<script>alert('Desvinculacion Registrada Correctamente'); location.replace(document.referrer)</script>";
        }
    }
}

//datos traslados y desvinculaciones del dia(16:00 dia anterior - 16:00 hoy)
//traslados
$query = "SELECT tr.*, 
                us.name AS soliN, -- Nombre del solicitante
                su_origen.nombre AS suOrigen, -- Sucursal de origen
                jo_origen.tipo_jornada AS joOrigen, -- Jornada de origen
                su_destino.nombre AS suDestino, -- Sucursal de destino
                jo_destino.tipo_jornada AS joDestino, -- Jornada de destino
                sup_origen.nombre_supervisor AS supOrigen, -- Supervisor de origen
                sup_destino.nombre_supervisor AS supDestino, -- Supervisor destino
                rol_origen.nombre_rol AS rolOrigen, -- rol origen
                rol_destino.nombre_rol AS rolDestino -- rol destino
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
            WHERE (tr.fecha_registro BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 DAY) + INTERVAL 16 HOUR 
            AND CURDATE() + INTERVAL 1 DAY + INTERVAL 16 HOUR) OR (tr.estado = 'En gestión')
            ";
if($_SESSION['cargo'] == 11){
    $usID = $_SESSION['id'];
    $query .= " AND solicitante = $usID";    
}
if($_SESSION['cargo'] == 13){
    $usID = $_SESSION['id'];
    $query .= " AND solicitante = $usID";    
}
$query .= " ORDER BY tr.fecha_registro ASC;";
$trasladosData = $con->prepare($query);
$trasladosData->execute();
$traslados = $trasladosData->get_result();
$num = $traslados->num_rows;

$query = "SELECT de.*, 
                su.nombre AS instalacion,
                us.name AS soliN,
                sup.nombre_supervisor AS supervisor,
                mo.motivo AS motivoEgreso,
                rl.nombre_rol AS rolN
            FROM desvinculaciones de
            JOIN user us ON(de.solicitante = us.id)
            JOIN sucursales su ON(de.instalacion = su.id)
            JOIN supervisores sup ON(de.supervisor_origen = sup.id)
            JOIN motivos_gestion mo ON(de.motivo = mo.id)
            LEFT JOIN roles rl ON(de.rol = rl.id)
            WHERE (de.fecha_registro BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 DAY) + INTERVAL 16 HOUR 
            AND CURDATE() + INTERVAL 1 DAY + INTERVAL 16 HOUR)
            OR (de.estado = 'En gestión')
";
if($_SESSION['cargo'] == 11){
    $usID = $_SESSION['id'];
    $query .= " AND solicitante = $usID";    
}
if($_SESSION['cargo'] == 13){
    $usID = $_SESSION['id'];
    $query .= " AND solicitante = $usID";    
}
$query .= " ORDER BY de.fecha_registro ASC;";
$desvData = $con->prepare($query);
$desvData->execute();
$desvinculaciones = $desvData->get_result();
$num_des = $desvinculaciones->num_rows;

if(isset($_POST['delTr'])){
    $id = $_POST['idTr'];
    $query = "DELETE FROM traslados WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "<script>alert('Traslado eliminado correctamente.');location.replace(document.referrer)</script>";
    } else {
        echo "<script>alert('Error al eliminar el supervisor.');</script>";
    }
    $stmt->close();
}

if(isset($_POST['delDesv'])){
    $id = $_POST['idDesv'];
    $query = "DELETE FROM desvinculaciones WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "<script>alert('Desvinculacion eliminado correctamente.');location.replace(document.referrer)</script>";
    } else {
        echo "<script>alert('Error al eliminar el supervisor.');</script>";
    }
    $stmt->close();
}
?>