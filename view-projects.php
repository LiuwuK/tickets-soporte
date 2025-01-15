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
                <h2>Proyectos</h2>
            </div>
            <br>
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
                                    <span class="label label-success"><?php echo $row['estado']; ?></span>
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
                                        <div class="info">
                                            <!-- Descripcion del proyecto -->
                                            <div class="pr-row">
                                                <div class="group">
                                                    <strong>Descripción</strong>
                                                    <p><?php echo $row['resumen']; ?></p>
                                                </div>
                                            </div>
                                            <!-- Datos cliente/ingeniero/tipo/distribuidor -->
                                            <div class="pr-row d-flex flex-row justify-content-between">
                                                <div class="group">
                                                    <strong>Cliente</strong>
                                                    <p><?php echo $row['cliente'];?></p>
                                                </div>
                                                <div class="group">
                                                    <strong>Tipo Proyecto</strong>
                                                    <p><?php echo $row['tipoP'];?></p>
                                                </div>
                                                <div class="group">
                                                    <strong>Distribuidor</strong>
                                                    <p><?php echo $row['distribuidor'];?></p>
                                                </div>
                                            </div>
                                            <!-- Datos de tipo proyecto (contacto/licitacion)  -->
                                            <div class="pr-row">
                                                <strong>Datos de <?php echo $row['tipoP'];?></strong>   
                                                <hr>
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
                                                <div class="group">
                                                    <label class="form-label">ID Licitación</label>
                                                    <p><?php echo $row_lt['licitacion_id'];?></p>
                                                    
                                                    <label class="form-label">Portal</label>
                                                    <p><?php echo $row_lt['portal'];?></p>
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
                                                <div class=" d-flex flex-row justify-content-evenly">
                                                    <div class="group">
                                                        <label class="form-label">Nombre</label>
                                                        <p><?php echo $row_ct['nombre'];?></p>

                                                        <label class="form-label">Correo</label>
                                                        <p><?php echo $row_ct['correo'];?></p>
                                                    </div>
                                                    <div class="group">
                                                        <label class="form-label">Cargo</label>
                                                        <p><?php echo $row_ct['cargo'];?></p>

                                                        <label class="form-label">Numero Contacto</label>
                                                        <p><?php echo $row_ct['numero'];?></p>
                                                    </div>
                                                </div>    
                                            <?php 
                                                }?>
                                            </div>

                                            <div class="pr-row">
                                                <div class="group">
                                                    <strong>Actividades</strong>
                                                    <hr>
                                                    <ul>
                                                    <?php 
                                                        $id =  $row['projectId'];
                                                        $query = "SELECT * 
                                                                    FROM actividades
                                                                    WHERE proyecto_id = $id";    
                                                        $actividades = $con->prepare($query);
                                                        $actividades->execute();
                                                        $result = $actividades->get_result();
                                                        
                                                        while ($row_ac = $result->fetch_assoc()) {?>
                                                            <li><?php echo $row_ac['nombre'];?> -- <?php echo $row_ac['fecha'];?></li>    
                                                        <?php }
                                                    ?>
                                                    </ul>
                                                </div>
                                            </div>
                                            <?php 
                                                if( $row['clasificacion'] == '1' ){ ?>
                                                    <div class="pr-row">
                                                        <strong>Gastos</strong>
                                                        <div class="group">
                                                            <ul>
                                                                <li>Software : <?php echo $row['costo_software'];?></li>
                                                                <li>Hardware : <?php echo $row['costo_hardware'];?></li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                            <?php } 
                                            ?>

                                            <div class="pr-row">
                                                <div class="group">
                                                    <strong>Monto</strong>
                                                    <p>$<?php echo $row['monto'];?></p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="footer d-flex">
                                            <!-- <button class="btn btn-updt">Actualizar</button> -->
                                        </div>
                                    </div>
                                    
                                </div>

                            <br>    
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






<!-- Popper.js (para tooltips y otros componentes) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<!-- Bootstrap Bundle (con Popper.js) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<!-- Complementos/Plugins-->
<script src="assets/plugins/jquery-scrollbar/jquery.scrollbar.min.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js" type="text/javascript"></script>
<!-- Scripts propios -->
<script src="assets/js/support_ticket.js" type="text/javascript"></script>
<script src="assets/js/general.js"></script>
<script src="assets/js/sidebar.js"></script>
</body>

</html>