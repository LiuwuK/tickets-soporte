<?php
require_once "config.php";
require_once "../../../vendor/autoload.php";

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit();
}

function getAuthorizationHeader() {
    $headers = apache_request_headers();
    
    // Normalizar claves a minúsculas para evitar problemas en Android
    $authorizationHeader = null;
    foreach ($headers as $key => $value) {
        if (strtolower($key) === 'authorization') {
            $authorizationHeader = $value;
            break;
        }
    }
    
    return $authorizationHeader;
}

function verifyJWTFromHeader() {
    $authorizationHeader = getAuthorizationHeader();
    
    if (!$authorizationHeader) {
        http_response_code(401);
        echo json_encode(["error" => "No se proporcionó un token"]);
        exit();
    }

    // Extraer el token (quitar "Bearer ")
    $token = str_replace("Bearer ", "", $authorizationHeader);

    try {
        if (!defined('JWT_SECRET') || !defined('JWT_ALG')) {
            throw new Exception("Configuración JWT no encontrada.");
        }
        
        $decoded = JWT::decode($token, new Key(JWT_SECRET, JWT_ALG));
        return (array) $decoded;

    } catch (ExpiredException $e) {
        http_response_code(401);
        echo json_encode(["error" => "El token ha expirado."]);
        exit();
    } catch (SignatureInvalidException $e) {
        http_response_code(401);
        echo json_encode(["error" => "Firma del token inválida."]);
        exit();
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(["error" => "Token no válido."]);
        exit();
    }
}

?>
