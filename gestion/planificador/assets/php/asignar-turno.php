<?php
session_start();
require_once '../../../../dbconnection.php';
require_once '../../../../checklogin.php';
check_login();

// Establecer el tipo de contenido como JSON
header('Content-Type: application/json');

// Función para enviar respuestas JSON consistentes
function sendJsonResponse($success, $message, $data = [], $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

try {
    // Verificar método HTTP
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendJsonResponse(false, 'Método no permitido', [], 405);
    }

    // Obtener y validar JSON
    $json = file_get_contents('php://input');
    if (empty($json)) {
        sendJsonResponse(false, 'Datos JSON no proporcionados', [], 400);
    }

    $data = json_decode($json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        sendJsonResponse(false, 'JSON inválido: ' . json_last_error_msg(), [], 400);
    }

    // Validación de campos requeridos
    $required = ['colaborador_id', 'sucursal_id', 'fecha_inicio', 'fecha_fin'];
    $missing = array_diff($required, array_keys($data));
    if (!empty($missing)) {
        sendJsonResponse(false, 'Faltan campos requeridos: ' . implode(', ', $missing), [], 400);
    }

    // Convertir y validar datos
    $colaboradorId = (int)$data['colaborador_id'];
    $sucursalId = (int)$data['sucursal_id'];
    $fechaInicio = $data['fecha_inicio'];
    $fechaFin = $data['fecha_fin'];
    $esRecurrente = !empty($data['es_recurrente']);

    // Validar fechas
    if (strtotime($fechaFin) < strtotime($fechaInicio)) {
        sendJsonResponse(false, 'La fecha final no puede ser anterior a la inicial', [], 400);
    }

    // Verificar conexión a base de datos
    if (!$con) {
        sendJsonResponse(false, 'Error de conexión a la base de datos', [], 500);
    }

    // Iniciar transacción
    $con->begin_transaction();

    if ($esRecurrente) {
        // Asignación recurrente con mismo turno
        if (empty($data['turno_id'])) {
            sendJsonResponse(false, 'Debe seleccionar un turno para la asignación recurrente', [], 400);
        }

        $turnoId = (int)$data['turno_id'];
        
        // Verificar que el turno existe
        $stmtTurno = $con->prepare("SELECT codigo, hora_entrada, hora_salida FROM turnos_instalacion WHERE id = ?");
        $stmtTurno->bind_param("i", $turnoId);
        $stmtTurno->execute();
        $resultTurno = $stmtTurno->get_result();
        $turno = $resultTurno->fetch_assoc();

        if (!$turno) {
            sendJsonResponse(false, 'El turno seleccionado no existe', [], 404);
        }

        // Obtener el día de la semana del turno base (0=domingo, 6=sábado)
        $stmtDia = $con->prepare("SELECT DAYOFWEEK(fecha)-1 FROM horarios_sucursal WHERE turno_id = ? LIMIT 1");
        $stmtDia->bind_param("i", $turnoId);
        $stmtDia->execute();
        $resultDia = $stmtDia->get_result();
        $diaSemanaTurno = $resultDia->fetch_row()[0];

        // Generar asignaciones recurrentes
        $fechaActual = $fechaInicio;
        $asignacionesCreadas = 0;
        $stmtAsignar = $con->prepare("INSERT INTO colaborador_turno 
                                    (colaborador_id, turno_id, fecha_inicio, fecha_fin, estado) 
                                    VALUES (?, ?, ?, ?, 'ACTIVO')
                                    ON DUPLICATE KEY UPDATE 
                                    turno_id = VALUES(turno_id),
                                    estado = 'ACTIVO',
                                    fecha_fin = VALUES(fecha_fin)");

        while (strtotime($fechaActual) <= strtotime($fechaFin)) {
            // Calcular fecha objetivo 
            $diasDiferencia = ($diaSemanaTurno - date('w', strtotime($fechaActual)) + 7) % 7;
            $fechaAsignar = date('Y-m-d', strtotime("$fechaActual +$diasDiferencia days"));

            if (strtotime($fechaAsignar) > strtotime($fechaFin)) break;

            // Calcular fecha fin de esta semana
            $fechaFinSemana = date('Y-m-d', strtotime("$fechaAsignar +6 days"));
            if (strtotime($fechaFinSemana) > strtotime($fechaFin)) {
                $fechaFinSemana = $fechaFin;
            }

            // Buscar horario existente para esta fecha
            $stmtHorario = $con->prepare("SELECT id FROM horarios_sucursal WHERE sucursal_id = ? AND fecha = ?");
            $stmtHorario->bind_param("is", $sucursalId, $fechaAsignar);
            $stmtHorario->execute();
            $resultHorario = $stmtHorario->get_result();
            $horarioId = $resultHorario->fetch_row()[0];

            if ($horarioId) {
                $stmtAsignar->bind_param("iiss", $colaboradorId, $turnoId, $fechaAsignar, $fechaFinSemana);
                $stmtAsignar->execute();
                $asignacionesCreadas++;
            }

            $fechaActual = date('Y-m-d', strtotime("$fechaActual +1 week"));
        }

        if ($asignacionesCreadas === 0) {
            sendJsonResponse(false, 'No se encontraron turnos para asignar en el período especificado', [], 404);
        }

        $mensaje = "Se asignó el turno {$turno['codigo']} semanalmente ($asignacionesCreadas veces)";
    } else {
        // Asignación por semana con diferentes turnos
        if (empty($data['turnos_semanas']) || !is_array($data['turnos_semanas'])) {
            sendJsonResponse(false, 'Debe seleccionar turnos para cada semana', [], 400);
        }

        $fechaActual = $fechaInicio;
        $semanaIndex = 0;
        $asignacionesCreadas = 0;
        $stmtAsignar = $con->prepare("INSERT INTO colaborador_turno 
                                    (colaborador_id, turno_id, fecha_inicio, fecha_fin, estado) 
                                    VALUES (?, ?, ?, ?, 'ACTIVO')
                                    ON DUPLICATE KEY UPDATE 
                                    turno_id = VALUES(turno_id),
                                    estado = 'ACTIVO',
                                    fecha_fin = VALUES(fecha_fin)");

        while (strtotime($fechaActual) <= strtotime($fechaFin) && $semanaIndex < count($data['turnos_semanas'])) {
            $turnoId = (int)$data['turnos_semanas'][$semanaIndex];
            if ($turnoId <= 0) {
                $semanaIndex++;
                $fechaActual = date('Y-m-d', strtotime("$fechaActual +1 week"));
                continue;
            }

            // Calcular fecha fin de esta semana (6 días después del inicio)
            $fechaFinSemana = date('Y-m-d', strtotime("$fechaActual +6 days"));
            // Asegurarnos de no pasarnos de la fecha fin global
            if (strtotime($fechaFinSemana) > strtotime($fechaFin)) {
                $fechaFinSemana = $fechaFin;
            }

            // ... (resto del código para verificar turno y horario)
            
            if ($horarioId) {
                // Usar fechaFinSemana en lugar de fechaActual
                $stmtAsignar->bind_param("iiss", $colaboradorId, $turnoId, $fechaActual, $fechaFinSemana);
                $stmtAsignar->execute();
                $asignacionesCreadas++;
            }

            $fechaActual = date('Y-m-d', strtotime("$fechaActual +1 week"));
            $semanaIndex++;
        }

        $mensaje = "Se asignaron $asignacionesCreadas turnos semanales";
    }

    $con->commit();
    sendJsonResponse(true, $mensaje, ['total_asignaciones' => $asignacionesCreadas]);

} catch (Exception $e) {
    if (isset($con) && $con->errno) {
        $con->rollback();
    }
    sendJsonResponse(false, $e->getMessage(), [], $e->getCode() ?: 500);
}