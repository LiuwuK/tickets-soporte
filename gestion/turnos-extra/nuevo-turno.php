<?php
session_start();
include("../../checklogin.php");
include BASE_PATH . 'dbconnection.php';
include("../assets/php/create-extra.php");

check_login();
?>
<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
  <meta charset="utf-8" />
  <title>Turnos extra</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <meta content="" name="description" />
  <meta content="" name="author" />

<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<!-- CSS personalizados -->
<link href="../../assets/css/sidebar.css" rel="stylesheet" type="text/css" />
<link href="../../projects/assets/css/create-project.css" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" href="../assets/css/nuevo-turno.css">

<!-- Toast notificaciones -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
</head>

<body class="test" >
    <!-- Sidebar -->
<div class="sidebar-overlay"></div> 
  <div class="page-container ">

    <div class="sidebar">
    <?php include("../../header-test.php"); ?>
      
    </div>
    <div class="page-content">
    <?php include("../../leftbar-test.php"); ?>
        <div class="content">
            <div class="page-title d-flex justify-content-between">
                <h2>Turnos Extra</h2>
                <button class=" btn-back" onclick="window.location.href='../turnos-extras.php';"> 
                    <i class="bi bi-arrow-left" ></i>
                </button>
            </div>
            <!-- Formulario nuevos turnos -->
            <form class="form-horizontal" name="form" method="POST" action="" >
                <div id="loading" style="display:none ;">
                    <div class="loading-spinner"></div>
                    <p>Procesando...</p>
                </div>  
                <div class="ticket-main col-xl-8 col-sm-12">
                    <div class="form-row d-flex justify-content-between mt-4">
                        <h3>Nuevo turno</h3>
                        <div class="excel">
                            <button class="btn btn-updt" onclick="window.location.href='assets/php/';">Importar Turnos extra</button>
                        </div>
                    </div>
                    <br>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="sucursal_id" class="form-label">Sucursal</label>
                            <select name="sucursal_id" id="sucursal_id" class="form-control form-control-sm" required>
                                <option value="">Seleccione una sucursal</option>
                                <!-- Opciones cargadas dinámicamente -->
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="fecha_turno" class="form-label">Fecha del Turno</label>
                            <input type="date" name="fecha_turno" id="fecha_turno" class="form-control form-control-sm" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="horas_cubiertas" class="form-label">Hora Cubiertas</label>
                            <input type="time" name="horas_cubiertas" id="horas_cubiertas" class="form-control form-control-sm" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="monto" class="form-label">Monto</label>
                            <input type="number" name="monto" id="monto" class="form-control form-control-sm" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="nombre_colaborador" class="form-label">Nombre del Colaborador</label>
                            <input type="text" name="nombre_colaborador" id="nombre_colaborador" class="form-control form-control-sm" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="rut" class="form-label">RUT</label>
                            <input type="text" name="rut" id="rut" class="form-control form-control-sm" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="motivo_turno_id" class="form-label">Motivo del Turno</label>
                            <select name="motivo_turno_id" id="motivo_turno_id" class="form-control form-control-sm" required>
                                <option value="">Seleccione un motivo</option>
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="autorizado_por" class="form-label">Autorizado por</label>
                            <select name="autorizado_por" id="autorizado_por" class="form-control form-control-sm" required>
                                <option value="">Seleccione un usuario</option>
                            </select>
                        </div>
                    </div>

                    <!-- Datos bancarios -->

                    <div class="form-row">
                        <div class="form-group col-md-6">
                        <h4>Datos bancarios</h4>   
                        </div>
                    </div>

                    <div class="footer">
                        <button type="submit" name="newExtra" class="btn btn-updt">Enviar</button>
                    </div>
                </div>
            </form>
        </div>   
    </div>

  </div>


<!-- Popper.js (para tooltips y otros componentes) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<!-- Bootstrap Bundle (con Popper.js) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<!-- Complementos/Plugins-->
<!-- Scripts propios -->
<script src="../../assets/js/sidebar.js"></script>
</body>

</html>