<?php

//obtener estados 
$query = "SHOW COLUMNS FROM turnos_extra LIKE 'estado'";
$result = $con->query($query);

if ($result) {
    $row = $result->fetch_assoc();
    $type = $row['Type']; 
    preg_match("/^enum\(\'(.*)\'\)$/", $type, $matches);
    $valoresEnum = explode("','", $matches[1]);
} else {
    die("Error al obtener los valores del enum.");
}

$turnos = [];

$query = 'SELECT su.nombre AS "instalacion",
            te.fecha_turno AS "fechaTurno", 
            te.horas_cubiertas AS "horas", 
            te.monto AS "monto",
            te.nombre_colaborador AS "colaborador", 
            te.rut AS "rut",  dp.banco AS "banco", 
            dp.rut_cta || "-" || dp.digito_verificador AS "RUTcta", 
            dp.numero_cuenta "numCuenta",
            mg.motivo AS "motivo", 
            te.estado AS "estado", 
            te.created_at AS "fechaCreacion",
            us.name AS "autorizadoPor",
            te.id  AS "id"
            FROM turnos_extra te
            JOIN sucursales su ON (te.sucursal_id = su.id)
            JOIN datos_pago dp ON (te.datos_bancarios_id = dp.id)
            JOIN motivos_gestion mg ON (te.motivo_turno_id = mg.id)
            JOIN user us ON (te.autorizado_por = us.id) ';
if ($_SESSION['cargo'] == 11){
    $id  = $_SESSION['id'];
    $query .= "WHERE te.autorizado_por = '$id' ";
}
if (isset($_SESSION['deptos']) && is_array($_SESSION['deptos'])) {
    $estados = [
        10 => "aprobado",
        2 => "aprobado por remuneraciones",
    ];

    $estadoEncontrado = false;
    foreach ($estados as $depto => $estado) {
        if (array_intersect([$depto], $_SESSION['deptos'])) {
            $query .= "WHERE te.estado = '$estado' ";
            $estadoEncontrado = true;
            break;
        }
    }
}

$query .= " ORDER BY te.created_at DESC"; 
$result = mysqli_query($con, $query);
$turnos = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Convertir a JSON para JavaScript
echo "<script>var turnosData = " . json_encode($turnos) . ";</script>";
?>