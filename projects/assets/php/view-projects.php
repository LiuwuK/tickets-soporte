<?php
declare(strict_types=1);

// Cargar filtros estáticos
$ingenieros   = $con->query("SELECT id,name FROM `user` WHERE cargo=1")->fetch_all(MYSQLI_ASSOC);
$clients      = $con->query("SELECT id,nombre FROM clientes")->fetch_all(MYSQLI_ASSOC);
$verticales   = $con->query("SELECT id,nombre FROM verticales")->fetch_all(MYSQLI_ASSOC);
$distData     = $con->query("SELECT id,nombre FROM distribuidores")->fetch_all(MYSQLI_ASSOC);
$tipoProyecto = $con->query("SELECT id,nombre FROM tipo_proyecto")->fetch_all(MYSQLI_ASSOC);
$portal       = $con->query("SELECT id,nombre_portal FROM portales")->fetch_all(MYSQLI_ASSOC);
$clasif       = $con->query("SELECT id,nombre FROM clasificacion_proyecto")->fetch_all(MYSQLI_ASSOC);
$statusF      = $con->query("SELECT id,nombre FROM estados WHERE type='project'")->fetch_all(MYSQLI_ASSOC);

// Filtros GET
$tipoPrj      = isset($_GET['tipoprjF']) ? intval($_GET['tipoprjF']) : 0;
$portalF      = isset($_GET['portalF'])    ? intval($_GET['portalF']) : 0;
$vertical_id  = isset($_GET['verticalF'])  ? intval($_GET['verticalF']) : 0;
$status_id    = isset($_GET['statusF'])    ? intval($_GET['statusF']) : 0;
$searchText   = isset($_GET['textSearch']) ? trim($_GET['textSearch']) : '';
$clasificacion= isset($_GET['clasif'])     ? intval($_GET['clasif']) : 0;

// Paginación
$limit = isset($_GET['limit']) ? max(1,intval($_GET['limit'])) : 10; // dinámico
$page  = isset($_GET['page'])  ? max(1,intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

// Construir condiciones dinámicas
$conditions = [];
$params = [];
$types = '';

$userId = $_SESSION['id'] ?? ($_SESSION['user_id'] ?? null);

if (isset($_SESSION['cargo']) && $_SESSION['cargo'] == 3) {
    $conditions[] = "es.nombre = 'ganado' AND pr.xfacturar = '1'";
} elseif (isset($_SESSION['cargo']) && $_SESSION['cargo'] == 5 && $userId) {
    $conditions[] = "pr.comercial_responsable = ?";
    $params[] = $userId;
    $types .= 'i';
}

// Filtros GET
if ($status_id) {
    $conditions[] = "pr.estado_id = ?";
    $params[] = $status_id; $types .= 'i';
}
if ($vertical_id) {
    $conditions[] = "pr.vertical = ?";
    $params[] = $vertical_id; $types .= 'i';
}
if ($portalF) {
    $conditions[] = "lic.portal = ?";
    $params[] = $portalF; $types .= 'i';
}
if ($tipoPrj) {
    $conditions[] = "pr.tipo = ?";
    $params[] = $tipoPrj; $types .= 'i';
}
if ($clasificacion) {
    $conditions[] = "pr.clasificacion = ?";
    $params[] = $clasificacion; $types .= 'i';
}
if ($searchText !== '') {
    $conditions[] = "(CAST(pr.id AS CHAR) LIKE ? OR pr.nombre LIKE ?)";
    $sw = "%{$searchText}%";
    $params[] = $sw; $params[] = $sw; $types .= 'ss';
}


$whereSql = '';
if (!empty($conditions)) {
    $whereSql = ' WHERE ' . implode(' AND ', $conditions);
}

// Consulta de conteo
// contamos DISTINCT 
$countSql = "
    SELECT COUNT(DISTINCT pr.id) AS total
    FROM proyectos pr
    JOIN estados es ON pr.estado_id = es.id
    LEFT JOIN licitacion_proyecto lic ON pr.id = lic.proyecto_id
    {$whereSql}
";

$countStmt = $con->prepare($countSql);
if ($countStmt === false) {
    die("Error preparando count query: " . $con->error);
}
if (!empty($params)) {
    $countStmt->bind_param($types, ...$params);
}
$countStmt->execute();
$totalRes = (int) $countStmt->get_result()->fetch_assoc()['total'];
$totalPages = (int) ceil($totalRes / $limit);
$countStmt->close();

// Consulta de datos (
$dataSql = "
    SELECT pr.id AS projectId,
           pr.nombre,
           pr.monto,
           pr.fecha_creacion,
           pr.clasificacion,
           es.id AS estado_id,
           es.nombre AS estado,
           ci.nombre_ciudad AS ciudadN,
           us.name AS ingeniero,
           us_com.name AS comercial,
           tp.nombre AS tipoP,
           dt.nombre AS distribuidorN,
           ep.nombre_etapa AS etapaN,
           cl.nombre AS clienteN,
           lic.licitacion_id,
           lic.portal AS portal_id,
           GROUP_CONCAT(DISTINCT CONCAT_WS('||',ac.nombre,ac.fecha_inicio) SEPARATOR ';;') AS actividades,
           GROUP_CONCAT(DISTINCT CONCAT_WS('||',c.nombre,c.correo,c.cargo,c.numero) SEPARATOR ';;') AS contactos,
           GROUP_CONCAT(DISTINCT CONCAT_WS('||',b.nombre,b.cantidad,b.total) SEPARATOR ';;') AS bom
    FROM proyectos pr
    JOIN estados es ON pr.estado_id = es.id
    LEFT JOIN ciudades ci ON pr.ciudad = ci.id
    LEFT JOIN user us ON pr.ingeniero_responsable = us.id
    LEFT JOIN user us_com ON pr.comercial_responsable = us_com.id
    LEFT JOIN distribuidores dt ON pr.distribuidor = dt.id
    LEFT JOIN tipo_proyecto tp ON pr.tipo = tp.id
    LEFT JOIN etapas_proyecto ep ON pr.estado_etapa = ep.id
    LEFT JOIN clientes cl ON pr.cliente = cl.id
    LEFT JOIN licitacion_proyecto lic ON pr.id = lic.proyecto_id
    LEFT JOIN actividades ac ON ac.proyecto_id = pr.id
    LEFT JOIN contactos_proyecto c ON c.proyecto_id = pr.id
    LEFT JOIN bom b ON b.proyecto_id = pr.id
    {$whereSql}
    GROUP BY pr.id
    ORDER BY pr.id DESC
    LIMIT ? OFFSET ?
";

$stmt = $con->prepare($dataSql);
if ($stmt === false) {
    die("Error preparando data query: " . $con->error);
}

$bindParams = $params;
$bindTypes  = $types;
$bindParams[] = $limit;  $bindTypes .= 'i';
$bindParams[] = $offset; $bindTypes .= 'i';

if (!empty($bindParams)) {
    $stmt->bind_param($bindTypes, ...$bindParams);
}
$stmt->execute();
$rt = $stmt->get_result();
$num = $rt->num_rows;

