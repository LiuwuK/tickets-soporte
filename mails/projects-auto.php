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
            WHERE pr.fecha_fin_contrato BETWEEN ? AND ?
            ORDER BY pr.fecha_fin_contrato ASC ";

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
            <h2>Recordatorio Fin de Contrato de los Siguientes Proyectos</h2>';

        foreach ($proyectos as $proyecto) {
            $contenido .= '
            <div class="proyecto">
                <p><strong>Nombre del proyecto:</strong> ' . $proyecto['nombre'] . '</p>
                <p><strong>Descripción:</strong> ' . $proyecto['resumen'] . '</p>
                <p><strong>Fecha fin Contrato:</strong> ' . date('d-m-Y', strtotime($proyecto['fechaFin'])) . '</p>
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
print_r($proyectosxfecha);
echo "</pre>";
?>  