<?php
session_start();
include("../../checklogin.php");
include("../../dbconnection.php");
include("../assets/php/ver-turnos.php");
check_login();

?>
<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
  <meta charset="utf-8" />
  <title>Turnos Extra</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <meta name="google" content="notranslate">
  <meta content="" name="description" />
  <meta content="" name="author" />
<!-- CSS de Choices.js -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<!-- CSS personalizados -->
<link href="../../assets/css/sidebar.css" rel="stylesheet" type="text/css"/>
<link href="../assets/css/historico-TD.css" rel="stylesheet" type="text/css"/>
<!-- Toast notificaciones -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
</head>

<body class="test" >
    <!-- Sidebar -->
  <div class="page-container ">
    <div class="sidebar">
      <?php include("../../header-test.php"); ?>
    </div>
    <div class="page-content">
    <?php include("../../leftbar-test.php"); ?>
        <div class="page-title d-flex justify-content-between">
          <h2>Turnos extra</h2>
          <button class=" btn-back" onclick="window.location.href='../turnos-extras.php';"> 
              <i class="bi bi-arrow-left" ></i>
          </button>
        </div> <br><br>
        <form method="GET" action="assets/php/exportar-turnos.php" class="">
          <div class="filtros d-flex justify-content-between form-f">
            <div class="d-flex justify-content-arround mb-3">
              <div class="all-fil">
                <label for="filtroTexto">Buscar</label>
                <input type="text" id="filtroTexto" name="texto" class="form-control form-control-sm fil" placeholder="Buscar por nombre, instalación, etc.">
              </div>
              <div class="all-fil">  
                <label for="filtroEstado" >Estado</label>
                <select id="filtroEstado" name="estado"  class="form-select form-select-sm fil">
                    <option value="">Todos los estados</option>
                    <option value="pendiente en operaciones">Pendiente en Operaciones</option>
                    <option value="aprobado">Aprobado</option>
                    <option value="rechazado">Rechazado</option>
                </select>
              </div>
              <div class="all-fil">
                <label for="filtroFechaInicio">Fecha Inicio</label>
                <input name="fecha_inicio" type="date" class="form-control form-control-sm fil" id="filtroFechaInicio">
              </div>
              <div class="all-fil">
                <label for="filtroFechaInicio">Fecha Fin</label>
                <input name="fecha_fin" type="date" class="form-control form-control-sm fil" id="filtroFechaFin">
              </div>
            </div>
              <button type="submit" class="btn btn-excel">
                  <i class="bi bi-file-earmark-excel"></i> 
                  Exportar a Excel
              </button>
            
          </div>
        </form>
        <div id="resultadoTurnos" class="content"></div>
    </div>
  </div>

<!-- Popper.js (para tooltips y otros componentes) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<!-- Bootstrap Bundle (con Popper.js) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<!-- Scripts propios -->
<script src="../assets/js/sidebar.js"></script>
<script src="../assets/js/ver-turno.js"></script>

</body>

</html>