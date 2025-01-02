<?php 
include("../../dbconnection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['ticketId']) && is_numeric($data['ticketId'])) {
        $tId = intval($data['ticketId']); 
        
        $stmt = $con->prepare("UPDATE notificaciones SET leida = 1 WHERE ticket_id = ?");
        $stmt->bind_param("i", $tId);

        if ($stmt->execute()) {
            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode(['message' => 'Consulta realizada con éxito']);
        } else {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode(['message' => 'Error al ejecutar la consulta']);
        }

        $stmt->close();
    } else {
        // ticketId no proporcionado o no válido
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['message' => 'ID de ticket inválido o no proporcionado']);
    }
} else {
    // Método no permitido
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Método no permitido']);
}
?>