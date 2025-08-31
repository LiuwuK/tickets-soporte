<?php
session_start();
include("../../../../checklogin.php");
check_login();
include("../../../../dbconnection.php");

header('Content-Type: application/json');

$tipo = $_POST['tipo'] ?? '';
$id = intval($_POST['id'] ?? 0);
$estado = $_POST['estado'] ?? '';

if(!$tipo || !$id || !$estado){
    echo json_encode(['success' => false]);
    exit;
}

if($tipo === 'traslado'){
    $stmt = $con->prepare("UPDATE traslados SET estado=? WHERE id=?");
} elseif($tipo === 'desvinculacion'){
    $stmt = $con->prepare("UPDATE desvinculaciones SET estado=? WHERE id=?");
} else {
    echo json_encode(['success' => false]);
    exit;
}

$stmt->bind_param('si', $estado, $id);
if($stmt->execute()){
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>
