<?php
header('Content-Type: application/json');
require_once '../../../../dbconnection.php';

if ($con->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de conexión a la base de datos']);
    exit;
}

if (!isset($_GET['sucursal_id']) || !is_numeric($_GET['sucursal_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID de sucursal inválido']);
    exit;
}

$sucursal_id = (int)$_GET['sucursal_id'];

$query = "SELECT hc.fecha, hc.tipo, hc.hora_entrada, hc.hora_salida, co.name 
          FROM horarios_colaboradores hc
          JOIN colaboradores co ON hc.colaborador_id = co.id
          WHERE hc.sucursal_id = ?";

$stmt = $con->prepare($query);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en la preparación de la consulta']);
    exit;
}

$stmt->bind_param("i", $sucursal_id);
$stmt->execute();
$result = $stmt->get_result();

$eventos = [];
$colaboradorColores = [];
$colores = [
    '#4CAF50', '#2196F3', '#FF9800', '#9C27B0', '#00BCD4', '#E91E63', '#FFC107', '#3F51B5',
    '#009688', '#3F51B5', '#795548', '#607D8B', '#CDDC39', '#FF5722', '#9E9E9E', '#673AB7'
];
$colorCount = count($colores);

while ($row = $result->fetch_assoc()) {
    $colaborador = $row['name'];
    // Asignar color único al colaborador si no tiene uno aún
    if (!isset($colaboradorColores[$colaborador])) {
        $colaboradorColores[$colaborador] = $colores[count($colaboradorColores) % $colorCount];
    }

    if ($row['tipo'] === 'TRABAJO') {
        $eventos[] = [
            'title' => $colaborador,
            'start' => $row['fecha'].'T'.$row['hora_entrada'],
            'end'   => $row['fecha'].'T'.$row['hora_salida'],
            'color' => $colaboradorColores[$colaborador]
        ];
    } else {
        // Evento de descanso con color fijo
        $eventos[] = [
            'title' => 'Descanso',
            'start' => $row['fecha'],
            'allDay' => true,
            'color' => '#f44336' 
        ];
    }
}

echo json_encode($eventos);
exit;
