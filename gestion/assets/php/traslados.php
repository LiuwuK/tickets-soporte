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
    $colaborador = $_POST['colaborador'];
    $rut = $_POST['rut'];
    $instOrigen = $_POST['instalacion'];
    $jorOrigen = $_POST['jornada'];
    $motivo = $_POST['motivo'];
    $instDestino = $_POST['inDestino'];
    $jorDestino = $_POST['joDestino'];
    $rol = $_POST['rol'];
    $fInicio = $_POST['fechaInicio'];
    $supDestino = $_POST['supervisorDestino'];

    $query =  "INSERT INTO 
                    traslados(supervisor_origen, nombre_colaborador, rut, instalacion_origen, 
                            jornada_origen, motivo_traslado, instalacion_destino, jornada_destino, 
                            rol, fecha_inicio_turno, supervisor_destino, solicitante)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
    $stmt = $con->prepare($query);
    $stmt->bind_param("issiiiiiisii",$supOrigen, $colaborador, $rut, $instOrigen, $jorOrigen, 
                    $motivo, $instDestino, $jorDestino, $rol, $fInicio, $supDestino, $solicitante);
    if($stmt->execute()){
        echo "<script>alert('Traslado Registrado Correctamente'); location.replace(document.referrer)</script>";
    }
}

if(isset($_POST['desvForm'])){
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    $supOrigen = $_POST['supervisorEncargado'];
    $colaborador = $_POST['colaborador'];
    $rut = $_POST['rut'];
    $instalacion = $_POST['instalacion'];
    $motivo = $_POST['motivo'];
    $obs = $_POST['observacion'];

    $query = "INSERT INTO desvinculaciones(supervisor_origen, colaborador, rut, instalacion, motivo, observacion, solicitante)
                VALUES (?,?,?,?,?,?,?)";
    $stmt = $con->prepare($query);
    $stmt->bind_param("issiisi", $supOrigen, $colaborador, $rut, $instalacion, $motivo, $obs, $solicitante);
    
    if($stmt->execute()){
        echo "<script>alert('Desvinculacion Registrada Correctamente'); location.replace(document.referrer)</script>";
    }
}
?>