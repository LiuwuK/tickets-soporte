<?php 

$cargo = $_SESSION['cargo'];
$depto = $_SESSION['deptos'];
if (!empty($depto)) {
  $deptoList = implode(',', array_map('intval', $depto)); 
}

//Obtener el email del usuario para enviar notificaciones 
$queryCliente = "SELECT email_id FROM ticket WHERE id = ?";
$stmtCliente = $con->prepare($queryCliente);
//---------------------------------------------------------------------------------------------------------
//Agregar tareas a un ticket--------------------------------------------------------------------------------
if (isset($_POST["addtsk"])) {
  $tId = $_POST['tk_id'];
  $userId = $_POST['user_id'];
  $titles = $_POST['title'];
  $ticketStatus = "En revisión";
//actualizar el estado del ticket cuando se le asignen tareas
  $queryUpdt = "UPDATE ticket 
                SET status= 10 
                WHERE id='$tId'";
  mysqli_query($con, $queryUpdt);

  if (!empty($titles)) {    
      $values = [];
      foreach ($titles as $title) {
          $title = trim($title);
          if (!empty($title)) {
              $title = mysqli_real_escape_string($con, $title);
              $values[] = "('$tId', '$title')";
          }
      }
      if (!empty($values)) {
          //consulta para insertar múltiples tareas de una vez
          $query = "INSERT INTO tasks (ticket_id, titulo) VALUES " . implode(", ", $values);
          if (mysqli_query($con, $query)) {
           echo '<script>alert("Tareas asignadas correctamente "); location.replace(document.referrer);</script>';
            //Enviar notificacion
            $stmtCliente->bind_param("i", $tId);
            $stmtCliente->execute();
            $stmtCliente->bind_result($clienteEmail);
            $stmtCliente->fetch();

            if ($clienteEmail) {
                //  Enviar la notificación por correo
                Notificaciones::enviarCorreo($clienteEmail, $tId,$titles, null,$ticketStatus, null);
            }
          } else {
              echo "Error al insertar tareas: " . mysqli_error($con);
          }
      }
  }

  $stmtCliente->close();
  updateNoti($tId, $userId);
}
//Eliminar tareas-------------------------------------------------------------------------------------------
elseif (isset($_POST["deltsk"])) {
  $tId = $_POST['tk_id'];
  $query = "DELETE FROM `tasks` WHERE id = ?";
  $stmt = $con->prepare($query);
  $stmt->bind_param("i",$tId); 
  $stmt->execute();
}
//Si se cierra o actualiza un ticket----------------------------------------------------------------------------------------
if (isset($_POST["end"]) OR isset($_POST["update"])){
  if (!empty($_FILES['files']['name'][0])) {
    $ticket_id = intval($_POST['frm_id']); 

    // Configuración de Supabase
    $supabase_url = 'https://zessdkphohirwcsbqnif.supabase.co';
    $supabase_key = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Inplc3Nka3Bob2hpcndjc2JxbmlmIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NDAwNjMxOTMsImV4cCI6MjA1NTYzOTE5M30.iTUsOH7OxO49h62FQCmXV05-DZKUwQ1RFLGdC_FEEWE';
    $bucket_name = "safeteck.uploads";

    if (!isset($_FILES['files']) || count($_FILES['files']['tmp_name']) == 0) {
        die("No se seleccionaron archivos.");
    }

    foreach ($_FILES['files']['tmp_name'] as $key => $tmp_name) {
        if ($_FILES['files']['error'][$key] !== UPLOAD_ERR_OK) {
            echo "Error al subir el archivo: " . $_FILES['files']['name'][$key];
            continue;
        }

        $file_path = $_FILES['files']['tmp_name'][$key];
        $file_name = str_replace(" ", "_", $_FILES['files']['name'][$key]); 
        $file_type = $_FILES['files']['type'][$key];

        $url = "$supabase_url/storage/v1/object/$bucket_name/$file_name";

        // Inicializar cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT"); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $supabase_key",
            "Content-Type: application/octet-stream",
            "x-upsert: true"
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents($file_path));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        // Verificar respuesta de Supabase
        if ($response === false) {
            echo "Error en cURL al subir {$file_name}: $error<br>";
            continue;
        }

        // Guardar la URL pública en la BD
        $fileUrl = "$supabase_url/storage/v1/object/public/$bucket_name/$file_name";
        $stmt = $con->prepare("INSERT INTO ticket_archivos (ticket_id, archivo) VALUES (?, ?)");
        $stmt->bind_param("is", $ticket_id, $fileUrl);
        $stmt->execute();

        echo "✅ Archivo subido correctamente: <a href='$fileUrl' target='_blank'>$file_name</a><br>";
    }
  }
}

//Actualizar estado del ticket------------------------------------------------------------------------------
//adminremark = mensaje del administrador
//fid = id del ticket

