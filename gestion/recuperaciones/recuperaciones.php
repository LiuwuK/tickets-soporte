<?php
session_start();
include("../../checklogin.php");
check_login();
require_once '../../dbconnection.php';
include("assets/php/recuperaciones.php");
?>
<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
  <meta charset="utf-8" />
  <title>Gestión</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <meta content="" name="description" />
  <meta content="" name="author" />

<!-- CSS de Choices.js -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<!-- Calendario CSS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
<!-- CSS personalizados -->
<link href="../../assets/css/sidebar.css" rel="stylesheet" type="text/css"/>
<link href="../../assets/css/main.css" rel="stylesheet" type="text/css"/>
<link href="../assets/css/recuperaciones.css" rel="stylesheet" type="text/css"/>
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
        <div class="content row d-flex justify-content-around">
            <div class="page-title d-flex justify-content-between">
                <h2>Recuperaciones</h2>
                <button class=" btn-back" onclick="window.location.href='../main.php';"> 
                    <i class="bi bi-arrow-left" ></i>
                </button>
            </div><br><br>

            <div class="col-md-4">
                <div class="rec-form" id="contenedor-resultados">
                    <h3>Total Recaudado</h3>
                </div>
                <div class="rec-form">
                    <form class="form-horizontal" name="form" method="POST" action="" >
                        <div id="loading" style="display:none ;">
                            <div class="loading-spinner"></div>
                            <p>Procesando...</p>
                        </div>
                        <h3>Ingresar Recuperación</h3>
                        <br>
                        <div class="form-group">
                            <label class="form-label">Instalación <span>*</span></label>
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
                        <div class="form-group">
                            <label for="monto" class="form-label">Monto <span>*</span></label>
                            <div class="input-group">
                                <span class="input-group-text" id="montoP">$</span>
                                <input type="number" name="monto" id="monto" class="form-control form-control-sm" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="fecha" class="form-label">Fecha de recuperación <span>*</span></label>
                            <input type="date" name="fecha" id="fecha" class="form-control form-control-md" required >
                        </div>
                        <div class="btn-div d-flex justify-content-end mt-4 ">
                            <button class="btn btn-updt" type="submit" name="send">Enviar</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="calendario col-md-7">
                <div class="filtros mb-4 d-flex justify-content-between">
                    <div class="filtro">
                        <label>Sucursal:</label>
                        <select id="filtro-sucursal" class="form-select ">
                            <option value="">Todas las sucursales</option>
                            <?php 
                                foreach ($inst AS $row) {
                                    echo "<option value='".$row['id']."'>".$row['nombre']."</option>";
                                };
                            ?>
                        </select>
                    </div>
                    <div class="exp">
                        <button type="button" class="btn btn-excel" onclick="window.location.href='assets/php/exportar-recuperaciones.php';">
                            <i class="bi bi-file-earmark-excel"></i> 
                            Exportar Recuperaciones
                        </button>
                    </div>
                </div>
                <div class="calendar-main" id="calendario">
                </div>
            </div>
        </div>   
    </div>
  </div>


<!-- Modal detalles del evento -->
<div class="modal fade" id="eventoModal" tabindex="-1" aria-labelledby="eventoModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
    <form class="form-horizontal" name="form" method="POST" action="" >
      <div class="modal-header">
        <h5 class="modal-title" id="eventoModalLabel">Detalles de Recuperación</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body d-flex modal-r">
        <div class="form-modal mb-3">
            <label for="inputFecha" class="form-label">Fecha de recuperación <span>*</span></label>
            <input type="date" name="fecha" id="inputFecha" class="form-control form-control-md" required>
        </div>
        <div class="form-modal">
            <label for="inputMonto" class="form-label">Monto Recuperado <span>*</span></label>
            <div class="input-group">
                <span class="input-group-text">$</span>
                <input type="number" name="monto" id="inputMonto" class="form-control form-control-sm" required>
            </div>
        </div>
        <input type="hidden" id="inputId" name="id">
      </div>
      <div class="modal-footer">
            <button type="submit" name="edit" class="btn btn-default" >Editar</button>
            <button type="submit" name="del" class="btn pull-right btn-del">Eliminar</button>
      </div>
    </form>
    </div>
  </div>
</div>


<!-- JS de Choices.js -->
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<!-- Popper.js (para tooltips y otros componentes) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<!-- Calendario -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/google-calendar/main.min.js"></script>
<!-- Bootstrap Bundle (con Popper.js) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<!-- Complementos/Plugins-->
<!-- Scripts propios -->
<script src="assets/js/recuperaciones.js"></script>
</body>

</html>