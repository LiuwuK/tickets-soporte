<?php
session_start();
//echo $_SESSION['id'];
//$_SESSION['msg'];
include("dbconnection.php");
include("checklogin.php");
include("assets/php/tickets-asignados.php");
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
  <link href="assets/css/sidebar.css" rel="stylesheet" type="text/css" />
  <link href="assets/css/manage_tickets.css" rel="stylesheet" type="text/css"/>
  <link href="assets/css/tickets-asignados.css" rel="stylesheet" type="text/css"/>
  <!-- Toast notificaciones -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
</head>

<body class="test" >
    <!-- Sidebar -->
    <div class="sidebar-overlay"></div> 
  <div class="page-container ">
    <div class="sidebar">
      <?php include("header-test.php"); ?>
      <?php include("assets/php/phone-sidebar.php"); ?>
    </div>
    <div class="page-content">
      <?php include("leftbar-test.php"); ?>
        <div class="content">
            <div class="page-title">
                <h2>
                  <i class="bi bi-pin-angle"></i>  
                  Tickets asignados
                </h2>
            </div>

            <!-- filtros 
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
             -->
            <!-- Listado de tickets -->
             <br><br>
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
                        
                          
                          <div class="actions"> <a class="view" href="javascript:;"><i class="bi bi-caret-down-fill"></i></a> </div>
                            <br>
                            <div class="prio-main d-flex justify-content-start">
                                <p><strong>Prioridad</strong></p>
                                <?php
                                foreach ($prioridades as $row_prio) {
                                  if ($row_prio['id'] == $row['prioprity']) {
                                      echo "<p>".$row_prio['nombre']."</p>";
                                  } 
                                }
                                ?>
                            </div>
                        </div>
                        <div class="grid-body  no-border" style="display:none">
                          <div class="post">
                            <div class="user-profile-pic-wrapper">
                              <div class="user-profile-pic-normal"> <img width="35" height="35" data-src-retina="assets/img/user.png" data-src="assets/img/user.png" src="assets/img/user.png" alt=""> </div>
                            </div>
                            <div class="info-wrapper">
                              <div class="info"><?php echo $row['ticket']; ?> </div>

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
                                  <br>
                                  <div class="comm">
                                    <label for="">Imagen</label>
                                    <div class="img">
                                      <?php
                                        if (isset($row['tecnicoImg'])) { ?>
                                          <input type="hidden" name="filePath" value="<?php echo $row['tecnicoImg'];?>">
                                          <img src="<?php echo $row['tecnicoImg'];?>" alt="">
                                      <?php                                            
                                      } else{ ?>
                                         <input class="form-control form-control-sm" type="file" id="tecnicoImg" name="tecnicoImg" accept="image/*" required>
                                      <?php
                                      }
                                      ?>
                                    </div>
                                    <label for="tmsg">Comentario</label>
                                    <textarea class="form-control form-control-sm" id="tmsg" name="tmsg" cols="110" rows="4" required="true"><?php echo $row['tmsg']; ?></textarea>
                                  </div>
                                  <div class="btn-div">
                                    <button name="update" type="submit" class="btn btn-updt" id="Update">Actualizar</button>
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



  <!-- Popper.js (para tooltips y otros componentes) -->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <!-- Bootstrap Bundle (con Popper.js) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Complementos/Plugins-->
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <script src="assets/plugins/jquery-scrollbar/jquery.scrollbar.min.js" type="text/javascript"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js" type="text/javascript"></script>
  <!-- Scripts propios -->
  <script src="assets/js/support_ticket.js" type="text/javascript"></script>  
  <script src="assets/js/general.js" type="text/javascript"></script>
  <script src="assets/js/tasks.js" type="text/javascript"></script>
  <script src="assets/js/sidebar.js"></script>
</body>

</html>