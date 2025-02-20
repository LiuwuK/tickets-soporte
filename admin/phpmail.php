<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('log_errors', 'On'); 
ini_set('error_log', 'C:/xampp/php/logs/php_error_log'); 


error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//credenciales (cambiar) USER = CORREO / PASS = CLAVE DE APLICACION GOOGLE 
$userMail = 'stsafeteck@gmail.com'; // correo
$pass = 'molc xtfj nfev kruf'; // Contraseña de aplicación
$tId  = '$tId'; 
$uid  = '$uid';
//http://186.67.95.90:8083
$projectUrl = 'http://192.168.100.177/tickets-soporte/admin/projects/view-projects.php?textSearch=$tId';    
$ticketUrl  = '$ticketUrl';
//Body para la funcion CreateTicketMail (Nuevo ticket)
$bodyNewTicket = "<body>
                    <table class='email-container' width='100%' cellspacing='0' cellpadding='0' role='presentation'>
                        <tr>
                            <td class='email-header'>
                                <h1>Nuevo Ticket Creado #$tId</h1>
                            </td>
                        </tr>
                        <tr>
                            <td class='email-body'>
                                <p>
                                    El usuario $uid ha creado un nuevo ticket en el sistema con el ID <strong>#$tId</strong>.
                                    Puedes revisarlo y realizar las acciones necesarias.
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td class='btn-div' >
                                <a href='$ticketUrl'>
                                <button class='button'>Ver Ticket</button>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td class='footer'>
                                <p>Este es un correo automatizado, por favor no respondas.</p>
                            </td>
                        </tr>
                    </table>
                </body>
                </html>";

//Body para la funcion CreateTicketMail (Nuevo Proyecto)
$bodyNewProject = "
                <body>
                    <table class='email-container' width='100%' cellspacing='0' cellpadding='0' role='presentation'>
                        <tr>
                            <td class='email-header'>
                                <h1>Nuevo Proyecto Creado #$tId</h1>
                            </td>
                        </tr>
                        <tr>
                            <td class='email-body'>
                                <p>
                                    El usuario $uid ha creado un nuevo proyecto en el sistema con el ID <strong>#$tId</strong>.
                                    Puedes revisarlo y realizar las acciones necesarias.
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td class='btn-div' >
                                <a href='$projectUrl'>
                                    <button class='button' >Ver Proyecto</button>
                                </a>   
                            </td>
                        </tr>
                        <tr>
                            <td class='footer'>

                                <p>Este es un correo automatizado, por favor no respondas.</p>
                            </td>
                        </tr>
                    </table>
                </body>
                </html>";


