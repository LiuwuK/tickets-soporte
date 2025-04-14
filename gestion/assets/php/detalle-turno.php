<?php
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Preparar la consulta SQL con parámetros
    $query = 'SELECT su.nombre AS instalacion,
                     te.fecha_turno AS fechaTurno, 
                     te.horas_cubiertas AS horas, 
                     te.monto AS monto,
                     te.nombre_colaborador AS colaborador, 
                     te.rut AS rut,  
                     bc.nombre_banco AS banco, 
                     CONCAT(dp.rut_cta, "-", dp.digito_verificador) AS RUTcta, 
                     dp.numero_cuenta AS numCuenta,
                     mg.motivo AS motivo, 
                     te.estado AS estado, 
                     te.created_at AS fechaCreacion,
                     us.name AS autorizadoPor,
                     te.id AS id,
                     te.motivo_rechazo AS motivoN,
                     te.persona_motivo AS persona_motivo,
                     te.contratado AS contratado,
                     te.justificacion AS justificacion,
                     te.nacionalidad AS nacionalidad,
                     CONCAT(TIME_FORMAT(te.hora_inicio, "%H:%i"), " - ", TIME_FORMAT(te.hora_termino, "%H:%i")) AS horario
              FROM turnos_extra te
              LEFT JOIN sucursales su ON te.sucursal_id = su.id
              JOIN datos_pago dp ON te.datos_bancarios_id = dp.id
              JOIN bancos bc ON dp.banco = bc.id
              JOIN motivos_gestion mg ON te.motivo_turno_id = mg.id
              JOIN `user` us ON te.autorizado_por = us.id
              WHERE te.id = ?';

    // Preparar la consulta
    $stmt = mysqli_prepare($con, $query);
    if (!$stmt) {
        die("Error al preparar la consulta: " . mysqli_error($con));
    }
    mysqli_stmt_bind_param($stmt, 'i', $id);
    if (!mysqli_stmt_execute($stmt)) {
        die("Error al ejecutar la consulta: " . mysqli_stmt_error($stmt));
    }
    $result = mysqli_stmt_get_result($stmt);
    if (!$result) {
        die("Error al obtener resultados: " . mysqli_error($con));
    }
    $row = mysqli_fetch_assoc($result);
    if (!$row) {
        die("No se encontró ningún turno con el ID proporcionado");
    }
} else {
    die("No se proporcionó ID de turno");
}
//aprobar turno
if(isset($_POST['approved'])){
    $id = $_GET['id'];
    $estado = 'aprobado';
    $query = "UPDATE turnos_extra 
                SET estado = ?,
                    motivo_rechazo = null,
                    justificacion = null
                WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("si", $estado, $id);
    $stmt->execute();
    echo "<script>alert('Turno Aprobado'); location.href='detalle-turno.php?id=$id';</script>";
}
//aprobar pago del turno
if(isset($_POST['pago'])){
    $id = $_GET['id'];
    $estado = 'pago procesado';
    $query = "UPDATE turnos_extra 
                SET estado = ? 
                WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("si", $estado, $id);
    $stmt->execute();
    echo "<script>alert('Turno Aprobado'); location.href='detalle-turno.php?id=$id';</script>";
}

//rechazar turno
if(isset($_POST['denTurno'])){
    $id = $_GET['id'];
    $estado = 'rechazado';
    $motivo = $_POST['motivoR'];
    $query = "UPDATE turnos_extra 
                SET estado = ? ,
                    motivo_rechazo = ?
                WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("ssi", $estado, $motivo, $id);
    $stmt->execute();
    echo "<script>alert('Turno Rechazado'); location.href='detalle-turno.php?id=$id';</script>";
}

//justificacion del turno
if(isset($_POST['justificar'])){
    $id = $_GET['id'];
    $estado = 'pendiente en operaciones';
    $justificacion = $_POST['justi'];
    $query = "UPDATE turnos_extra 
                SET estado = ? ,
                    justificacion = ?
                WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("ssi", $estado, $justificacion, $id);
    $stmt->execute();
    echo "<script>alert('Turno Justificado'); location.href='detalle-turno.php?id=$id';</script>";
}
?>