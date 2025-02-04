<?php
session_start();
include("../checklogin.php");
include("../dbconnection.php");
include("assets/php/view-projects.php");
header('Content-Type: text/html; charset=utf-8');
check_login();
?>
<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <meta charset="utf-8" />
    <title>Proyectos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta content="" name="description" />
    <meta content="" name="author" />

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- CSS personalizados -->
    <link href="../assets/css/sidebar.css" rel="stylesheet" type="text/css" />
    <link href="../tickets/assets/css/manage_tickets.css" rel="stylesheet" type="text/css"/>
    <link href="assets/css/view-projects.css" rel="stylesheet" type="text/css"/>
    <!-- Toast notificaciones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
</head>

<body class="test" >
    <!-- Sidebar -->
  <div class="page-container ">

    <div class="sidebar">
    <?php include("../header-test.php"); ?>
      
    </div>
    <div class="page-content">
    <?php include("../leftbar-test.php"); ?>
        <div class="content">
            <div class="page-title d-flex justify-content-between">
                <h2>Proyectos por facturar</h2>
                <button class=" btn-back" onclick="window.location.href='projects-main.php';"> 
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
                        <div class="search-div d-flex">
                            <label class="form-label" >Buscar</labe>
                            <input type="text" class="form-control form-control-sm" id="textSearch" name="textSearch" placeholder="Nombre/ID del proyecto">
                        </div>
                        <div class="fil-div">
                            <label class="form-label" for="prio">Vertical</label>
                            <select name="verticalF" class="form-select form-select-sm" id="prio">
                                <option value="">Ver todo</option> 
                                <?php
                                foreach ($verticales as $row) {
                                    // Opcion para filtrar por vertical
                                    $selected = isset($_GET['verticalF']) && $_GET['verticalF'] == $row['id'] ? 'selected' : '';
                                    echo "<option value='" . $row['id'] . "' $selected>" . $row['nombre'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="fil-div">
                            <label class="form-label" for="prio">Distribuidor</label>
                            <select name="distribuidorF" class="form-select form-select-sm" id="prio">
                                <option value="">Ver todo</option> 
                                <?php
                                foreach ($distData as $row) {
                                    // Opcion para filtrar por vertical
                                    $selected = isset($_GET['distribuidorF']) && $_GET['distribuidorF'] == $row['id'] ? 'selected' : '';
                                    echo "<option value='" . $row['id'] . "' $selected>" . $row['nombre'] . "</option>";
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
                while ($row = mysqli_fetch_array($rt)) {?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="grid simple no-border">
                            <!-- Vista cerrada -->
                            <div class="grid-title no-border descriptive clickable">
                                <h4 class="semi-bold"><?php echo $row['nombre']; ?></h4>
                                <p>
                                    <span class="text-success bold">Proyecto #<?php echo $row['id']; ?></span> - Fecha de Creación <?php echo $row['fecha_creacion']; ?> 
                                    <?php
                                        if ($row['estado_id'] == '20') {
                                        ?>
                                        <span class="label label-success"><?php echo $row['estado']; ?></span>
                                        <?php
                                        }else if ($row['estado_id'] == '21'){
                                        ?>
                                        <span class="label label-important"><?php echo $row['estado']; ?></span>
                                        <?php
                                        }else{?>
                                            <span class="label label-warning"><?php echo $row['estado']; ?></span>
                                            <?php
                                        };
                                        
                                    ?>
                                    <span class="label label-success"><?php echo '$'.number_format($row['monto'], 0, '.', ',');?></span>
                                </p>
                                <div class="actions"> 
                                    <a class="view" href="javascript:;"><i class="bi bi-caret-down-fill"></i></a> 
                                </div>
                                <p>Ciudad: <span><?php echo $row['ciudadN']; ?></span></p>
                                <p>Distribuidor: <span><?php echo $row['distribuidorN']; ?></span></p>
                                <p><span>Ingeniero responsable</span>: <?php echo $row['ingeniero_responsable'] ? $row['ingeniero'] : "Sin asignar" ;?></p>

                            </div>

                            <!-- Vista completa -->
                            <div class="grid-body  no-border" style="display:none">
                            <hr>
                                <div class="post">
                                    <div class="info-wrapper"> 
                                        <div class="info d-flex">
                                            <div class="left-bar"></div>
                                            <div class="info">
                                                <h3 class="mb-3">Lista de materiales</h3>
                                            <?php
                                                $total = 0;
                                                $id =  $row['id'];
                                                $query = "SELECT * 
                                                            FROM bom
                                                            WHERE proyecto_id = $id";    
                                                $bom = $con->prepare($query);
                                                $bom->execute();
                                                $result = $bom->get_result();
                                                $num = $result->num_rows;
                                            if ($num > 0){
                                                while($material = $result->fetch_assoc() ){
                                                $total = $total + $material['total'];
                                            ?>       
                                                <div class="materials list-group-item">
                                                    <div><?php echo $material['nombre'];?></div>
                                                    <div><i class="bi bi-x-lg"></i></div>
                                                    <div><?php echo $material['cantidad'];?></div>
                                                    <div><?php echo '$'.number_format($material['total'], 0, '.', ',');;?></div>
                                                </div>
                                            <?php
                                                }
                                                echo "<div class='material-total'>Total: $".number_format($total, 0, '.', ',')."</div>";  
                                            }else{
                                                echo "<p>Aun no tiene materiales asociados</p>";
                                            }
                                            ?>
                                        </div>
                                        </div>
                                        <br>
                                        <div class="footer d-flex justify-content-between">
                                            <button id="billButton" class="btn btn-updt" data-bs-toggle="modal" data-bs-target="#closeModal" data-id="<?php echo $row['id']; ?>">Facturar</button>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                            <!------------------>
                        </div>
                    </div>
                </div>    
                <?php }
                } else { ?>
                    <h3 align="center" style="color:red;">Sin proyectos que mostrar</h3>
            <?php 
            } ?>

        </div>   
    </div>
  </div>



<!-- modal facturar proyecto -->
<div class="modal fade" id="closeModal" tabindex="-1" aria-labelledby="closeModalLabel" aria-hidden="true">
    <form name="form" id="endBtn" method="post">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="closeModalLabel"> Confirmación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body endModal">
                    <p class="text-center">El proyecto # sera facturado</p>
                    <input type="hidden" name="pId">
                </div>
                <div class="modal-footer">
                    <button type="submit" name="billBtn" class="btn btn-updt">Facturar</button>
                    <button type="button" class="btn btn-del" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </form>
</div>



<!-- Popper.js (para tooltips y otros componentes) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<!-- Bootstrap Bundle (con Popper.js) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<!-- Complementos/Plugins-->

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js" type="text/javascript"></script>
<!-- Scripts propios -->
<script src="../assets/js/support_ticket.js" type="text/javascript"></script>
<script src="../assets/js/general.js"></script>
<script src="../assets/js/sidebar.js"></script>
<script src="assets/js/bill-projects.js"></script>
</body>

</html>