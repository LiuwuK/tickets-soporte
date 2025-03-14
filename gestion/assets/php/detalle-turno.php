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
                     dp.banco AS banco, 
                     CONCAT(dp.rut_cta, "-", dp.digito_verificador) AS RUTcta, 
                     dp.numero_cuenta AS numCuenta,
                     mg.motivo AS motivo, 
                     te.estado AS estado, 
                     te.created_at AS fechaCreacion,
                     us.name AS autorizadoPor,
                     te.id AS id
              FROM turnos_extra te
              JOIN sucursales su ON te.sucursal_id = su.id
              JOIN datos_pago dp ON te.datos_bancarios_id = dp.id
              JOIN motivos_gestion mg ON te.motivo_turno_id = mg.id
              JOIN `user` us ON te.autorizado_por = us.id
              WHERE te.id = ?';

    // Preparar la consulta
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $row = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}

?>