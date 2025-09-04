<?php
function buildQueryHistorico($con, $filters, $mode = 'data') {
    $fecha_inicio = $filters['fecha_inicio'] ?? '';
    $fecha_fin    = $filters['fecha_fin'] ?? '';
    $tipo         = $filters['tipo'] ?? '';
    $estado       = $filters['estado'] ?? '';
    $cargo        = $filters['cargo'] ?? 0;

    $filtrosTraslados        = [];
    $filtrosDesvinculaciones = [];

    // Filtro por fecha
    if (!empty($fecha_inicio)) {
        if (empty($fecha_fin)) {
            $fecha_fin = date('Y-m-d');
        }
        $filtrosTraslados[]        = "tr.fecha_registro BETWEEN '$fecha_inicio' AND '$fecha_fin'";
        $filtrosDesvinculaciones[] = "de.fecha_registro BETWEEN '$fecha_inicio' AND '$fecha_fin'";
    }

    // Filtro por tipo
    if ($tipo === "traslado") {
        $filtrosDesvinculaciones[] = "1=0";
    }
    if ($tipo === "desvinculacion") { // sin tilde
        $filtrosTraslados[] = "1=0";
    }

    // Filtro por cargo/estado
    if ($cargo == 13) {
        $filtrosTraslados[]        = "tr.estado = 'realizado'";
        $filtrosDesvinculaciones[] = "de.estado = 'realizado'";
    } else {
        if (!empty($estado)) {
            $filtrosTraslados[]        = "tr.estado = '$estado'";
            $filtrosDesvinculaciones[] = "de.estado = '$estado'";
        }
    }

    $whereTraslados        = !empty($filtrosTraslados) ? "WHERE " . implode(" AND ", $filtrosTraslados) : "";
    $whereDesvinculaciones = !empty($filtrosDesvinculaciones) ? "WHERE " . implode(" AND ", $filtrosDesvinculaciones) : "";

    $queryTraslados = "
        SELECT tr.id AS id,
               tr.estado AS estadoN,
               'Traslado' AS tipo,
               DATE(tr.fecha_registro) AS fecha_registro,
               TIME(tr.fecha_registro) AS hora_registro,
               us.name AS soliN,
               sup_origen.nombre_supervisor AS supOrigen,
               tr.nombre_colaborador AS colaborador,
               tr.rut AS rutC,
               su_origen.nombre AS suOrigen,
               jo_origen.tipo_jornada AS joOrigen,
               rol_origen.nombre_rol AS rolOrigen,
               mg.motivo AS motivoN,
               su_destino.nombre AS suDestino,
               jo_destino.tipo_jornada AS joDestino,
               rol_destino.nombre_rol AS rolDestino,
               tr.fecha_inicio_turno AS fecha_turno,
               sup_destino.nombre_supervisor AS supDestino,
               tr.observacion AS observacion,
               tr.obs_rrhh AS obs_rrhh
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
        $whereTraslados
    ";

    $queryDesvinculaciones = "
        SELECT de.id AS id,
               de.estado AS estadoN,
               'Desvinculacion' AS tipo,
               DATE(de.fecha_registro) AS fecha_registro,
               TIME(de.fecha_registro) AS hora_registro,
               us.name AS soliN,
               sup.nombre_supervisor AS supOrigen,
               de.colaborador AS colaborador,
               de.rut AS rutC,
               su.nombre AS suOrigen,
               '' AS joOrigen,
               de.rol AS rolOrigen,
               mo.motivo AS motivoN,
               '' AS suDestino,
               '' AS joDestino,
               '' AS rolDestino,
               '' AS fecha_turno,
               '' AS supDestino,
               de.observacion AS observacion,
               de.obs_rrhh AS obs_rrhh
        FROM desvinculaciones de
        JOIN user us ON de.solicitante = us.id
        JOIN sucursales su ON de.instalacion = su.id
        JOIN supervisores sup ON de.supervisor_origen = sup.id
        JOIN motivos_gestion mo ON de.motivo = mo.id
        $whereDesvinculaciones
    ";

    $unionQuery = "($queryTraslados) UNION ALL ($queryDesvinculaciones)";

    if ($mode === 'count') {
        return "SELECT COUNT(*) AS total FROM ($unionQuery) AS sub";
    }

    $unionQuery .= " ORDER BY fecha_registro DESC";

    if ($mode === 'data' && isset($filters['limit']) && isset($filters['offset'])) {
        $unionQuery .= " LIMIT " . intval($filters['limit']) . " OFFSET " . intval($filters['offset']);
    }

    return $unionQuery;
}
?>
