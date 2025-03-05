<?php
session_start();
include("../checklogin.php");
include("../dbconnection.php");
include("assets/php/traslados.php");
check_login();

?>
<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
  <meta charset="utf-8" />
  <title>Traslados y Descvinculacion</title>
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
<link href="assets/css/traslados.css" rel="stylesheet" type="text/css"/>
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
          <h2>Traslado y Descvinculacion</h2>
          <button class=" btn-back" onclick="window.location.href='main.php';"> 
              <i class="bi bi-arrow-left" ></i>
          </button>
        </div> 
        <div class="content">
          <div class="col-md-10 form-data mx-auto">
            <div class="form-row mx-auto d-flex justify-content-between">
              <div class ="form-group">
                  <label class="form-label">Solicitante</label>
                  <div >
                    <input type="text" class="form-control form-control-sm"  name="solicitante" value="<?php echo $_SESSION['name'];?>" disabled>
                  </div>
              </div>
              <div class ="form-group">
                <label for="fechaSoli" class="form-label">Fecha <span>*</span></label>
                <input type="date" class="form-control form-control-sm" id="fechaSoli" name="fechaSoli" disabled>
              </div>
            </div>
            <div class="form-row mx-auto d-flex">
              <div class="form-group">
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="forms" id="traslado" onclick="mostrarFormulario('trasladoForm')">
                  <label class="form-check-label" for="traslado">
                    Formulario de Traslado
                  </label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="forms" id="desvinculacion" onclick="mostrarFormulario('desvinculacionForm')">
                  <label class="form-check-label" for="desvinculacion">
                    Formulario de Desvinculacion
                  </label>
                </div>
              </div>
            </div>
          </div>
          <!-- Formulario de Traslado -->
          <form name="form" method="POST" enctype="multipart/form-data">
            <div id="loading" style="display:none ;">
                <div class="loading-spinner"></div>
                <p>Procesando...</p>
            </div>
            <div class="col-md-10 form-data mx-auto" id="trasladoForm" style="display: none;">
              <h3>Formulario de Traslado</h3>
              <div class="form-row mx-auto">
                <div class ="form-group">  
                    <div class="d-flex msg">
                      <label class="form-label">Supervisor Origen <span>*</span></label>
                      <p>(Supervisor a cargo de Instalación de origen)</p>
                    </div>
                    <div >
                      <select name="supervisor" id="supervisor" class="form-select form-select-sm search-form">
                        <option value="">Seleccionar</option>
                        <?php
                        foreach ($sup AS $row) {
                          echo "<option value=".$row['id'].">".$row['nombre_supervisor'] ."</option>";
                        };
                        ?>
                      </select>
                    </div>
                </div>
              </div>
              <div class="form-row mx-auto">
                <div class="form-group">
                  <label for="colaborador" class="form-label">Nombre Colaborador <span>*</span></label>
                  <input type="text" class="form-control form-control-sm" id="colaborador" name="colaborador" required>
                </div>
                <div class="form-group ">
                  <div class="d-flex msg">
                    <label for="rut" class="form-label">Rut <span>*</span></label>
                    <p>(Sin puntos ni guion)</p>
                  </div>
                  <input type="text" class="form-control form-control-sm" id="rut" name="rut" maxlength="12" required>
                  
                </div>
              </div>
              <div class="form-row mx-auto">
                <div class="form-group">
                  <label class="form-label">Instalación de Origen <span>*</span></label>
                  <div>
                      <select name="instalacion" id="instalacion" class="form-select form-select-sm search-form" required>
                          <option value="">Seleccionar</option>
                          <?php
                          foreach ($inst AS $row) {
                              echo "<option value='".$row['id']."'>".$row['nombre']."</option>";
                          };
                          ?>
                      </select>
                  </div>
              </div>
                <div class ="form-group">
                    <label class="form-label">Jornada de Origen <span>*</span></label>
                    <div >
                      <select name="jornada" id="jornada" class="form-select form-select-sm search-form">
                        <option value="">Seleccionar</option>
                        <?php
                        foreach ($jornada AS $row) {
                          echo "<option value=".$row['id'].">".$row['tipo_jornada'] ."</option>";
                        };
                        ?>
                      </select>
                    </div>
                </div>

              </div>
              <div class="form-row mx-auto">
                <div class ="form-group">
                  <label class="form-label">Rol de Origen <span>*</span></label>
                  <div >
                      <select name="rolOrigen" class="form-select form-select-sm search-form" required>
                        <option value="">Seleccionar</option>
                        <?php
                        foreach ($rol AS $row) {
                          echo "<option value=".$row['id'].">".$row['nombre_rol'] ."</option>";
                        };
                        ?>
                      </select>
                  </div>
                </div>
                <div class ="form-group">
                    <label class="form-label">Motivo de traslado</label>
                    <div >
                        <select name="motivo" class="form-select form-select-sm search-form" required>
                          <option value="">Seleccionar</option>
                          <?php
                          foreach ($motivoT AS $row) {
                            echo "<option value=".$row['id'].">".$row['motivo'] ."</option>";
                          };
                          ?>
                        </select>
                    </div>
                </div>
              </div>
              <div class="form-row mx-auto">
                <div class ="form-group">
                  <label class="form-label">Instalacion de Destino <span>*</span></label>
                  <div >
                      <select name="inDestino" class="form-select form-select-sm search-form" required>
                        <option value="">Seleccionar</option>
                        <?php
                        foreach ($inst AS $row) {
                          echo "<option value=".$row['id'].">".$row['nombre'] ."</option>";
                        };
                        ?>
                      </select>
                  </div>
                </div>
                <div class ="form-group">
                  <label class="form-label">Jornada de Destino <span>*</span></label>
                  <div >
                      <select name="joDestino" class="form-select form-select-sm search-form" required>
                        <option value="">Seleccionar</option>
                        <?php
                        foreach ($jornada AS $row) {
                          echo "<option value=".$row['id'].">".$row['tipo_jornada'] ."</option>";
                        };
                        ?>
                      </select>
                  </div>
                </div>
              </div>
              <div class="form-row mx-auto">
                <div class ="form-group">
                  <label class="form-label">Rol de Destino<span>*</span></label>
                  <div >
                      <select name="rolDestino" class="form-select form-select-sm search-form" required>
                        <option value="">Seleccionar</option>
                        <?php
                        foreach ($rol AS $row) {
                          echo "<option value=".$row['id'].">".$row['nombre_rol'] ."</option>";
                        };
                        ?>
                      </select>
                  </div>
                </div>
                <div class ="form-group">
                  <label for="fechaInicio" class="form-label">Fecha de Inicio de Turno <span>*</span></label>
                  <input type="date" class="form-control form-control-sm" id="fechaInicio" name="fechaInicio" required>
                </div>
              </div>
              <div class="form-row mx-auto">
                <div class ="form-group">  
                    <div class="d-flex msg">
                      <label class="form-label">Supervisor Destino <span>*</span></label>
                      <p>(Supervisor a cargo de instalación de destino)</p>
                    </div>
                    <div >
                        <select name="supervisorDestino" class="form-select form-select-sm search-form" required>
                          <option value="">Seleccionar</option>
                          <?php
                          foreach ($sup AS $row) {
                            echo "<option value=".$row['id'].">".$row['nombre_supervisor'] ."</option>";
                          };
                          ?>
                        </select>
                    </div>
                </div>
              </div>
              <div class="footer">
                <button tyoe="submit" name="trasladoForm" class="btn btn-updt">Enviar</button>
              </div>
            </div>
          </form>
          <!-- Fin Formulario traslado -->
          <!-- formulario desvinculacion -->
          <form name="form" method="POST" enctype="multipart/form-data">
            <div id="loading" style="display:none ;">
                <div class="loading-spinner"></div>
                <p>Procesando...</p>
            </div>
            <div class="col-md-10 form-data mx-auto" id="desvinculacionForm" style="display: none;">
              <h3>Formulario de Desvinculación</h3>
              <div class="form-row mx-auto">
                <div class ="form-group">  
                    <div class="d-flex msg">
                      <label class="form-label">Supervisor Encargado <span>*</span></label>
                      <p>(Supervisor a cargo de Instalación)</p>
                    </div>
                    <div >
                        <select name="supervisorEncargado" class="form-select form-select-sm search-form" required>
                          <option value="">Seleccionar</option>
                          <?php
                          foreach ($sup AS $row) {
                            echo "<option value=".$row['id'].">".$row['nombre_supervisor'] ."</option>";
                          }
                          ?>
                        </select>
                    </div>
                    
                </div>
              </div>
              <div class="form-row mx-auto">
                <div class="form-group">
                  <label for="colaborador" class="form-label">Nombre Colaborador <span>*</span></label>
                  <input type="text" class="form-control form-control-sm" id="colaborador" name="colaborador" required>
                </div>
                <div class="form-group ">
                  <div class="d-flex msg">
                    <label for="rutDesvinculacion" class="form-label">Rut <span>*</span></label>
                    <p>(Sin puntos ni guion)</p>
                  </div>
                  <input type="text" class="form-control form-control-sm" id="rut" name="rut" maxlength="12" required>
                  
                </div>
              </div>
              <div class="form-row mx-auto">
                <div class ="form-group">
                    <label class="form-label">Instalacion de Origen <span>*</span></label>
                    <div >
                        <select name="instalacion" class="form-select form-select-sm search-form" required>
                          <option value="">Seleccionar</option>
                          <?php
                          foreach ($inst AS $row) {
                            echo "<option value=".$row['id'].">".$row['nombre'] ."</option>";
                          };
                          ?>
                        </select>
                    </div>
                </div>
                <div class ="form-group">
                    <label class="form-label">Motivo de Egreso <span>*</span></label>
                    <div >
                        <select name="motivo" class="form-select form-select-sm search-form" required>
                          <option value="">Seleccionar</option>
                          <?php
                          foreach ($motivoE AS $row) {
                            echo "<option value=".$row['id'].">".$row['motivo'] ."</option>";
                          };
                          ?>
                        </select>
                    </div>
                </div>

              </div>
              <div class="form-row mx-auto">
                <div class="form-group">
                  <label for="observacion" class="form-label">Observación</label>
                  <textarea class="form-control form-control-sm" id="observacion" name="observacion" rows="6"></textarea>
                </div>
              </div>
            <!-- Fin formulario desvinculacion -->
              <div class="footer">
                <button name="desvForm" class="btn btn-updt">Enviar</button>
              </div>
            </div>
          </form> 
          <!-- Traslados -->
          <div class="form-data col-md-10 mx-auto">
            <h3>Traslados</h3>
            <?php 
            if($num > 0) {
              echo '<button class="btn btn-updt" onclick="window.location.href=\'assets/php/excel-traslados.php\';">Descargar</button>';
              while($row = $traslados->fetch_assoc()){
                $date = date("Y-m-d H:i", strtotime($row['fecha_registro']));
            ?>
              <div class="mt-3 card col-md-11 mx-auto p-3 card-t">
                <div class="colab-data">
                  <strong>Colaborador: <?php echo $row['nombre_colaborador'];?></strong>
                  <p>Solicitante: <?php echo $row['soliN']?></p>
                  <p>Fecha: <?php echo $date?></p>
                  <p>Estado: 
                    <select class="form-select form-select-sm estado-select" data-id="<?php echo $row['id']; ?>">
                        <option value="En gestión" <?php if ($row['estado'] == 'En gestión') echo 'selected'; ?>>En gestión</option>
                        <option value="Realizado" <?php if ($row['estado'] == 'Realizado') echo 'selected'; ?>>Realizado</option>
                        <option value="Anulado" <?php if ($row['estado'] == 'Anulado') echo 'selected'; ?>>Anulado</option>
                    </select>
                  </p>
                </div>
                <div class="origen-data">
                  <strong>Instalacion de Origen: <?php echo $row['suOrigen'] ?></strong>
                  <p>Supervisor: <?php echo $row['supOrigen'];?></p>
                  <p>Jornada Origen: <?php echo $row['joOrigen'];?></p>
                  <p>Rol: <?php echo $row['rolOrigen'];?></p>
                </div>
                <div class="destino-data">
                  <strong>Instalacion de Destino: <?php echo $row['suDestino'] ?></strong>
                  <p>Supervisor: <?php echo $row['supDestino'];?></p>
                  <p>Jornada Destino: <?php echo $row['joDestino'];?></p>
                  <p>Rol: <?php echo $row['rolDestino'];?></p>
                </div>
                <div class="delete-btns">
                  <button class="btn btn-del del-btn" name="delTraslado" data-bs-toggle="modal" data-bs-target="#delTraslado" data-sup-id="<?php echo $row['id'];?>" >Eliminar</button>
                </div>
              </div>  
            <?php   
              }
            } else{
                echo "<p>No hay traslados registrados el dia de hoy</p>";
              }
            ?>
          </div>
          <!-- Desvinculaciones -->
          <div class="form-data col-md-10 mx-auto">
            <h3>Desvinculaciones</h3>
            <?php 
            if($num_des > 0) {
              echo '<button class="btn btn-updt" onclick="window.location.href=\'assets/php/excel-desvinculaciones.php\';">Descargar</button>';
              while($row = $desvinculaciones->fetch_assoc()){
                $date = date("Y-m-d H:i", strtotime($row['fecha_registro']));
            ?>
              <div class="mt-3 card col-md-11 mx-auto p-3 card-t">
                <div class="colab-data">
                  <strong>Colaborador: <?php echo $row['colaborador'];?></strong>
                  <p>Solicitante: <?php echo $row['soliN']?></p>
                  <p>Fecha: <?php echo $date?></p>
                  <p>Estado: 
                    <select class="form-select form-select-sm desv-select" data-id="<?php echo $row['id']; ?>">
                      <option value="En gestión" <?php if ($row['estado'] == 'En gestión') echo 'selected'; ?>>En gestión</option>
                      <option value="Realizado" <?php if ($row['estado'] == 'Realizado') echo 'selected'; ?>>Realizado</option>
                      <option value="Anulado" <?php if ($row['estado'] == 'Anulado') echo 'selected'; ?>>Anulado</option>
                    </select>
                  </p>
                </div>
                <div class="origen-data">
                  <strong>Instalacion de Origen: <?php echo $row['instalacion'] ?></strong>
                  <p>Supervisor: <?php echo $row['supervisor'];?></p>
                </div>
                <div class="destino-data">
                  <strong>Motivo de Egreso: <?php echo $row['motivoEgreso'] ?></strong>
                  <p>Observación:  <?php echo $row['observacion'];?></p>
                </div>
                <div class="delete-btns">
                  <button class="btn btn-del del-btn" name="delDesv" data-bs-toggle="modal" data-bs-target="#delDesv" data-sup-id="<?php echo $row['id'];?>" >Eliminar</button>
                </div>
              </div>  
            <?php   
              }
            } else{
                echo "<p>No hay desvinculaciones registradas el dia de hoy</p>";
              }
            ?>
          </div>
        </div>   
    </div>
  </div>

