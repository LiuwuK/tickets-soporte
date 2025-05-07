<?php
session_start();
include("../checklogin.php");
include("../dbconnection.php");
include("assets/php/clients.php");
check_login();
?>
<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
  <meta charset="utf-8" />
  <title>Historico Cliente</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <meta content="" name="description" />
  <meta content="" name="author" />

<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<!-- CSS personalizados -->
<link href="../assets/css/sidebar.css" rel="stylesheet" type="text/css"/>
<link href="assets/css/clients.css" rel="stylesheet" type="text/css"/>
<!-- Toast notificaciones -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
<!-- Graficos -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="test" >
    <!-- Sidebar -->
  <div class="page-container ">
    <div class="sidebar">
      <?php include("../header-test.php"); ?>
      <?php include("../assets/php/phone-sidebar.php"); ?>
    </div>
    <div class="page-content">
    <?php 
      include("../leftbar-test.php");
      $row = $clientes->fetch_assoc();
      $img = !empty($row['img_perfil']) && file_exists($row['img_perfil']) ? $row['img_perfil'] : '../assets/img/user.png';
    ?>
        <div class="page-title d-flex justify-content-between">
          <h2>Historico cliente</h2>
          <button class="btn btn-back" onclick="window.location.href='clients.php';"> 
            <i class="bi bi-arrow-left" ></i>
          </button>
        </div> 
        <div class="col-md-10 client-card mx-auto d-flex">
          <img src="<?php echo $img ?>" alt="">
          <div class="info-body">
            <h4><?php echo $row['nombre'];?></h4>
            <div class="editable-info">
              <p>
                  <strong>Encargado:</strong>
                  <span class="editable" contenteditable="true" data-field="encargado">
                      <?php echo !empty(trim($row['encargado'])) ? htmlspecialchars(trim($row['encargado'])) : 'Sin definir'; ?>
                  </span>
                  || 
                  <strong>Cargo:</strong>
                  <span class="editable" contenteditable="true" data-field="cargo">
                      <?php echo !empty(trim($row['cargo'])) ? htmlspecialchars(trim($row['cargo'])) : 'Sin definir'; ?>
                  </span>
              </p>
              <p>
                  <strong>Correo:</strong>
                  <span class="editable" contenteditable="true" data-field="correo">
                      <?php echo !empty(trim($row['correo'])) ? htmlspecialchars(trim($row['correo'])) : 'Sin definir'; ?>
                  </span>
              </p>
            </div>

            <p class="mb-3" >Vertical: <?php echo $row['verticalN'];?></p>
            <h5>Monto total Proyectos: <?php echo '$'.number_format($monto['monto_total'], 0, '.', ',');?></h5>
            <button id="guardarCambios" class="btn btn-default mt-2" disabled>Guardar Cambios</button>
          </div>
        </div>
        <div class="col-md-10 compe-card mx-auto">
          <div class="title d-flex">
            <h4>Lista de competidores</h4>
            <button type="button" class="btn btn-sm btn-updt"  data-bs-toggle="modal" data-bs-target="#compModal">
                <i class="bi bi-plus"></i> 
            </button>
          </div>
          <div class="competidores">
            <!-- while competidores -->
            <?php
            if($num_com > 0){
              while($row = $competidores->fetch_assoc()){
                $img = !empty($row['img_perfil']) && file_exists($row['img_perfil']) ? $row['img_perfil'] : '../assets/img/user.png';
              ?>
                <div class="list d-flex">
                  <div class="img">
                    <img src="<?php echo $img?>" alt="">
                  </div>
                  <div class="info-comp">
                    <strong><?php echo $row['nombre_competidor']; ?> </strong>
                    <p><?php echo $row['rut']; ?></p>
                    <p><?php echo $row['especialidad']; ?></p>
                  </div>
                </div>  
              <?php
              }
            }else {
              echo "<h5 class='text-center'>No hay competidores registrados</h5>";
            }
            ?>
          </div>
        </div>   

        <div class="col-md-10 compe-card mx-auto">
          <div class="title d-flex">
            <h4>Lista de Actividades</h4>
            <button type="button" class="btn btn-sm btn-updt"  data-bs-toggle="modal" data-bs-target="#actividadModal">
                <i class="bi bi-plus"></i> 
            </button>
          </div>
          <div class="competidores">
            <!-- while actividades -->
            <?php
            if($num_com > 0){
              while($row = $actividades->fetch_assoc()){
                $date = DateTime::createFromFormat('Y-m-d H:i:s', $row['fecha_inicio']);
                $ff = $date->format('d-m-Y H:i');
              ?>
                <div class="list-act d-flex justify-content-center">
                  <div class="info-comp justify-content-start">
                    <strong ><?php echo $row['nombre']; ?></strong>
                    <p class="mb-1"><?php echo $ff ?></p>
                    <p><?php echo $row['descripcion']; ?></p>
                  </div>
                </div>  
              <?php
              }
            }else {
              echo "<h5 class='text-center'>No hay competidores registrados</h5>";
            }
            ?>
          </div>
        </div>
        <div class="col-md-10 compe-card mx-auto">
          <h4>Licitaciones </h4>
          <div class="licitacion">
          <?php
          if($num_lic > 0){
            while($row = $licitaciones->fetch_assoc()){
            ?>
              <div class="lici-info d-flex">
                <div class="lici-left">
                  <strong ><?php echo $row['nombre']; ?></strong>
                  <span class="label label-success"><?php echo '$'.number_format($row['monto'], 0, '.', ',');?></span>
                  <p class="mt-3">Clasificacion: <?php echo $row['clasiN']; ?></p>
                  <p>Competidor: <?php echo !empty($row['competidorN']) ? $row['competidorN'] : 'Sin asignar';?></p>
                  <p>Fecha Adjudicacion: <?php echo $row['fecha_adjudicacion']; ?></p>
                  <p>Fecha Renovacion: <?php echo $row['fecha_fin_contrato']; ?></p>
                </div>
                <div class="lici-desc">
                    <p><?php echo $row['resumen']; ?></p>
                </div>
              </div>
            <?php
            }
          }else {
            echo "<h5 class='text-center'>No hay licitaciónes asociadas a este cliente</h5>";
          }
          ?>
          </div>
        </div>
    </div>
  </div>

