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

<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<!-- CSS personalizados -->
<link href="assets/css/sidebar.css" rel="stylesheet" type="text/css" />
<link href="assets/css/custom-icon-set.css" rel="stylesheet" type="text/css" />
<link href="assets/css/create-project.css" rel="stylesheet" type="text/css" />

<!-- Toast notificaciones -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
</head>

<body class="test" >
    <!-- Sidebar -->
  <div class="page-container ">

    <div class="sidebar">
      <?php include("leftbar copy.php"); ?>
    </div>
    <div class="page-content">
        <div class="content">
            <div class="page-title">
                <h2>Crear Proyecto</h2>
            </div>
            <!-- Formulario crear proyectos -->
            <form name="newProject" id="newProject" method="post">
                <div class="project-main" >  <br><br>      
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name" class="form-label">Nombre Proyecto</label>
                            <input type="text" class="form-control form-control-sm" id="name" name="name" required="required">
                        </div>
                        <div class="form-group">
                            <label for="client" class="form-label">Cliente</label>
                            <input type="text" class="form-control form-control-sm" id="client" name="client" required="required">          
                        </div>
                    </div>
                    <div class="form-row">
                        <div class ="form-group">
                            <label class="form-label">Tipo de proyecto</label>
                            <div>
                            <select name="pType" id="pType" class="form-select form-select-sm" required>
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
                        <label class="form-label">Clasificación</label>
                        <div>
                            <select name="pClass" id="pClass" class="form-select form-select-sm" required>
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
                    <div class="form-row"  id="licitacion" style="display: none;">
                        <div class="form-group">
                            <strong>Datos Licitación</strong>
                            <label for="licID" class="form-label">ID licitación</label>
                            <input type="text" class="form-control form-control-sm" id="licID" name="licID" >
                        </div>
                    </div>            
                    <div class="form-row" id="contactoT" style="display: none;">
                        <strong>Datos de Contacto</strong>
                    </div>
                    <div class="form-row" id="contacto" style="display: none;">
                            <div class="form-group">
                            <label for="cName" class="form-label">Nombre</label>
                            <input type="text" class="form-control form-control-sm" id="cName" name="cName" >

                            <label for="cEmail" class="form-label">Email </label>
                            <input type="email" class="form-control form-control-sm" id="cEmail" name="cEmail" >
                            </div>

                            <div class="form-group">
                            <label for="cargo" class="form-label">Cargo</label>
                            <input type="text" class="form-control form-control-sm" id="cargo" name="cargo" >

                            <label for="cNumero" class="form-label">Numero de contacto </label>
                            <input type="text" class="form-control form-control-sm" id="cNumero" name="cNumero" >
                            </div>
                    
                    </div>
                    <div class="form-row">
                    <div class ="form-group">
                        <label class="form-label">Ciudad</label>
                        <div >
                        <select name="city" class="form-select form-select-sm" required>
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
                    <label class="form-label">Estatus</label>
                    <div >
                        <select name="status" class="form-select form-select-sm" required>
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
                        <label class="form-label" for="comercial">Comercial responsable</label>
                        <input class="form-control form-control-sm" type="text" name="comercial" id="comercial" value="<?php echo $_SESSION['name']; ?>" disabled>
                    </div> 
                    <div class ="form-group">
                        <label class="form-label">Ingeniero responsable</label>
                        <div >
                        <select name="ingeniero" class="form-select form-select-sm" >
                            <option value="">Seleccionar</option>
                            <?php
                            while ($row = mysqli_fetch_assoc($inge)) {
                                echo "<option value=".$row['id'].">".$row['name'] ."</option>";
                            };
                            ?>
                        </select>
                        </div>
                    </div>
                    </div>
                    <div class="form-row">
                    <div class="form-group">
                        <label for="dist" class="form-label">Distribuidor</label>
                        <input type="text" class="form-control form-control-sm" id="dist" name="dist" required="required">          
                    </div>
                    <div class="form-group">
                        <label for="monto" class="form-label">Monto Proyecto</label>
                        <input type="text" class="form-control form-control-sm" id="monto" name="monto">          
                    </div>
                    </div>
                    <div class="form-row" style="display:none" id="classInfo">
                        <div class="expenses ">
                        <div class="title">
                            <label for="soft" class="form-label">Software</label>
                            <input type="checkbox" id="software" name="software">
                        </div>
                        <input type="text" id="software-input" name="software-input" class="hidden form-control" placeholder="USD 0">
                            <br>
                        <div class="title">
                            <label for="hard" class="form-label">Hardware</label>
                            <input type="checkbox" id="hardware" name="hardware">   
                        </div>
                        <input type="text" id="hardware-input" name="hardware-input" class="hidden form-control" placeholder="USD 0">
                        </div>

                    </div>
                    <div class="form-row">
                    <div class="expenses">
                        <div class="title">
                        <label for="bom" class="form-label">BOM</label>
                        <input type="checkbox" id="bom" name="bom">
                        </div>
                        <input type="file" id="bom-input" name="bom-input" class="hidden" placeholder="BOM">
                    </div>
                    </div>  
                    <div class="form-row">
                    <div class="form-group">
                        <div class="label-container">
                        <label class="label">Actividades</label>
                        <button type="button" class="btn btn-add-task" data-bs-toggle="modal" data-bs-target="#actividadModal">
                            <i class="bi bi-calendar-plus"></i> 
                        </button>
                        </div>  
                        <div id="events-list">
                        <ul id="listadoActividades" class="list-group"></ul>
                        </div>
                    </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                        <label for="desc" class="form-label">Resumen</labe>
                        <textarea class="form-control form-control-sm" id="desc" name="desc" rows="4"></textarea>     
                        </div>
                    </div>       
                    <div class="footer">
                        <button class="btn btn-reset">Resetear</button>
                        <button type="submit" id="newProject" name="newProject" class="btn pull-right">Crear</button>
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
                <input type="text" class="form-control form-control-sm" id="nombreActividad" name="nombreActividad" required>
            </div>
            <div class="mb-3">
                <label for="fechaActividad" class="form-label">Fecha</label>
                <input type="date" class="form-control form-control-sm" id="fechaActividad" name="fechaActividad" required>
            </div>
            <div class="mb-3">
                <label for="descripcionActividad" class="form-label">Descripción</label>
                <textarea class="form-control form-control-sm" id="descripcionActividad" name="descripcionActividad" rows="3"></textarea>
            </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-reset" data-bs-dismiss="modal">Cerrar</button>
            <button type="submit" form="formActividad" class="btn pull-right">Agregar</button>
        </div>
        </div>
    </div>
    </div>

    <br><br>
    </div>

  </div>

<!-- Bootstrap Bundle (con Popper.js) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<!-- Complementos/Plugins (sin jQuery) -->
<script src="assets/plugins/breakpoints.js"></script>
<script src="assets/plugins/pace/pace.min.js"></script>

<!-- Scripts propios -->
<script src="assets/js/create-project.js"></script>
</body>

</html>