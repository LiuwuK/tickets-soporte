<?php
require_once '../../../vendor/autoload.php'; 
session_start();

$client = new Google_Client();
$client->setAuthConfig('../../js/json/credentials.json'); 
$client->setRedirectUri('http://localhost/tickets-soporte/assets/php/gauth/oauth-callback.php'); 
$client->addScope(Google_Service_Calendar::CALENDAR_READONLY);
$client->setAccessType('offline');

$authUrl = $client->createAuthUrl();
header('Location: ' . $authUrl);
exit;
?>