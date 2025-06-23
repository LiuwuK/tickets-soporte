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

    // Validar campos requeridos (eliminamos sucursal_id de los requeridos)
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

    // Modificamos la consulta para eliminar sucursal_id
    $stmtAsignar = $con->prepare("INSERT INTO colaborador_turno 
                                (colaborador_id, turno_id, fecha_inicio, fecha_fin, estado) 
                                VALUES (?, ?, ?, ?, 'ACTIVO')
                                ON DUPLICATE KEY UPDATE 
                                turno_id = VALUES(turno_id),
                                estado = 'ACTIVO',
                                fecha_fin = VALUES(fecha_fin)");

    if ($esRecurrente) {
        if (empty($data['turno_id'])) {
            sendJsonResponse(false, 'Falta el ID del turno para asignación recurrente', [], 400);
        }

        $turnoId = (int)$data['turno_id'];
        
        $stmtTurno = $con->prepare("SELECT id, codigo FROM turnos_instalacion WHERE id = ?");
        if (!$stmtTurno) {
            throw new Exception("Error al preparar consulta: " . $con->error);
        }
        
        $stmtTurno->bind_param("i", $turnoId);
        if (!$stmtTurno->execute()) {
            throw new Exception("Error al ejecutar consulta: " . $stmtTurno->error);
        }
        
        $resultTurno = $stmtTurno->get_result();
        $turno = $resultTurno->fetch_assoc();

        if (!$turno) {
            sendJsonResponse(false, 'El turno seleccionado no existe', [], 404);
        }

        $fechaActual = clone $fechaInicio;
        
        while ($fechaActual <= $fechaFin) {
            $fechaInicioSemana = $fechaActual->format('Y-m-d');
            $fechaFinSemana = clone $fechaActual;
            $fechaFinSemana->modify('+6 days');
            
            if ($fechaFinSemana > $fechaFin) {
                $fechaFinSemana = clone $fechaFin;
            }
            
            $fechaFinSemanaStr = $fechaFinSemana->format('Y-m-d');

            // Verificamos disponibilidad del turno (sin sucursal_id)
            $stmtHorarios = $con->prepare("SELECT COUNT(*) FROM horarios_sucursal 
                                         WHERE turno_id = ? AND fecha BETWEEN ? AND ?");
            if (!$stmtHorarios) {
                throw new Exception("Error al preparar consulta: " . $con->error);
            }
            
            $stmtHorarios->bind_param("iss", $turnoId, $fechaInicioSemana, $fechaFinSemanaStr);
            if (!$stmtHorarios->execute()) {
                throw new Exception("Error al ejecutar consulta: " . $stmtHorarios->error);
            }
            
            $count = $stmtHorarios->get_result()->fetch_row()[0];

            if ($count > 0) {
                // Asignamos turno (sin sucursal_id)
                if (!$stmtAsignar->bind_param("iiss", 
                    $colaboradorId, 
                    $turnoId, 
                    $fechaInicioSemana, 
                    $fechaFinSemanaStr
                )) {
                    throw new Exception("Error al bindear parámetros: " . $stmtAsignar->error);
                }
                
                if (!$stmtAsignar->execute()) {
                    throw new Exception("Error al asignar turno: " . $stmtAsignar->error);
                }
                
                $asignacionesCreadas++;
            }

            $fechaActual->modify('+7 days');
        }
        
        $mensaje = "Se asignó el turno {$turno['codigo']} semanalmente ($asignacionesCreadas semanas)";
    } else {
        if (empty($data['turnos_semanas']) || !is_array($data['turnos_semanas'])) {
            sendJsonResponse(false, 'Faltan los turnos por semana', [], 400);
        }

        $fechaActual = clone $fechaInicio;
        $semanaIndex = 0;
        
        foreach ($data['turnos_semanas'] as $turnoId) {
            if (empty($turnoId)) {
                $semanaIndex++;
                $fechaActual->modify('+7 days');
                continue;
            }

            $turnoId = (int)$turnoId;
            $fechaInicioSemana = $fechaActual->format('Y-m-d');
            $fechaFinSemana = clone $fechaActual;
            $fechaFinSemana->modify('+6 days');
            
            if ($fechaFinSemana > $fechaFin) {
                $fechaFinSemana = clone $fechaFin;
            }
            
            $fechaFinSemanaStr = $fechaFinSemana->format('Y-m-d');

            $stmtTurno = $con->prepare("SELECT id, codigo FROM turnos_instalacion WHERE id = ?");
            if (!$stmtTurno) {
                throw new Exception("Error al preparar consulta: " . $con->error);
            }
            
            $stmtTurno->bind_param("i", $turnoId);
            if (!$stmtTurno->execute()) {
                throw new Exception("Error al ejecutar consulta: " . $stmtTurno->error);
            }
            
            $turno = $stmtTurno->get_result()->fetch_assoc();

            if (!$turno) {
                $semanaIndex++;
                $fechaActual->modify('+7 days');
                continue;
            }

            $stmtHorarios = $con->prepare("SELECT COUNT(*) FROM horarios_sucursal 
                                         WHERE turno_id = ? AND fecha BETWEEN ? AND ?");
            if (!$stmtHorarios) {
                throw new Exception("Error al preparar consulta: " . $con->error);
            }
            
            $stmtHorarios->bind_param("iss", $turnoId, $fechaInicioSemana, $fechaFinSemanaStr);
            if (!$stmtHorarios->execute()) {
                throw new Exception("Error al ejecutar consulta: " . $stmtHorarios->error);
            }
            
            $count = $stmtHorarios->get_result()->fetch_row()[0];

            if ($count > 0) {
                if (!$stmtAsignar->bind_param("iiss", 
                    $colaboradorId, 
                    $turnoId, 
                    $fechaInicioSemana, 
                    $fechaFinSemanaStr
                )) {
                    throw new Exception("Error al bindear parámetros: " . $stmtAsignar->error);
                }
                
                if (!$stmtAsignar->execute()) {
                    throw new Exception("Error al asignar turno: " . $stmtAsignar->error);
                }
                
                $asignacionesCreadas++;
            }

            $semanaIndex++;
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