if (isset($_POST['update'])) {
  $adminremark = $_POST['aremark'];
  $fid = $_POST['frm_id'];
  $userId = $_POST['userId'];

  $ticketStatus = "En revisión";
  
  //actualizar el estado del ticket
  $queryUpdt = "UPDATE ticket SET admin_remark='$adminremark', status = 10 WHERE id='$fid'";
  mysqli_query($con, $queryUpdt);
 
  //actualizar el estado de las tareas
  if($_POST["tasks"]){
    $task  = $_POST["tasks"];
    $taskStatus = [];
    foreach ($task as $tskid => $taskData){
      $newStatus = $taskData['newstatus'];
      $title = $taskData['titulo'];
      $currentStatus = $taskData ['oldstatus'];

      //actualizar solo si el estado cambia 
      if ((int)$currentStatus !== (int)$newStatus){
        $query = "UPDATE tasks SET estado_id = ? WHERE id = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("ii", $newStatus, $tskid); 
        $stmt->execute();
        $stmt->close();
        
        $query = "SELECT nombre 
                  FROM estados
                  WHERE id = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("i", $newStatus);
        $stmt->execute();
        $stmt->bind_result($statusName);
        $stmt->fetch();
        $stmt->close();
        
        $taskStatus[] = $title." -- Estado: ".$statusName;
      }
    }
  }


  $stmtCliente->bind_param("i", $fid);
  $stmtCliente->execute();
  $stmtCliente->bind_result($clienteEmail);
  $stmtCliente->fetch();
 
  if ($clienteEmail) {
      //  Enviar la notificación por correo  
      Notificaciones::enviarCorreo($clienteEmail, $fid,null, $adminremark ,$ticketStatus, $taskStatus);
  }
  $stmtCliente->close();
  echo '<script>alert("Ticket actualizado correctamente "); location.replace(document.referrer);</script>';
  //enviar notificacion al usuario
  updateNoti($fid, $userId);
}
//Cerrar ticket -------------------------------------------------------------------------------------------------------------
else if (isset($_POST['end'])) {
  $adminremark = $_POST['aremark'];
  $fid = $_POST['frm_id'];
  $ticketStatus = "Cerrado";
  $userId = $_POST['userId'];
  
  $queryUpdt = "UPDATE ticket SET admin_remark='$adminremark', status= 12 where id='$fid'";
  mysqli_query($con, $queryUpdt);

    //actualizar el estado de las tareas
        if($_POST["tasks"]){
        $task  = $_POST["tasks"];
        $taskStatus = [];
      foreach ($task as $tskid => $taskData){
        $newStatus = $taskData['newstatus'];
        $title = $taskData['titulo'];
        $currentStatus = $taskData ['oldstatus'];
  
        //actualizar solo si el estado cambia 
        if ((int)$currentStatus !== (int)$newStatus){
          $query = "UPDATE tasks SET estado_id = ? WHERE id = ?";
          $stmt = $con->prepare($query);
          $stmt->bind_param("ii", $newStatus, $tskid); 
          $stmt->execute();
          $stmt->close();
          
          $query = "SELECT nombre 
                    FROM estados
                    WHERE id = ?";
          $stmt = $con->prepare($query);
          $stmt->bind_param("i", $newStatus);
          $stmt->execute();
          $stmt->bind_result($statusName);
          $stmt->fetch();
          $stmt->close();
          
          $taskStatus[] = $title." -- Estado: ".$statusName;
        }
      }
    }
  
    $stmtCliente->bind_param("i", $fid);
    $stmtCliente->execute();
    $stmtCliente->bind_result($clienteEmail);
    $stmtCliente->fetch();
  
    if ($clienteEmail) {
        //  Enviar la notificación por correo
        echo '<script>alert("Ticket finalizado correctamente "); location.replace(document.referrer);</script>'; 
        Notificaciones::enviarCorreo($clienteEmail, $fid,null, $adminremark ,$ticketStatus, $taskStatus);
    }

    //funcion para enviar notificaciones a tiempo real y almacenarlas en la db
    updateNoti($fid, $userId);
    $stmtCliente->close();
}
//----------------------------------------------------------------------------------------------------------

//carga las prioridades y estados de ticket para filtrar--------------------------------------------------------------
$query_prio = "SELECT * FROM prioridades ";
$prioData = mysqli_query($con, $query_prio);
while ($row = mysqli_fetch_assoc($prioData)) {
  $prioridades[] = $row; 
}
//obtener usuarios
if (!empty($depto)) {
  $query_user = "SELECT DISTINCT u.*
                    FROM user u
                    INNER JOIN usuario_departamento du ON u.id = du.usuario_id
                    WHERE du.departamento_id IN ($deptoList)";
  $userData = mysqli_query($con, $query_user);
  while ($row = mysqli_fetch_assoc($userData)) {
    $usuarios[] = $row; 
  }
} else {
  // Si el usuario no tiene departamentos, la consulta no devuelve resultados
  $query_user = "SELECT * FROM user WHERE 0"; 
}

