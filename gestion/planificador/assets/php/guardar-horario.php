<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Cabeceras JSON
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');

// Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Conexión DB
require_once '../../../../dbconnection.php';
if ($con->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos']);
    exit;
}

try {
    // Leer datos JSON
    $json = file_get_contents("php://input");
    if (!$json) {
        throw new Exception("No se recibió ningún cuerpo JSON", 400);
    }

    $data = json_decode($json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Error al decodificar JSON: " . json_last_error_msg(), 400);
    }

    // Validación básica
    $required = ['colaborador_id', 'sucursal_id', 'fecha_inicio', 'turno_id', 'hora_entrada', 'hora_salida', 'duracion'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            throw new Exception("Falta el campo obligatorio: $field", 400);
        }
    }

    // Variables
    $colaboradorId = (int)$data['colaborador_id'];
    $sucursalId = (int)$data['sucursal_id'];
    $fechaInicio = new DateTime($data['fecha_inicio']);
    $turnoId = (int)$data['turno_id'];
    $horaEntrada = $con->real_escape_string($data['hora_entrada']);
    $horaSalida = $con->real_escape_string($data['hora_salida']);
    $jornada = isset($data['jornada']) ? $con->real_escape_string($data['jornada']) : '';
    $duracion = (int)$data['duracion'];

    //definir patron
    $patron = '5x2'; 
    if (preg_match('/(\d+)x(\d+)/', $jornada, $coincidencias)) {
        $patron = $coincidencias[0]; 
    }
    list($diasTrabajo, $diasDescanso) = explode('x', $patron);
    $diasTrabajo = (int)$diasTrabajo;
    $diasDescanso = (int)$diasDescanso;

    $stmt = $con->prepare("INSERT INTO horarios_colaboradores 
        (colaborador_id, sucursal_id, fecha, turno_id, hora_entrada, hora_salida, tipo) 
        VALUES (?, ?, ?, ?, ?, ?, ?)");

    if (!$stmt) {
        throw new Exception("Error al preparar la consulta: " . $con->error, 500);
    }

    // Lógica de inserción por patrón
    $diasGenerados = 0;
    $semanasGeneradas = 0;
    $esDiaTrabajo = true;
    $contador = 0;

    $con->begin_transaction();

    while ($semanasGeneradas < $duracion) {
        $fecha = clone $fechaInicio;
        $fecha->modify("+{$diasGenerados} days");
        $tipo = $esDiaTrabajo ? 'TRABAJO' : 'DESCANSO';

        $fechaStr = $fecha->format('Y-m-d');
        $turno = $esDiaTrabajo ? $turnoId : null;
        $entrada = $esDiaTrabajo ? $horaEntrada : null;
        $salida = $esDiaTrabajo ? $horaSalida : null;

        $stmt->bind_param(
            "iisisss",
            $colaboradorId,
            $sucursalId,
            $fechaStr,
            $turno,
            $entrada,
            $salida,
            $tipo
        );


        if (!$stmt->execute()) {
            throw new Exception("Error al insertar día: " . $stmt->error, 500);
        }

        $contador++;
        if ($esDiaTrabajo && $contador >= $diasTrabajo) {
            $esDiaTrabajo = false;
            $contador = 0;
        } elseif (!$esDiaTrabajo && $contador >= $diasDescanso) {
            $esDiaTrabajo = true;
            $contador = 0;
            $semanasGeneradas++;
        }

        $diasGenerados++;
    }

    $con->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Horario guardado correctamente',
        'total_dias' => $diasGenerados
    ]);
} catch (Exception $e) {
    $con->rollback();
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
