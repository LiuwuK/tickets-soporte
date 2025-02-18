<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
require_once "../../../dbconnection.php";
require_once 'auth_middleware.php';

// Verificar el método de la solicitud
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
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

$userData = verifyJWTFromHeader();
$user_id = $userData['id'];
$email = $userData['email'];

try {
    $data = json_decode(file_get_contents("php://input"), true);
    if (
        !isset($data['subject']) ||
        !isset($data['department']) ||
        !isset($data['description'])
    ) {
        http_response_code(400);
        echo json_encode(["error" => "Datos incompletos"]);
        exit;
    }

    // Asignar los valores a variables
    $subject = $data['subject'];
    $task_type = $data['department']; 
    $ticket = $data['description'];
    $status = "11";
    // Preparar la consulta SQL
    $query = "INSERT INTO ticket (email_id,subject, task_type, ticket, status, user_id) 
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $con->prepare($query);
    $stmt->bind_param("ssisii", $email, $subject, $task_type, $ticket, $status,$user_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        http_response_code(201);
        echo json_encode(array("message" => "Ticket creado correctamente."));
    } else {
        http_response_code(500);
        echo json_encode(array("message" => "Error al crear el ticket."));
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array("message" => "Error en el servidor: " . $e->getMessage()));
}
?>