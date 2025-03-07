<?php
    $prioridad = mysqli_query($con, "select * from prioridades ");
    $deptos = mysqli_query($con, "select * from departamentos_usuarios where tipo = 'departamento'");
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
            // Procesar la imagen
            if(isset($_FILES['ticketImage']) && $_FILES['ticketImage']['error'] === UPLOAD_ERR_OK){
                // Configuración de Supabase
                $supabase_url = 'https://zessdkphohirwcsbqnif.supabase.co';
                $supabase_key = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Inplc3Nka3Bob2hpcndjc2JxbmlmIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NDAwNjMxOTMsImV4cCI6MjA1NTYzOTE5M30.iTUsOH7OxO49h62FQCmXV05-DZKUwQ1RFLGdC_FEEWE';
                $bucket_name = "safeteck.uploads";

                // Verifica si se subió correctamente el archivo
                if (!isset($_FILES["ticketImage"]) || $_FILES["ticketImage"]["error"] != UPLOAD_ERR_OK) {
                    die("Error al subir el archivo.");
                }

                $file_path = $_FILES["ticketImage"]["tmp_name"];
                $file_name = str_replace(" ", "_", $_FILES["ticketImage"]["name"]); // Evitar espacios en el nombre
                $file_type = $_FILES["ticketImage"]["type"];

                // URL correcta para subir archivos a Supabase Storage
                $url = "$supabase_url/storage/v1/object/$bucket_name/$file_name";

                // Inicializar cURL
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT"); // Supabase usa PUT para subir archivos
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    "Authorization: Bearer $supabase_key",
                    "Content-Type: $file_type",
                    "x-upsert: true" // Permite sobreescribir archivos con el mismo nombre
                ]);
                curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents($file_path)); // Cargar el archivo
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                $response = curl_exec($ch);
                $error = curl_error($ch);
                curl_close($ch);

                // Manejo de respuesta y errores
                if ($response === false) {
                    echo "Error en cURL: $error";
                } else {
                    $filePath = $url;
                    echo "✅ Archivo subido correctamente: ";
                }    
            } 
        //-----------------------------------------------------------------------------------------------------------------------

        $query = "INSERT INTO ticket (email_id, subject, task_type, prioprity, ticket, status, posting_date, user_id, ticket_img, usuario_asignado) 
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
                if(Notificaciones::crearTicketMail($ticketId, 'ticket', $user, $tt)){
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