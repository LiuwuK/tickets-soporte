<?php
session_start();
//echo $_SESSION['id'];
//$_SESSION['msg'];
include("dbconnection.php");
include("checklogin.php");
check_login();

//se obtienen los tickets del usuario-------------------------------------------------------------------------
$query = "select * from ticket where email_id='".$_SESSION['login']."'";
$rt = mysqli_query($con, $query);
$num = mysqli_num_rows($rt);
//------------------------------------------------------------------------------------------------------------


?>
<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
  <meta charset="utf-8" />
  <title>Usuario | Tickets de Soporte</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <meta content="" name="description" />
  <meta content="" name="author" />

  <link href="assets/plugins/pace/pace-theme-flash.css" rel="stylesheet" type="text/css" media="screen" />
  <link href="assets/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.css" rel="stylesheet" type="text/css" />
  <link href="assets/plugins/boostrapv3/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
  <link href="assets/plugins/boostrapv3/css/bootstrap-theme.min.css" rel="stylesheet" type="text/css" />
  <link href="assets/plugins/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css" />
  <link href="assets/css/animate.min.css" rel="stylesheet" type="text/css" />
  <link href="assets/plugins/jquery-scrollbar/jquery.scrollbar.css" rel="stylesheet" type="text/css" />
  <link href="assets/css/style.css" rel="stylesheet" type="text/css" />
  <link href="assets/css/responsive.css" rel="stylesheet" type="text/css" />
  <link href="assets/css/custom-icon-set.css" rel="stylesheet" type="text/css" />
  <!-- END CSS TEMPLATE -->
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->

