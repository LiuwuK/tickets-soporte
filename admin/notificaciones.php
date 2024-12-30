<?php
require __DIR__ . '/../vendor/autoload.php';

use WebSocket\Client;

function updateNoti($ticketId, $userId) {
    $client = new Client("ws://localhost:8080/notifications");
    $message = json_encode([
        'type' => 'ticket_update',
        'ticketId' => $ticketId,
        'clientId' => $userId,
        'message' => "El ticket #{$ticketId} ha sido actualizado."
    ]);
    $client->send($message);
}

function ticketNoti() {
    $client = new Client("ws://localhost:8080/notifications");
    $message = json_encode([
        'type' => 'new_ticket',
        'message' => "Se ha creado un nuevo ticket."
    ]);
    $client->send($message);
}