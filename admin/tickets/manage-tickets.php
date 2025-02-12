<?php
session_start();
include("../dbconnection.php");
include("../../checklogin.php");
include("../phpmail.php");
include("../notificaciones.php");
include("assets/php/manage-tickets.php");
header('Content-Type: text/html; charset=utf-8');
check_login();

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

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <!-- CSS personalizados -->
  <link href="../../assets/css/sidebar.css" rel="stylesheet" type="text/css" />
  <link href="../../tickets/assets/css/manage_tickets.css" rel="stylesheet" type="text/css"/>
  <!-- Toast notificaciones -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
</head>

<body class="test" >
    <!-- Sidebar -->
  <div class="page-container ">
    <div class="sidebar">
      <?php include("../header.php"); ?>
    </div>
    <div class="page-content">
      <?php include("../leftbar.php"); ?>
        <div class="content">
            <div class="page-title d-flex justify-content-between">
                <h2>
                  <i class="bi bi-ticket-perforated"></i>  
                  Tickets
                </h2>
                <button class=" btn-back" onclick="window.location.href='tickets-main.php';"> 
                    <i class="bi bi-arrow-left" ></i>
                </button>
            </div>

            <!-- filtros  -->
            <div class="d-flex justify-content-end">
                <button class="btn btn-sm" id="toggleFiltersBtn">
                    <i class="bi bi-arrow-down-short"></i> Filtros
                </button>
            </div>
            <div>        
                <form method="GET" action="" id="filtersForm" class="mt-3" >
                    <div class="fil-main form-group">
                        <div class="search-div d-flex justify-content-center">
                            <label class="form-label" >Buscar</labe>
                            <input type="text" class="form-control form-control-sm" id="textSearch" name="textSearch" placeholder="Nombre/ID del ticket">
                        </div>
                        <div class="fil-div">
                            <label class="form-label" for="prio">Prioridad</label>
                            <select name="priority" class="form-select form-select-sm" id="prio">
                                <option value="">Ver todo</option> 
                                <?php
                                foreach ($prioridades as $row) {
                                    // Opcion para filtrar por prioridad
                                    $selected = isset($_GET['priority']) && $_GET['priority'] == $row['id'] ? 'selected' : '';
                                    echo "<option value='" . $row['id'] . "' $selected>" . $row['nombre'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="fil-div">
                            <label class="form-label" for="st">Estado</label>
                            <select name="statusF" class="form-select form-select-sm" id="st">
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
                            <button type="submit" class="btn">Filtrar</button>
                        </div>
                    </div>
                    <br>
                </form>
                <br>
            </div>
            <!-- Listado de tickets -->
            <?php 
            if ($num > 0) {
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
                        
                          <p>Creado por: <?php echo $row['userN'];?></p>
                          <p>Departamento: <?php echo $row['areaN']?></p>
                          <div class="actions"> <a class="view" href="javascript:;"><i class="bi bi-caret-down-fill"></i></a> </div>
                          <div class="d-flex">
                            <form  name="asignarPrio" id="asignarPrio" method="post" style="margin-right:5px">
                              <br>
                              <div class="ing-main d-flex justify-content-start">
                                  <p style="width:70px !important"><strong>Prioridad</strong></p>
                                  <select id="prioridadSelect" name="prioridad"  class="prioridad-select form-select form-select-sm" data-initial-value="<?php echo $row['prioprity'] ? $row['prioprity'] : "Sin asignar" ;?>">
                                      <?php
                                      if (empty($row['prioprity'])) {
                                          echo "<option selected>Sin asignar</option>";
                                      }
                                      foreach ($prioridades as $row_prio) {
                                          if ($row_prio['id'] == $row['prioprity']) {
                                              echo "<option value=\"".$row_prio['id']."\" selected>".$row_prio['nombre']."</option>";
                                          } else {
                                              echo "<option value=\"".$row_prio['id']."\">".$row_prio['nombre']."</option>";
                                          }
                                      }
                                      ?>
                                  </select>
                                  <input type="hidden" id="tId" name="tId" value="<?php echo $row['ticketId']; ?>">
                                  <button type="submit" class="btn btn-updt save-button" name="asignarPrio" style="display:none">Asignar</button>
                              </div>
                            </form>
                            <form  name="asignarUser" id="asignarUser" method="post">
                              <br>
                              <div class="ing-main d-flex justify-content-start " >
                                  <p style="width:120px !important"><strong>Usuario Asignado</strong></p>
                                  <select id="userSelect" name="userasig"  class="prioridad-select form-select form-select-sm" data-initial-value="<?php echo $row['usuario_asignado'] ? $row['usuario_asignado'] : "Sin asignar" ;?>">
                                      <?php
                                      if (empty($row['usuario_asignado'])) {
                                          echo "<option selected>Sin asignar</option>";
                                      }
                                      foreach ($usuarios as $row_user) {
                                          if ($row_user['id'] == $row['usuario_asignado']) {
                                              echo "<option value=\"".$row_user['id']."\" selected>".$row_user['name']."</option>";
                                          } else {
                                              echo "<option value=\"".$row_user['id']."\">".$row_user['name']."</option>";
                                          }
                                      }
                                      ?>
                                  </select>
                                  <input type="hidden" id="tId" name="tId" value="<?php echo $row['ticketId']; ?>">
                                  <button type="submit" class="btn btn-updt save-button" name="asignarUser" style="display:none">Asignar</button>
                              </div>
                            </form>
                          </div>
                        </div>
                        <div class="grid-body  no-border" style="display:none">
                          <div class="post">
                            <div class="user-profile-pic-wrapper">
                              <div class="user-profile-pic-normal"> <img width="35" height="35" data-src-retina="../../assets/img/user.png" data-src="../../assets/img/user.png" src="../../assets/img/user.png" alt=""> </div>
                            </div>
                            <div class="info-wrapper">
                              <div class="info"><?php echo $row['ticket'];?> </div>
                              <?php
                                if (isset($row['ticket_img'])) { ?>
                                  <div class="img">
                                      <img src="../../tickets/<?php echo $row['ticket_img'];?>" alt="">
                                  </div>
                              <?php                                            
                                }
                              ?>
                            </div>
                            
                          </div>
                          <br>
                          <div class="form-actions">
                            <div class="post col-md-12">

                              <div class="info-ticket d-flex justify-content-center">
                                <form name="form" method="post" enctype="multipart/form-data">
                                <div id="loading" style="display:none ;">
                                  <div class="loading-spinner"></div>
                                  <p>Procesando...</p>
                                </div>

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
                                          echo "<h3>Tareas </h3> <hr>";
                                          while($tsk = $tasks->fetch_assoc()) {
                                            ?>
                                              <div class="t-header d-flex justify-content-between w-80">
                                                <h5><?php echo $tsk["titulo"]?></h5>
                                                <button class="btn btn-danger tsk" type="button" data-bs-toggle="modal" data-bs-target="#delTasks" data-task-id="<?php echo $tsk['tskId']; ?>">
                                                  <i class="bi bi-dash"></i>
                                                </button>
                                              </div>
                                              <div class="status-div">
                                                <span>Estado</span>
                                                <select class="form-select form-select-sm" name="tasks[<?php echo $tsk['tskId']; ?>][newstatus]">
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
                                          echo "<h4>No hay tareas asociadas </h4>";
                                        }
                                        $stmt->close(); 
                                      }  
                                      else {
                                        echo "Error en la consulta: ".$con->error;
                                      }
                                    ?>
                                    <hr>
                                  </div>
                                  <?php 
                                    if($row['tmsg'] or $row['tecnicoImg']){
                                  ?>
                                    <div class="tinfo mb-3 mt-3">
                                      <h4>Respuesta Técnico</h4>
                                      <hr>
                                      <div class="user-profile-pic-wrapper mt-2">
                                        <div class="user-profile-pic-normal"> <img width="50" height="50" data-src-retina="../../assets/img/admin.jpg" data-src="../../assets/img/admin.jpg" src="../../assets/img/admin.jpg" alt=""> </div>
                                      </div>
                                      <div class="info-wrapper mb-3">
                                        <div class="info"><?php echo $row['tmsg'];?> </div>
                                        <?php
                                          if (isset($row['tecnicoImg'])) { ?>
                                            <div class="img timg">
                                                <img class="mx-auto"  src="../<?php echo $row['tecnicoImg'];?>" alt="">
                                            </div>
                                        <?php                                            
                                          }
                                        ?>
                                      </div>
                                    </div>
                                    <br><br>
                                    
                                  <?php
                                    }
                                  ?>
                                  <!-- Final listar tareas -->
                                  <hr>
                                  <div class="comm">
                                    <textarea class="form-control form-control-sm" name="aremark" cols="110" rows="4" required="true"><?php echo $row['admin_remark']; ?></textarea>
                                  </div>
                                  <div class="btn-div">
                                    <button 
                                      name="tasks" 
                                      type="button" 
                                      class="btn btn-updt taskbtn" 
                                      data-bs-toggle="modal" 
                                      data-bs-target="#addTasks" 
                                      data-ticket-id="<?php echo $row['ticketId']; ?>" 
                                      data-user-id="<?php echo $row['user_id']; ?>">
                                      Agregar tareas
                                    </button>
                                    <button name="update" type="submit" class="btn btn-default" id="Update">Actualizar</button>
                                    <button name="end" type="submit" class="btn btn-del" id="Update">Cerrar </button>
                                    <input type="hidden" name="userId" value="<?php echo $row['user_id'];?>">
                                    <input name="frm_id" type="hidden" id="frm_id" value="<?php echo $row['ticketId'];?>" />
                                  </div>
                                </form>                      
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                
                    </div>
                  </div>
                <?php } 
                } else { ?>
                    <h3 align="center" style="color:red;">Sin registros que mostrar</h3>
            <?php 
            } ?>
                    </div>
                </div>
        

        </div>   
    </div>
  </div>

  <!-- Add tasks modal -->
  <div id="addTasks" class="modal fade" tabindex="-1" aria-labelledby="addTasksLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <!-- Header del modal -->
        <div class="modal-header">
          <h5 class="modal-title" id="addTasksLabel">Crear tareas</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <!-- Formulario dentro del modal -->
        <form name="taskForm" method="post" enctype="multipart/form-data">
          <div class="modal-body" id="tasksContainer">
            <input name="user_id" type="hidden" id="modalUserId"/>  
            <input name="tk_id" type="hidden" id="modalTicketId"/>
            <div>
              <span class="btn btn-danger del-task float-end" id="delTaskBtn" type="button">
                <i class="bi bi-dash add-icon"></i>
              </span>
              <span class="btn btn-success add-task float-end me-2" id="addTaskBtn" type="button">
                <i class="bi bi-plus add-icon"></i>
              </span>
            </div>
            <div class="form-group">
              <label for="title1">Tarea #1</label>
              <input name="title[]" type="text" required="true" class="form-control" id="title1" placeholder="">
            </div>
          </div>
          <!-- Footer del modal -->
          <div class="modal-footer">
            <button name="addtsk" type="submit" class="btn btn-updt">Enviar</button>
            <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cerrar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- fin modal  -->
  <!-- Borrar task modal -->
  <div id="delTasks" class="modal fade" tabindex="-1" aria-labelledby="delTasksLabel" aria-hidden="true">
  <div class="modal-dialog">
    <!-- Contenido del modal -->
    <div class="modal-content">
      <!-- Header -->
      <div class="modal-header">
        <h5 class="modal-title" id="delTasksLabel">Confirmación</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <!-- Formulario -->
      <form name="delForm" method="post" enctype="multipart/form-data">
        <div class="modal-body del-modal" id="delContainer">
          <input name="tk_id" type="hidden" id="taskId" />
          <h4>¿Estás seguro que quieres eliminar esta tarea?</h4>
        </div>
        <!-- Footer -->
        <div class="modal-footer del-footer">
          <button name="deltsk" type="submit" class="btn btn-del">Eliminar</button>
          <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
  </div>
  <!-- Final modal -->


  <!-- Popper.js (para tooltips y otros componentes) -->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <!-- Bootstrap Bundle (con Popper.js) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Complementos/Plugins-->
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <script src="../../assets/plugins/jquery-scrollbar/jquery.scrollbar.min.js" type="text/javascript"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js" type="text/javascript"></script>
  <!-- Scripts propios -->
  <script src="../../assets/js/support_ticket.js" type="text/javascript"></script>  
  <script src="../../assets/js/general.js" type="text/javascript"></script>
  <script src="../../tickets/assets/js/tasks.js" type="text/javascript"></script>
  <script src="../../assets/js/sidebar.js"></script>
</body>

</html>