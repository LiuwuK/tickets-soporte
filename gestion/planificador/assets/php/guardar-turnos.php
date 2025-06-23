<?php
session_start();
require_once '../../../../dbconnection.php';
require_once '../../../../checklogin.php';
check_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['success' => false, 'message' => 'Método no permitido']));
}

if (!isset($_POST['sucursal_id']) || !is_numeric($_POST['sucursal_id'])) {
    die(json_encode(['success' => false, 'message' => 'ID de sucursal inválido']));
}

$sucursal_id = $_POST['sucursal_id'];

// Eliminar turnos existentes
if(isset($_POST['delTurno'])){
//$con->query("DELETE FROM turnos_instalacion WHERE sucursal_id = $sucursal_id");
}
// Insertar nuevos turnos
if (isset($_POST['turnos']) && is_array($_POST['turnos'])) {
    // Primero, consultar el máximo código existente para esta sucursal
    $maxCodigoQuery = $con->prepare("SELECT MAX(codigo) as max_codigo FROM turnos_instalacion WHERE sucursal_id = ?");
    $maxCodigoQuery->bind_param("i", $sucursal_id);
    $maxCodigoQuery->execute();
    $result = $maxCodigoQuery->get_result();
    $row = $result->fetch_assoc();
    $maxCodigo = $row['max_codigo'];
    
    // Determinar el número inicial
    $initialNumber = 1;
    if ($maxCodigo) {
        preg_match('/\d+$/', $maxCodigo, $matches);
        if ($matches) {
            $initialNumber = (int)$matches[0] + 1;
        }
    }
    
    $stmt = $con->prepare("INSERT INTO turnos_instalacion 
                          (sucursal_id, nombre_turno, jornada_id, hora_entrada, hora_salida, codigo) 
                          VALUES (?, ?, ?, ?, ?, ?)");
    
    $currentNumber = $initialNumber;
    foreach ($_POST['turnos'] as $turno) {
        // Guarda turno base
        $stmt = $con->prepare("INSERT INTO turnos_instalacion (sucursal_id, nombre_turno, jornada_id, codigo) VALUES (?, ?, ?, ?)");
        $codigo = 'M' . $currentNumber++;
        $stmt->bind_param("isss", $sucursal_id, $turno['nombre'], $turno['jornada_id'], $codigo);
        $stmt->execute();
        $turno_id = $stmt->insert_id;

        // Guarda horarios por día
        foreach ($turno['dias'] as $dia => $horarios) {
            $entrada = $horarios['entrada'] ?? null;
            $salida = $horarios['salida'] ?? null;

            if ($entrada && $salida) {
                $stmtDia = $con->prepare("INSERT INTO turno_dias (turno_id, dia_semana, hora_entrada, hora_salida) VALUES (?, ?, ?, ?)");
                $stmtDia->bind_param("isss", $turno_id, $dia, $entrada, $salida);
                $stmtDia->execute();
            }
        }
    }

    header('Location: ../../detalle-instalacion.php?id='.$sucursal_id);
    exit;
} else {
    $_SESSION['error_message'] = 'No se recibieron datos de turnos válidos';
    header('Location: ../../detalle-instalacion.php?id='.$sucursal_id);
    exit;
}