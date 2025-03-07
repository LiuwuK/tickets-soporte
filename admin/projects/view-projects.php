<?php
session_start();
include("../../checklogin.php");
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
    <link href="../../assets/css/sidebar.css" rel="stylesheet" type="text/css" />
    <link href="../../tickets/assets/css/manage_tickets.css" rel="stylesheet" type="text/css"/>
    <link href="../../projects/assets/css/view-projects.css" rel="stylesheet" type="text/css"/>
    <!-- Toast notificaciones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
</head>

<body class="test" >
    <!-- Sidebar -->
  <div class="page-container ">

    <div class="sidebar">
    <?php include("../header.php");?>
    </div>
    <div class="page-content">
    <?php include("../leftbar.php");?>
        <div class="content">
            <div class="page-title d-flex justify-content-between">
                <h2>
                    <i class="bi bi-clipboard2"></i>
                    Proyectos
                </h2>
                <button class=" btn-back" onclick="window.location.href='project-main.php';"> 
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
                        <div class="fil-div">
                            <label class="form-label" for="st">Portal</label>
                            <select name="portalF" class="form-select form-select-sm" id="st">
                                <option value="">Ver todo</option>    
                                <?php
                                foreach($portal AS $pt) {
                                    $select = isset($_GET['portalF']) && $_GET['portalF'] == $pt['id'] ? 'selected' : '';
                                    echo "<option value='" . $pt['id'] . "' $select>" . $pt['nombre_portal'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="fil-div">
                            <label class="form-label" for="st">Tipo de Proyecto</label>
                            <select name="tipoprjF" class="form-select form-select-sm" id="tipoprjF">
                                <option value="">Ver todo</option>    
                                <?php
                                foreach($tipoProyecto AS $tprj) {
                                    $select = isset($_GET['tipoprjF']) && $_GET['tipoprjF'] == $tprj['id'] ? 'selected' : '';
                                    echo "<option value='" . $tprj['id'] . "' $select>" . $tprj['nombre'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="fil-div">
                            <label class="form-label" for="st">Clasificación</label>
                            <select name="clasif" class="form-select form-select-sm" id="clasif">
                                <option value="">Ver todo</option>    
                                <?php
                                foreach($clasif AS $cl) {
                                    $select = isset($_GET['clasif']) && $_GET['clasif'] == $cl['id'] ? 'selected' : '';
                                    echo "<option value='" . $cl['id'] . "' $select>" . $cl['nombre'] . "</option>";
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
                                <p>
                                    <strong>Fecha de Cierre Documental</strong>:<span><?php echo !empty($row['fecha_cierre_documental']) ? $row['fecha_cierre_documental'] : 'Sin Asignar';?></span> 
                                    <strong style="margin-left:5px">Fecha Adjudicacion</strong>:<span><?php echo !empty($row['fecha_adjudicacion']) ? $row['fecha_adjudicacion'] : 'Sin Asignar';?></span> 
                                </p>
                                <p>
                                    <strong>Ciudad</strong>:<span><?php echo $row['ciudadN'];?></span> 
                                    <strong style="margin-left:5px">Cliente</strong>:<span><?php echo !empty($row['clienteN']) ? $row['clienteN'] : 'Sin Asignar'; ?></span>
                                </p>
                                <form  name="asignarIng" id="asignarIng" method="post">
                                    <div class="ing-main d-flex justify-content-start">
                                        <p><span>Ingeniero responsable</span></p>
                                        <select id="ingenieroSelect" name="ingeniero"  class="ingeniero-select form-select form-select-sm" data-initial-value="<?php echo $row['ingeniero_responsable'] ? $row['ingeniero_responsable'] : "Sin asignar" ;?>">
                                            <?php
                                            if (empty($row['ingeniero_responsable'])) {
                                                echo "<option selected>Sin asignar</option>";
                                            }
                                            foreach ($ingenieros as $row_inge) {
                                                if ($row_inge['id'] == $row['ingeniero_responsable']) {
                                                    echo "<option value=\"".$row_inge['id']."\" selected>".$row_inge['name']."</option>";
                                                } else {
                                                    echo "<option value=\"".$row_inge['id']."\">".$row_inge['name']."</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                        <input type="hidden" id="pId" name="pId" value="<?php echo $row['id']; ?>">
                                        <button type="submit" class="btn btn-updt save-button" name="asignarIng" style="display:none">Asignar</button>
                                        
                                    </div>
                                </form>
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
                                                <!-- Datos cliente/ingeniero/tipo/distribuidor/vertical -->
                                                <div class="pr-row ">
                                                    <div class="group">
                                                        <strong>Cliente</strong>
                                                        <?php
                                                            if(isset($row['cliente'])){
                                                                foreach ($clients as $row_client) {
                                                                  if ($row_client['id'] == $row['cliente']) {
                                                                      echo "<p>".$row_client['nombre']."</p>";
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
                                                <?php
                                                if($row['tipo'] != 3){
                                                    echo '<div class="pr-row"><strong>Datos de '.$row['tipoP'].'</strong>';
                                                    //Licitacion
                                                    if($row['tipo'] == '1' ){
                                                        $id =  $row['projectId'];
                                                        $query = "SELECT lic.*, pt.nombre_portal AS portalN
                                                                    FROM licitacion_proyecto lic
                                                                    LEFT JOIN portales pt ON(lic.portal = pt.id)
                                                                    WHERE proyecto_id = $id";    
                                                        $licitacion = $con->prepare($query);
                                                        $licitacion->execute();
                                                        $result = $licitacion->get_result();
                                                        $row_lt = $result->fetch_assoc();
                                                        $licitacion->close();
                                                        ?>
                                                    <div class="group lic d-flex">
                                                        <strong class="form-label">ID Licitación</strong>
                                                        <p>: <?php echo !empty($row_lt['licitacion_id']) ? $row_lt['licitacion_id'] : 'Sin asignar';?></p>
                                                    </div>      
                                                    <div class="group lic d-flex">                                                        
                                                        <strong>Portal </strong>
                                                        <p>: <?php echo !empty($row_lt['portalN']) ? $row_lt['portalN'] : 'Sin asignar';?></p>
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
                                                        $numCt = $result->num_rows;
                                                        $contactos = [];
                                                        while ($row_ct = $result->fetch_assoc()) {
                                                            $contactos[] = $row_ct; 
                                                        }
                                                    ?>
                                                    <div class="pr-row d-flex">
                                                        <?php
                                                            if ($numCt > 0 ){ 
                                                                foreach ($contactos as $contacto) {
                                                            ?>
                                                            <div class="group d-flex cnt card p-2">
                                                                <div class="cnt-div d-flex">
                                                                    <strong class="form-label">Nombre </strong>
                                                                    <p>: <?php echo $contacto['nombre'];?></p>
                                                                </div>

                                                                <div class="cnt-div d-flex">
                                                                    <strong class="form-label">Correo </strong>
                                                                    <p>: <?php echo $contacto['correo'];?></p>
                                                                </div >

                                                                <div class="cnt-div d-flex">
                                                                    <strong class="form-label">Cargo </strong>
                                                                    <p>: <?php echo $contacto['cargo'];?></p>
                                                                </div>

                                                                <div class="cnt-div d-flex">
                                                                    <strong class="form-label">Contacto </strong>
                                                                    <p>: <?php echo $contacto['numero'];?></p>
                                                                </div>
                                                            </div>
                                                            <?php       
                                                                }
                                                            }else{
                                                                echo "<p>No hay contactos registrados</p>";
                                                            }
                                                        ?>
                                                    </div>    
                                                <?php 
                                                    }
                                                    echo '</div>';
                                                }
                                                ?>

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
                                                            $num = $result->num_rows;
                                                            
                                                            if($num > 0){
                                                                while ($row_ac = $result->fetch_assoc()) {
                                                                    $fecha_original = $row_ac['fecha_inicio']; 
                                                                    setlocale(LC_TIME, 'es_ES.UTF-8', 'spanish');
                                                                    // Formatear la fecha
                                                                    $timestamp = strtotime($fecha_original);
                                                                    $fecha = strftime('%e de %B %Y, %H:%M', $timestamp);
                                                                ?>
                                                                    <li><?php echo $row_ac['nombre'];?> -- <?php echo $fecha;?></li>    
                                                                <?php }
                                                            }else{
                                                                echo "<p> Sin tareas asignadas </p>";
                                                            }
                                                        ?>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="pr-row">
                                                    <div class="group">
                                                        <strong>Lista de materiales (BOM)</strong>
                                                        <ul>
                                                        <?php 
                                                            $total = 0;
                                                            $id =  $row['projectId'];
                                                            $query = "SELECT * 
                                                                        FROM bom
                                                                        WHERE proyecto_id = $id";    
                                                            $actividades = $con->prepare($query);
                                                            $actividades->execute();
                                                            $result = $actividades->get_result();
                                                            $num = $result->num_rows;
                                                            
                                                            if($num > 0){
                                                                while ($row_bom = $result->fetch_assoc()) {
                                                                    $total = $total +  $row_bom['total'];
                                                                ?>
                                                                   <div class="material-item">
                                                                        <div>
                                                                            <li> <?php echo $row_bom['nombre'];?></li>   
                                                                        </div>
                                                                        <div>
                                                                            <i class="bi bi-x-lg"></i>
                                                                        </div> 
                                                                        <div>
                                                                            <p><?php echo $row_bom['cantidad']?></p>
                                                                        </div>
                                                                        <div>
                                                                            <p>
                                                                                <?php echo '$'.number_format($row_bom['total'], 0, '.', ','); ?>
                                                                            </p>
                                                                        </div>
                                                                   </div>
                                                                <?php }
                                                            }else{
                                                                echo "<p> No se le han asignado materiales</p>";
                                                            }
                                                        ?>
                                                        </ul>
                                                    </div>
                                                    <div class="group">
                                                        <strong>Total BOM</strong>
                                                        <?php
                                                        if($total > 0){
                                                            echo '<p> $'.number_format($total, 0, '.', ',').'</p>'; 
                                                        }else{
                                                            echo "<p> No se le han asignado materiales</p>";
                                                        } 
                                                        ?>
                                                    </div>
                                                </div>
                                                <!--
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
                                                -->
                                                <div class="pr-row">
                                                    <div class="group">
                                                        <strong>Monto Proyecto</strong>
                                                        <p> <span><?php echo '$'.number_format($row['monto'], 0, '.', ',');?></span></p>
                                                    </div>
                                                </div>
                                                <!--
                                                <div class="pr-row">
                                                    <div class="group">
                                                        <strong>Costo Real</strong>
                                                        <p> <span><?php echo '$'.number_format($row['costo_real'], 0, '.', ',');?></span></p>
                                                    </div>
                                                </div>
                                                -->
                                            </div>
                                        </div>
                                        <br>
                                        <div class="footer d-flex justify-content-between">
                                            <button id="editButton" class="btn btn-updt" data-id="<?php echo $row['projectId']; ?>">Editar</button>
                                            <button type="button" class="btn btn-del" data-bs-toggle="modal" data-bs-target="#closeModal" data-pid="<?php echo $row['id']; ?>">Cerrar</button>
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
<!-- Modal cerrar proyecto -->
<div class="modal fade" id="closeModal" tabindex="-1" aria-labelledby="closeModalLabel" aria-hidden="true">
    <form name="endBtn" id="endBtn" method="post">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="closeModalLabel">Seleccionar Estado del Proyecto</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body endModal">
            <label for="estado">Seleccionar estado:</label>
            <select name="estado" id="estado" class="form-select form-select-sm">
                <option value="20">Ganado</option>
                <option value="21">Perdido</option>
            </select>
            <input type="hidden" name="pId">
        </div>
        <div class="modal-footer">
            <button type="submit" name="endBtn" class="btn btn-updt">Cerrar Proyecto</button>
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
<script src="../../assets/js/support_ticket.js" type="text/javascript"></script>
<script src="../../assets/js/general.js"></script>
<script src="../../projects/assets/js/view-projects.js"></script>
<script src="../../assets/js/sidebar.js"></script>

</body>

</html>