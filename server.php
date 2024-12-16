<?php
require 'vendor/autoload.php';
require 'dbconnection.php';
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
if (!$con) {
    echo "Error de conexión a la base de datos: " . mysqli_connect_error();
    exit;
}


class Chat implements MessageComponentInterface {
    protected $clients;
    private $ticketClients = [];

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "Nueva conexión: ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        global $con;
    
        $data = json_decode($msg);
        $ticketId = $data->ticket_id;
        $sender = $data->sender;
        $message = $data->message;
    
        // Guardar el mensaje en la base de datos usando mysqli
        $stmt = $con->prepare("INSERT INTO messages (ticket_id, sender, message) VALUES (?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("iss", $ticketId, $sender, $message);
            $stmt->execute();
            $stmt->close();
        } else {
            echo "Error al preparar la consulta: " . $con->error . "\n";
        }
    
        // Asociamos al cliente con el ticket_id
        if (!isset($this->ticketClients[$ticketId])) {
            $this->ticketClients[$ticketId] = [];
        }
        $this->ticketClients[$ticketId][$from->resourceId] = $from;
    
        // Enviar mensaje a los clientes conectados al ticket
        foreach ($this->ticketClients[$ticketId] as $client) {
            $client->send(json_encode([
                'ticket_id' => $ticketId,
                'sender' => $sender,
                'message' => $message
            ]));
        }
    }
    public function onClose(ConnectionInterface $conn) {
        // Eliminamos el cliente de todos los tickets
        foreach ($this->ticketClients as $ticketId => $clients) {
            if (isset($clients[$conn->resourceId])) {
                unset($this->ticketClients[$ticketId][$conn->resourceId]);
            }
        }
    
        // Finalmente, eliminamos de la colección general
        $this->clients->detach($conn);
        echo "Conexión cerrada: ({$conn->resourceId})\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Error: {$e->getMessage()}\n";
        $conn->close();
    }
}

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Chat()
        )
    ),
    8080
);

echo "Servidor WebSocket ejecutándose en el puerto 8080\n";
$server->run();
