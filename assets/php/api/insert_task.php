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

$token = str_replace("Bearer ", "", $headers['Authorization']);

// Validar el token y obtener el ID del usuario
$userData = verifyJWT($token);
if (!$userData) {
    http_response_code(401);
    echo json_encode(["error" => "Token inválido"]);
    exit;
}

try {
    $data = json_decode(file_get_contents("php://input"), true);
    if (
        !isset($data['title']) ||
        !isset($data['id'])
    ) {
        http_response_code(400);
        echo json_encode(["error" => "Datos incompletos"]);
        exit;
    }
    $titulo = $data['title']; 
    $tid = $data['id'];
    
    $query = "INSERT INTO tasks (titulo, ticket_id) 
              VALUES (?, ?)";
    $stmt = $con->prepare($query);
    $stmt->bind_param("si", $titulo, $tid);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        http_response_code(201);
        echo json_encode(array("message" => "Tarea creada correctamente."));
    } else {
        http_response_code(500);
        echo json_encode(array("message" => "Error al crear la tarea."));
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array("message" => "Error en el servidor: " . $e->getMessage()));
}
?>