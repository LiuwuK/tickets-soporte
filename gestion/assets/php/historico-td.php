<?php
$limite = 20;
$pagina = isset($_GET['pagina']) ? (int)trim($_GET['pagina']) : 1;
if ($pagina < 1) $pagina = 1;
$offset = ($pagina - 1) * $limite;

// Filtros del formulario
$texto = isset($_GET['texto']) ? $_GET['texto'] : '';
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : '';
$estado = isset($_GET['estado']) ? $_GET['estado'] : '';
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : '';
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : '';

$texto_sql = mysqli_real_escape_string($con, $texto);
$tipo_sql = mysqli_real_escape_string($con, $tipo);
$estado_sql = mysqli_real_escape_string($con, $estado);
$fecha_inicio_sql = mysqli_real_escape_string($con, $fecha_inicio);
$fecha_fin_sql = mysqli_real_escape_string($con, $fecha_fin);


$subquery = "
    SELECT tr.id, tr.fecha_registro AS fecha, 'traslado' AS tipo, tr.nombre_colaborador AS colaborador, 
           tr.rut, tr.estado, us.name AS solicitante, tr.observacion, tr.obs_rrhh
    FROM traslados tr
    JOIN user us ON tr.solicitante = us.id
    UNION ALL
    SELECT de.id, de.fecha_registro AS fecha, 'desvinculación' AS tipo, de.colaborador, de.rut, de.estado, 
           us.name AS solicitante, de.observacion, de.obs_rrhh
    FROM desvinculaciones de
    JOIN user us ON de.solicitante = us.id
";

// Armar los filtros
$where = [];

// Restricción por cargo
if($_SESSION['cargo'] == 13 && $_SESSION['id'] != 50){
    $where[] = "estado='realizado'";
}

// Filtros de búsqueda
if($texto_sql !== ''){
    $where[] = "(colaborador LIKE '%$texto_sql%' OR tipo LIKE '%$texto_sql%' OR estado LIKE '%$texto_sql%' 
                OR solicitante LIKE '%$texto_sql%' OR rut LIKE '%$texto_sql%' 
                OR observacion LIKE '%$texto_sql%' OR obs_rrhh LIKE '%$texto_sql%')";
}
if($tipo_sql !== '') $where[] = "tipo='$tipo_sql'";
if($estado_sql !== '') $where[] = "estado='$estado_sql'";
if($fecha_inicio_sql !== '') $where[] = "fecha >= '$fecha_inicio_sql'";
if($fecha_fin_sql !== '') $where[] = "fecha <= '$fecha_fin_sql'";

// Construir la cadena WHERE
$where_sql = '';
if(count($where) > 0){
    $where_sql = " WHERE " . implode(" AND ", $where);
}

// Consulta para contar total
$countQuery = "SELECT COUNT(*) AS total FROM (SELECT * FROM ($subquery) AS t $where_sql) AS combined";
$countResult = mysqli_query($con, $countQuery);
if(!$countResult){
    die("Error en consulta COUNT: " . mysqli_error($con));
}
$totalRows = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalRows / $limite);

// Consulta final con datos y paginación
$queryFinal = "SELECT * FROM (SELECT * FROM ($subquery) AS t $where_sql) AS combined 
               ORDER BY fecha DESC 
               LIMIT $limite OFFSET $offset";


$result = mysqli_query($con, $queryFinal);
if(!$result){
    die("Error en consulta principal: " . mysqli_error($con));
}

$historico = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>
