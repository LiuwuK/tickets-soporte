<?php
session_start();
include("../../checklogin.php");
include("../../dbconnection.php");
include("../assets/php/detalle-turno.php");
check_login();

?>
<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
  <meta charset="utf-8" />
  <title>Turnos Extra</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
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

<!-- Graficos -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
          <h2 class="det-view">Detalle del Turno</h2>
          <button class=" btn-back" onclick="window.location.href='ver-turnos.php';"> 
              <i class="bi bi-arrow-left" ></i>
          </button>
        </div> <br><br>
        <div class="d-container col-md-9 mx-auto">
          <!-- Datos Colaborador -->
          <div class="d-flex justify-content-between mt-3 mb-2">
            <h4 class="">Datos colaborador</h4>
            <span class="label label-estado"><?php echo $row['estado']; ?></span>
          </div>
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
          <!-- DATOS GENERALES -->
          <h4>Datos del Turno</h4>
          <hr width="90%" class="mx-auto">
          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="instalacion" >Instalación</label>
              <input type="text" name="instalacion" id="instalacion" value="<?php echo $row['instalacion']; ?>" class="form-control form-control-sm " readonly/>
            </div>
            <div class="form-group">
              <label class="form-label" for="horas" >Horas Cubiertas</label>
              <input type="number" name="horas" id="horas" value="<?php echo $row['horas']; ?>" class="form-control form-control-sm " readonly/>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="fechaTurno" >Fecha del Turno</label>
              <input type="text" name="fechaTurno" id="fechaTurno" value="<?php echo $row['fechaTurno']; ?>" class="form-control form-control-sm " readonly/>
            </div>
            <div class="form-group">
              <label class="form-label" for="monto" >Monto</label>
              <input type="text" name="monto" id="monto" value="$<?php echo number_format($row['monto'], 0, ',', '.'); ?>"  class="form-control form-control-sm " readonly/>
            </div>
          </div>

          <div class="form-row mt-3">
            <div class="form-group">
              <label class="form-label" for="motivo" >Motivo</label>
              <input type="text" name="motivo" id="motivo" value="<?php echo $row['motivo']; ?>" class="form-control form-control-sm " readonly/>
            </div>
            <div class="form-group">
              <label class="form-label" for="autorizadoPor" >Autorizado Por</label>
              <input type="text" name="autorizadoPor" id="autorizadoPor" value="<?php echo $row['autorizadoPor']; ?>" class="form-control form-control-sm " readonly/>
            </div>
          </div>
                    
          <br>
          <!-- Datos del pago -->
          <h4>Datos de Pago</h4>
          <hr width="90%" class="mx-auto">
          <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="banco" >Banco</label>
                <input type="text" name="banco" id="banco" value="<?php echo $row['banco']; ?>" class="form-control form-control-sm " readonly/>
            </div>
            <div class="form-group">
                <label for="rutCta" class="form-label">Rut de la Cuenta</label>
                <input name="rutCta" type="text" class="form-control form-control-sm" value="<?php echo $row['RUTcta'];?>"  readonly>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="numCuenta" >Numero de la cuenta</label>
                <input type="text" name="numCuenta" id="numCuenta" value="<?php echo $row['numCuenta']; ?>" class="form-control form-control-sm " readonly/>
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
<script src="../../assets/js/sidebar.js"></script>
</body>

</html>