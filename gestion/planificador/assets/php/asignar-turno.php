<?php
ob_start();
session_start();
require_once '../../../../dbconnection.php';
require_once '../../../../checklogin.php';

if (!$con) {
    header('Content-Type: application/json');
    die(json_encode([
        'success' => false,
        'message' => 'Error de conexión a la base de datos'
    ]));
}

check_login();

function sendJsonResponse($success, $message, $data = [], $statusCode = 200) {
    if (ob_get_length()) ob_clean();
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendJsonResponse(false, 'Método no permitido', [], 405);
    }

    $json = file_get_contents('php://input');
    if (empty($json)) {
        sendJsonResponse(false, 'Datos no recibidos', [], 400);
    }

    $data = json_decode($json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        sendJsonResponse(false, 'JSON inválido: ' . json_last_error_msg(), [], 400);
    }

    // Validar campos requeridos
    $required = ['colaborador_id', 'fecha_inicio', 'fecha_fin'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            sendJsonResponse(false, "Campo requerido faltante: $field", [], 400);
        }
    }

    $fechaInicio = new DateTime($data['fecha_inicio']);
    $fechaFin = new DateTime($data['fecha_fin']);

    if ($fechaFin < $fechaInicio) {
        sendJsonResponse(false, 'La fecha final no puede ser anterior a la inicial', [], 400);
    }

    $con->begin_transaction();

    $colaboradorId = (int)$data['colaborador_id'];
    $esRecurrente = !empty($data['es_recurrente']);
    $asignacionesCreadas = 0;

    // Preparar statement de asignación
    $stmtAsignar = $con->prepare("INSERT INTO colaborador_turno 
        (colaborador_id, turno_id, bloque_id, fecha_inicio, fecha_fin, estado) 
        VALUES (?, ?, ?, ?, ?, 'ACTIVO')
        ON DUPLICATE KEY UPDATE 
            turno_id = VALUES(turno_id),
            bloque_id = VALUES(bloque_id),
            estado = 'ACTIVO',
            fecha_fin = VALUES(fecha_fin)");

    if ($esRecurrente) {
        // Asignación recurrente
        if (empty($data['turno_id']) || empty($data['bloque_id'])) {
            sendJsonResponse(false, 'Faltan turno_id o bloque_id para la asignación recurrente', [], 400);
        }

        $turnoId = (int)$data['turno_id'];
        $bloqueId = $data['bloque_id']; // STRING

        // Verificar que el turno y bloque existen
        $stmtTurno = $con->prepare("
            SELECT t.id, t.codigo 
            FROM turnos_instalacion t
            JOIN horarios_sucursal hs ON hs.turno_id = t.id
            WHERE t.id = ? AND hs.bloque_id = ?
            LIMIT 1
        ");
        $stmtTurno->bind_param("is", $turnoId, $bloqueId);
        $stmtTurno->execute();
        $turno = $stmtTurno->get_result()->fetch_assoc();

        if (!$turno) {
            sendJsonResponse(false, 'El turno seleccionado no existe para el bloque indicado', [], 404);
        }

        // Iterar por semanas
        $fechaActual = clone $fechaInicio;
        while ($fechaActual <= $fechaFin) {
            $fechaInicioSemana = $fechaActual->format('Y-m-d');
            $fechaFinSemana = clone $fechaActual;
            $fechaFinSemana->modify('+6 days');
            if ($fechaFinSemana > $fechaFin) {
                $fechaFinSemana = clone $fechaFin;
            }
            $fechaFinSemanaStr = $fechaFinSemana->format('Y-m-d');

            $stmtAsignar->bind_param("iisss", 
                $colaboradorId, 
                $turnoId, 
                $bloqueId, 
                $fechaInicioSemana, 
                $fechaFinSemanaStr
            );
            $stmtAsignar->execute();
            $asignacionesCreadas++;

            $fechaActual->modify('+7 days');
        }

        $mensaje = "Se asignó el turno {$turno['codigo']} semanalmente ($asignacionesCreadas semanas)";
    } 
    else {
        if (empty($data['turnos_semanas']) || !is_array($data['turnos_semanas'])) {
            sendJsonResponse(false, 'Faltan los turnos por semana', [], 400);
        }

        $fechaActual = clone $fechaInicio;
        foreach ($data['turnos_semanas'] as $asignacion) {
            if (empty($asignacion['turno_id']) || empty($asignacion['bloque_id'])) {
                $fechaActual->modify('+7 days');
                continue;
            }

            $turnoId = (int)$asignacion['turno_id'];
            $bloqueId = $asignacion['bloque_id']; // STRING

            $fechaInicioSemana = $fechaActual->format('Y-m-d');
            $fechaFinSemana = clone $fechaActual;
            $fechaFinSemana->modify('+6 days');
            if ($fechaFinSemana > $fechaFin) {
                $fechaFinSemana = clone $fechaFin;
            }
            $fechaFinSemanaStr = $fechaFinSemana->format('Y-m-d');

            // Verificar turno y bloque
            $stmtTurno = $con->prepare("
                SELECT t.id 
                FROM turnos_instalacion t
                JOIN horarios_sucursal hs ON hs.turno_id = t.id
                WHERE t.id = ? AND hs.bloque_id = ?
                LIMIT 1
            ");
            $stmtTurno->bind_param("is", $turnoId, $bloqueId);
            $stmtTurno->execute();
            $turno = $stmtTurno->get_result()->fetch_assoc();

            if (!$turno) {
                $fechaActual->modify('+7 days');
                continue;
            }

            $stmtAsignar->bind_param("iisss", 
                $colaboradorId, 
                $turnoId, 
                $bloqueId, 
                $fechaInicioSemana, 
                $fechaFinSemanaStr
            );
            $stmtAsignar->execute();
            $asignacionesCreadas++;

            $fechaActual->modify('+7 days');
        }

        $mensaje = "Se asignaron $asignacionesCreadas turnos semanales";
    }

    if ($asignacionesCreadas === 0) {
        sendJsonResponse(false, 'No se crearon asignaciones. Verifique que existan horarios para los turnos seleccionados', [], 404);
    }

    $con->commit();
    sendJsonResponse(true, $mensaje, ['asignaciones' => $asignacionesCreadas]);

} catch (Exception $e) {
    if (isset($con) && $con->errno) {
        $con->rollback();
    }
    sendJsonResponse(false, $e->getMessage(), [], $e->getCode() ?: 500);
} finally {
    if (ob_get_length()) ob_end_clean();
}
