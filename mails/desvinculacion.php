<?php
require_once __DIR__ . '/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
setlocale(LC_TIME, 'es_ES.UTF-8', 'spanish');
include('../dbconnection.php');
//credenciales  USER = CORREO / PASS = CLAVE DE APLICACION GOOGLE 
$user = 'stsafeteck@gmail.com'; // correo
$pass = 'molc xtfj nfev kruf'; // Contraseña de aplicación


$query = "SELECT de.*, 
                su.nombre AS instalacion,
                us.name AS soliN,
                sup.nombre_supervisor AS supervisor,
                mo.motivo AS motivoEgreso
            FROM desvinculaciones de
            JOIN user us ON(de.solicitante = us.id)
            JOIN sucursales su ON(de.instalacion = su.id)
            JOIN supervisores sup ON(de.supervisor_origen = sup.id)
            JOIN motivos_gestion mo ON(de.motivo = mo.id)
            WHERE de.fecha_registro BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 DAY) + INTERVAL 16 HOUR 
            AND CURDATE() + INTERVAL 1 DAY + INTERVAL 16 HOUR
            ORDER BY de.fecha_registro ASC";

$stmt = $con->prepare($query);
$stmt->execute();
$desvData = $stmt->get_result();
$desvinculaciones = [];
while ($row = mysqli_fetch_assoc($desvData)) {
    $desvinculaciones[] = $row; 
}

$mail = new PHPMailer(true);
if(!empty($desvinculaciones)){
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
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        $mail->clearAddresses();
        $mail->setFrom('stsafeteck@gmail.com', 'Desvinculaciones');
        $destinatarios = [
            'fnorton@gruposafeteck.com' => 'Usuario',
            'fponce@gruposaeteck.com' => 'Usuario',
            'dfsilva@gruposafeteck.com' => 'Usuario',
            'kcarvajal@gruposafeteck.com' => 'Usuario',
            'mserrano@gruposerrano.com' => 'Usuario',
            'bjaramillo@gruposafeteck.com' => 'Usuario',
            'dvegas@gruposafeteck.com' => 'Usuario',
            'asistencia@gruposafeteck.com' => 'Usuario',
            'mpacheco@gruposafeteck.com' => 'Usuario',
        ];

        foreach ($destinatarios as $email => $nombre) {
            $mail->addAddress($email, $nombre);
        }

        $mail->isHTML(true);
        $mail->Subject = 'Desvinculaciones del día de hoy';

        $contenido = '
            <!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="UTF-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Desvinculaciones</title>
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
                    .traslado {
                        background-color: #ffffff;
                        border: 1px solid #ddd;
                        border-radius: 5px;
                        padding: 15px;
                        margin-bottom: 20px;
                        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
                    }
                    .traslado p {
                        font-size: 16px;
                        line-height: 1.6;
                        margin: 2px 0;
                    }
                    .traslado h3{
                        margin: 0px;
                        color: #33435e
                    }
                    .traslado strong {
                        color: #2980b9;
                    }
                    .footer {
                        text-align: center;
                        font-size: 12px;
                        color: #777;
                        margin-top: 30px;
                    }
                    .soli-data{
                        display: flex;
                        justify-content: start;
                    }
                    .origen{
                        margin-top:10px;
                    }

                </style>
            </head>
            <body>
            <h2>Desvinculaciónes del día</h2>';

        foreach ($desvinculaciones as $dv) {
            echo "<pre>";
            print_r($dv);
            echo "</pre>"; 
            
            $contenido .= '
            <div class="traslado">
                <div class="soli-data" style="margin-bottom:25px;">
                    <p><strong>Solicitado por</strong>: '.$dv['soliN'].'</p>
                    <p style="margin-left:5px"><strong>Fecha</strong>: '.$dv['fecha_registro'].'</p>
                </div>
                <div class="colaborador">
                    <h3>Datos del colaborador</h3>
                    <p><strong>Nombre:</strong> '.$dv['colaborador'].'</p>
                    <p><strong>Rut:</strong> '.$dv['rut'].'</p>
                    <p><strong>Observacion:</strong> '.$dv['observacion'].'</p>
                </div>

                <div class="origen">
                    <h3>Datos de Instalacion</h3>
                    <p><strong>Nombre Sucursal:</strong> '.$dv['instalacion'].'</p>
                    <p><strong>Supervisor:</strong> '.$dv['supervisor'].'</p>
                </div>

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
}else{
    echo "No hay desvinculaciones el dia de hoy";
}

?>
