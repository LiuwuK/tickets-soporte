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

//obtener proyectos con fecha_fin_contrato en los siguientes 3/2/1 mes/es 
$actual  = date('Y-m-d');
$tresM =  date('Y-m-d', strtotime('+3 months'));

$query = "SELECT pr.id, pr.nombre, pr.fecha_fin_contrato AS fechaFin, us.email, pr.resumen
            FROM proyectos pr
            JOIN user us ON(pr.comercial_responsable = us.id)
            WHERE  pr.fecha_fin_contrato BETWEEN ? AND ?";

$stmt = $con->prepare($query);
$stmt->bind_param('ss', $actual, $tresM);
$stmt->execute();
$proyectos = $stmt->get_result();
$proyectosxfecha = [];

foreach ($proyectos as $proyecto) {
    $fechaProyecto = date('Y-m', strtotime($proyecto['fechaFin']));
    $email = $proyecto['email'];

    $proyectosxfecha[$fechaProyecto][$email][] = $proyecto;
}

foreach ($proyectosxfecha as $fecha => $proyectoGrupo) {
    foreach ($proyectoGrupo as $email => $proyectos) { 
        $destinatario = $email; 
    }

    $mail = new PHPMailer(true);

    try {
        // Configuración de correo
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $user;
        $mail->Password = $pass;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';

        $mail->clearAddresses();
        $mail->setFrom('stsafeteck@gmail.com', 'IMPORTANTE!!');
        $mail->addAddress($destinatario, 'Usuario');  

        $fechaFin = strtotime($proyectos[0]['fechaFin']); 
        $mes = strftime('%B', $fechaFin);

        $mail->isHTML(true);
        $mail->Subject = 'Recordatorio de finalización de contrato para el mes de '.$mes;

        $contenido = '<h1>Recordatorio fin de contrato de los siguientes Proyectos</h1>';
        foreach ($proyectos as $proyecto) {
            $contenido .= '<p><strong>Nombre del proyecto:</strong> ' . $proyecto['nombre'] . '</p>';
            $contenido .= '<p><strong>Descripción:</strong> ' . $proyecto['resumen'] . '</p>';
            $contenido .= '<p><strong>Fecha fin Contrato:</strong> ' . date('d-m-Y', strtotime($proyecto['fechaFin'])) . '</p>';
            $contenido .= '<hr>';
        }

        $mail->Body = $contenido;

        $mail->send();

    } catch (Exception $e) {
        echo "No se pudo enviar el correo. Error: {$mail->ErrorInfo}<br>";
    }
}

echo "<pre>";
print_r($proyectosxfecha);
echo "</pre>";
?>  