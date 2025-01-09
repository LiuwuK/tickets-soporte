<?php
session_start();
include("../checklogin.php");
include("../dbconnection.php");
include("../assets/php/view-tickets.php");
header('Content-Type: text/html; charset=utf-8');
check_login();
?>
<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <meta charset="utf-8" />
    <title>Tickets </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta content="" name="description" />
    <meta content="" name="author" />

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- CSS personalizados -->
    <link href="assets/css/sidebar.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/manage_tickets.css" rel="stylesheet" type="text/css"/>
    <!-- Toast notificaciones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
</head>

<body class="test" >
    <!-- Sidebar -->
  <div class="page-container ">

    <div class="sidebar">
    <?php include("header-test.php"); ?>
      
    </div>
    <div class="page-content">
    <?php include("leftbar-test.php"); ?>
        <div class="content">
            <div class="page-title">
                <h2>Ver tickets</h2>
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
                                while ($row = mysqli_fetch_assoc($prioridad)) {
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



            <?php 
            if ($num > 0) {
                while ($row = mysqli_fetch_array($rt)) {
            ?>
                <div class="row">
                    <div class="col-md-12">
                    <div class="grid simple no-border">
                        <div class="grid-title no-border descriptive clickable">
                        <h4 class="semi-bold"><?php echo $row['subject']; ?></h4>
                        <p><span class="text-success bold">Ticket #<?php echo $row['ticketId']; ?></span> - Fecha de Creación <?php echo $row['posting_date']; ?> 
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
                        </p>
                        <div class="actions"> <a class="view" href="javascript:;"><i class="bi bi-caret-down-fill"></i></a> </div>
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

                        <?php if ($row['admin_remark'] != '') : ?>
                            <div class="form-actions">
                            <div class="post col-md-12">
                                <div class="user-profile-pic-wrapper">
                                <div class="user-profile-pic-normal"> <img width="35" height="35" data-src-retina="../assets/img/admin.jpg" data-src="../assets/img/admin.jpg" src="../assets/img/admin.jpg" alt="Admin"> </div>
                                </div>
                                <div class="info-wrapper">
                                <br>
                                <div class="comm">
                                    <h4>Procedimiento a seguir</h4>
                                    <div>
                                    <ul>
                                        <?php
                                        //Obtener las task asociadas al ticket
                                        $tkid = $row['ticketId'];
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
                                    <p><?php echo $row['admin_remark']; ?></p>
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
<script src="../assets/plugins/jquery-scrollbar/jquery.scrollbar.min.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js" type="text/javascript"></script>
<!-- Scripts propios -->
<script src="../assets/js/support_ticket.js" type="text/javascript"></script>
<script src="../assets/js/general.js"></script>
<script src="../assets/js/sidebar.js"></script>
</body>

</html>