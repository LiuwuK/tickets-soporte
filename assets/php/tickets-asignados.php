<?php 
    include("dbconnection.php");
    include("admin/phpmail.php");
    include("admin/notificaciones.php");

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
//----------------------------------------------------------------------------------------------------------

//Actualizar estado del ticket------------------------------------------------------------------------------
//adminremark = mensaje del administrador
//fid = id del ticket

if (isset($_POST['update'])) {
  $adminremark = $_POST['aremark'];
  $fid = $_POST['frm_id'];
  $userId = $_POST['userId'];
  $filePath = $_POST['filePath'];
  $ticketStatus = "En revisión";

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
  
  //actualizar el estado del ticket
  $queryUpdt = "UPDATE ticket SET admin_remark='$adminremark', status = 10, ticket_img = '$filePath' WHERE id='$fid'";
  mysqli_query($con, $queryUpdt);


  $stmtCliente->bind_param("i", $fid);
  $stmtCliente->execute();
  $stmtCliente->bind_result($clienteEmail);
  $stmtCliente->fetch();
 
  if ($clienteEmail) {
      //  Enviar la notificación por correo  
      Notificaciones::enviarCorreo($clienteEmail, $fid,null, $adminremark ,$ticketStatus, null);
  }
  $stmtCliente->close();
  echo '<script>alert("Ticket actualizado correctamente "); location.replace(document.referrer);</script>';
  //enviar notificacion al usuario
  //updateNoti($fid, $userId);
}
//Cerrar ticket -------------------------------------------------------------------------------------------------------------
else if (isset($_POST['end'])) {
  $adminremark = $_POST['aremark'];
  $fid = $_POST['frm_id'];
  $ticketStatus = "Pendiente de Cierre";
  $userId = $_POST['userId'];
  $filePath = $_POST['filePath'];

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
  $queryUpdt = "UPDATE ticket 
                  SET admin_remark='$adminremark', 
                      status= 22,
                      ticket_img = '$filePath'
                WHERE id='$fid'";
  mysqli_query($con, $queryUpdt);

  
    $stmtCliente->bind_param("i", $fid);
    $stmtCliente->execute();
    $stmtCliente->bind_result($clienteEmail);
    $stmtCliente->fetch();
  
    if ($clienteEmail) {
        //  Enviar la notificación por correo
        echo '<script>alert("Ticket cerrado correctamente "); location.replace(document.referrer);</script>'; 
        Notificaciones::enviarCorreo($clienteEmail, $fid,null, $adminremark ,$ticketStatus, null);
    }

    //funcion para enviar notificaciones a tiempo real y almacenarlas en la db
    //updateNoti($fid, $userId);
    $stmtCliente->close();
}
//----------------------------------------------------------------------------------------------------------

//carga las prioridades y estados de ticket para filtrar--------------------------------------------------------------
$query_prio = "SELECT * FROM prioridades ";
$prioData = mysqli_query($con, $query_prio);

while ($row = mysqli_fetch_assoc($prioData)) {
  $prioridades[] = $row; 
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
$query = "SELECT ti.id AS ticketId, 
                 pr.id AS prioridadId,
                 st.nombre AS statusN,
                 ti.*, pr.*
          FROM ticket ti 
          LEFT JOIN prioridades pr ON ti.prioprity = pr.id
          JOIN estados st ON ti.status = st.id
          WHERE tecnico_asignado  = ".$_SESSION['user_id']."";
//FILTROS
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
//----------------------------------------

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
//----------------------------------------------------------------------------------------------------------
?>
