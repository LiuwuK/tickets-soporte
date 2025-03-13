<?php
$query = "SELECT * FROM sucursales";
$sucursalesData = $con->prepare($query);
$sucursalesData->execute();
$sucursalData = $sucursalesData->get_result();
while ($row = mysqli_fetch_assoc($sucursalData)) {
    $inst[] = $row; 
}
//motivos turnos extra
$query = "SELECT * 
            FROM motivos_gestion
            WHERE tipo_motivo = 'turnos'";
$motivosData = $con->prepare($query);
$motivosData->execute();
$motivoData = $motivosData->get_result();
while ($row = mysqli_fetch_assoc($motivoData)) {
    $motivo[] = $row; 
}
if(isset($_POST['newExtra'])){
    //Insertar datos bancarios
    $banco = $_POST['banco'];
    $rutBanco = $_POST['rut'];
    $partes = explode('-', $rutBanco);
    $rutNum = $partes[0];
    $dv = end($partes); 
    $numCta = $_POST['numCta'];

    // Verificar si ya existe
    $queryCheck = "SELECT id FROM datos_pago WHERE banco = ? AND rut_cta = ? AND digito_verificador = ? AND numero_cuenta = ?";
    $stmtCheck = $con->prepare($queryCheck);
    $stmtCheck->bind_param("ssss", $banco, $rutNum, $dv, $numCta);
    $stmtCheck->execute();
    $stmtCheck->bind_result($bancoID);
    $stmtCheck->fetch();
    $stmtCheck->close();
    if (!$bancoID) {
        //Insertar datos bancarios
        $queryInsert = "INSERT INTO datos_pago (banco, rut_cta, digito_verificador, numero_cuenta) VALUES (?, ?, ?, ?)";
        $stmtInsert = $con->prepare($queryInsert);
        $stmtInsert->bind_param("ssss", $banco, $rutNum, $dv, $numCta);
        $stmtInsert->execute();
        $bancoID = $stmtInsert->insert_id; // Obtener el ID recién insertado
        $stmtInsert->close();
    }
    //Insertar Turno
    $instalacion = $_POST['instalacion'];
    $fecha_turno = $_POST['fecha_turno'];
    $horas = $_POST['horas_cubiertas'];
    $monto = $_POST['monto'];
    $colaborador = $_POST['nombre_colaborador'];
    $rut = $_POST['rutCta'];
    $motivo = $_POST['motivo_turno'];
    $autorizado = $_SESSION['id'];

    $query = "INSERT INTO turnos_extra (sucursal_id, fecha_turno, horas_cubiertas, monto, nombre_colaborador, rut, datos_bancarios_id,
                                        motivo_turno_id, autorizado_por)
                VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $con->prepare($query);
    $stmt->bind_param("isiissiii", $instalacion, $fecha_turno, $horas, $monto, $colaborador, $rut, $bancoID, $motivo, $autorizado);
    $stmt->execute();
    $bancoID = $stmt->insert_id;
    $stmt->close();

    echo "<script>alert('Turno Agregado Correctamente.'); location.href='nuevo-turno.php';</script>";
}
?>