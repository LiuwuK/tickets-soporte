<?php
require 'dbconnection.php';

$ticketId = isset($_GET['ticket_id']) ? (int) $_GET['ticket_id'] : 0;

if ($ticketId <= 0) {
    echo json_encode(['error' => 'ID del ticket no válido']);
    exit;
}

$sql = "SELECT sender, message, created_at FROM messages WHERE ticket_id = ? ORDER BY created_at ASC";
$stmt = $con->prepare($sql);

if ($stmt) {
    $stmt->bind_param("i", $ticketId);
    $stmt->execute();
    $result = $stmt->get_result();
    $messages = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    echo json_encode($messages);
} else {
    echo json_encode(['error' => 'Error al preparar la consulta: ' . $con->error]);
}
?>      