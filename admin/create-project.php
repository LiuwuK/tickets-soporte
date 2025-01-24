<?php
session_start();
include("checklogin.php");
include("dbconnection.php");
include("phpmail.php");
include("../assets/php/create-project.php");

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

<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<!-- CSS personalizados -->
<link href="../assets/css/sidebar.css" rel="stylesheet" type="text/css" />
<link href="../assets/css/create-project.css" rel="stylesheet" type="text/css" />

<!-- Toast notificaciones -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
</head>

<body class="test" >
    <!-- Sidebar -->
  <div class="page-container ">

    <div class="sidebar">
    <?php include("header.php"); ?>
      
    </div>
    <div class="page-content">
    <?php include("leftbar.php"); ?>
        <div class="content">
            <div class="page-title">
                <h2>
                    <i class="bi bi-clipboard2-plus"></i> 
                    Crear Proyecto
                </h2>
            </div>
            <!-- Formulario crear proyectos -->
            <form name="form" id="newProject" method="post">
                <div id="loading" style="display:none ;">
                    <div class="loading-spinner"></div>
                    <p>Procesando...</p>
                </div>  
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
                    <div class="form-row" id="licitacionT" style="display: none;">
                            <strong>Datos Licitación</strong>
                    </div>
                    <div class="form-row"  id="licitacion" style="display: none;">
                        <div class="form-group">
                            <label for="licID" class="form-label">ID licitación</label>
                            <input type="text" class="form-control form-control-sm" id="licID" name="licID" >
                        </div>

                        <div class="form-group">
                            <label for="portal" class="form-label">Portal</label>
                            <input type="text" class="form-control form-control-sm" id="portal" name="portal" >
                        </div>
                    </div>            
                    <div class="form-row justify-content-start" id="contactoT" style="display: none;">
                        <strong>Datos de Contacto</strong>
                        <button type="button" class="btn btn-add-task" data-bs-toggle="modal" data-bs-target="#contactoModal">
                            <i class="bi bi-plus-circle"></i> 
                        </button>
                    </div>
                    <div class="d-flex contactos" id="contacto" style="display: hidden;">
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

                        <div class="form-group">
                            <label class="form-label">Vertical</label>
                            <div>
                            <select name="vertical" class="form-select form-select-sm" required>
                                <option value="">Seleccionar</option>
                                <?php
                                    while ($row = mysqli_fetch_assoc($vertical)) {
                                        echo "<option value=".$row['id'].">".$row['nombre'] ."</option>";
                                    };
                                ?>  
                            </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="">Monto</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="montoP">$</span>
                                <input name="montoP" type="number" class="form-control form-control-sm" placeholder="1,000,000" aria-label="Monto" aria-describedby="montoP">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="">Fecha de Cierre</label>
                            <div class="input-group">
                                <span class="input-group-text" id="fCierre"><i class="bi bi-exclamation-lg"></i></span>
                                <input name="fCierre" type="date" class="form-control form-control-sm" placeholder="12-05-2025" aria-label="Date" aria-describedby="fCierre">
                            </div>
                        </div>
                    </div>
                <!--  Costo software y hardware
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
                    -->
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
        
    <br><br>
    </div>
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


<!-- Modal contactos -->
<div class="modal fade" id="contactoModal" tabindex="-1" aria-labelledby="contactoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="contactoModalLabel">Agregar Contacto</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form id="formContacto">
                <div class="form-row-modal">
                    <div class="form-group">
                        <label for="cName" class="form-label">Nombre</label>
                        <input type="text" class="form-control form-control-sm mb-3" id="cName" name="cName" >

                        <label for="cEmail" class="form-label">Email </label>
                        <input type="email" class="form-control form-control-sm" id="cEmail" name="cEmail" >
                    </div>

                    <div class="form-group">
                        <label for="cargo" class="form-label">Cargo</label>
                        <input type="text" class="form-control form-control-sm mb-3" id="cargo" name="cargo" >

                        <label for="cNumero" class="form-label">Numero de contacto </label>
                        <input type="text" class="form-control form-control-sm" id="cNumero" name="cNumero" >
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-reset" data-bs-dismiss="modal">Cerrar</button>
            <button type="submit" form="formContacto" class="btn pull-right">Agregar</button>
        </div>
        </div>
    </div>
</div>

  </div>

                    
<script>
    //contactos
    document.getElementById('formContacto').addEventListener('submit', function (e) {
    e.preventDefault(); 

    const nombre = document.getElementById('cName').value.trim();
    const email = document.getElementById('cEmail').value.trim();
    const cargo = document.getElementById('cargo').value.trim();
    const contacto = document.getElementById('cNumero').value.trim();
    

    const nuevoContacto = document.createElement('div');
    nuevoContacto.className = 'card mt-3 p-2';
    nuevoContacto.innerHTML = `
        <div class="d-flex justify-content-evenly">
        <div class="cinfo">
            <strong class="ml-3" >Nombre</strong> 
            <p>${nombre}</p>
        </div>  
        <div class="cinfo">
            <strong class="ml-3" >Cargo</strong> 
            <p> ${cargo}</p>
        </div> 
        <div class="cinfo">
            <strong class="ml-3" >Email</strong> 
            <p> ${email}</p>
        </div> 
        <div class="cinfo">
            <strong class="ml-3" >Numero de Contacto</strong> 
            <p> ${contacto}</p>
        </div> 
        </div>
        
        <input type="hidden" name="contacto[nombre][]" value="${nombre}">
        <input type="hidden" name="contacto[email][]" value="${email}">
        <input type="hidden" name="contacto[cargo][]" value="${cargo}">
        <input type="hidden" name="contacto[contacto][]" value="${contacto}">
    `;
    document.getElementById('contacto').appendChild(nuevoContacto);
    document.getElementById('formContacto').reset();
    const modalElement = document.getElementById('contactoModal');
    const modalInstance = bootstrap.Modal.getInstance(modalElement);
    modalInstance.hide();
    });

</script>
<!-- Popper.js (para tooltips y otros componentes) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>

<!-- Bootstrap Bundle (con Popper.js) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<!-- Complementos/Plugins-->

<!-- Scripts propios -->
<script src="../assets/js/create-project.js"></script>
<script src="../assets/js/sidebar.js"></script>
</body>

</html>