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

// Consulta optimizada para obtener múltiples colaboradores por turno
$query = "SELECT 
            hc.id AS horario_id,
            hc.fecha, 
            hc.tipo, 
            hc.hora_entrada, 
            hc.hora_salida, 
            ti.codigo,
            c.id AS colaborador_id,
            CONCAT(c.name, ' ', c.fname) AS nombre_colaborador
          FROM horarios_sucursal hc
          JOIN turnos_instalacion ti ON hc.turno_id = ti.id
          LEFT JOIN colaborador_turno ct ON hc.turno_id = ct.turno_id 
            AND hc.fecha BETWEEN ct.fecha_inicio AND ct.fecha_fin
          LEFT JOIN colaboradores c ON ct.colaborador_id = c.id
          WHERE hc.sucursal_id = ?
          ORDER BY hc.fecha, hc.hora_entrada, ti.codigo";

$stmt = $con->prepare($query);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en la preparación de la consulta: ' . $con->error]);
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

// Primero agrupar los resultados por horario
$horarios = [];
while ($row = $result->fetch_assoc()) {
    $horarioId = $row['horario_id'];
    if (!isset($horarios[$horarioId])) {
        $horarios[$horarioId] = [
            'fecha' => $row['fecha'],
            'tipo' => $row['tipo'],
            'hora_entrada' => $row['hora_entrada'],
            'hora_salida' => $row['hora_salida'],
            'codigo' => $row['codigo'],
            'colaboradores' => []
        ];
    }
    
    if ($row['colaborador_id']) {
        $horarios[$horarioId]['colaboradores'][] = $row['nombre_colaborador'];
    }
}

// generar los eventos
foreach ($horarios as $horario) {
    $codigoTurno = $horario['codigo'];

    if (!isset($colaboradorColores[$codigoTurno])) {
        $colaboradorColores[$codigoTurno] = $colores[count($colaboradorColores) % $colorCount];
    }

    if ($horario['tipo'] === 'TRABAJO') {
        $hayColaboradores = !empty($horario['colaboradores']);
        
        $colaboradoresTexto = $hayColaboradores
            ? implode(', ', $horario['colaboradores'])
            : 'Sin asignar';

        $titulo = $codigoTurno . ' - ' . ($hayColaboradores ? 'TURNO ASIGNADO' : 'SIN ASIGNAR');

        $eventos[] = [
            'groupId' => $horario['fecha'] . $codigoTurno,
            'title' => $titulo,
            'start' => $horario['fecha'] . 'T' . $horario['hora_entrada'],
            'end' => $horario['fecha'] . 'T' . $horario['hora_salida'],
            'color' => $colaboradorColores[$codigoTurno],
            'extendedProps' => [
                'codigo_turno' => $codigoTurno,
                'colaboradores' => $colaboradoresTexto
            ]
        ];
    } else {
        $eventos[] = [
            'title' => 'Descanso',
            'start' => $horario['fecha'],
            'allDay' => true,
            'color' => '#f44336'
        ];
    }
}


echo json_encode($eventos);
exit;