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
              <?php 
                if ($row['razon_social'] === 'ARGENTO SEGURIDAD') {
                  echo '<img src="assets/img/argento.png" alt="Logo ARGENTO" />';
                } elseif ($row['razon_social'] === 'RESULVE') {
                  echo '<img src="assets/img/resuelve.png" alt="Logo resuelve" />';
                } elseif ($row['razon_social'] === 'SAFETECK SPA') {
                  echo '<img src="assets/img/safeteck.png" alt="Logo safeteck" />';
                } else {
                  echo '<h1>Logo razón social</h1>';
                }
              ?>
            </div>
        </div>
        <!-- TURNOS -->
        <div class="main-turnos col-md-11 d-flex justify-content-between">          
          <div class="d-container col-md-6 info-turnos">
          <form id="formTurnos" action="assets/php/guardar-turnos.php" method="post" >
            <table class="table table-hover" id="tabla-turnos">
                <thead>
                    <tr>
                        <th scope="col" class="align-middle text-center" >Turno</th>
                        <th scope="col" class="align-middle text-center">Tipo Jornada</th>
                        <th scope="col" class="align-middle text-center">Hora Entrada</th>
                        <th scope="col" class="align-middle text-center">Hora Salida</th>
                        <th scope="col" class="align-middle text-center">Codigo</th>
                        <th scope="col" class="align-middle text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody id="cuerpo-tabla">
                  <!-- Mostrar turnos existentes -->
                  <?php 
                    $codigo = 1;
                    if (!empty($turnosExistentes)) {
                        foreach ($turnosExistentes as $index => $turno): ?>
                        <tr>
                            <td class="align-middle text-center">
                              <?= $turno['nombre_turno'] ?>
                            </td>
                            <td class="align-middle text-center">
                              <?= $turno['nJo'] ?>
                            </td>
                            <td class="align-middle text-center">
                                <?= $turno['hora_entrada'] ?>
                            </td>
                            <td class="align-middle text-center">
                              <?= $turno['hora_salida'] ?>
                            </td>
                            <td class="align-middle text-center">
                              <?= 'M'.$codigo ?>
                            </td>
                            <td class="align-middle text-center">
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
        
        <!-- COLABORADORES -->
        <div class="main-colab col-md-11 d-flex justify-content-between align-items-start">
          <div class="d-container col-md-6 h-auto">
            <table class="table table-hover" id="tabla-colab">
              <thead>
                <tr>
                    <th scope="col" class="align-middle text-center w-20">Rut</th>
                    <th scope="col" class="align-middle text-center">Nombre</th>
                    <th scope="col" class="align-middle text-center">Telefono</th>
                    <th scope="col" class="align-middle text-center">Fecha Inicio Contrato</th>
                    <th scope="col" class="align-middle text-center">Acciones</th>
                </tr>
              </thead>
              <tbody id="cuerpo-colab">
                <!-- Mostrar colaboradores Asociados -->
                <?php 
                  if (!empty($colaboradorAsociado)) {
                    foreach ($colaboradorAsociado as $index => $colab): ?>
                    <tr data-id="<?= $colab['id'] ?>">
                      <td class="align-middle text-center">
                        <?= $colab['rut'] ?>
                      </td>
                      <td class="align-middle text-center">
                        <?= $colab['name'].' '.$colab['fname'].' '.$colab['mname'] ?>
                      </td>
                      <td class="align-middle text-center">
                        <?= $colab['phone'] ? $colab['phone'] : 'Sin definir'; ?>
                      </td>
                      <td class="align-middle text-center">
                        <?= $colab['entry_date'] ?>
                      </td>
                      <td class="align-middle text-center">
                        <button type="submit" class="btn btn-updt">Asignar Horario</button>
                      </td>
                    </tr>
                    <?php 
                    endforeach; 
                  }
                ?>
                                

              </tbody>
            </table>
          </div>
          <div class="d-container col-md-5 calendario" id="calendar"></div>
        </div>                  
    </div>
  </div>

<!-- Modal para asignar turno -->
<div class="modal fade" id="modalHorario" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Asignar Turno a <span id="nombreColaborador"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formHorario">
          <input type="hidden" id="colaboradorId" >
          <input type="hidden" id="sucursalId" value="<?= $_GET['id'] ?>">
          
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Fecha Inicio</label>
              <input type="date" class="form-control" id="fechaInicio" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Turno</label>
              <select class="form-control" id="turnoSeleccionado" required>
                <option value="">Seleccionar turno</option>
                <?php foreach($turnosExistentes as $turno): ?>
                <option value="<?= $turno['id'] ?>" 
                        data-hora-entrada="<?= $turno['hora_entrada'] ?>"
                        data-hora-salida="<?= $turno['hora_salida'] ?>"
                        data-jornada="<?= $turno['nJo'] ?>">
                  <?= $turno['nombre_turno'] ?> (<?= $turno['hora_entrada'] ?> - <?= $turno['hora_salida'] ?>)
                </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Duración (semanas)</label>
              <input type="number" class="form-control" id="duracion" min="1" value="4" required>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="guardarHorario">Guardar Turno</button>
      </div>
    </div>
  </div>
</div>
<script>
  // Asignar Horario
document.querySelectorAll('#tabla-colab .btn-updt').forEach(btn => {
  btn.addEventListener('click', function() {
    const fila = this.closest('tr');
    colaboradorSeleccionado = {
      id: fila.dataset.id,
      nombre: fila.querySelector('td:nth-child(2)').textContent.trim()
    };
    
    // Configurar el modal
    document.getElementById('nombreColaborador').textContent = colaboradorSeleccionado.nombre;
    document.getElementById('colaboradorId').value = colaboradorSeleccionado.id;
    document.getElementById('fechaInicio').valueAsDate = new Date();
    
    // Mostrar el modal
    const modal = new bootstrap.Modal(document.getElementById('modalHorario'));
    modal.show();
  });
});

// Guardar el horario
document.getElementById('guardarHorario').addEventListener('click', async function() {
    const btn = this;
    try {
        // Deshabilitar botón durante la solicitud
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Guardando...';

        // Validar formulario
        const form = document.getElementById('formHorario');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        // Obtener datos del formulario
        const turnoSelect = document.getElementById('turnoSeleccionado');
        const turnoOption = turnoSelect.selectedOptions[0];
        
        const datos = {
            colaborador_id: document.getElementById('colaboradorId').value,
            sucursal_id: document.getElementById('sucursalId').value,
            fecha_inicio: document.getElementById('fechaInicio').value,
            turno_id: turnoSelect.value,
            hora_entrada: turnoOption.dataset.horaEntrada,
            hora_salida: turnoOption.dataset.horaSalida,
            jornada: turnoOption.dataset.jornada,
            duracion: document.getElementById('duracion').value
        };

        console.log(datos)
        // Realizar petición
        const response = await fetch('assets/php/guardar-horario.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(datos)
        });

        // Verificar si la respuesta es JSON
        const contentType = response.headers.get('content-type');
        const text = await response.text();

        if (!contentType || !contentType.includes('application/json')) {
            console.error('Respuesta no JSON:', text);
            throw new Error(`La respuesta del servidor no es JSON: ${text}`);
        }

        const data = JSON.parse(text);
        

        if (!response.ok || !data.success) {
            throw new Error(data.message || 'Error desconocido al guardar el horario');
        }

        // Éxito - actualizar interfaz
        alert(data.message);
        if (typeof calendar !== 'undefined' && calendar.refetchEvents) {
            calendar.refetchEvents();
        }
        
        // Cerrar modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalHorario'));
        if (modal) modal.hide();

    } catch (error) {
        console.error('Error al guardar horario:', error);
        alert(`Error: ${error.message}`);

    } finally {
        // Restaurar botón
        btn.disabled = false;
        btn.innerHTML = 'Guardar Horario';
    }
});
//CALENDARIO
  document.addEventListener('DOMContentLoaded', function() {
    const sucursalId = document.getElementById('sucursalId').value;
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      locale: 'es',
      headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay'
      },
      dateClick: function(info) {
          alert('Fecha clickeada: ' + info.dateStr);
      },
      eventClick: function(info) {
          alert('Evento clickeado: ' + info.event.title);
      },
      events:  `assets/php/listar-horarios.php?sucursal_id=${sucursalId}`
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