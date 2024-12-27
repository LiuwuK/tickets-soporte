<?php
ini_set('log_errors', 'On'); 
ini_set('error_log', 'C:/xampp/php/logs/php_error_log'); 


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

class Notificaciones {
    public static function enviarCorreo($destinatario, $tId, $tareas = null, $comentario = null, $estadoTicket = null, $tasksStatus = null) {
        $mail = new PHPMailer(true);
        $asunto = "Actualización de su Ticket ";
        //credenciales (cambiar)
        $user = 'kevinantecao1206@gmail.com';
        $pass = '';
        
        try {
            // Configuración SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $user; 
            $mail->Password = $pass; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
        
            // Configuración del remitente y destinatario
            $mail->setFrom('kevinantecao1206@gmail.com', 'Soporte');
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
                                        background-color: #4CAF50;
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
                                        background-color: #4CAF50;
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
}
