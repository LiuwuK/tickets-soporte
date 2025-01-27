<?php

    $prioridad = mysqli_query($con, "select * from prioridades ");



    //obtener tecnicos
    $query = "SELECT * 
                FROM  user
                WHERE cargo = '5'";
    $tecnicos = mysqli_query($con,$query);

    if (isset($_POST['send'])) {
        $email = isset($_SESSION['login']) ? $_SESSION['login'] : $_SESSION['alogin'];
        $userId = $_SESSION['id'];
        $subject = $_POST['subject'];
        $tt = $_POST['tasktype'];
        if($_SESSION['role'] == 'admin'){
            $priority = $_POST['priority'];
            $tecnicoAsg = $_POST['tecnico'];
        } else {
            $priority = null;
        }
        $ticket = $_POST['description'];
        $st = 11; // El estado del ticket
        $pdate = date('Y-m-d'); 

        //Subida de imagen-------------------------------------------------------------------------------------------------------
            // Configuración del directorio de carga
            if ($_SESSION['role'] == 'admin'){
                $uploadDir = '../assets/uploads/';
            }else{
                $uploadDir = 'assets/uploads/';
            }
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Procesar la imagen
            if(isset($_FILES['ticketImage']) && $_FILES['ticketImage']['error'] === UPLOAD_ERR_OK){
                $uploadedFile = $_FILES['ticketImage'];
                $filePath = '';

                $fileName = uniqid('ticket_', true) .'.'. pathinfo($_FILES['ticketImage']['name'], PATHINFO_EXTENSION);
                $targetPath = $uploadDir . $fileName;

                // Mover archivo a la carpeta de destino
                if (move_uploaded_file($uploadedFile['tmp_name'], $targetPath)) {
                    $dir = 'assets/uploads/';
                    $filePath = $dir . $fileName;
                } else {
                    echo "Error al subir la imagen.";
                    exit;
                }
                
            } 
        //-----------------------------------------------------------------------------------------------------------------------

        $query = "INSERT INTO ticket (email_id, subject, task_type, prioprity, ticket, status, posting_date, user_id, ticket_img, tecnico_asignado) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        if ($stmt = mysqli_prepare($con, $query)) {
            
            mysqli_stmt_bind_param($stmt, "sssisisisi",  $email, $subject, $tt, $priority, $ticket, $st, $pdate, $userId, $filePath, $tecnicoAsg);            
            // Ejecutar la consulta
            if (mysqli_stmt_execute($stmt)) {
                
                $ticketId = mysqli_insert_id($con);
                $user = $_SESSION['name'];
                //Envio de notificacion a tiempo real 
                //ticketNoti($ticketId,$userId );
                //Envio de correo
                
                if(Notificaciones::crearTicketMail($ticketId, 'ticket')){
                    //echo "<script>alert('correo enviado Correctamente'); location.replace(document.referrer)</script>";
                } else {
                    //echo "<script>alert('Hubo un error al enviar el correo'); location.replace(document.referrer)</script>";
                }
                echo "<script>alert('Ticket Registrado Correctamente'); location.replace(document.referrer)</script>";
            } else {
                echo "<script>alert('Error al registrar el ticket');</script>";
            }
            
            mysqli_stmt_close($stmt);
        } else {
            echo "<script>alert('Error al preparar la consulta');</script>";
        }
    }
?>