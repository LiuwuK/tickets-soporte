<?php
// Obtener supervisores y departamentos
$stmt_s = $con->prepare("SELECT id, nombre_supervisor FROM supervisores");
$stmt_s->execute();
$result_sup = $stmt_s->get_result();
$supervisores = $result_sup->fetch_all(MYSQLI_ASSOC);

$stmt_d = $con->prepare("SELECT id, nombre_departamento FROM departamentos");
$stmt_d->execute();
$result_dep = $stmt_d->get_result();
$departamentos = $result_dep->fetch_all(MYSQLI_ASSOC);

// Configuración de paginación
$limite = 30;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina < 1) $pagina = 1;
$offset = ($pagina - 1) * $limite;

// Obtener filtros
$filtros = [
    'texto' => isset($_GET['texto']) ? trim($_GET['texto']) : '',
    'supervisor' => isset($_GET['supervisor']) ? (int)$_GET['supervisor'] : 0,
    'centro' => isset($_GET['centro']) ? (int)$_GET['centro'] : 0
];

// consulta base
$query = "SELECT su.*, su.supervisor_id AS supId, su.departamento_id AS cc, 
                 dt.nombre_departamento AS cost_center, sup.nombre_supervisor AS 'nSup', 
                 ci.nombre_ciudad AS 'nCiudad'
          FROM sucursales su 
          JOIN departamentos dt ON (su.departamento_id = dt.id)
          JOIN supervisores sup ON (su.supervisor_id = sup.id)
          JOIN ciudades ci ON (su.ciudad_id = ci.id)
          WHERE 1=1";

$count_query = "SELECT COUNT(*) as total
                FROM sucursales su 
                JOIN departamentos dt ON (su.departamento_id = dt.id)
                JOIN supervisores sup ON (su.supervisor_id = sup.id)
                JOIN ciudades ci ON (su.ciudad_id = ci.id)
                WHERE 1=1";

// Aplicar filtros
$where_conditions = [];
$params = [];
$types = '';

if (!empty($filtros['texto'])) {
    $where_conditions[] = " (su.nombre LIKE ? OR sup.nombre_supervisor LIKE ? OR ci.nombre_ciudad LIKE ? OR su.estado LIKE ? OR dt.nombre_departamento LIKE ?) ";
    $search_term = '%' . $filtros['texto'] . '%';
    $params = array_merge($params, array_fill(0, 5, $search_term));
    $types .= str_repeat('s', 5);
}

if (!empty($filtros['supervisor'])) {
    $where_conditions[] = " su.supervisor_id = ? ";
    $params[] = $filtros['supervisor'];
    $types .= 'i';
}

if (!empty($filtros['centro'])) {
    $where_conditions[] = " su.departamento_id = ? ";
    $params[] = $filtros['centro'];
    $types .= 'i';
}

// Aplicar condiciones WHERE
if (!empty($where_conditions)) {
    $query .= " AND " . implode(" AND ", $where_conditions);
    $count_query .= " AND " . implode(" AND ", $where_conditions);
}

// Consulta para contar total
$stmt_count = $con->prepare($count_query);
if (!empty($params)) {
    $stmt_count->bind_param($types, ...$params);
}
$stmt_count->execute();
$total_result = $stmt_count->get_result();
$total_rows = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limite);

// Consulta principal con paginación
$query .= " ORDER BY su.nombre ASC LIMIT ? OFFSET ?";
$params_pagination = $params;
$types_pagination = $types . 'ii';
$params_pagination[] = $limite;
$params_pagination[] = $offset;

$stmt = $con->prepare($query);
if (!empty($params_pagination)) {
    $stmt->bind_param($types_pagination, ...$params_pagination);
}
$stmt->execute();
$result = $stmt->get_result();
$sucursal = $result->fetch_all(MYSQLI_ASSOC);
?>