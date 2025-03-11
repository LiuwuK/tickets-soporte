<?php
session_start();
include("../checklogin.php");
include("../dbconnection.php");
include("assets/php/detalle-historico.php");
check_login();

?>
<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
  <meta charset="utf-8" />
  <title>Historico Traslados y Desvinculaciones</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <meta content="" name="description" />
  <meta content="" name="author" />
<!-- CSS de Choices.js -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<!-- CSS personalizados -->
<link href="../assets/css/sidebar.css" rel="stylesheet" type="text/css"/>
<link href="assets/css/historico-TD.css" rel="stylesheet" type="text/css"/>
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
    <?php include("../leftbar-test.php"); ?>
        <div class="page-title d-flex justify-content-between">
          <h2 class="det-view">Detalle <?php echo $_GET['tipo'].' #'.$_GET['id']?></h2>
          <button class=" btn-back" onclick="window.location.href='historico-TD.php';"> 
              <i class="bi bi-arrow-left" ></i>
          </button>
        </div> <br><br>
        <div class="d-container col-md-9 mx-auto">
          <?php 
            if($_GET['tipo'] == 'traslado'){
              while ($row = mysqli_fetch_assoc($tr)) {
          ?>
            <!-- Datos Colaborador -->
            <h4 class="mt-3">Datos colaborador</h4>
            <hr width="90%" class="mx-auto">
            <div class="form-row">
              <div class="form-group">
                <label class="form-label" for="colaborador" >Nombre Colaborador</label>
                <input type="text" name="colaborador" id="colaborador" value="<?php echo $row['colaborador']; ?>" class="form-control form-control-sm " readonly/>
              </div>
              <div class="form-group">
                <label class="form-label" for="rutC" >Rut Colaborador</label>
                <input type="text" name="rutC" id="rutC" value="<?php echo $row['rutC']; ?>" class="form-control form-control-sm " readonly/>
              </div>
            </div>
            <br>
            <!-- Datos instalacion origen -->
            <h4>Datos Instalación Origen</h4>
            <hr width="90%" class="mx-auto">
            <div class="form-row">
              <div class="form-group">
                <label class="form-label" for="instOrigen" >Instalación Origen</label>
                <input type="text" name="instOrigen" id="instOrigen" value="<?php echo $row['suOrigen']; ?>" class="form-control form-control-sm " readonly/>
              </div>
              <div class="form-group">
                <label class="form-label" for="supOrigen" >Supervisor Origen</label>
                <input type="text" name="supOrigen" id="supOrigen" value="<?php echo $row['supOrigen']; ?>" class="form-control form-control-sm " readonly/>
              </div>
            </div>

            <div class="form-row mt-3">
              <div class="form-group">
                <label class="form-label" for="rolOrigen" >Rol Origen</label>
                <input type="text" name="rolOrigen" id="rolOrigen" value="<?php echo $row['rolOrigen']; ?>" class="form-control form-control-sm " readonly/>
              </div>
              <div class="form-group">
                <label class="form-label" for="joOrigen" >Jornada Origen</label>
                <input type="text" name="joOrigen" id="joOrigen" value="<?php echo $row['joOrigen']; ?>" class="form-control form-control-sm " readonly/>
              </div>
            </div>
            <br>
             <!-- Datos instalacion destino -->
             <h4>Datos Instalación Destino</h4>
             <hr width="90%" class="mx-auto">
            <div class="form-row mt-3">
              <div class="form-group">
                <label class="form-label" for="instOrigen" >Instalación Destino</label>
                <input type="text" name="instDestino" id="instDestino" value="<?php echo $row['suDestino']; ?>" class="form-control form-control-sm " readonly/>
              </div>
              <div class="form-group">
                <label class="form-label" for="supDestino" >Supervisor Destino</label>
                <input type="text" name="supDestino" id="supDestino" value="<?php echo $row['supDestino']; ?>" class="form-control form-control-sm " readonly/>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label class="form-label" for="rolDestino" >Rol Destino</label>
                <input type="text" name="rolDestino" id="rolDestino" value="<?php echo $row['rolDestino']; ?>" class="form-control form-control-sm " readonly/>
              </div>
              <div class="form-group">
                <label class="form-label" for="joDestino" >Jornada Destino</label>
                <input type="text" name="joDestino" id="joDestino" value="<?php echo $row['joDestino']; ?>" class="form-control form-control-sm " readonly/>
              </div>
            </div>
            <br>
            <!-- Estado, fecha y observacion -->
            <h4>Informacion General</h4>
            <hr width="90%" class="mx-auto">
            <div class="form-row">
              <div class="form-group">
              <?php 
                if($_SESSION['cargo'] !== 13){
              ?>
                <label for="estado" class="form-label">Estado</label>
                <select class="form-select form-select-sm estado-select" data-id="<?php echo $_GET['id']; ?>">
                    <option value="En gestión" <?php if ($row['estado'] == 'En gestión') echo 'selected'; ?>>En gestión</option>
                    <option value="Realizado" <?php if ($row['estado'] == 'Realizado') echo 'selected'; ?>>Realizado</option>
                    <option value="Anulado" <?php if ($row['estado'] == 'Anulado') echo 'selected'; ?>>Anulado</option>
                </select>
              <?php   
                }else{
              ?>
                  <label class="form-label" for="estado" >Estado</label>
                  <input type="text" name="estado" id="estado" value="<?php echo $row['estado']; ?>" class="form-control form-control-sm " readonly/>
              <?php  
                }
              ?>
              </div>
              <div class="form-group">
                  <label for="fInicio" class="form-label">Fecha Inicio de Turno</label>
                  <input name="fInicio" type="date" class="form-control form-control-sm" value="<?php echo $row['fecha_turno'];?>" aria-label="Date" aria-describedby="fInicio" readonly>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                  <label class="form-label" for="motivoN" >Motivo de Traslado</label>
                  <input type="text" name="motivoN" id="motivoN" value="<?php echo $row['motivoN']; ?>" class="form-control form-control-sm " readonly />
                </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                  <label for="desc" class="form-label">Observación SSPP</labe>
                  <textarea class="form-control form-control-sm" id="desc" name="desc" rows="3" readonly><?php echo $row['obs'];?></textarea>     
              </div>
            </div>
            
            <form id="newform" method="POST">
              <?php
                if($_SESSION['cargo'] == 12){
              ?>
                <div class="form-row">
                  <div class="form-group">
                      <label for="desc" class="form-label">Observación RRHH</labe>
                      <textarea class="form-control form-control-sm" id="descRRHH" name="descRRHH" rows="3"><?php echo $row['obs_rrhh'];?></textarea>     
                  </div>
                </div>
                <div class="form-row justify-content-end">
                  <button class="btn btn-updt" name="rrhh" id="updateBtn" disabled>Actualizar</button>
                </div>
              <?php
              }else{
                ?>
                  <div class="form-row">
                    <div class="form-group">
                        <label for="desc" class="form-label">Observación RRHH</labe>
                        <textarea class="form-control form-control-sm" id="descRRHH" name="descRRHH" rows="3"><?php echo $row['obs_rrhh'];?></textarea>     
                    </div>
                  </div>
                <?php  
                }
                ?>
            </form>
          <?php
            }
            }else{
              while ($row = mysqli_fetch_assoc($dv)){
          ?>
          <!-- colaborador -->
            <h4 class="mt-3">Datos colaborador</h4>
            <hr width="90%" class="mx-auto">
            <div class="form-row">
              <div class="form-group">
                <label class="form-label" for="colaborador" >Nombre Colaborador</label>
                <input type="text" name="colaborador" id="colaborador" value="<?php echo $row['colaborador']; ?>" class="form-control form-control-sm " readonly/>
              </div>
              <div class="form-group">
                <label class="form-label" for="rutC" >Rut Colaborador</label>
                <input type="text" name="rutC" id="rutC" value="<?php echo $row['rut']; ?>" class="form-control form-control-sm " readonly/>
              </div>
            </div>
            <br>
          <!-- instalacion -->
            <h4 class="mt-3">Datos de Instalacion</h4>
            <hr width="90%" class="mx-auto">
            <div class="form-row">
              <div class="form-group">
                <label class="form-label" for="instalacion" >Instalacion Origen</label>
                <input type="text" name="instalacion" id="instalacion" value="<?php echo $row['instalacion']; ?>" class="form-control form-control-sm " readonly/>
              </div>
              <div class="form-group">
                <label class="form-label" for="supervisor" >Supervisor Encargado</label>
                <input type="text" name="supervisor" id="supervisor" value="<?php echo $row['supervisor']; ?>" class="form-control form-control-sm " readonly/>
              </div>
            </div>
            <br>
          <!-- Observacion, motivo y estado  -->
          <h4>Informacion General</h4>
            <hr width="90%" class="mx-auto">
            <div class="form-row">
              <div class="form-group">
                <?php 
                  if($_SESSION['cargo'] !== 13){
                ?>
                  <label for="estado" class="form-label">Estado</label>
                  <select class="form-select form-select-sm desv-select" data-id="<?php echo $_GET['id']; ?>">
                      <option value="En gestión" <?php if ($row['estado'] == 'En gestión') echo 'selected'; ?>>En gestión</option>
                      <option value="Realizado" <?php if ($row['estado'] == 'Realizado') echo 'selected'; ?>>Realizado</option>
                      <option value="Anulado" <?php if ($row['estado'] == 'Anulado') echo 'selected'; ?>>Anulado</option>
                  </select>
                <?php   
                  }else{
                ?>
                    <label class="form-label" for="estado" >Estado</label>
                    <input type="text" name="estado" id="estado" value="<?php echo $row['estado']; ?>" class="form-control form-control-sm " readonly/>
                <?php  
                  }
                ?>
              </div>
              <div class="form-group">
                <label class="form-label" for="motivoEgreso" >Motivo de Egreso</label>
                <input type="text" name="motivoEgreso" id="motivoEgreso" value="<?php echo $row['motivoEgreso']; ?>" class="form-control form-control-sm" readonly/>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                  <label for="desc" class="form-label">Observación SSPP</labe>
                  <textarea class="form-control form-control-sm" id="desc" name="desc" rows="3" readonly><?php echo $row['observacion'];?></textarea>     
              </div>
            </div>
            <form id="newform" method="POST">      
              <?php
                if($_SESSION['cargo'] == 12){
              ?>
                <div class="form-row">
                  <div class="form-group">
                      <label for="desc" class="form-label">Observación RRHH</labe>
                      <textarea class="form-control form-control-sm" id="descRRHH" name="descRRHH" rows="3"><?php echo $row['obs_rrhh'];?></textarea>     
                  </div>
                </div>
                <div class="form-row justify-content-end">
                  <button class="btn btn-updt" name="rrhh" id="updateBtn" disabled>Actualizar</button>
                </div>
              <?php
              }else{
              ?>
                <div class="form-row">
                  <div class="form-group">
                      <label for="desc" class="form-label">Observación RRHH</labe>
                      <textarea class="form-control form-control-sm" id="descRRHH" name="descRRHH" rows="3"><?php echo $row['obs_rrhh'];?></textarea>     
                  </div>
                </div>
              <?php  
              }
              ?>
            </form>
          <?php
            }
          } 
          ?>
        
        </div>
    </div>
  </div>
<script>
const descField = document.getElementById('descRRHH');
const updateBtn = document.getElementById('updateBtn');

function toggleButtonState() {
    if (descField.value.trim() !== '') {
        updateBtn.disabled = false; 
    } else {
        updateBtn.disabled = true;
    }
}

descField.addEventListener('input', toggleButtonState);
toggleButtonState();
</script>
<!-- Popper.js (para tooltips y otros componentes) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<!-- Bootstrap Bundle (con Popper.js) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<!-- Scripts propios -->
<script src="../assets/js/sidebar.js"></script>
<script src="assets/js/detalle-historico.js"></script>
</body>

</html>