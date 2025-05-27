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
//$con->query("DELETE FROM turnos_instalacion WHERE sucursal_id = $sucursal_id");

// Insertar nuevos turnos
if (isset($_POST['turnos']) && is_array($_POST['turnos'])) {
    $stmt = $con->prepare("INSERT INTO turnos_instalacion 
                          (sucursal_id, nombre_turno, jornada_id, hora_entrada, hora_salida) 
                          VALUES (?, ?, ?, ?, ?)");
    
    foreach ($_POST['turnos'] as $turno) {
        if (empty($turno['nombre']) || empty($turno['jornada_id']) || 
            empty($turno['hora_entrada']) || empty($turno['hora_salida'])) {
            continue;
        }
        
        $stmt->bind_param(
            "issss",
            $sucursal_id,
            $turno['nombre'],
            $turno['jornada_id'],
            $turno['hora_entrada'],
            $turno['hora_salida']
        );
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = 'Los turnos se han guardado correctamente';
        } else {
            $_SESSION['error_message'] = 'Error al guardar los turnos: '.$con->error;
        }
    }

    header('Location: ../../detalle-instalacion.php?id='.$sucursal_id);
    exit;
} else {
    $_SESSION['error_message'] = 'Error al guardar los turnos: '.$con->error;
    header('Location: ../../detalle-instalacion.php?id='.$sucursal_id);
    exit;
}