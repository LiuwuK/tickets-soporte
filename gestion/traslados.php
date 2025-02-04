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
                      <select name="solicitante" class="form-select form-select-sm" >
                        <option value="">Seleccionar</option>
                        <?php
                        while ($row = mysqli_fetch_assoc($result)) {
                          echo "<option value=".$row['id'].">".$row['name']."</option>";
                        };
                        ?>
                      </select>
                  </div>
              </div>
              <div class ="form-group">
                <label for="fechaSoli" class="form-label">Fecha <span>*</span></label>
                <input type="date" class="form-control form-control-sm" id="fechaSoli" name="fechaSoli" required>
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
                    Formulario de Descvinculacion
                  </label>
                </div>
              </div>
            </div>
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
                      <select name="supervisor" class="form-select form-select-sm" required>
                        <option value="">Seleccionar</option>
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
              <div class ="form-group">
                  <label class="form-label">Instalacion de Origen <span>*</span></label>
                  <div >
                      <select name="instalacion" class="form-select form-select-sm" required>
                        <option value="">Seleccionar</option>
                      </select>
                  </div>
              </div>
              <div class ="form-group">
                  <label class="form-label">Jornada de Origen <span>*</span></label>
                  <div >
                      <select name="jornada" class="form-select form-select-sm" required>
                        <option value="">Seleccionar</option>
                      </select>
                  </div>
              </div>

            </div>
            <div class="form-row mx-auto">
              <div class ="form-group">
                  <label class="form-label">Motivo de traslado</label>
                  <div >
                      <select name="motivo" class="form-select form-select-sm" >
                        <option value="">Seleccionar</option>
                      </select>
                  </div>
              </div>
            </div>
            <div class="form-row mx-auto">
              <div class ="form-group">
                <label class="form-label">Instalacion de Destino <span>*</span></label>
                <div >
                    <select name="inDestino" class="form-select form-select-sm" required>
                      <option value="">Seleccionar</option>
                    </select>
                </div>
              </div>
              <div class ="form-group">
                <label class="form-label">Jornada de Destino <span>*</span></label>
                <div >
                    <select name="joDestino" class="form-select form-select-sm" required>
                      <option value="">Seleccionar</option>
                    </select>
                </div>
              </div>
            </div>
            <div class="form-row mx-auto">
              <div class ="form-group">
                <label class="form-label">Rol <span>*</span></label>
                <div >
                    <select name="rol" class="form-select form-select-sm" required>
                      <option value="">Seleccionar</option>
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
                      <select name="supervisorDestino" class="form-select form-select-sm" required>
                        <option value="">Seleccionar</option>
                      </select>
                  </div>
              </div>
            </div>
            <div class="footer">
              <button class="btn btn-updt">Enviar</button>
            </div>
          </div>

          <div class="col-md-10 form-data mx-auto" id="desvinculacionForm" style="display: none;">
            <h3>Formulario de Desvinculacion</h3>
            <div class="form-row mx-auto">
              <div class ="form-group">  
                  <div class="d-flex msg">
                    <label class="form-label">Supervisor Encargado <span>*</span></label>
                    <p>(Supervisor a cargo de Instalación)</p>
                  </div>
                  <div >
                      <select name="supervisorEncargado" class="form-select form-select-sm" required>
                        <option value="">Seleccionar</option>
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
                      <select name="instalacion" class="form-select form-select-sm" required>
                        <option value="">Seleccionar</option>
                      </select>
                  </div>
              </div>
              <div class ="form-group">
                  <label class="form-label">Motivo de Egreso <span>*</span></label>
                  <div >
                      <select name="motivo" class="form-select form-select-sm" required>
                        <option value="">Seleccionar</option>
                      </select>
                  </div>
              </div>

            </div>
            <div class="form-row mx-auto">
              <div class="form-group">
                <label for="observacion" class="form-label">Observación <span>*</span></label>
                <textarea class="form-control form-control-sm" id="observacion" name="observacion" rows="6"></textarea>
              </div>
            </div>

            <div class="footer">
              <button class="btn btn-updt">Enviar</button>
            </div>
          </div>
        </div>   
    </div>
  </div>

<!-- Popper.js (para tooltips y otros componentes) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<!-- Bootstrap Bundle (con Popper.js) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<!-- Scripts propios -->
<script src="../assets/js/sidebar.js"></script>
<script src="assets/js/traslados.js"></script>

</body>

</html>