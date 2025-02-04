<?php
    
//se obtienen los tickets del usuario-------------------------------------------------------------------------
//La query cambia dependiendo si se filtra por prioridad y/o estado
$priority_id = isset($_GET['priority']) ? intval($_GET['priority']) : '';
$status_id = isset($_GET['statusF']) ? intval($_GET['statusF']) : '';
$searchText = isset($_GET['textSearch']) ? trim($_GET['textSearch']) : '';

// Inicia la consulta base
$query = "SELECT ti.id AS ticketId, 
                 pr.id AS prioridadId,
                 st.nombre AS statusN,
                 ti.*, pr.*
          FROM ticket ti 
          LEFT JOIN prioridades pr ON ti.prioprity = pr.id
          JOIN estados st ON ti.status = st.id
          WHERE email_id='" . $_SESSION['login'] . "'";

// Filtros dinámicos
  $conditions = [];
  $params = [];
  $types = '';

// Filtrar por prioridad
if (!empty($priority_id)) {
    $conditions[] = "ti.prioprity = ?";
    $params[] = $priority_id;
    $types .= 'i';
}

// Filtrar por estado
if (!empty($status_id)) {
    $conditions[] = "ti.status = ?";
    $params[] = $status_id;
    $types .= 'i';
}

// Filtrar por texto (nombre del ticket o ID)
if (!empty($searchText)) {
    $conditions[] = "(ti.id LIKE ? OR ti.subject LIKE ?)";
    $searchWildcard = '%' . $searchText . '%';
    $params[] = $searchWildcard;
    $params[] = $searchWildcard;
    $types .= 'ss';
}


if (!empty($conditions)) {
    $query .= ' AND ' . implode(' AND ', $conditions); 
}
$stmt = $con->prepare($query);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$rt = $stmt->get_result();

if (!$rt) {
    die("Error en la consulta: " . mysqli_error($con));
}
//total de resultados
$num = $rt->num_rows; 
//----------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------
//carga las prioridades y estados de ticket para filtrar------------------------------------------------------
$query_prio = "SELECT * FROM prioridades ";
$prioridad = mysqli_query($con, $query_prio);

$query_st = "SELECT * FROM estados WHERE type = 'ticket'";
$statusF = mysqli_query($con, $query_st);

?>