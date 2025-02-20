<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once "../../../dbconnection.php";
require_once "config.php";
require_once "../../../vendor/autoload.php";  

use Firebase\JWT\JWT;

$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $data["email"] ?? "";
    $password = $data["password"] ?? "";

    if (empty($email) || empty($password)) {
        echo json_encode(["error" => "Correo y contraseña son requeridos"]);
        exit;
    }

    // Buscar usuario en la BD
    $stmt = $con->prepare("SELECT id, password, name FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user["password"])) {
        // Crear el token JWT
        $payload = [
            "id" => $user["id"],
            "email" => $email,
            "name" => $user["name"],
            "exp" => time() + (60 * 60 * 24) 
        ];
        $token = JWT::encode($payload, JWT_SECRET, JWT_ALG);

        echo json_encode(["token" => $token, "message" => "Login exitoso"]);
    } else {
        echo json_encode(["error" => "Usuario o contraseña incorrectos"]);
    }
} else {
    echo json_encode(["error" => "Método no permitido"]);
}
?>
