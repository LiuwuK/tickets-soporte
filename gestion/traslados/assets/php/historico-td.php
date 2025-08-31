<?php
require_once __DIR__ . '/../helpers/historico-query.php';

$limite = 30;
$pagina = max(1, isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1);
$offset = ($pagina - 1) * $limite;

// Filtros
$filters = [
    'texto'        => $_GET['texto'] ?? '',
    'tipo'         => $_GET['tipo'] ?? '',
    'estado'       => $_GET['estado'] ?? '',
    'fecha_inicio' => $_GET['fecha_inicio'] ?? '',
    'fecha_fin'    => $_GET['fecha_fin'] ?? '',
    'cargo'        => $_SESSION['cargo'] ?? null,
    'usuario_id'   => $_SESSION['id'] ?? null,
    'limit'        => $limite,
    'offset'       => $offset
];

// Conteo total
$countQuery = buildQueryHistorico($con, $filters, 'count'); 
$totalRows  = mysqli_fetch_assoc(mysqli_query($con, $countQuery))['total'];
$totalPages = ceil($totalRows / $limite);

// Consulta paginada
$dataQuery = buildQueryHistorico($con, $filters, 'data'); 
$result    = mysqli_query($con, $dataQuery);
$historico = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Función de paginación
function buildQueryPage($page, $filters) {
    $params = [];
    if (!empty($filters['texto']))        $params['texto'] = $filters['texto'];
    if (!empty($filters['tipo']))         $params['tipo'] = $filters['tipo'];
    if (!empty($filters['estado']))       $params['estado'] = $filters['estado'];
    if (!empty($filters['fecha_inicio'])) $params['fecha_inicio'] = $filters['fecha_inicio'];
    if (!empty($filters['fecha_fin']))    $params['fecha_fin'] = $filters['fecha_fin'];
    $params['pagina'] = $page;
    return '?'.http_build_query($params);
}