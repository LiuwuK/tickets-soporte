<?php
require 'dbconnection.php';

$ticket_id = $_GET['ticket_id'] ?? null;

if (!$ticket_id) {
    echo json_encode(['error' => 'No se proporcionó un ticket_id.']);
    exit;
}

$query = $con->prepare("SELECT sender, message, created_at FROM messages WHERE ticket_id = ? ORDER BY created_at ASC");
$query->bind_param("i", $ticket_id);
$query->execute();
$result = $query->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

echo json_encode($messages);