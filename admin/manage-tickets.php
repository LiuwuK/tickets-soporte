<?php
session_start();
//echo $_SESSION['id'];
//$_SESSION['msg'];
include("dbconnection.php");
include("checklogin.php");
include("phpmail.php");

check_login();

//Obtener el email del usuario para enviar notificaciones 

$queryCliente = "SELECT email_id FROM ticket WHERE id = ?";
$stmtCliente = $con->prepare($queryCliente);
//---------------------------------------------------------------------------------------------------------
//Agregar tareas a un ticket--------------------------------------------------------------------------------
if (isset($_POST["addtsk"])) {
  $tId = $_POST['tk_id'];
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
              
          } else {
              echo "Error al insertar tareas: " . mysqli_error($con);
          }
      } else {
          echo '<script>alert("Debe agregar al menos una tarea.");</script>';
      }
  }

  //Enviar notificacion

  $stmtCliente->bind_param("i", $tId);
  $stmtCliente->execute();
  $stmtCliente->bind_result($clienteEmail);
  $stmtCliente->fetch();

  if ($clienteEmail) {
      //  Enviar la notificación por correo
      echo '<script>alert("Tareas asignadas correctamente "); location.replace(document.referrer);</script>';  
      Notificaciones::enviarCorreo($clienteEmail, $tId,$titles, null,$ticketStatus, null);
  }

  $stmtCliente->close();
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
  $ticketStatus = "En revisión";
  
  //actualizar el estado del ticket
  $queryUpdt = "UPDATE ticket SET admin_remark='$adminremark', status = 10 WHERE id='$fid'";
  mysqli_query($con, $queryUpdt);
 
  //actualizar el estado de las tareas
  $task  = $_POST["tasks"];
  $taskStatus = [];

  if($task){
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

  echo '<script>';
  echo 'console.log(' . json_encode($taskStatus) . ')';
  echo '</script>';

  $stmtCliente->bind_param("i", $fid);
  $stmtCliente->execute();
  $stmtCliente->bind_result($clienteEmail);
  $stmtCliente->fetch();

  if ($clienteEmail) {
      //  Enviar la notificación por correo
      echo '<script>alert("Ticket actualizado correctamente "); location.replace(document.referrer);</script>';  
      Notificaciones::enviarCorreo($clienteEmail, $fid,null, $adminremark ,$ticketStatus, $taskStatus);
  }

  $stmtCliente->close();
}
else if (isset($_POST['end'])) {
  $adminremark = $_POST['aremark'];
  $fid = $_POST['frm_id'];
  $ticketStatus = "Cerrado";

  $queryUpdt = "UPDATE ticket SET admin_remark='$adminremark', status= 12 where id='$fid'";
  mysqli_query($con, $queryUpdt);

    //actualizar el estado de las tareas
    $task  = $_POST["tasks"];
    $taskStatus = [];
  
    if($task){
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
        echo '<script>alert("Ticket actualizado correctamente "); location.replace(document.referrer);</script>';  
        Notificaciones::enviarCorreo($clienteEmail, $fid,null, $adminremark ,$ticketStatus, $taskStatus);
    }
  
    $stmtCliente->close();

}
//----------------------------------------------------------------------------------------------------------

//carga las prioridades y estados de ticket para filtrar--------------------------------------------------------------
$query_prio = "SELECT * FROM prioridades ";
$prioridad = mysqli_query($con, $query_prio);

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
          JOIN prioridades pr ON ti.prioprity = pr.id
          JOIN estados st ON ti.status = st.id";

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
  $query .= ' WHERE ' . implode(' AND ', $conditions);
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
//----------------------------------------------------------------------------------------------------------
//Obtener todos los estados de las task --------------------------------------------------------------------

$query = "SELECT * FROM estados WHERE type = 'task'";
$status = mysqli_query($con, $query);

$estados = [];
while ($estado = $status->fetch_assoc()) {
    $estados[] = $estado;
}
//----------------------------------------------------------------------------------------------------------



