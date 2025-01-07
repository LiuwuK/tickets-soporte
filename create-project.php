<?php
session_start();
include("checklogin.php");
include("dbconnection.php");
include("admin/notificaciones.php");
include("assets/php/create-project.php");

check_login();
?>
<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
  <meta charset="utf-8" />
  <title>Projects </title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <meta content="" name="description" />
  <meta content="" name="author" />
  
<!-- Estilos base -->
<link href="assets/plugins/pace/pace-theme-flash.css" rel="stylesheet" type="text/css" media="screen" />
<link href="assets/plugins/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css" />
<link href="assets/css/animate.min.css" rel="stylesheet" type="text/css" />
<link href="assets/plugins/jquery-scrollbar/jquery.scrollbar.css" rel="stylesheet" type="text/css" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

<!-- css -->
<link href="assets/css/style.css" rel="stylesheet" type="text/css" />
<link href="assets/css/responsive.css" rel="stylesheet" type="text/css" />
<link href="assets/css/custom-icon-set.css" rel="stylesheet" type="text/css" />
<link href="assets/css/create-project.css" rel="stylesheet" type="text/css" />

<!-- toast notificaciones -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
</head>

<body class="" >
  <?php include("header.php"); ?>
  <div class="page-container row-fluid">
    <?php include("leftbar.php"); ?>
    <div class="clearfix"></div>
  </div>
  </div>
  <div class="page-content">
    <div class="clearfix"></div>
    <div class="content">
      <div class="page-title">
        <h3>Crear Proyecto</h3>
      </div>
      <form name="newProject" id="newProject" method="post">
        <div class="project-main" >  <br><br>      
            <div class="form-row">
              <div class="form-group">
                <label for="name" class="control-label">Nombre Proyecto</label>
                <input type="text" class="form-control rounded-0" id="name" name="name" required="required">
              </div>
              <div class="form-group">
                <label for="client" class="control-label">Cliente</label>
                <input type="text" class="form-control rounded-0" id="client" name="client" required="required">          
              </div>
            </div>
            <div class="form-row">
              <div class ="form-group">
                <label>Tipo de proyecto</label>
                <div>
                  <select name="type" id="pType" class="form-control select" required>
                    <option value="">Seleccionar</option> 
                    <?php
                      while ($row = mysqli_fetch_assoc($types)) {
                          echo "<option value=".$row['id'].">".$row['nombre'] ."</option>";
                      };
                    ?>
                  </select>
                </div>
              </div> 
              
            <div class ="form-group">
              <label>Clasificación</label>
              <div>
                <select name="sorting" id="pClass" class="form-control select" required>
                  <option value="">Seleccionar</option>
                  <?php
                    while ($row = mysqli_fetch_assoc($class)) {
                        echo "<option value=".$row['id'].">".$row['nombre'] ."</option>";
                    };
                  ?>
                </select>
              </div>
            </div>
            </div>
            <!-- datos de licitacion y contacto -->
            <div class="form-row"  id="licitacion">
                  <div class="form-group">
                    <strong>Datos Licitación</strong>
                    <label for="licID" class="control-label">ID licitación</label>
                    <input type="text" class="form-control rounded-0" id="licID" name="licID" >
                  </div>
            </div>
            
            <div class="form-row" id="contacto">
                <strong>Datos de Contacto</strong>
            </div>
            <div class="form-row" id="contacto">
                    <div class="form-group">
                      <label for="cName" class="control-label">Nombre</label>
                      <input type="text" class="form-control rounded-0" id="cName" name="cName" >

                      <label for="cEmail" class="control-label">Email </label>
                      <input type="email" class="form-control rounded-0" id="cEmail" name="cEmail" >
                    </div>

                    <div class="form-group">
                      <label for="cargo" class="control-label">Cargo</label>
                      <input type="text" class="form-control rounded-0" id="cargo" name="cargo" >

                      <label for="cNumero" class="control-label">Numero de contacto </label>
                      <input type="text" class="form-control rounded-0" id="cNumero" name="cNumero" >
                    </div>
            
            </div>
            <!--  -->
            <div class="form-row">
              <div class ="form-group">
                <label>Ciudad</label>
                <div >
                  <select name="city" class="form-control select" required>
                      <option value="">Seleccionar</option>
                      <?php
                        while ($row = mysqli_fetch_assoc($cities)) {
                            echo "<option value=".$row['id'].">".$row['nombre_ciudad'] ."</option>";
                        };
                      ?>
                  </select>
                </div>
              </div> 
              
            <div class ="form-group">
              <label>Estatus</label>
              <div >
                <select name="status" class="form-control select" required>
                    <?php
                      while ($row = mysqli_fetch_assoc($status)) {
                          echo "<option value=". $row['id'].">".$row['nombre'] ."</option>";
                      };
                    ?>
                </select>
              </div>
            </div>
            </div>
            <div class="form-row">
              <div class ="form-group">
                <label for="comercial">Comercial responsable</label>
                <input type="text" name="comercial" id="comercial" value="<?php echo $_SESSION['name']; ?>" disabled>
              </div> 
              <div class ="form-group">
                <label>Ingeniero responsable</label>
                <div >
                  <select name="ingeniero" class="form-control select" >
                      <option value="">Seleccionar</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label for="dist" class="control-label">Distribuidor</label>
                <input type="text" class="form-control rounded-0" id="dist" name="dist" required="required">          
              </div>
              <div class="form-group">
                <label for="monto" class="control-label">Monto Proyecto</label>
                <input type="text" class="form-control rounded-0" id="monto" name="monto">          
              </div>
            </div>
            <div class="form-row" style="display:none" id="classInfo">
                <div class="expenses">
                  <label for="soft" class="control-label">Software</label>
                  <input type="checkbox" id="software" name="software">
                  <input type="text" id="software-input" name="software-input" class="hidden" placeholder="USD 0">
                </div>

                <div class="expenses">
                  <label for="hard" class="control-label">Hardware</label>
                  <input type="checkbox" id="hardware" name="hardware">   
                  <input type="text" id="hardware-input" name="hardware-input" class="hidden" placeholder="USD 0">
                </div>
            </div>
            <div class="form-row">
              <div class="expenses">
                <label for="bom" class="control-label">BOM</label>
                <input type="checkbox" id="bom" name="bom">
                <input type="file" id="bom-input" name="bom-input" class="hidden" placeholder="BOM">
              </div>
            </div>  
            <div class="form-row">
              <div class="form-group">
                <div class="label-container">
                  <label class="control-label">Actividades</label>
                  <button type="button" class="btn btn-primary btn-add-task" data-bs-toggle="modal" data-bs-target="#actividadModal">
                    <i class="fa fa-plus"></i> 
                  </button>
                </div>  
                <div id="events-list">
                  <ul id="listadoActividades" class="list-group"></ul>
                </div>
              </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                  <label for="desc" class="control-label">Resumen</labe>
                  <textarea class="form-control rounded-0" id="desc" name="desc" ></textarea>     
                </div>
            </div>       
            <div class="footer">
                <button class="btn btn-default">Resetear</button>
                <button type="submit" id="newProject" name="newProject" class="btn btn-primary pull-right">Crear</button>
            </div>
        </div>
      </form>
      
    </div>   
    

