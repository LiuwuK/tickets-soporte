<?php
//Obtener jornadas
$query = "SELECT * FROM jornadas
          ORDER BY `jornadas`.`tipo_jornada` DESC";
$jornadas = mysqli_query($con, $query);

if (!$jornadas) {
    die("Error al obtener jornadas: " . mysqli_error($con));
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // información de sucursal
    $query = "SELECT su.*, 
                    dt.nombre_departamento AS cost_center, 
                    sup.nombre_supervisor AS nSup, 
                    ci.nombre_ciudad AS nCiudad
                FROM sucursales su 
                LEFT JOIN departamentos dt ON su.departamento_id = dt.id
                LEFT JOIN supervisores sup ON su.supervisor_id = sup.id
                LEFT JOIN ciudades ci ON su.ciudad_id = ci.id
                WHERE su.id = ?";
            
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
    $sucursal = mysqli_fetch_assoc($result); 
    if (!$sucursal) {
        die("No se encontró ninguna sucursal con el ID proporcionado");
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
    $turnosExistentes = [];
    $query = "SELECT t.*, j.tipo_jornada AS nJo FROM turnos_instalacion t 
            LEFT JOIN jornadas j ON t.jornada_id = j.id 
            WHERE t.sucursal_id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($turno = $result->fetch_assoc()) {
        // Obtener horarios por día para este turno
        $queryDias = "SELECT dia_semana, hora_entrada, hora_salida 
                    FROM turno_dias WHERE turno_id = ?";
        $stmtDias = $con->prepare($queryDias);
        $stmtDias->bind_param("i", $turno['id']);
        $stmtDias->execute();
        $resultDias = $stmtDias->get_result();
        
        $dias = [];
        while ($dia = $resultDias->fetch_assoc()) {
            $dias[$dia['dia_semana']] = [
                'entrada' => $dia['hora_entrada'],
                'salida' => $dia['hora_salida']
            ];
        }
        $turno['dias'] = $dias;
        $turnosExistentes[] = $turno;
    }
    mysqli_stmt_close($stmt);
    // Consulta modificada con LEFT JOIN
    $query = "SELECT c.*, GROUP_CONCAT(DISTINCT ti.codigo SEPARATOR ', ') AS codigos_turnos
                FROM colaboradores c
                LEFT JOIN colaborador_turno ct ON c.id = ct.colaborador_id 
                    AND ct.fecha_fin >= CURDATE()
                LEFT JOIN turnos_instalacion ti ON ct.turno_id = ti.id
                WHERE c.vigente = 1 
                AND c.facility = ?
                GROUP BY c.id
                ORDER BY c.name"; 

    $stmt = mysqli_prepare($con, $query);
    if (!$stmt) {
        die("Error al preparar la consulta de colaboradores: " . mysqli_error($con));
    }
    mysqli_stmt_bind_param($stmt, 'i', $id);
    if (!mysqli_stmt_execute($stmt)) {
        die("Error al ejecutar consulta de colaboradores: " . mysqli_stmt_error($stmt));
    }
    $result = mysqli_stmt_get_result($stmt);
    $colaboradorAsociado = [];
    while ($fila = mysqli_fetch_assoc($result)) {
        // Si no tiene turnos, codigos_turnos será NULL
        $fila['codigos_turnos'] = $fila['codigos_turnos'] ?? 'Sin turnos asignados';
        $colaboradorAsociado[] = $fila;
    }
    mysqli_stmt_close($stmt);

} else {
    die("No se proporcionó ID de turno");
}


?>