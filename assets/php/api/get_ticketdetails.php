<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
require_once "../../../dbconnection.php";
require_once 'auth_middleware.php';
// Verificar el método de la solicitud
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido"]);
    exit;
}

// Obtener el token del encabezado
$headers = apache_request_headers();
if (!isset($headers['Authorization'])) {
    http_response_code(401);
    echo json_encode(["error" => "No se proporcionó un token"]);
    exit;
}

$token = str_replace("Bearer ", "", $headers['Authorization']);

// Validar el token y obtener el ID del usuario
$userData = verifyJWT($token);
if (!$userData) {
    http_response_code(401);
    echo json_encode(["error" => "Token inválido"]);
    exit;
}

$tID = intval($_GET['ticketId']);

try {
    // Obtener los tickets del usuario autenticado
    $stmt = $con->prepare("SELECT ti.*, es.nombre AS estadoN
                                    FROM ticket ti
                                    JOIN estados es ON(ti.status = es.id) 
                                    WHERE ti.id = ?");
    $stmt->bind_param("i", $tID); 
    $stmt->execute();
    $result = $stmt->get_result();
    $tickets = $result->fetch_all(MYSQLI_ASSOC);

    echo json_encode(["ticket" => $tickets]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error en la base de datos", "detalle" => $e->getMessage()]);
}
?>