?>
<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
  <meta charset="utf-8" />
  <title>Soporte Ticket</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <meta content="" name="description" />
  <meta content="" name="author" />

  <link href="../assets/plugins/pace/pace-theme-flash.css" rel="stylesheet" type="text/css" media="screen" />
  <link href="../assets/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.css" rel="stylesheet" type="text/css" />
  <link href="../assets/plugins/boostrapv3/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
  <link href="../assets/plugins/boostrapv3/css/bootstrap-theme.min.css" rel="stylesheet" type="text/css" />
  <link href="../assets/plugins/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css" />
  <link href="../assets/css/animate.min.css" rel="stylesheet" type="text/css" />
  <link href="../assets/plugins/jquery-scrollbar/jquery.scrollbar.css" rel="stylesheet" type="text/css" />
  <link href="../assets/css/style.css" rel="stylesheet" type="text/css" />
  <link href="../assets/css/responsive.css" rel="stylesheet" type="text/css" />
  <link href="../assets/css/custom-icon-set.css" rel="stylesheet" type="text/css" />
  <link href="../assets/css/manage_tickets.css" rel="stylesheet" />
</head>

<body class="">
  <?php include("header.php"); ?>
  <div class="page-container row">

    <?php include("leftbar.php"); ?>

    <div class="clearfix"></div>
  </div>
  </div>
  <div class="page-content">
    <div class="clearfix"></div>
    <div class="content">
      <ul class="breadcrumb">
        <li>
          <p>Inicio</p>
        </li>
        <li><a href="#" class="active">Ver Tickets</a></li>
      </ul>
      <div class="page-title">
        <h3>Lista de Tickets</h3>
      </div>
      
      <button class="btn btn-secondary pull-right" id="toggleFiltersBtn">
            <i class="glyphicon glyphicon-chevron-down"></i> Filtros
      </button>

      <div>        
        <form method="GET" action="" id="filtersForm" class="mt-3" style="display: none;">
            <div class="fil-main form-group">
                <div class="search-div">
                  <label for="textSearch">Buscar:</labe>
                  <input type="text" class="form-control" id="textSearch" name="textSearch" placeholder="Nombre/ID del ticket">
                </div>
                <div class="fil-div">
                    <label for="prio">Prioridad</label>
                    <select name="priority" class="form-control select" id="prio">
                        <option value="">Ver todo</option> 
                        <?php
                        while ($row = mysqli_fetch_assoc($prioridad)) {
                            // Opcion para filtrar por prioridad
                            $selected = isset($_GET['priority']) && $_GET['priority'] == $row['id'] ? 'selected' : '';
                            echo "<option value='" . $row['id'] . "' $selected>" . $row['nombre'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="fil-div">
                    <label for="st">Estado</label>
                    <select name="statusF" class="form-control select" id="st">
                        <option value="">Ver todo</option>    
                        <?php
                        while ($st = mysqli_fetch_assoc($statusF)) {
                            $select = isset($_GET['statusF']) && $_GET['statusF'] == $st['id'] ? 'selected' : '';
                            echo "<option value='" . $st['id'] . "' $select>" . $st['nombre'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="fil-btn">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                </div>
            </div>
            <br>
        </form>
        <br>
      </div>

      <div class="clearfix"></div>
      <?php 
      while ($row = mysqli_fetch_array($rt)) {
      ?>
        <div class="row">
          <div class="col-md-12">
            <div class="grid simple no-border">
              <div class="grid-title no-border descriptive clickable">
                <h4 class="semi-bold"><?php echo $row['subject']; ?></h4>
                <p><span class="text-success bold">Ticket #<?php echo $_SESSION['sid'] = $row['ticketId']; ?></span> - Fecha de creación <?php echo $row['posting_date']; ?>
                  <?php
                    if ($row['statusN'] == 'Abierto') {
                    ?>
                    <span class="label label-success"><?php echo $row['statusN']; ?></span>
                    <?php
                    }else if ($row['statusN'] == 'Cerrado'){
                    ?>
                    <span class="label label-important"><?php echo $row['statusN']; ?></span>
                    <?php
                    }else{?>
                      <span class="label label-warning"><?php echo $row['statusN']; ?></span>
                      <?php
                    };
                  
                  ?>
              
                </>
                <div class="actions"> <a class="view" href="javascript:;"><i class="fa fa-angle-down"></i></a> </div>
              </div>
              <div class="grid-body  no-border" style="display:none">
                <div class="post">
                  <div class="user-profile-pic-wrapper">
                    <div class="user-profile-pic-normal"> <img width="35" height="35" data-src-retina="../assets/img/user.png" data-src="../assets/img/user.png" src="../assets/img/user.png" alt=""> </div>
                  </div>
                  <div class="info-wrapper">
                    <div class="info"><?php echo $row['ticket']; ?> </div>
                    <div class="clearfix"></div>
                  </div>
                  <div class="clearfix"></div>
                </div>
                <br>
                <div class="form-actions">
                  <div class="post col-md-12">
                    <div class="user-profile-pic-wrapper">
                      <div class="user-profile-pic-normal"> <img width="35" height="35" data-src-retina="../assets/img/user.png" data-src="../assets/img/user.png" src="../assets/img/user.png" alt=""> </div>
                    </div>
                    <div class="info-ticket d-flex">
                      <form name="adminr" method="post" enctype="multipart/form-data">
                        <br>    
                        <!-- listar tareas -->
                        <div>
                          <?php
                            //Obtener las task asociadas al ticket
                            $tkid = $row['ticketId'];
                            $query = "SELECT ta.id AS tskId, ta.titulo, es.nombre, es.id AS statusId
                                      FROM tasks ta
                                      JOIN estados es ON(ta.estado_id = es.id)
                                      WHERE ta.ticket_id = ?";

                            $stmt = $con->prepare($query);
                             
                            if($stmt){
                              $stmt->bind_param("i", $tkid); 
                              $stmt->execute();
                              $tasks = $stmt->get_result();

                              if($tasks->num_rows > 0) {
                                echo "<h2>Tareas </h2> <hr>";
                                while($tsk = $tasks->fetch_assoc()) {
                                  ?>
                                    <h4>
                                      <?php echo $tsk["titulo"]?>
                                    </h4>
                                    <button class="btn btn-danger tsk pull-right"  type="button" data-toggle="modal" data-target="#delTasks" data-task-id="<?php echo $tsk['tskId'];?>">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                    <div class="status-div">
                                      <span>Estado</span>
                                      <select class="form-control select" name="tasks[<?php echo $tsk['tskId']; ?>][newstatus]">
                                        <option value="<?php echo $tsk['statusId']; ?>" selected>
                                            <?php echo $tsk['nombre']; ?>
                                        </option>

                                        <?php foreach ($estados as $estado) {
                                           if ($estado['id'] != $tsk['statusId']) { // Excluir el estado actual 
                                           ?>
                                          <option value="<?php echo $estado['id']; ?>">
                                            <?php echo $estado['nombre']; ?>  
                                          </option>
                                        <?php 
                                            } 
                                          } ?>
                                      </select>
                                      <input type="hidden" name="tasks[<?php echo $tsk['tskId']; ?>][oldstatus]" value="<?php echo $tsk['statusId']; ?>" />
                                      <input type="hidden" name="tasks[<?php echo $tsk['tskId']; ?>][titulo]" value="<?php echo $tsk['titulo']; ?>" />
                                    </div>
                                  <?php
                                }
                              }else{
                                echo "<h3>No hay tareas asociadas </h3>";
                              }
                              $stmt->close(); 
                            }  
                            else {
                              echo "Error en la consulta: ".$con->error;
                            }
                          ?>
                        </div>
                        <!-- Final listar tareas -->
                        <hr>
                        <div class="comm">
                          <textarea name="aremark" cols="110" rows="4" required="true"><?php echo $row['admin_remark']; ?></textarea>
                        </div>
                          <div class="btn-div">
                            <button name="tasks" type="button" class=" btn btn-success taskbtn" data-toggle="modal" data-target="#addTasks" data-ticket-id="<?php echo $row['ticketId']; ?>">Agregar tareas</button>
                            <button name="update" type="submit" class="btn btn-primary" id="Update">Actualizar</button>
                            <button name="end" type="submit" class="btn btn-danger" id="Update">Cerrar </button>
                            <input name="frm_id" type="hidden" id="frm_id" value="<?php echo $row['ticketId']; ?>" />
                          </div>
                      </form>                      
                    </div>
                    <div class="clearfix"></div>
                  </div>
                  <div class="clearfix"></div>
                </div>
              </div>
            </div>
      
          </div>
        </div>
      <?php } ?>


    </div>
  </div>
  </div>
  
                    

  </div>
  </div>
  </div>

  </div>
  </div>
  </div>
  </div>
  </div>

  </div>
  <!-- END CONTAINER -->


 <!-- Add tasks modal -->
  <div id="addTasks" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <!-- Contenido del modal-->
      <div class="modal-content">
        
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Crear tareas</h4>
        </div>
        <form name="taskForm" method="post" enctype="multipart/form-data">
          <div class="modal-body" id="tasksContainer">
              <input name="tk_id" type="hidden" id="modalTicketId"/>
              <div>
                <span class="btn btn-danger del-task pull-right" id="delTaskBtn" type="button">
                    <i class="fa fa-minus add-icon" ></i>
                </span>

                <span class="btn add-task pull-right" id="addTaskBtn" type="button">
                    <i class="fa fa-plus add-icon" ></i>
                </span>
              </div>
  
              <div class="form-group">
                <label for="title1">Tarea #1</label>
                <input name="title[]" type="text" class="form-control" id="title1" placeholder="">
              </div>

          </div>
          <div class="modal-footer">
            <button name="addtsk" type="submit" class="btn btn-default">Enviar</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- fin modal  -->

  <div id="delTasks" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <!-- Contenido del modal-->
      <div class="modal-content">
        
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Confirmación</h4>
        </div>
        <form name="delForm" method="post" enctype="multipart/form-data">
          <div class="modal-body del-modal" id="delContainer">
              <input name="tk_id" type="hidden" id="taskId"/>
              <h3>Estas seguro que quieres eliminar esta tarea?</h3>
          </div>
          <div class="modal-footer del-footer">
            <button name="deltsk" type="submit" class="btn btn-default">Eliminar</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Borrar task modal -->


  <!-- Final modal -->
  <!-- BEGIN CORE JS FRAMEWORK-->
  <script src="../assets/plugins/jquery-1.8.3.min.js" type="text/javascript"></script>
  <script src="../assets/plugins/jquery-ui/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
  <script src="../assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
  <script src="../assets/plugins/breakpoints.js" type="text/javascript"></script>
  <script src="../assets/plugins/jquery-unveil/jquery.unveil.min.js" type="text/javascript"></script>
  <script src="../assets/plugins/jquery-block-ui/jqueryblockui.js" type="text/javascript"></script>
  <!-- END CORE JS FRAMEWORK -->
  <!-- BEGIN PAGE LEVEL JS -->
  <script src="../assets/plugins/pace/pace.min.js" type="text/javascript"></script>
  <script src="../assets/plugins/jquery-scrollbar/jquery.scrollbar.min.js" type="text/javascript"></script>
  <script src="../assets/plugins/jquery-numberAnimate/jquery.animateNumbers.js" type="text/javascript"></script>
  <script src="../assets/plugins/bootstrap-wysihtml5/wysihtml5-0.3.0.js" type="text/javascript"></script>
  <script src="../assets/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.js" type="text/javascript"></script>
  <script src="../assets/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
  <!-- END PAGE LEVEL PLUGINS -->
  <script src="../assets/js/support_ticket.js" type="text/javascript"></script>
  <!-- BEGIN CORE TEMPLATE JS -->
  <script src="../assets/js/general.js" type="text/javascript"></script>
  <script src="../assets/js/tasks.js" type="text/javascript"></script>
  <script src="../assets/js/core.js" type="text/javascript"></script>
  <script src="../assets/js/chat.js" type="text/javascript"></script>
  <script src="../assets/js/live_chat.js" type="text/javascript"></script>
  <script src="../assets/js/demo.js" type="text/javascript"></script>
  <!-- END CORE TEMPLATE JS -->
</body>

</html>