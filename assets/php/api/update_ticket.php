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

$userData = verifyJWTFromHeader();

try {
    $data = json_decode(file_get_contents("php://input"), true);
    if (
        !isset($data['comentario']) ||
        !isset($data['estado']) ||
        !isset($data['id'])
    ) {
        http_response_code(400);
        echo json_encode(["error" => "Datos incompletos"]);
        exit;
    }

    // Asignar los valores a variables
    $comentario = $data['comentario'];
    $estado = $data['estado']; 
    $id = $data['id'];
    // Preparar la consulta SQL
    $query = "UPDATE ticket 
                SET admin_remark = ?, status = ? 
                WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("sii", $comentario, $estado, $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        http_response_code(201);
        echo json_encode(array("message" => "Ticket acrualizado correctamente."));
    } else {
        http_response_code(500);
        echo json_encode(array("message" => "Error al actualizar el ticket."));
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array("message" => "Error en el servidor: " . $e->getMessage()));
}
?>