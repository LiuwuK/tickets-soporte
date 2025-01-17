<?php
include("dbconnection.php");
require __DIR__ . '/../vendor/autoload.php';
use WebSocket\Client;

//notificacion actualizar ticket
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
    insertNoti($userId, $msg,$ticketId,false);
}

//notificacion crear ticket
function ticketNoti($ticketId, $userId) {
    $client = new Client("ws://localhost:8080/notifications");
    $msg = "Se ha creado el ticket #{$ticketId}.";
    $message = json_encode([
        'type' => 'new_ticket',
        'message' => $msg
    ]);
    $client->send($message);
    insertNoti($userId, $msg,$ticketId,true);
}

//insert notificaciones 
function insertNoti($userId, $msg, $ticketId, $isAdmin) {
    global $con;
    $url = "?textSearch={$ticketId}";
    $adm = true;

    if (!$isAdmin) {
        $query = "INSERT INTO notificaciones (usuario_id, mensaje, url, ticket_id) 
                  VALUES (?,?,?,?)";
        $stmt = $con->prepare($query);
        $stmt->bind_param("issi", $userId, $msg, $url, $ticketId); 
    } else {
        $query = "INSERT INTO notificaciones (usuario_id, mensaje, url, ticket_id, admin) 
                  VALUES (?,?,?,?,?)";
        $stmt = $con->prepare($query);
        $stmt->bind_param("issii", $userId, $msg, $url, $ticketId, $adm); 
    }

    if ($stmt === false) {
        die("Error preparando la consulta: ".$con->error); 
    }

    if (!$stmt->execute()) {
        die("Error ejecutando la consulta: ".$stmt->error); 
    }
    echo "Notificación insertada correctamente.";
    $stmt->close();
}