$query_st = "SELECT * FROM estados WHERE type = 'ticket'";
$statusF = mysqli_query($con, $query_st);

//----------------------------------------------------------------------------------------------------------

//Carga de tickets -----------------------------------------------------------------------------------------
//La query cambia dependiendo si se filtra por prioridad y/o estado (FILTROS)
$priority_id = isset($_GET['priority']) ? intval($_GET['priority']) : '';
$status_id = isset($_GET['statusF']) ? intval($_GET['statusF']) : '';
$searchText = isset($_GET['textSearch']) ? trim($_GET['textSearch']) : '';
//----------------------------------------------------------------------------------------------------------
$uid = $_SESSION['id'];
if (!empty($depto)) {
  $query = "SELECT ti.id AS ticketId, 
                  pr.id AS prioridadId,
                  st.nombre AS statusN,
                  ti.*, pr.*, pr.nombre AS prioN,
                  us.name AS userN
            FROM ticket ti 
            LEFT JOIN prioridades pr ON ti.prioprity = pr.id
            LEFT JOIN user us ON(us.id = ti.user_id)
            JOIN estados st ON ti.status = st.id
           ";

  if($_SESSION['role'] != "supervisor"){
      $query .= " WHERE usuario_asignado = $uid ";
  }else{
    $query .= "  WHERE ti.task_type IN ($deptoList) ";
  }
  // Filtros dinámicos
  $conditions = [];
  $params = [];
  $types = '';

  // Filtrar por prioridad
  if (!empty($priority_id)) {
      $conditions[] = "ti.prioprity = ?";
      $params[] = $priority_id;
      $types .= 'i';
  }

  // Filtrar por estado
  if (!empty($status_id)) {
      $conditions[] = "ti.status = ?";
      $params[] = $status_id;
      $types .= 'i';
  }else{
    $conditions[] = "ti.status != ?";
    $params[] = 12;
    $types .= 'i';
  }

  // Filtrar por texto (nombre del ticket o ID)
  if (!empty($searchText)) {
      $conditions[] = "(ti.id LIKE ? OR ti.subject LIKE ?)";
      $searchWildcard = '%' . $searchText . '%';
      $params[] = $searchWildcard;
      $params[] = $searchWildcard;
      $types .= 'ss';
  }

  // Combinar las condiciones
  if (!empty($conditions)) {
    $query .= ' AND ' . implode(' AND ', $conditions);
  }

  $stmt = $con->prepare($query);
  if (!empty($params)) {
      $stmt->bind_param($types, ...$params);
  }
  $stmt->execute();
  $rt = $stmt->get_result(); 
  if (!$rt) {
      die("Error en la consulta: " . mysqli_error($con));
  }
  $num = $rt->num_rows; 
}else{
  $query = "SELECT * FROM ticket WHERE 0";
  $stmt = $con->prepare($query);
  $stmt->execute();
  $rt = $stmt->get_result(); 
  $num = $rt->num_rows; 
}
//----------------------------------------------------------------------------------------------------------
//Obtener todos los estados de las task --------------------------------------------------------------------

$query = "SELECT * FROM estados WHERE type = 'task'";
$status = mysqli_query($con, $query);

$estados = [];
while ($estado = $status->fetch_assoc()) {
    $estados[] = $estado;
}
//----------------------------------------------------------------------------------------------------------
//asignar prioridad
if (isset($_POST["asignarPrio"])) {
  $prioId =  $_POST['prioridad'];
  $tID    =  $_POST['tId'];

  $query =  " UPDATE ticket
              SET prioprity = ?
              WHERE id = ?";
  $stmt = $con->prepare($query);
  $stmt->bind_param("ii",$prioId, $tID);

  if ($stmt->execute()) {
      echo "<script>alert('Prioridad asignada correctamente');location.replace(document.referrer)</script>";
  } else {
      echo "<script>alert('error');location.replace(document.referrer)</script>";
  }

  $stmt->close();
}
//asignar usuario
if (isset($_POST["asignarUser"])) {
  $userID =  $_POST['userasig'];
  $tID    =  $_POST['tId'];

  $query =  " UPDATE ticket
              SET usuario_asignado = ?
              WHERE id = ?";
  $stmt = $con->prepare($query);
  $stmt->bind_param("ii",$userID, $tID);

  if ($stmt->execute()) {
      Notificaciones::asignarUsuario($tID,$userID);
      echo "<script>alert('Usuario asignado correctamente');location.replace(document.referrer)</script>";
  } else {
      echo "<script>alert('error');location.replace(document.referrer)</script>";
  }

  $stmt->close();
}
?>
