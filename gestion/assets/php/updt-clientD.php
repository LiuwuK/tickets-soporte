<?php
include("../../../dbconnection.php");

header('Content-Type: application/json');

$response = ['success' => false];

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validar y sanitizar los datos
    $id = filter_var($data['id'], FILTER_VALIDATE_INT);
    $encargado = htmlspecialchars($data['datos']['encargado']);
    $cargo = htmlspecialchars($data['datos']['cargo']);
    $correo = filter_var($data['datos']['correo'], FILTER_VALIDATE_EMAIL);
    
    if (!$correo) {
        throw new Exception("Correo electrónico no válido");
    }
    
    $stmt = $con->prepare("UPDATE clientes SET encargado = ?, cargo = ?, correo = ? WHERE id = ?");
    $stmt->bind_param("sssi", $encargado, $cargo, $correo, $id);
    
    if ($stmt->execute()) {
        $response['success'] = true;
    } else {
        throw new Exception($stmt->error);
    }
    
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response);
?>