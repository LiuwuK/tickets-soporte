<?php
include("dbconnection.php");
require __DIR__ . '/../vendor/autoload.php';
use WebSocket\Client;

//notificacion para clientes
function updateNoti($ticketId, $userId) {
    
    $msg = "El ticket #{$ticketId} ha sido actualizado.";
    $client = new Client("ws://localhost:8080/notifications");
    $message = json_encode([
        'type' => 'ticket_update',
        'ticketId' => $ticketId,
        'clientId' => $userId,
        'message' => $msg
    ]);
    $client->send($message);
    insertNoti($userId, $msg);
}
//notificacion para admin
function ticketNoti() {
    $client = new Client("ws://localhost:8080/notifications");
    $message = json_encode([
        'type' => 'new_ticket',
        'message' => "Se ha creado un nuevo ticket."
    ]);
    $client->send($message);
}

//insert notificaciones 
function insertNoti($userId,$msg){
    global $con;
    $query = "INSERT INTO notificaciones (usuario_id, mensaje) VALUES (?,?)";
    $stmt = $con->prepare($query);
    echo "<script>console.log('Mensaje desde PHP: " .$query. "');</script>";

    if ($stmt === false) {
        die("Error preparando la consulta: " . $con->error); 
    }

    $stmt->bind_param("is", $userId, $msg); 
    if (!$stmt->execute()) {
        die("Error ejecutando la consulta: " . $stmt->error); 
    }
    echo "Notificación insertada correctamente.";
    $stmt->close();
}