<!-- Modal Competidores -->
<div class="modal fade" id="compModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="compModalLabel" aria-hidden="true">>
  <div class="modal-dialog">
      <div class="modal-content">
      <div class="modal-header">
          <h5 class="modal-title" id="compModalLabel">Nuevo Competidor</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
          <form id="form" method="post" enctype="multipart/form-data">
            <div class="form-row-modal mb-3">
              <div class="form-group">
                  <label for="nombreCompetidor" class="form-label">Nombre</label>
                  <input type="text" class="form-control form-control-sm" id="nombreCompetidor" name="nombreCompetidor" required>
              </div>
            </div>

            <div class="form-row-modal mb-3">
              <div class="form-group">
                <label for="rut" class="form-label">Rut <span>(Sin puntos ni guion)</span></label>
                <input type="text" class="form-control form-control-sm" id="rut" name="rut" maxlength="12" required>
              </div>
            </div>
            <div class="form-row-modal mb-3">
              <div class="form-group">
                  <label for="especialidad" class="form-label">Especialidad</span></label>
                  <input type="text" class="form-control form-control-sm" id="especialidad" name="especialidad" required>
              </div>
            </div>
            <div class="form-row-modal mb-3">
                <div class="form-group">
                    <label for="compImg" class="form-label">Subir Imagen</label>
                    <input class="form-control form-control-sm" type="file" id="compImg" name="compImg" accept="image/*">
                </div>
              </div>
          </form>
      </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" form="form" name="addComp" class="btn pull-right btn-updt">Agregar</button>
      </div>
      </div>
  </div>
