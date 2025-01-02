<?php
    $prioridad = mysqli_query($con, "select * from prioridades ");

    if (isset($_POST['send'])) {
        $email = $_SESSION['login'];
        $userId = $_SESSION['user_id'];
        $subject = $_POST['subject'];
        $tt = $_POST['tasktype'];
        $priority = $_POST['priority'];
        $ticket = $_POST['description'];
        $st = 11; // El estado del ticket
        $pdate = date('Y-m-d'); 
      

        $query = "INSERT INTO ticket (email_id, subject, task_type, prioprity, ticket, status, posting_date, user_id) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        if ($stmt = mysqli_prepare($con, $query)) {
            
            mysqli_stmt_bind_param($stmt, "sssisisi",  $email, $subject, $tt, $priority, $ticket, $st, $pdate, $userId);            
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