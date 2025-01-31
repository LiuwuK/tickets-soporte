<?php
require_once __DIR__ . '/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


include('../dbconnection.php');
//credenciales  USER = CORREO / PASS = CLAVE DE APLICACION GOOGLE 
$user = 'stsafeteck@gmail.com'; // correo
$pass = 'molc xtfj nfev kruf'; // Contraseña de aplicación

//OR DATE(ac.fecha_inicio) = DATE_ADD(?, INTERVAL 3 DAY)
$hoy = date('Y-m-d');

$query = "SELECT ac.*
            FROM actividades ac
            WHERE DATE(ac.fecha_inicio) = ? 
            ORDER BY ac.fecha_inicio ASC";
$stmt = $con->prepare($query);
$stmt->bind_param('s', $hoy);
$stmt->execute();
$actividades = $stmt->get_result();
$actividadesXfecha = [];

foreach ($actividades as $actividad) {
    $fechaActividad = date('Y-m-d', strtotime($actividad['fecha_inicio']));
    $area = $actividad['area'];

    $actividadesXfecha[$fechaActividad][$area][] = $actividad;
}

// Obtener todos los supervisores
$query = "SELECT email, cargo FROM user WHERE rol = 'supervisor'";
$stmt = $con->prepare($query);
$stmt->execute();
$supervisoresData = $stmt->get_result();

$supervisores = [];
while ($row = $supervisoresData->fetch_assoc()) {
    $supervisores[$row['cargo']] = $row['email'];
}

foreach ($actividadesXfecha as $fecha => $fechaGrupo) {
    foreach ($fechaGrupo as $area => $act) { 
        $supervisor = isset($supervisores[$area]) ? $supervisores[$area] : 'Area sin supervisor';
    }
    echo $supervisor;
    
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
        $mail->addAddress($supervisor, 'Usuario');  

        $mail->isHTML(true);
        $mail->Subject = 'Actividades para el día de hoy';

        $contenido = '
            <!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="UTF-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Recordatorio de Proyectos</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        background-color: #f4f4f9;
                        color: #333;
                        margin: 0;
                        padding: 0;
                    }
                    h2 {
                        color: #fff;
                        text-align: center;
                        background-color: #33435e;
                        padding: 20px 0;
                        margin-bottom: 30px;
                    }
                    .proyecto {
                        background-color: #ffffff;
                        border: 1px solid #ddd;
                        border-radius: 5px;
                        padding: 15px;
                        margin-bottom: 20px;
                        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
                    }
                    .proyecto p {
                        font-size: 16px;
                        line-height: 1.6;
                        margin: 5px 0;
                    }
                    .proyecto strong {
                        color: #2980b9;
                    }
                    .footer {
                        text-align: center;
                        font-size: 12px;
                        color: #777;
                        margin-top: 30px;
                    }
                </style>
            </head>
            <body>
            <h2>Recordatorio de actividades para el día</h2>';

        foreach ($act as $actividad) {
            $contenido .= '
            <div class="proyecto">
                <p><strong>Nombre del proyecto:</strong> '.$actividad['nombre'] . '</p>
                <p><strong>Descripción:</strong> ' . $actividad['descripcion'] . '</p>
                <p><strong>Fecha :</strong> ' . date('d-m-Y', strtotime($actividad['fecha_inicio'])) . '</p>
            </div>';
        }

        $contenido .= '
        <div class="footer">
            <p>Este es un recordatorio automático, no responda a este correo.</p>
        </div>
        </body>
        </html>';

        $mail->Body = $contenido;

        $mail->send();

    } catch (Exception $e) {
        echo "No se pudo enviar el correo. Error: {$mail->ErrorInfo}<br>";
    }

}

echo "<pre>";
print_r( $actividadesXfecha);
echo "</pre>";
?>  