<!-- modal eliminar traslados -->
<div class="modal fade" id="delTraslado" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="delTrasladoLabel" aria-hidden="true">
  <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="delTrasladoLabel">Traslado de </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          <p>¿Estás seguro de que quieres eliminar este Traslado?</p>
          <form id="delTr" method="POST">
            <input type="hidden" name="idTr" id="idTr">
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" name="delTr" class="btn pull-right btn-del">Eliminar</button>
            </div>
          </form>
        </div>
      </div>
  </div>
</div>
<!-- modal eliminar desvinculacion -->
<div class="modal fade" id="delDesv" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="delDesvLabel" aria-hidden="true">
  <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="delDesvLabel">Desvinculación de </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          <p>¿Estás seguro de que quieres eliminar esta Desvinculación?</p>
          <form id="delTr" method="POST">
            <input type="hidden" name="idDesv" id="idDesv">
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" name="delDesv" class="btn pull-right btn-del">Eliminar</button>
            </div>
          </form>
        </div>
      </div>
  </div>
</div>

<!-- JS de Choices.js -->
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<!-- Popper.js (para tooltips y otros componentes) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<!-- Bootstrap Bundle (con Popper.js) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<!-- Scripts propios -->
<script src="../assets/js/sidebar.js"></script>
<script src="assets/js/traslados.js"></script>

</body>

</html>