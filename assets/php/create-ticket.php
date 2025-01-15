<?php
    $prioridad = mysqli_query($con, "select * from prioridades ");

    if (isset($_POST['send'])) {
        $email = isset($_SESSION['login']) ? $_SESSION['login'] : $_SESSION['alogin'];
        $userId = $_SESSION['id'];
        $subject = $_POST['subject'];
        $tt = $_POST['tasktype'];
        $priority = $_POST['priority'];
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
        $uploadedFile = $_FILES['ticketImage'];
        $filePath = '';

        if ($uploadedFile['error'] === UPLOAD_ERR_OK) {
            $fileName = uniqid('ticket_', true) . '.' . pathinfo($_FILES['ticketImage']['name'], PATHINFO_EXTENSION);
            $targetPath = $uploadDir . $fileName;

            // Mover archivo a la carpeta de destino
            if (move_uploaded_file($uploadedFile['tmp_name'], $targetPath)) {
                $filePath = $targetPath;
            } else {
                echo "Error al subir la imagen.";
                exit;
            }
        } else {
            echo "Error en la carga de la imagen.";
            exit;
        }
        
        $query = "INSERT INTO ticket (email_id, subject, task_type, prioprity, ticket, status, posting_date, user_id, ticket_img) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        if ($stmt = mysqli_prepare($con, $query)) {
            
            mysqli_stmt_bind_param($stmt, "sssisisis",  $email, $subject, $tt, $priority, $ticket, $st, $pdate, $userId, $filePath);            
            // Ejecutar la consulta
            if (mysqli_stmt_execute($stmt)) {
                
                $ticketId = mysqli_insert_id($con);
                echo "<script>alert('Ticket Registrado Correctamente'); location.replace(document.referrer)</script>";
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