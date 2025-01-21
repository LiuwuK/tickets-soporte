<?php
session_start();
include("checklogin.php");
include("dbconnection.php");
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
    <link href="assets/css/sidebar.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/manage_tickets.css" rel="stylesheet" type="text/css"/>
    <link href="assets/css/view-projects.css" rel="stylesheet" type="text/css"/>
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
                <h2>Proyectos por facturar</h2>
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
                            <input type="text" class="form-control form-control-sm" id="textSearch" name="textSearch" placeholder="Nombre/ID del ticket">
                        </div>
                        <div class="fil-div">
                            <label class="form-label" for="st">Estado</label>
                            <select name="statusF" class="form-select form-select-sm" id="st">
                                <option value="">Ver todo</option>    

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
                                    <span class="label label-success"><?php echo '$'.number_format($row['costo_real'], 0, '.', ',');?></span>
                                </p>
                                <div class="actions"> 
                                    <a class="view" href="javascript:;"><i class="bi bi-caret-down-fill"></i></a> 
                                </div>
                                <p>Ciudad: <span><?php echo $row['ciudadN']; ?></span></p>
                                <p><span>Ingeniero responsable</span>: <?php echo $row['ingeniero_responsable'] ? $row['ingeniero'] : "Sin asignar" ;?></p>

                            </div>

                            <!-- Vista completa -->
                            <div class="grid-body  no-border" style="display:none">
                            <hr>
                                <div class="post">
                                    <div class="info-wrapper"> 
                                        <div class="info d-flex">
                                            <div class="left-bar"></div>
                                            <div class="main-info">
                                                <!-- Descripcion del proyecto -->
                                                <div class="pr-row">
                                                    <div class="group">
                                                        <strong>Descripción</strong>
                                                        <p><?php echo $row['resumen']; ?></p>
                                                    </div>
                                                </div>
                                                <!-- Datos cliente/ingeniero/tipo/distribuidor -->
                                                <div class="pr-row ">
                                                    <div class="group">
                                                        <strong>Cliente</strong>
                                                        <p><?php echo $row['cliente'];?></p>
                                                    </div>
                                                </div>
                                                <div class="pr-row"> 
                                                    <div class="group">
                                                        <strong>Distribuidor</strong>
                                                        <?php
                                                            if(isset($row['distribuidor'])){
                                                                foreach ($distData as $row_dist) {
                                                                  if ($row_dist['id'] == $row['distribuidor']) {
                                                                      echo "<p>".$row_dist['nombre']."</p>";
                                                                  } 
                                                                }
                                                            }else{
                                                                echo "<p> Sin asignar </p>";
                                                            }
                                                        ?>
                                                    </div> 
                                                </div>
                                                <div class="pr-row"> 
                                                    <div class="group">
                                                        <strong>Vertical</strong>
                                                        <?php
                                                            if(isset($row['vertical'])){
                                                                foreach ($verticales as $row_vertical) {
                                                                  if ($row_vertical['id'] == $row['vertical']) {
                                                                      echo "<p>".$row_vertical['nombre']."</p>";
                                                                  } 
                                                                }
                                                            }else{
                                                                echo "<p> Sin asignar </p>";
                                                            }
                                                        ?>
                                                    </div> 
                                                </div>
                                                <div class="pr-row">
                                                    <div class="group">
                                                        <strong>Comercial responsable</strong>
                                                        <p><?php echo $row['comercial'];?></p>
                                                    </div>
                                                </div>
                                                <div class="pr-row">
                                                    <div class="group">
                                                        <strong>Tipo Proyecto</strong>
                                                        <p><?php echo $row['tipoP'];?></p>
                                                    </div>
                                                </div>
                                                <!-- Datos de tipo proyecto (contacto/licitacion)  -->
                                                <div class="pr-row">
                                                    <strong>Datos de <?php echo $row['tipoP'];?></strong>   

                                                <?php
                                                    //Licitacion
                                                    if($row['tipo'] == '1' ){
                                                        $id =  $row['projectId'];
                                                        $query = "SELECT * 
                                                                    FROM licitacion_proyecto
                                                                    WHERE proyecto_id = $id";    
                                                        $licitacion = $con->prepare($query);
                                                        $licitacion->execute();
                                                        $result = $licitacion->get_result();
                                                        $row_lt = $result->fetch_assoc();
                                                        $licitacion->close();
                                                        ?>
                                                    <div class="group lic d-flex">
                                                        <strong class="form-label">ID Licitación</strong>
                                                        <p>: <?php echo $row_lt['licitacion_id'];?></p>
                                                    </div>      
                                                    <div class="group lic d-flex">                                                        
                                                        <strong>Portal </strong>
                                                        <p>: <?php echo$row_lt['portal'];?></p>
                                                    </div>                                                    
                                                <?php
                                                    //Contacto 
                                                    }else if($row['tipo'] == '2'){
                                                        $id =  $row['projectId'];
                                                        $query = "SELECT * 
                                                                    FROM contactos_proyecto
                                                                    WHERE proyecto_id = $id";    
                                                        $contacto = $con->prepare($query);
                                                        $contacto->execute();
                                                        $result = $contacto->get_result();
                                                        $row_ct = $result->fetch_assoc();
                                                        $contacto->close();
                                                    ?>
                                                    <div class="pr-row">
                                                        <div class="group d-flex cnt">
                                                            <strong class="form-label">Nombre</strong>
                                                            <p>: <?php echo $row_ct['nombre'];?></p>
                                                        </div>

                                                        <div class="group d-flex cnt">
                                                            <strong class="form-label">Correo</strong>
                                                            <p>: <?php echo $row_ct['correo'];?></p>
                                                        </div>
                                                        <div class="group d-flex cnt">
                                                            <strong class="form-label">Cargo</strong>
                                                            <p>: <?php echo $row_ct['cargo'];?></p>
                                                        </div>

                                                        <div class="group d-flex cnt">
                                                            <strong class="form-label">Numero Contacto</strong>
                                                            <p>: <?php echo $row_ct['numero'];?></p>
                                                        </div>
                                                    </div>    
                                                <?php 
                                                    }?>
                                                </div>

                                                <div class="pr-row">
                                                    <div class="group">
                                                        <strong>Actividades</strong>
                                                        <ul>
                                                        <?php 
                                                            $id =  $row['projectId'];
                                                            $query = "SELECT * 
                                                                        FROM actividades
                                                                        WHERE proyecto_id = $id";    
                                                            $actividades = $con->prepare($query);
                                                            $actividades->execute();
                                                            $result = $actividades->get_result();
                                                            $act_num = $result->num_rows;
                                                            if($act_num < 0 ){
                                                                while ($row_ac = $result->fetch_assoc()) {
                                                                    $fecha_original = $row_ac['fecha']; 
                                                                    setlocale(LC_TIME, 'es_ES.UTF-8', 'spanish');
                                                                    // Formatear la fecha
                                                                    $timestamp = strtotime($fecha_original);
                                                                    $fecha = strftime('%e de %B %Y', $timestamp);
                                                                ?>
                                                                    <li><?php echo $row_ac['nombre'];?> -- <?php echo $fecha;?></li>    
                                                                <?php }
                                                            }else{
                                                                echo "<li>Sin actividades asignadas</li>";
                                                            }
                                                        ?>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <?php 
                                                    if( $row['clasificacion'] == '1' ){ ?>
                                                        <div class="pr-row">
                                                            <strong>Gastos</strong>
                                                            <div class="group d-flex lic">
                                                                <strong>Software</strong>
                                                                <p>: $<?php echo $row['costo_software'];?></p>
                                                            </div>
                                                            <div class="group d-flex lic">
                                                                <strong>Hardware</strong>
                                                                <p>: $<?php echo $row['costo_hardware'];?></p>
                                                            </div>
                                                        </div>
                                                <?php } 
                                                ?>

                                                <div class="pr-row">
                                                    <div class="group">
                                                        <strong>Monto Estimado</strong>
                                                        <p><?php echo '$'.number_format($row['monto'], 0, '.', ',');?></p>
                                                    </div>
                                                </div>

                                                <div class="pr-row">
                                                    <div class="group">
                                                        <strong>Costo Real</strong>
                                                        <p><?php echo '$'.number_format($row['costo_real'], 0, '.', ','); ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="footer d-flex justify-content-between">
                                            <button id="billButton" class="btn btn-updt" data-id="<?php echo $row['projectId']; ?>">Facturar</button>
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

<script>

</script>




<!-- Popper.js (para tooltips y otros componentes) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<!-- Bootstrap Bundle (con Popper.js) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<!-- Complementos/Plugins-->

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js" type="text/javascript"></script>
<!-- Scripts propios -->
<script src="assets/js/support_ticket.js" type="text/javascript"></script>
<script src="assets/js/general.js"></script>
<script src="assets/js/sidebar.js"></script>
</body>

</html>