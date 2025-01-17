<?php
    $prioridad = mysqli_query($con, "select * from prioridades ");

    if (isset($_POST['send'])) {
        $email = isset($_SESSION['login']) ? $_SESSION['login'] : $_SESSION['alogin'];
        $userId = $_SESSION['id'];
        $subject = $_POST['subject'];
        $tt = $_POST['tasktype'];
        if($_SESSION['role'] == 'admin'){
            $priority = $_POST['priority'];
        } else {
            $priority = null;
        }
        $ticket = $_POST['description'];
        $st = 11; // El estado del ticket
        $pdate = date('Y-m-d'); 
      
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
        
        $query = "INSERT INTO ticket (email_id, subject, task_type, prioprity, ticket, status, posting_date, user_id, ticket_img) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        if ($stmt = mysqli_prepare($con, $query)) {
            
            mysqli_stmt_bind_param($stmt, "sssisisis",  $email, $subject, $tt, $priority, $ticket, $st, $pdate, $userId, $filePath);            
            // Ejecutar la consulta
            if (mysqli_stmt_execute($stmt)) {
                
                $ticketId = mysqli_insert_id($con);
                echo "<script>alert('Ticket Registrado Correctamente'); location.replace(document.referrer)</script>";
                //envio de notificacion a tiempo real 
                ticketNoti($ticketId,$userId );
            } else {
                echo "<script>alert('Error al registrar el ticket');</script>";
            }
            
            mysqli_stmt_close($stmt);
        } else {
            echo "<script>alert('Error al preparar la consulta');</script>";
        }
    }
?>