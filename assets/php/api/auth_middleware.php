<?php
require_once "config.php";
require_once "../../../vendor/autoload.php";

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

function verifyJWT($token) {
    try {
        if (!defined('JWT_SECRET') || !defined('JWT_ALG')) {
            throw new Exception("Configuración JWT no encontrada.");
        }

        $decoded = JWT::decode($token, new Key(JWT_SECRET, JWT_ALG));
        return (array) $decoded;  // Token válido, devuelve los datos del usuario

    } catch (ExpiredException $e) {
        return ["error" => "El token ha expirado."];
    } catch (SignatureInvalidException $e) {
        return ["error" => "Firma del token inválida."];
    } catch (Exception $e) {
        return ["error" => "Token no válido."];
    }
}
?>
