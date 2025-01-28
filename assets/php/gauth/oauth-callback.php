<?php
require_once '../../../vendor/autoload.php';
session_start();
include('../../../dbconnection.php');

$client = new Google_Client();
$client->setAuthConfig('../../js/json/credentials.json');
$client->setRedirectUri('http://localhost/tickets-soporte/assets/php/gauth/oauth-callback.php');
$client->addScope(Google_Service_Calendar::CALENDAR_READONLY);

if (isset($_GET['code'])) {
    try {
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

        if (isset($token['error'])) {
            echo "Error al autenticar: " . $token['error_description']; 
            exit;
        }

        $_SESSION['access_token'] = $token;
        if (isset($token['refresh_token'])) {
            $userId = $_SESSION['id'];
            $_SESSION['refresh_token'] = $token['refresh_token'];
 
            $query = "UPDATE user
                        SET refresh_token = ?,
                            access_token = ?
                        WHERE id = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("ssi", $token['refresh_token'], $token['access_token'], $userId);
            $stmt->execute();
           
        }
        header('Location: http://localhost/tickets-soporte/dashboard.php');
        exit;

    } catch (Exception $e) {
        echo "Error durante la autenticación: " . $e->getMessage();
        exit;
    }
} else {
    echo "No se recibió ningún código de autenticación.";
}
?>