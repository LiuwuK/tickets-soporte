<?php
session_start();
include("../../checklogin.php");
include("../../dbconnection.php");
include("assets/php/detalle-instalacion.php");
check_login();


if (isset($_SESSION['success_message'])) {
    echo '<script>alert("'.addslashes($_SESSION['success_message']).'");</script>';
    unset($_SESSION['success_message']);
}

if (isset($_SESSION['error_message'])) {
    echo '<script>alert("'.addslashes($_SESSION['error_message']).'");</script>';
    unset($_SESSION['error_message']);
}

?>
<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
  <meta charset="utf-8" />
  <title>Instalaciones</title>
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
<link href="../assets/css/historico-TD.css" rel="stylesheet" type="text/css"/>
<link href="assets/css/detalle-instalacion.css" rel="stylesheet" type="text/css"/>
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
          <h2 class="det-view">Detalle instalacion</h2>
          <button class=" btn-back" onclick="window.location.href='instalaciones.php';"> 
              <i class="bi bi-arrow-left" ></i>
          </button>
        </div> <br><br>
        <!-- INFO SUCURSAL -->
        <div class="d-container col-md-11 mx-auto d-flex justify-content-arround">
            <div class="info col-md-6">
              <h3><?php echo $row['nombre']; ?></h3>
              <p>
                <strong>Razon Social: </strong>
                <?php echo $row['razon_social'] ? $row['razon_social'] : 'Sin definir'; ?>
              </p>
              <p>
                <strong>Ceco: </strong>
                <?php echo $row['cost_center']; ?>
              </p>
              <p>
                <strong>Direccion: </strong>
                <?php echo $row['direccion_calle']; ?>
              </p>
              <p>
                <strong>Supervisor: </strong>
                <?php echo $row['nSup']; ?>
              </p>
              <p>
                <strong>Puestos: </strong>
              </p>
              <p>
                <strong>Dotacion Optima: </strong>
              </p>
              <p>
                <strong>Dotacion Real: </strong>
                <?php echo $dotacion; ?>
              </p>

            </div>
            <div class="img col-md-6 text-center">
              <h1>Logo razon social</h1>
            </div>
        </div>
        <!-- TURNOS -->
        <div class="main-turnos col-md-11 d-flex justify-content-between">          
          <div class="d-container col-md-6 info-turnos">
          <form id="formTurnos" action="assets/php/guardar-turnos.php" method="post" >
            <table class="table table-hover" id="tabla-turnos">
                <thead>
                    <tr>
                        <th scope="col">Turno</th>
                        <th scope="col">Tipo Jornada</th>
                        <th scope="col">Hora Entrada</th>
                        <th scope="col">Hora Salida</th>
                        <th scope="col">Codigo</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody id="cuerpo-tabla">
                    <!-- Mostrar turnos existentes -->
                    <?php 
                      $codigo = 1;
                      if (!empty($turnosExistentes)) {
                          foreach ($turnosExistentes as $index => $turno): ?>
                          <tr>
                              <td>
                                  <?= $turno['nombre_turno'] ?>
                              </td>
                              <td>
                                  <?= $turno['nJo'] ?>
                              </td>
                              <td>
                                  <?= $turno['hora_entrada'] ?>
                              </td>
                              <td>
                                  <?= $turno['hora_salida'] ?>
                              </td>
                              <td>
                                  <?= 'M'.$codigo ?>
                              </td>
                              <td>
                                  <button type="button" class="btn btn-danger" onclick="eliminarTurno(this)" <?= count($turnosExistentes) <= 1 ? 'disabled' : '' ?>>X</button>
                              </td>
                          </tr>
                          <?php 
                          $codigo = $codigo + 1;
                          endforeach; 
                      }
                    ?>
                                      
                    <!-- Plantilla para nuevas filas (hidden) -->
                    <tr id="plantilla-fila" style="display: none;">
                      <td><input type="text" name="nuevos_turnos[][nombre]" class="form-control"></td>
                      <td>
                          <select name="nuevos_turnos[][jornada_id]" class="form-control">
                              <option value="">Seleccione una jornada</option>
                              <?php
                              mysqli_data_seek($jornadas, 0); 
                              while ($row = mysqli_fetch_assoc($jornadas)) {
                                  echo '<option value="'.htmlspecialchars($row['id']).'">'
                                      .htmlspecialchars($row['tipo_jornada'])
                                      .'</option>';
                              }
                              ?>
                          </select>
                      </td>
                      <td><input type="time" name="nuevos_turnos[][hora_entrada]" class="form-control"></td>
                      <td><input type="time" name="nuevos_turnos[][hora_salida]" class="form-control"></td>
                      <td>
                          <input type="hidden" name="sucursal_id" value="<?= $_GET['id'] ?>">
                      </td>
                      <td><button type="button" class="btn btn-danger" onclick="eliminarTurno(this)">X</button></td>
                    </tr>
                </tbody>
            </table>
              
            <div class="mb-3">
                <button type="button" class="btn btn-updt" onclick="agregarTurno()">Agregar Turno</button>
                <button type="submit" class="btn btn-default">Guardar Cambios</button>
            </div>
          </form>
          </div>

          <div class="d-container col-md-5">
          </div>
        </div>
        
        <div class="main-colab col-md-11 d-flex justify-content-between align-items-start">
          <div class="d-container col-md-5 h-auto">
                    
          </div>
          <div class="d-container col-md-6 calendario" id="calendar"></div>
        </div>                  
    </div>
  </div>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: [
            {
                title: 'Turno Mañana',
                start: new Date(),
                color: '#378006'
            }
        ],
        dateClick: function(info) {
            alert('Fecha clickeada: ' + info.dateStr);
        },
        eventClick: function(info) {
            alert('Evento clickeado: ' + info.event.title);
        }
    });
    
    calendar.render();
});
// Contador para nuevos turnos
let contadorNuevos = <?= !empty($turnosExistentes) ? count($turnosExistentes) : 0 ?>;
</script>
<!-- Popper.js (para tooltips y otros componentes) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<!-- Bootstrap Bundle (con Popper.js) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<!-- Calendario -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/google-calendar/main.min.js"></script>
<!-- Scripts propios -->
<script src="../../assets/js/sidebar.js"></script>
<script src="assets/js/detalle-instalacion.js"></script>
</body>

</html>