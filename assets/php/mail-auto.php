<?php
require_once __DIR__ . '/../../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


include('../../dbconnection.php');
//credenciales  USER = CORREO / PASS = CLAVE DE APLICACION GOOGLE 
$user = 'stsafeteck@gmail.com'; // correo
$pass = 'molc xtfj nfev kruf'; // Contraseña de aplicación
$tId  = '$tId'; 

//= DATE_ADD(?, INTERVAL 3 DAY)
$hoy = date('Y-m-d');

$query = "SELECT ac.*
            FROM actividades ac
            WHERE DATE(ac.fecha_inicio) = ? OR 
            DATE(ac.fecha_inicio)";
$stmt = $con->prepare($query);
$stmt->bind_param('s', $hoy);
$stmt->execute();
$actividades = $stmt->get_result();
$actividadesPorFecha = [];

foreach ($actividades as $actividad) {
    $fechaActividad = date('Y-m-d', strtotime($actividad['fecha_inicio']));
    $area = $actividad['area'];

    $actividadesXfecha[$fechaActividad][$area][] = $actividad;
}
foreach ($actividadesXfecha as $fecha => $areas) {
    foreach ($areas as $areaId => $actividades) {
        $query = "SELECT email FROM user WHERE cargo = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param('s', $areaId);
        $stmt->execute();
        $emails = $stmt->get_result();

        $emailsxarea = [];
        while ($email = $emails->fetch_assoc()) {
            $emailsxarea[] = $email['email'];
        }
    }

}
/*
foreach ($actividadesXfecha as $fecha => $actividadesGrupo) {
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
        $mail->setFrom('stsafeteck@gmail.com', 'Soporte');
        $mail->addAddress($actividadesGrupo[0]['user_email'], 'Usuario'); 

        $mail->isHTML(true);
        $mail->Subject = 'Recordatorio de Actividades para la semana del ' . date('d-m-Y', strtotime($fecha));
        
        $contenido = '<h1>Recordatorio de Actividades</h1>';
        foreach ($actividadesGrupo as $actividad) {
            $contenido .= '<p><strong>Título:</strong> ' . $actividad['titulo'] . '</p>';
            $contenido .= '<p><strong>Descripción:</strong> ' . $actividad['descripcion'] . '</p>';
            $contenido .= '<p><strong>Fecha:</strong> ' . date('d-m-Y', strtotime($actividad['fecha'])) . '</p>';
            $contenido .= '<hr>';
        }
        $mail->Body    = $contenido;

        // Enviar correo
        $mail->send();
        echo 'Correo enviado a ' . $actividadesGrupo[0]['user_email'] . '<br>';
    } catch (Exception $e) {
        echo "No se pudo enviar el correo. Error: {$mail->ErrorInfo}<br>";
    }
}
*/
// Enviar correos para cada grupo de actividades por fecha
echo "<pre>";
print_r( $actividadesXfecha);
echo "</pre>";
?>  