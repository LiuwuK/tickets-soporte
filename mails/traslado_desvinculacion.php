<?php
require_once __DIR__ . '/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
setlocale(LC_TIME, 'es_ES.UTF-8', 'spanish');
include('../dbconnection.php');
//credenciales  USER = CORREO / PASS = CLAVE DE APLICACION GOOGLE 
$user = 'stsafeteck@gmail.com'; // correo
$pass = 'molc xtfj nfev kruf'; // Contraseña de aplicación
$tId  = '$tId'; 


$query = " ";

$stmt = $con->prepare($query);
$stmt->bind_param('ss', $actual, $tresM);
$stmt->execute();
$forms = $stmt->get_result();
$formXtipo = [];

foreach ($forms as $form) {
    $tipoF = ;
    $formXtipo[$tipoF][] = $form;
}

?>