class Notificaciones {
    //Envio de correo cuando se actualiza un ticket
    public static function enviarCorreo($destinatario, $tId, $tareas = null, $comentario = null, $estadoTicket = null, $tasksStatus = null) {
        $mail = new PHPMailer(true);
        $asunto = "Actualización de su Ticket ";
        global $userMail, $pass;
        
        try {
            // Configuración SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $userMail; 
            $mail->Password = $pass; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
        
            // Configuración del remitente y destinatario
            $mail->setFrom('stsafeteck@gmail.com', 'Soporte');
            $mail->addAddress($destinatario);
            $mail->CharSet = 'UTF-8';

            // Contenido del correo
            $mail->isHTML(true);
            $mail->Subject = $asunto;
            $mail->Body = " <html>
                            <head>
                                <style>
                                    body {
                                        font-family: Arial, sans-serif;
                                        background-color: #f4f4f9;
                                        margin: 0;
                                        padding: 20px;
                                    }
                                    .email-container {
                                        background-color: #ffffff;
                                        border-radius: 8px;
                                        padding: 20px;
                                        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                                        max-width: 600px;
                                        margin: 0 auto;
                                    }
                                    .email-header {
                                        background-color: #33435e;
                                        color: #ffffff;
                                        padding: 10px;
                                        border-radius: 8px 8px 0 0;
                                        text-align: center;
                                    }
                                    .email-body {
                                        text-align: justify;
                                        margin-top: 20px;
                                        line-height: 1.6;
                                    }
                                    .footer {
                                        margin-top: 30px;
                                        font-size: 12px;
                                        color: #777;
                                        text-align: center;
                                    }
                                    .btn-div{
                                        text-align:center;
                                    }
                                    .button {
                                        background-color: #33435e;
                                        color: white;
                                        padding: 10px 20px;
                                        text-decoration: none;
                                        border-radius: 5px;
                                        display: inline-block;
                                        margin-top: 20px;
                                    }
                                </style>
                            </head>
                            <body>
                                <div class='email-container'>
                                    <div class='email-header'>
                                        <h1>Actualización de su Ticket #$tId</h1>
                                    </div>
                                    <div class='email-body'>
                                        <p>Estimado cliente,</p>
                                        <p>
                                            Te informamos que el ticket <strong>#$tId</strong> ha sido actualizado.
                                            A continuación, te mostramos los detalles de la actualización:
                                        </p>
                                        <p></p>";
                                        if ($estadoTicket) {
                                            $mail->Body .= "
                                                <p><strong>Estado del ticket: </strong>".htmlspecialchars($estadoTicket)."</p>
                                            ";
                                        }
                                        if (!empty($tareas) && is_array($tareas)) {
                                            $mail->Body .= "<p><strong>Tareas asignadas:</strong></p><ul>";
                                            foreach ($tareas as $tarea) {
                                                $mail->Body .= "<li>" . htmlspecialchars($tarea) . "</li>";
                                            }
                                            $mail->Body .= "</ul>";
                                        }
                                        if (!empty($tasksStatus) && is_array($tasksStatus)) {
                                            $mail->Body .= "<p><strong>Tareas actualizadas:</strong></p><ul>";
                                            foreach ($tasksStatus as $tasks) {
                                                $mail->Body .= "<li>" .htmlspecialchars($tasks)."</li>";
                                            }
                                            $mail->Body .= "</ul>";
                                        }
                                        if ($comentario) {
                                            $mail->Body .= " 
                                                <p><strong>Comentario:</strong> ".htmlspecialchars($comentario)."</p>";
                                        }
                        $mail->Body .= "<p>Para más detalles, puedes acceder a tu cuenta en el sistema de soporte.</p>
                                        <div class='btn-div'>
                                            <a href='http://localhost/tickets-soporte/view-tickets.php?textSearch=$tId' class='button'>Ver Ticket</a>
                                        </div>
                                    </div>
                                    <div class='footer'>
                                        <p>Este es un correo automatizado, por favor no respondas.</p>
                                    </div>
                                </div>
                            </body>
                            </html>";
            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Error al enviar correo: " . $mail->ErrorInfo);
            return false;
        }
    }
    //Envio de correo cuando se crea un ticket/proyecto
    public static function crearTicketMail($tId, $type, $uid, $depto = null) {
        global $userMail, $pass, $bodyNewProject, $bodyNewTicket, $ticketUrl;
        global $con; 

        $destinatarios = []; 
        $roles = [];
        $mail = new PHPMailer(true);
        $mail->SMTPDebug = 2;
        $mail->Debugoutput = 'error_log';  
    
        if($type == 'ticket'){
            $asunto = "Nuevo Ticket Creado #$tId";  
        }
        else if ($type == 'project') {
            $asunto = "Nuevo Proyecto Creado #$tId";
            $body = str_replace(['$tId', '$uid'], [$tId, $uid], $bodyNewProject);
        } else {
            return false;
        }
    
        try {
            $query = "SELECT email, rol
                        FROM user 
                        WHERE rol = 'admin'";
            $result = $con->query($query);
    
            while ($row = $result->fetch_assoc()) {
                $destinatarios[] = $row['email'];
                $roles[$row['email']] = $row['rol'];
            }
    
            // Si es ticket, también obtener supervisores del departamento
            if ($type == 'ticket' && $depto !== null) {
                $query = "SELECT us.email, us.rol
                          FROM user us 
                          JOIN usuario_departamento ud ON us.id = ud.usuario_id
                          WHERE us.rol = 'supervisor' AND ud.departamento_id = ?";
                
                $stmt = $con->prepare($query);
                $stmt->bind_param("i", $depto);
                $stmt->execute();
                $result = $stmt->get_result();
            
                while ($row = $result->fetch_assoc()) {
                    $destinatarios[] = $row['email'];
                    $roles[$row['email']] = $row['rol'];
                }
            }
    
            // Verificar si hay destinatarios
            if (count($destinatarios) > 0) { 
                // Configuración SMTP
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = $userMail; 
                $mail->Password = $pass; 
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                $mail->SMTPOptions = [
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    ]
                ];
    
                // Enviar a cada destinatario
                foreach ($destinatarios as $destinatario) {

                    if($type == 'ticket'){ 
                        if($roles[$destinatario] == 'supervisor'){
                            $ticketUrl  = 'http://186.67.95.90:8083/tickets-soporte/tickets/manage-tickets.php?textSearch='.$tId;
                        }else{
                            $ticketUrl  = 'http://186.67.95.90:8083/tickets-soporte/admin/tickets/manage-tickets.php?textSearch='.$tId;
                        }
                        $body = str_replace(['$tId', '$uid', '$ticketUrl'], [$tId, $uid, $ticketUrl], $bodyNewTicket);
                    }
                    
                    $mail->clearAddresses(); 
                    $mail->setFrom('stsafeteck@gmail.com', 'Soporte');
                    $mail->addAddress($destinatario);
    
                    // Contenido del correo
                    $mail->isHTML(true);
                    $mail->Subject = $asunto;
                    $mail->Body = "
                        <html>
                        <head>
                        <style>
                            body {
                                font-family: Arial, sans-serif;
                                background-color: #f4f4f9;
                                margin: 0;
                                padding: 20px;
                            }
                            .email-container {
                                min-height: 500px;
                                background-color: #ffffff;
                                border-radius: 8px;
                                padding: 20px;
                                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                                max-width: 600px;
                                margin: 0 auto;
                            }
                            .email-header {
                                background-color: #33435e;
                                color: #ffffff;
                                border-radius: 8px 8px 0 0;
                                text-align: center;
                                padding: 10px;
                            }
                            .email-body {
                                text-align: justify;
                                margin-top: 20px;
                                line-height: 1.6;
                            }
                            .footer {
                                margin-top: 30px;
                                font-size: 12px;
                                color: #777;
                                text-align: center;
                            }
                            .btn-div {
                                text-align: center;
                                margin-top: 20px;
                            }
                            .button {
                                background-color: #33435e;
                                color: white;
                                padding: 10px 20px;
                                text-decoration: none;
                                border-radius: 5px;
                                display: inline-block;
                                margin-top: 20px;
                            }
                        </style>
                        </head>
                        <body>
                            <div class='email-container'>
                                <div class='email-header'>
                                    <h2>$asunto</h2>
                                </div>
                                <div class='email-body'>
                                    $body
                                </div>
                                <div class='footer'>
                                    Este es un mensaje automático, por favor no responder.
                                </div>
                            </div>
                        </body>
                        </html>
                    ";
    
                    // Envía el correo
                    $mail->send();
                }
                return true;
            } else {
                error_log("No hay destinatarios para enviar el correo.");
                return false;
            }
        } catch (Exception $e) {
            error_log("Error al enviar correo: " . $mail->ErrorInfo);
            return false;
        }
    }
    

    public static function asignarUsuario($tid, $uid) {
        global $userMail, $pass, $bodyNewProject, $bodyNewTicket;

        $mail = new PHPMailer(true);
        $mail->SMTPDebug = 2;
        $mail->Debugoutput = 'error_log';  
        
        try {
            // Consulta para obtener los correos de todos los administradores
            global $con; 
            $query = "SELECT email, rol
                        FROM user 
                        WHERE id = $uid";
            $result = $con->query($query);
            if ($result->num_rows > 0) { 
                // Configuración SMTP
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = $userMail; 
                $mail->Password = $pass; 
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                $mail->SMTPOptions = array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    )
                );

                while ($row = $result->fetch_assoc()) {
                    $destinatario = $row['email'];
                    $type = $row['rol'];
                    
                    if( $type == 'admin'){
                        $url = 'http://192.168.100.177/tickets-soporte/admin/tickets/manage-tickets.php?textSearch='.$tid;
                    }else{
                        $url = 'http://192.168.100.177/tickets-soporte/tickets/manage-tickets.php?textSearch='.$tid;
                    }

                    $mail->clearAddresses();
                    $mail->setFrom('stsafeteck@gmail.com', 'Soporte');
                    $mail->addAddress($destinatario);

                    // Contenido del correo
                    $mail->isHTML(true);
                    $mail->Subject = 'Nuevo ticket asignado';
                    $mail->Body = "
                        <html>
                        <head>
                        <style>
                            body {
                                font-family: Arial, sans-serif;
                                background-color: #f4f4f9;
                                margin: 0;
                                padding: 20px;
                            }
                            .email-container {
                                min-height: 500px;
                                background-color: #ffffff;
                                border-radius: 8px;
                                padding: 20px;
                                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                                max-width: 600px;
                                margin: 0 auto;
                            }
                            .email-header {
                                background-color: #33435e;
                                color: #ffffff;
                                border-radius: 8px 8px 0 0;
                                text-align: center;
                            }
                            .email-body {
                                text-align: justify;
                                margin-top: 20px;
                                line-height: 1.6;
                            }
                            .footer {
                                margin-top: 30px;
                                font-size: 12px;
                                color: #777;
                                text-align: center;
                            }
                            .btn-div {
                                text-align: center;
                                margin-top: 20px;
                            }
                            .button {
                                background-color: #33435e;
                                color: white;
                                padding: 10px 20px;
                                text-decoration: none;
                                border-radius: 5px;
                                display: inline-block;
                                margin-top: 20px;
                            }
                        </style>
                        </head>
                        <body>
                            <table class='email-container' width='100%' cellspacing='0' cellpadding='0' role='presentation'>
                                <tr>
                                    <td class='email-header'>
                                        <h1>Nuevo Ticket Asignado #$tid</h1>
                                    </td>
                                </tr>
                                <tr>
                                    <td class='email-body'>
                                        <p>
                                            Se le ha asignado el Ticket <strong>#$tid</strong>.
                                            Puedes revisarlo y realizar las acciones necesarias.
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td class='btn-div' >
                                        <a href='$url'>
                                            <button class='button' >Ver Ticket</button>
                                        </a>   
                                    </td>
                                </tr>
                                <tr>
                                    <td class='footer'>
                                        <p>Este es un correo automatizado, por favor no respondas.</p>
                                    </td>
                                </tr>
                            </table>
                        </body>
                        </html>";
                    // Envía el correo
                    $mail->send();
                }
                return true;
            } else {
                error_log("No hay usuarios para enviar el correo.");
                return false;
            }
        } catch (Exception $e) {
            error_log("Error al enviar correo: " . $mail->ErrorInfo);
            return false;
        }
    }
}
