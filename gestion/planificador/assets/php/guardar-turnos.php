<?php
session_start();
ob_start();
require_once '../../../../dbconnection.php';
require_once '../../../../checklogin.php';
check_login();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

if (!isset($_POST['sucursal_id']) || !is_numeric($_POST['sucursal_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID de sucursal inválido']);
    exit;
}

$sucursal_id = (int)$_POST['sucursal_id'];
$response = ['success' => false, 'message' => ''];

try {
    $con->begin_transaction();

    // Eliminar turnos marcados para eliminación
    if (!empty($_POST['turnos_eliminados'])) {
        $ids_eliminar = array_map('intval', $_POST['turnos_eliminados']);
        $placeholders = implode(',', array_fill(0, count($ids_eliminar), '?'));
        
        /* Eliminar días 
        $stmt = $con->prepare("DELETE FROM turno_dias WHERE turno_id IN ($placeholders)");
        $stmt->bind_param(str_repeat('i', count($ids_eliminar)), ...$ids_eliminar);
        $stmt->execute();
        */
        // Eliminar turnos
        $stmt = $con->prepare("DELETE FROM turnos_instalacion WHERE id IN ($placeholders) AND sucursal_id = ?");
        $ids_eliminar[] = $sucursal_id;
        $stmt->bind_param(str_repeat('i', count($ids_eliminar)), ...$ids_eliminar);
        $stmt->execute();
    }

    // Actualizar turnos existentes
    if (!empty($_POST['turnos'])) {
        foreach ($_POST['turnos'] as $turno) {
            if (empty($turno['id'])) continue;
            
            $turno_id = (int)$turno['id'];
            // Actualizar información básica del turno
            $stmt = $con->prepare("UPDATE turnos_instalacion 
                                 SET nombre_turno = ?, jornada_id = ?
                                 WHERE id = ? AND sucursal_id = ?");
            $stmt->bind_param("siii", $turno['nombre'], $turno['jornada_id'], $turno_id, $sucursal_id);
            $stmt->execute();
            
            // Eliminar y recrear horarios
            $stmt = $con->prepare("DELETE FROM turno_dias WHERE turno_id = ?");
            $stmt->bind_param("i", $turno_id);
            $stmt->execute();
            
            // Insertar nuevos horarios
            if (!empty($turno['dias'])) {
                foreach ($turno['dias'] as $dia => $horarios) {
                    if (empty($horarios['entrada'])) continue;
                    
                    $stmt = $con->prepare("INSERT INTO turno_dias 
                                         (turno_id, dia_semana, hora_entrada, hora_salida) 
                                         VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("isss", $turno_id, $dia, $horarios['entrada'], $horarios['salida']);
                    $stmt->execute();
                }
            }
        }
    }

        // Insertar nuevos turnos
    if (!empty($_POST['nuevos_turnos'])) {
        // Obtener el último código
        $maxCodigoQuery = $con->prepare("SELECT MAX(codigo) as max_codigo FROM turnos_instalacion WHERE sucursal_id = ?");
        $maxCodigoQuery->bind_param("i", $sucursal_id);
        $maxCodigoQuery->execute();
        $result = $maxCodigoQuery->get_result();
        $row = $result->fetch_assoc();
        $currentNumber = $row['max_codigo'] ? (int)substr($row['max_codigo'], 1) + 1 : 1;
        
        foreach ($_POST['nuevos_turnos'] as $turno) {
            $codigo = 'M' . $currentNumber++;
            
            // Insertar turno
            $stmt = $con->prepare("INSERT INTO turnos_instalacion 
                                  (sucursal_id, nombre_turno, jornada_id, codigo) 
                                  VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $sucursal_id, $turno['nombre'], $turno['jornada_id'], $codigo);
            $stmt->execute();
            $turno_id = $con->insert_id;
            
            // Insertar horarios
            if (!empty($turno['dias'])) {
                foreach ($turno['dias'] as $dia => $horarios) {
                    if (empty($horarios['entrada'])) continue;
                    
                    $stmt = $con->prepare("INSERT INTO turno_dias 
                                         (turno_id, dia_semana, hora_entrada, hora_salida) 
                                         VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("isss", $turno_id, $dia, $horarios['entrada'], $horarios['salida']);
                    $stmt->execute();
                }
            }
        }
    }



    $con->commit();
    $response = [
        'success' => true,
        'message' => 'Turnos actualizados correctamente',
        'redirect' => "detalle-instalacion.php?id=$sucursal_id"
    ];
} catch (Exception $e) {
    $con->rollback();
    $response = [
        'success' => false,
        'message' => 'Error al actualizar turnos: ' . $e->getMessage()
    ];
}
ob_clean();
echo json_encode($response);
exit;
?>