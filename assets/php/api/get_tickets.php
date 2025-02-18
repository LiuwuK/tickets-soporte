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

$userData = verifyJWTFromHeader();
$user_id = $userData['id'];

try {
    // Obtener los tickets del usuario autenticado
    $stmt = $con->prepare("SELECT ti.*, es.nombre AS estadoN
                                    FROM ticket ti
                                    JOIN estados es ON(ti.status = es.id) 
                                    WHERE ti.user_id = ?
                                    ORDER BY ti.id DESC");
    $stmt->bind_param("i", $user_id); 
    $stmt->execute();
    $result = $stmt->get_result();
    $tickets = $result->fetch_all(MYSQLI_ASSOC);

    echo json_encode(["tickets" => $tickets]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error en la base de datos", "detalle" => $e->getMessage()]);
}
?>