<!-- Modal actividades -->
<div class="modal fade" id="actividadModal" tabindex="-1" aria-labelledby="actividadModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="actividadModalLabel">Agregar Actividad</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formActividad">
          <div class="mb-3">
            <label for="nombreActividad" class="form-label">Nombre de la Actividad</label>
            <input type="text" class="form-control" id="nombreActividad" name="nombreActividad" required>
          </div>
          <div class="mb-3">
            <label for="fechaActividad" class="form-label">Fecha</label>
            <input type="date" class="form-control" id="fechaActividad" name="fechaActividad" required>
          </div>
          <div class="mb-3">
            <label for="descripcionActividad" class="form-label">Descripción</label>
            <textarea class="form-control" id="descripcionActividad" name="descripcionActividad" rows="3"></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <button type="submit" form="formActividad" class="btn btn-primary">Agregar</button>
      </div>
    </div>
  </div>
</div>

  <br><br>
  </div>
  
  </div>
  </div>
  
  <!-- Biblioteca base -->
<script src="assets/plugins/jquery-1.8.3.min.js" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<!-- Complementos/Plugins -->
<script src="assets/plugins/jquery-ui/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
<script src="assets/plugins/breakpoints.js" type="text/javascript"></script>
<script src="assets/plugins/jquery-unveil/jquery.unveil.min.js" type="text/javascript"></script>
<script src="assets/plugins/jquery-block-ui/jqueryblockui.js" type="text/javascript"></script>
<script src="assets/plugins/jquery-scrollbar/jquery.scrollbar.min.js" type="text/javascript"></script>
<script src="assets/plugins/pace/pace.min.js" type="text/javascript"></script>
<script src="assets/plugins/jquery-numberAnimate/jquery.animateNumbers.js" type="text/javascript"></script>

<!-- Scripts propios -->
<script src="assets/js/core.js" type="text/javascript"></script>
<script src="assets/js/chat.js" type="text/javascript"></script>
<script src="assets/js/demo.js" type="text/javascript"></script>
<script src="assets/js/calendar.js" type="text/javascript"></script>
<script src="assets/js/create-project.js" type="text/javascript"></script>

</body>

</html>