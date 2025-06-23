<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

require_once '../../../../dbconnection.php';

try {
    $json = file_get_contents("php://input");
    $data = json_decode($json, true);

    // Validación
    $required = ['sucursal_id', 'fecha_inicio', 'fecha_fin', 'turno_id', 'patron_jornada', 'horarios'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            throw new Exception("Falta el campo obligatorio: $field", 400);
        }
    }

    // Validar que hay al menos un día con horario
    if (count($data['horarios']) === 0) {
        throw new Exception("Debe proporcionar al menos un día con horario", 400);
    }

    // Procesamiento de fechas
    $fechaInicio = new DateTime($data['fecha_inicio']);
    $fechaFin = new DateTime($data['fecha_fin']);
    
    if ($fechaFin < $fechaInicio) {
        throw new Exception("La fecha final no puede ser anterior a la fecha inicial", 400);
    }

    // Datos del turno
    $sucursalId = (int)$data['sucursal_id'];
    $turnoId = (int)$data['turno_id'];
    $horarios = $data['horarios'];
    
    // Procesar patrón de jornada
    $patron = $data['patron_jornada'];
    if (!preg_match('/^\d+[xX]\d+$/i', $patron)) {
        throw new Exception("Formato de patrón inválido. Debe ser como '5x2' o '5X2'", 400);
    }

    // Convertir a minúsculas para procesamiento consistente
    $patronLower = strtolower($patron);
    list($diasTrabajo, $diasDescanso) = array_map('intval', explode('x', $patronLower));

    // Validar números positivos
    if ($diasTrabajo <= 0 || $diasDescanso <= 0) {
        throw new Exception("Los días de trabajo y descanso deben ser mayores a 0", 400);
    }   

    // Consulta para insertar horarios
    $stmt = $con->prepare("INSERT INTO horarios_sucursal 
                          (sucursal_id, fecha, turno_id, hora_entrada, hora_salida, tipo) 
                          VALUES (?, ?, ?, ?, ?, ?)");
 
    $diasMap = [
        'lunes' => 1,
        'martes' => 2,
        'miércoles' => 3,
        'jueves' => 4,
        'viernes' => 5,
        'sábado' => 6,
        'domingo' => 0
    ];

    // Generación de días
    $con->begin_transaction();
    $fechaActual = clone $fechaInicio;
    $esDiaTrabajo = true;
    $contadorDiasPatron = 0;
    $totalTurnosGenerados = 0;

    while ($fechaActual <= $fechaFin) {
        if ($esDiaTrabajo) {
            // Verificar si este día de la semana tiene horario definido
            $nombreDia = strtolower($fechaActual->format('l')); // 'Monday', 'Tuesday', etc.
            $nombreDiaEsp = array_search($fechaActual->format('w'), $diasMap);
            
            // Buscar horario para este día
            foreach ($horarios as $diaEsp => $horario) {
                if ($diasMap[$diaEsp] == $fechaActual->format('w')) {
                    $fechaStr = $fechaActual->format('Y-m-d');
                    $tipo = 'TRABAJO';
                    
                    $stmt->bind_param(
                        "isisss",
                        $sucursalId,
                        $fechaStr,
                        $turnoId,
                        $horario['entrada'],
                        $horario['salida'],
                        $tipo
                    );
                    
                    if (!$stmt->execute()) {
                        throw new Exception("Error al generar turno: " . $stmt->error, 500);
                    }
                    $totalTurnosGenerados++;
                    break;
                }
            }
        }

        // Avanzar en el patrón
        $contadorDiasPatron++;
        if ($esDiaTrabajo && $contadorDiasPatron >= $diasTrabajo) {
            $esDiaTrabajo = false;
            $contadorDiasPatron = 0;
        } elseif (!$esDiaTrabajo && $contadorDiasPatron >= $diasDescanso) {
            $esDiaTrabajo = true;
            $contadorDiasPatron = 0;
        }

        $fechaActual->modify('+1 day');
    }

    $con->commit();

    echo json_encode([
        'success' => true,
        'message' => "Turnos generados correctamente",
        'total_dias' => $fechaInicio->diff($fechaFin)->days + 1,
        'turnos_generados' => $totalTurnosGenerados,
        'fecha_inicio' => $fechaInicio->format('Y-m-d'),
        'fecha_fin' => $fechaFin->format('Y-m-d')
    ]);

} catch (Exception $e) {
    $con->rollback();
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}