<body class="">
  <?php include("header.php"); ?>
  <div class="page-container row">
    <?php include("leftbar.php"); ?>
    <div class="clearfix"></div>
  </div>
  </div>
  <div class="page-content">
    <div id="portlet-config" class="modal hide">
      <div class="modal-header">
        <button data-dismiss="modal" class="close" type="button"></button>
        <h3>Widget Settings</h3>
      </div>
      <div class="modal-body"> Widget settings form goes here </div>
    </div>
    <div class="clearfix"></div>
    <div class="content">
      <ul class="breadcrumb">
        <li>
          <p>Inicio</p>
        </li>
        <li><a href="#" class="active">Ver Tickets</a></li>
      </ul>
      <div class="page-title">
        <h3>Ticket de Soporte</h3>
      </div>
      <div class="clearfix"></div>

      <h4> <span class="semi-bold">Tickets</span></h4>
      <br>
      <?php 
      if ($num > 0) {
        while ($row = mysqli_fetch_array($rt)) {
      ?>
          <div class="row">
            <div class="col-md-12">
              <div class="grid simple no-border">
                <div class="grid-title no-border descriptive clickable">
                  <h4 class="semi-bold"><?php echo $row['subject']; ?></h4>
                  <p><span class="text-success bold">Ticket #<?php echo $row['id']; ?></span> - Fecha de Creación <?php echo $row['posting_date']; ?> 
                    <?php
                    if ($row['status'] == 'Abierto') {
                    ?>
                    <span class="label label-success"><?php echo $row['status']; ?></span>
                    <?php
                    }else if ($row['status'] == 'Cerrado'){
                    ?>
                    <span class="label label-important"><?php echo $row['status']; ?></span>
                    <?php
                    }else{?>
                      <span class="label label-warning"><?php echo $row['status']; ?></span>
                      <?php
                    };
                  
                
                  ?>
                  </p>
                  <div class="actions"> <a class="view" href="javascript:;"><i class="fa fa-angle-down"></i></a> </div>
                </div>
                <div class="grid-body  no-border" style="display:none">
                  <div class="post">
                    <div class="user-profile-pic-wrapper">
                      <div class="user-profile-pic-normal"> <img width="35" height="35" data-src-retina="assets/img/user.png" data-src="assets/img/user.png" src="assets/img/user.png" alt=""> </div>
                    </div>
                    <div class="info-wrapper">

                      <!-- CHAT A TIEMPO REAL ------------------------------------------------------------------------
                      <div class="chatbox" id="chat_area_<?php echo $row['id']; ?>"></div>
                      <input type="hidden" id="sender_<?php echo $row['id']; ?>" value="<?php echo$_SESSION["name"]; ?>">
                      <input type="hidden" id="ticket_id_<?php echo $row['id']; ?>" value="<?php echo $row['id']; ?>" />
                      <input type="text" id="message_input_<?php echo $row['id']; ?>" placeholder="Escribe tu mensaje" />
                      <button onclick="sendMessage(document.getElementById('message_input_<?php echo $row['id']; ?>').value, <?php echo $row['id']; ?>, document.getElementById('sender_<?php echo $row['id']; ?>').value)">Enviar</button>
                        <style>
                          .chatbox{
                            padding: 10px;
                            min-height:400px;
                            max-height:400px;
                            width: 90%;
                            border: 1px solid black;
                            overflow-y: scroll;
                          }
                        </style> 
                      ----------------------------------------------------------------------------------------------------->
                      <div class="info"><?php echo $row['ticket']; ?> </div>
                      <div class="clearfix"></div>
                    </div>
                    <div class="clearfix"></div>
                  </div>
                  <br>

                  <?php if ($row['admin_remark'] != '') : ?>
                    <div class="form-actions">
                      <div class="post col-md-12">
                        <div class="user-profile-pic-wrapper">
                          <div class="user-profile-pic-normal"> <img width="35" height="35" data-src-retina="assets/img/admin.jpg" data-src="assets/img/admin.jpg" src="assets/img/admin.jpg" alt="Admin"> </div>
                        </div>
                        <div class="info-wrapper">
                          <br>
                          <div class="comm">
                            <h4>Procedimiento a seguir</h4>
                            <div>
                              <ul>
                                <?php
                                  //Obtener las task asociadas al ticket
                                  $tkid = $row['id'];
                                  $query = "SELECT ta.id AS tskId, ta.titulo, es.nombre
                                            FROM tasks ta
                                            JOIN estados es ON(ta.estado_id = es.id)
                                            WHERE ta.ticket_id = ?";

                                  $stmt = $con->prepare($query);
                                  
                                  if($stmt){
                                    $stmt->bind_param("i", $tkid); 
                                    $stmt->execute();
                                    $tasks = $stmt->get_result();
                                    if($tasks->num_rows > 0) {
                                      while($tsk = $tasks->fetch_assoc()) {
                                        ?>
                                            <li><?php echo $tsk["titulo"]?> </li>

                                            <p style="margin-left:15px"> Estado: <?php echo $tsk["nombre"]?></p>
                                            
                                        <?php

                                      }

                                    }else {
                                      echo "Actualmente no tiene tareas asignadas";
                                    }
                                    $stmt->close(); 
                                    }  
                                    else {
                                      echo "Error en la consulta: ".$con->error;
                                    }
                                ?>
                              </ul>
                            </div>
                          </div>
                          <div class="tasks">
                            <h4>Comentario</h4>
                            <p style="margin-left:30px"><?php echo $row['admin_remark']; ?></p>
                          </div>
                          <hr>
                          <p class="small-text">Publicado en <?php echo $row['admin_remark_date']; ?></p>
                        </div>
                        <div class="clearfix"></div>
                      </div>
                      <div class="clearfix"></div>
                    </div>
                  <?php endif; ?>

                </div>
              </div>
            <?php }
        } else { ?>
            <h3 align="center" style="color:red;">Sin registros que mostrar</h3>
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
  </div>
  <!-- BEGIN CHAT -->

  </div>
  <!-- END CONTAINER -->
  <!-- BEGIN CORE JS FRAMEWORK-->
  
  <script src="assets/plugins/jquery-1.8.3.min.js" type="text/javascript"></script>
  <script src="assets/plugins/jquery-ui/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
  <script src="assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
  <script src="assets/plugins/breakpoints.js" type="text/javascript"></script>
  <script src="assets/plugins/jquery-unveil/jquery.unveil.min.js" type="text/javascript"></script>
  <script src="assets/plugins/jquery-block-ui/jqueryblockui.js" type="text/javascript"></script>
  <!-- END CORE JS FRAMEWORK -->
  <!-- BEGIN PAGE LEVEL JS -->
  <script src="assets/plugins/pace/pace.min.js" type="text/javascript"></script>
  <script src="assets/plugins/jquery-scrollbar/jquery.scrollbar.min.js" type="text/javascript"></script>
  <script src="assets/plugins/jquery-numberAnimate/jquery.animateNumbers.js" type="text/javascript"></script>
  <script src="assets/plugins/bootstrap-wysihtml5/wysihtml5-0.3.0.js" type="text/javascript"></script>
  <script src="assets/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.js" type="text/javascript"></script>
  <script src="assets/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
  <!-- END PAGE LEVEL PLUGINS -->
  <script src="assets/js/support_ticket.js" type="text/javascript"></script>
  <!-- BEGIN CORE TEMPLATE JS -->
  <script src="assets/js/live_chat.js" type="text/javascript"></script>
  <script src="assets/js/core.js" type="text/javascript"></script>
  <script src="assets/js/chat.js" type="text/javascript"></script>
  <script src="assets/js/demo.js" type="text/javascript"></script>
  <!-- END CORE TEMPLATE JS -->
</body>

</html>