<?php
require '../../vendor/autoload.php';

// Obtener estados
$query = "SHOW COLUMNS FROM turnos_extra LIKE 'estado'";
$result = $con->query($query);
if ($result) {
    $row = $result->fetch_assoc();
    preg_match("/^enum\(\'(.*)\'\)$/", $row['Type'], $matches);
    $valoresEnum = explode("','", $matches[1]);
} else {
    die("Error al obtener los valores del enum.");
}

// Obtener supervisores
$stmt_s = $con->prepare("SELECT id, name FROM user WHERE cargo = ?");
$cargo_id = 11;
$stmt_s->bind_param("i", $cargo_id);
$stmt_s->execute();
$result_sup = $stmt_s->get_result();

// Paginación
$limite = 20;
$pagina = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
$offset = ($pagina - 1) * $limite;

// Filtros
$filtros = [
    'texto' => isset($_GET['texto']) ? trim($_GET['texto']) : '',
    'estado' => isset($_GET['estado']) ? trim($_GET['estado']) : '',
    'supervisor' => isset($_GET['supervisor']) ? (int)$_GET['supervisor'] : 0,
    'fecha_inicio' => isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : '',
    'fecha_fin' => isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : '',
    'semana_actual' => isset($_GET['semana_actual']) && $_GET['semana_actual'] == 1
];

// Si está activo "Semana en Gestión", fijamos fechas
if ($filtros['semana_actual']) {
    $hoy = new DateTime();
    $inicioSemana = clone $hoy;
    $inicioSemana->modify('monday this week');
    $finSemana = clone $hoy;
    $finSemana->modify('sunday this week');
    $filtros['fecha_inicio'] = $inicioSemana->format('Y-m-d');
    $filtros['fecha_fin'] = $finSemana->format('Y-m-d');
}

// Consulta base
$query = "
SELECT 
    su.nombre AS instalacion,
    te.fecha_turno AS fechaTurno,
    te.horas_cubiertas AS horas,
    te.monto AS monto,
    te.nombre_colaborador AS colaborador,
    te.rut AS rut,
    dp.banco AS banco,
    CONCAT(dp.rut_cta, '-', dp.digito_verificador) AS RUTcta,
    dp.numero_cuenta AS numCuenta,
    mg.motivo AS motivo,
    te.estado AS estado,
    te.created_at AS fechaCreacion,
    us.name AS autorizadoPor,
    te.id AS id,
    te.autorizado_por AS supID,
    CASE WHEN ht.tiene_historico IS NULL THEN 0 ELSE 1 END AS tiene_historico
FROM turnos_extra te
LEFT JOIN sucursales su ON te.sucursal_id = su.id
JOIN datos_pago dp ON te.datos_bancarios_id = dp.id
JOIN motivos_gestion mg ON te.motivo_turno_id = mg.id
JOIN user us ON te.autorizado_por = us.id
LEFT JOIN (
    SELECT turno_id, 1 AS tiene_historico
    FROM historico_turnos
    GROUP BY turno_id
) ht ON ht.turno_id = te.id
WHERE 1=1
";

// Conteo total
$count_query = "
SELECT COUNT(*) AS total
FROM turnos_extra te
LEFT JOIN sucursales su ON te.sucursal_id = su.id
JOIN datos_pago dp ON te.datos_bancarios_id = dp.id
JOIN motivos_gestion mg ON te.motivo_turno_id = mg.id
JOIN user us ON te.autorizado_por = us.id
LEFT JOIN (
    SELECT turno_id, 1 AS tiene_historico
    FROM historico_turnos
    GROUP BY turno_id
) ht ON ht.turno_id = te.id
WHERE 1=1
";

// Aplicar filtros dinámicos
$where_conditions = [];
$params = [];
$types = '';

// Solo ver mis turnos si cargo 11
if ($_SESSION['cargo'] == 11) {
    $where_conditions[] = " te.autorizado_por = ? ";
    $params[] = $_SESSION['id'];
    $types .= 'i';
}

// Filtros de texto
if (!empty($filtros['texto'])) {
    $where_conditions[] = "(te.nombre_colaborador LIKE ? OR te.rut LIKE ? OR us.name LIKE ? OR su.nombre LIKE ? OR mg.motivo LIKE ?)";
    $search = '%' . $filtros['texto'] . '%';
    $params = array_merge($params, array_fill(0, 5, $search));
    $types .= str_repeat('s', 5);
}

// Estado
if (!empty($filtros['estado'])) {
    $where_conditions[] = " te.estado = ? ";
    $params[] = $filtros['estado'];
    $types .= 's';
}

// Supervisor
if (!empty($filtros['supervisor'])) {
    $where_conditions[] = " te.autorizado_por = ? ";
    $params[] = $filtros['supervisor'];
    $types .= 'i';
}

// Fechas
if (!empty($filtros['fecha_inicio'])) {
    $where_conditions[] = " te.fecha_turno >= ? ";
    $params[] = $filtros['fecha_inicio'];
    $types .= 's';
}
if (!empty($filtros['fecha_fin'])) {
    $where_conditions[] = " te.fecha_turno <= ? ";
    $params[] = $filtros['fecha_fin'];
    $types .= 's';
}

// Aplicar condiciones
if (!empty($where_conditions)) {
    $where_sql = " AND " . implode(" AND ", $where_conditions);
    $query .= $where_sql;
    $count_query .= $where_sql;
}

// Conteo total
$stmt_count = $con->prepare($count_query);
if (!empty($params)) $stmt_count->bind_param($types, ...$params);
$stmt_count->execute();
$total_result = $stmt_count->get_result();
$total_rows = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limite);

// Consulta principal con paginación
$query .= " ORDER BY te.created_at DESC LIMIT ? OFFSET ?";
$params_pagination = $params;
$types_pagination = $types . 'ii';
$params_pagination[] = $limite;
$params_pagination[] = $offset;

$stmt = $con->prepare($query);
$stmt->bind_param($types_pagination, ...$params_pagination);
$stmt->execute();
$result = $stmt->get_result();
$turnos = $result->fetch_all(MYSQLI_ASSOC);
?>