</div>

<div class="modal fade" id="actividadModal"  data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="actividadModalLabel" aria-hidden="true">
  <div class="modal-dialog">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="actividadModalLabel">Agregar Actividad</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
              <form id="formA" method="post">
              <div class="mb-3">
                  <label for="nombreActividad" class="form-label">Nombre de la Actividad</label>
                  <input type="text" class="form-control form-control-sm" id="nombreActividad" name="nombreActividad" required>
              </div>
              <div class="form-row-modal mb-3 d-flex">
                  <div class="form-group">
                      <label for="fechaInicio" class="form-label">Fecha inicio</label>
                      <input type="datetime-local" class="form-control form-control-sm" id="fechaInicio" name="fechaInicio" required>
                  </div>
                  <div class="form-group">
                      <label for="fechaTermino" class="form-label">Fecha termino</label>
                      <input type="datetime-local" class="form-control form-control-sm" id="fechaTermino" name="fechaTermino" required>
                  </div>
              </div>
              <div class="mb-3">
                  <label for="descripcionActividad" class="form-label">Descripción</label>
                  <textarea class="form-control form-control-sm" id="descripcionActividad" name="descripcionActividad" rows="3"></textarea>
              </div>
              </form>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cerrar</button>
              <button type="submit" form="formA" name="addAct" class="btn btn-updt pull-right">Agregar</button>
          </div>
      </div>
  </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const editables = document.querySelectorAll('.editable');
    const guardarBtn = document.getElementById('guardarCambios');
    
    // Almacenar valores originales
    const originalValues = {};
    editables.forEach(editable => {
        const field = editable.dataset.field;
        originalValues[field] = editable.textContent.trim();
        editable.dataset.original = editable.textContent.trim();
    });

    // Función para verificar cambios
    function checkForChanges() {
        let hasChanges = false;
        
        editables.forEach(editable => {
            const field = editable.dataset.field;
            const currentValue = editable.textContent.trim();
            
            if (currentValue !== originalValues[field]) {
                hasChanges = true;
            }
        });
        
        guardarBtn.disabled = !hasChanges;
        guardarBtn.classList.toggle('btn-updt', hasChanges);
        guardarBtn.classList.toggle('btn-default', !hasChanges);
    }
    editables.forEach(editable => {
        editable.addEventListener('input', checkForChanges);    
        editable.addEventListener('blur', function() {
            this.textContent = this.textContent.trim();
            checkForChanges();
        });
    });

    guardarBtn.addEventListener('click', function() {
        const cambios = {};
        
        editables.forEach(editable => {
            const field = editable.dataset.field;
            cambios[field] = editable.textContent.trim();
        });
        
        if (cambios.correo && cambios.correo !== 'Sin definir') {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(cambios.correo)) {
                alert('Por favor ingrese un correo electrónico válido');
                return;
            }
        }
        
        fetch('assets/php/updt-clientD.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                id: <?php echo $_GET['clientID'] ?>,
                datos: cambios
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Cambios guardados correctamente');
                editables.forEach(editable => {
                    const field = editable.dataset.field;
                    originalValues[field] = editable.textContent.trim();
                    editable.dataset.original = editable.textContent.trim();
                });
                guardarBtn.disabled = true;
                guardarBtn.classList.remove('btn-updt');
                guardarBtn.classList.add('btn-default');
            } else {
                alert('Error al guardar: ' + (data.error || ''));
            }
        });
    });
});
</script>

<style>
.btn-updt {
    transition: all 0.3s ease;
}
.btn-updt:disabled {
    cursor: not-allowed;
    opacity: 0.65;
}
</style>

<!-- Popper.js (para tooltips y otros componentes) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<!-- Bootstrap Bundle (con Popper.js) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<!-- Scripts propios -->
<script src="../assets/js/sidebar.js"></script>
<script src="assets/js/client-info.js"></script>
</body>

</html>