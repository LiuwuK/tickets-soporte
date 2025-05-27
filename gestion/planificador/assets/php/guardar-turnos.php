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
        if (empty($turno['nombre']) || empty($turno['jornada_id']) || 
            empty($turno['hora_entrada']) || empty($turno['hora_salida'])) {
            continue;
        }
        
        $codigo = 'M' . $currentNumber;
        $currentNumber++;
        
        $stmt->bind_param(
            "isssss",
            $sucursal_id,
            $turno['nombre'],
            $turno['jornada_id'],
            $turno['hora_entrada'],
            $turno['hora_salida'],
            $codigo
        );
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = 'Los turnos se han guardado correctamente';
        } else {
            $_SESSION['error_message'] = 'Error al guardar los turnos: '.$con->error;
            break; // salir si hay errores
        }
    }

    header('Location: ../../detalle-instalacion.php?id='.$sucursal_id);
    exit;
} else {
    $_SESSION['error_message'] = 'No se recibieron datos de turnos válidos';
    header('Location: ../../detalle-instalacion.php?id='.$sucursal_id);
    exit;
}