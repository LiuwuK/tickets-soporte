<?php
require_once __DIR__ . '/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
setlocale(LC_TIME, 'es_ES.UTF-8', 'spanish');
include('../dbconnection.php');
//credenciales  USER = CORREO / PASS = CLAVE DE APLICACION GOOGLE 
$user = 'stsafeteck@gmail.com'; // Correo
$pass = 'molc xtfj nfev kruf'; // Contraseña de aplicación

$query = "SELECT
                tr.estado,
                tr.nombre_colaborador,
                tr.rut,
                tr.fecha_inicio_turno,
                tr.fecha_registro,
                us.name AS soliN, -- Nombre del solicitante
                su_origen.nombre AS suOrigen, -- Sucursal de origen
                jo_origen.tipo_jornada AS joOrigen, -- Jornada de origen
                su_destino.nombre AS suDestino, -- Sucursal de destino
                jo_destino.tipo_jornada AS joDestino, -- Jornada de destino
                sup_origen.nombre_supervisor AS supOrigen, -- Supervisor de origen
                sup_destino.nombre_supervisor AS supDestino, -- Supervisor destino
                rol_origen.nombre_rol AS rolOrigen, -- rol origen
                rol_destino.nombre_rol AS rolDestino, -- rol destino
                mo.motivo AS motivoTraslado
            FROM traslados tr
            JOIN user us ON tr.solicitante = us.id
            JOIN sucursales su_origen ON tr.instalacion_origen = su_origen.id
            JOIN sucursales su_destino ON tr.instalacion_destino = su_destino.id
            JOIN jornadas jo_origen ON tr.jornada_origen = jo_origen.id
            JOIN jornadas jo_destino ON tr.jornada_destino = jo_destino.id
            JOIN supervisores sup_origen ON tr.supervisor_origen = sup_origen.id
            JOIN supervisores sup_destino ON tr.supervisor_destino = sup_destino.id
            JOIN roles rol_origen ON tr.rol_origen = rol_origen.id
            JOIN roles rol_destino ON tr.rol_destino = rol_destino.id
            JOIN motivos_gestion mo ON(tr.motivo_traslado = mo.id)
            WHERE tr.fecha_registro BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 DAY) + INTERVAL 16 HOUR 
            AND CURDATE() + INTERVAL 1 DAY + INTERVAL 16 HOUR
            ORDER BY tr.fecha_registro ASC";

$stmt = $con->prepare($query);
$stmt->execute();
$trasladosData = $stmt->get_result();
$traslados = [];
while ($row = mysqli_fetch_assoc($trasladosData)) {
    $traslados[] = $row; 
}

$mail = new PHPMailer(true);
if(!empty($traslados)){
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
        $mail->setFrom('stsafeteck@gmail.com', 'Traslados');
        $destinatarios = [
            'fnorton@gruposafeteck.com' => 'Supervisor',
            'fponce@gruposafeteck.com' => 'Usuario',
            'fsilva@gruposafeteck.com' => 'Usuario',
            'kcarvajal@gruposafeteck.com' => 'Usuario',
            'mserrano@gruposafeteck.com' => 'Usuario',
            'bjaramillo@gruposafeteck.com' => 'Usuario',
            'dvegas@gruposafeteck.com' => 'Usuario',
            'asistencia@gruposafeteck.com' => 'Usuario',
            'mpacheco@gruposafeteck.com' => 'Usuario',
            'MVERGARA@gruposafeteck.com' => 'Usuario',
        ];

        foreach ($destinatarios as $email => $nombre) {
            $mail->addAddress($email, $nombre);
        }
        $mail->addAddress('fnorton@gruposafeteck.com', 'Usuario');  

        $mail->isHTML(true);
        $mail->Subject = 'Traslados del día de hoy';

        $contenido = '
            <!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="UTF-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Traslados</title>
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
                    .body{
                        display: flex;
                        justify-content: space-between;
                    }

                </style>
            </head>
            <body>
            <h2>Traslados del día</h2>';

        foreach ($traslados as $tr) {
            echo "<pre>";
            print_r($tr);
            echo "</pre>"; 
            
            $contenido .= '
            <div class="traslado">
                <div class="soli-data" style="margin-bottom:25px;">
                    <p><strong>Solicitado por</strong>: '.$tr['soliN'].'</p>
                    <p style="margin-left:5px"><strong>Fecha</strong>: '.$tr['fecha_registro'].'</p>
                    <p style="margin-left:5px"><strong>Estado</strong>: '.$tr['estado'].'</p>
                </div>
                <div class="colaborador">
                    <h3>Datos del colaborador</h3>
                    <p><strong>Nombre:</strong> '.$tr['nombre_colaborador'].'</p>
                    <p><strong>Rut:</strong> '.$tr['rut'].'</p>
                </div>
                <div class="body " style="margin-top:10px;">
                    <div class="origen">
                        <h3>Datos Instalacion Origen</h3>
                        <p><strong>Nombre Sucursal:</strong> '.$tr['suOrigen'].'</p>
                        <p><strong>Supervisor:</strong> '.$tr['supOrigen'].'</p>
                        <p><strong>Jornada:</strong> '.$tr['joOrigen'].'</p>
                        <p><strong>Rol Origen:</strong>'.$tr['rolOrigen'].'</p>
                    </div>
                    <div class="destino">
                        <h3>Datos Instalacion Destino</h3>
                        <p><strong>Nombre Sucursal:</strong> '.$tr['suDestino'].'</p>
                        <p><strong>Supervisor:</strong> '.$tr['supDestino'].'</p>
                        <p><strong>Jornada:</strong> '.$tr['joDestino'].'</p>
                         <p><strong>Rol Destino:</strong>'.$tr['rolDestino'].'</p>
                    </div>
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
    echo "No hay traslados el dia de hoy";
}

?>
