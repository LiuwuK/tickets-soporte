<?php
//Obtener jornadas
$query = "SELECT * FROM jornadas";
$jornadas = mysqli_query($con, $query);

// Verificamos si hay resultados
if (!$jornadas) {
    die("Error al obtener jornadas: " . mysqli_error($con));
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // información de sucursal
    $query = "SELECT su.*, dt.nombre_departamento AS cost_center, sup.nombre_supervisor AS 'nSup', ci.nombre_ciudad AS 'nCiudad'
              FROM sucursales su 
              JOIN departamentos dt ON(su.departamento_id = dt.id)
              JOIN supervisores sup ON(su.supervisor_id = sup.id)
              JOIN ciudades ci ON(su.ciudad_id = ci.id)
              WHERE su.id = ?
              ORDER BY su.nombre ASC";
    
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
    mysqli_stmt_close($stmt); 

    //dotación real
    $sql = "SELECT COUNT(*) AS 'real' 
            FROM colaboradores 
            WHERE facility = ? AND vigente = 1";
    $stmt_dtc = mysqli_prepare($con, $sql);
    if (!$stmt_dtc) {
        die("Error al preparar la consulta de dotación: " . mysqli_error($con));
    }
    mysqli_stmt_bind_param($stmt_dtc, "i", $id); 
    if (!mysqli_stmt_execute($stmt_dtc)) {
        die("Error al ejecutar consulta de dotación: " . mysqli_stmt_error($stmt_dtc));
    }
    $dReal = mysqli_stmt_get_result($stmt_dtc);
    
    if ($dtc = mysqli_fetch_assoc($dReal)) {
        $dotacion = $dtc['real'];
    } else {
        echo json_encode(['error' => 'No se encontraron colaboradores']);
    }
    mysqli_stmt_close($stmt_dtc); 
    
    //turnos
    $query = "SELECT ti.*, jo.tipo_jornada AS 'nJo' 
              FROM turnos_instalacion ti
              JOIN jornadas jo ON (jo.id = ti.jornada_id)
              WHERE sucursal_id = ?";
    
    $stmt = mysqli_prepare($con, $query);
    if (!$stmt) {
        die("Error al preparar la consulta de turnos: " . mysqli_error($con));
    }
    mysqli_stmt_bind_param($stmt, 'i', $id);
    if (!mysqli_stmt_execute($stmt)) {
        die("Error al ejecutar consulta de turnos: " . mysqli_stmt_error($stmt));
    }
    $result = mysqli_stmt_get_result($stmt);
    $turnosExxistentes = [];
    while ($fila = mysqli_fetch_assoc($result)) {
        $turnosExistentes[] = $fila;
    }
    mysqli_stmt_close($stmt);
    
} else {
    die("No se proporcionó ID de turno");
}


?>