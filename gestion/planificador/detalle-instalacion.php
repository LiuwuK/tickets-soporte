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
                    if (!empty($turnosExistentes)) {
                      foreach ($turnosExistentes as $index => $turno): ?>
                      <tr data-turno-id="<?= $turno['id'] ?>" data-jornada="<?= $turno['nJo'] ?>" data-codigo="<?= $turno['nJo'] ?>" >
                        <input type="hidden" id="sucursalId" value="<?= $_GET['id'] ?>">
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
                          <?= $turno['codigo'] ?>
                        </td>
                        <td class="align-middle text-center">
                          <button type="button" class="btn btn-updt btn-dates">Asignar fecha</button>
                          <button type="button" class="btn btn-danger" onclick="eliminarTurno(this)" <?= count($turnosExistentes) <= 1 ? 'disabled' : '' ?>>X</button>
                        </td>
                      </tr>
                      <?php 
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
                  <th scope="col" class="align-middle text-center">Rol</th>        
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
                    <tr data-id="<?= $colab['id'] ?>" data-sucursal-id="<?= $_GET['id'] ?>">
                      <td class="align-middle text-center">
                        <?= 'por definir' ?>
                      </td>
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
                        <button type="button" class="btn btn-updt btn-rol">Asignar rol</button>
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

<div class="modal fade" id="modalHorario" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Asignar Fechas a jornada</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formHorario">
          <input type="hidden" id="sucursalId" value="<?= $_GET['id'] ?>">
          <input type="hidden" id="turnoId">
          <input type="hidden" id="horaEntrada">
          <input type="hidden" id="horaSalida">
          <input type="hidden" id="patronJornada">
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Fecha Inicio</label>
              <input type="date" class="form-control" id="fechaInicio" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Fecha Termino</label>
              <input type="date" class="form-control" id="fechaTermino" required>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="guardarHorario">Guardar turnos</button>
      </div>
    </div>
  </div>
</div>

<!-- modal asignacion rol -->
<div class="modal fade" id="modalAsignarTurno">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Asignar Turno a Colaborador</h5>
      </div>
      <div class="modal-body">
        <form id="formAsignarTurno">
          <div class="mb-3">
            <label class="form-label">Fecha de inicio</label>
            <input type="date" class="form-control" id="fechaInicioAsignacion" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Fecha de finalización</label>
            <input type="date" class="form-control" id="fechaFinAsignacion">
          </div>
          <input type="hidden" id="sucursalId" value="<?= $_GET['id'] ?>">
          <input type="hidden" id="colabId" name="colabId">

          <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" id="mismoTurnoCheck">
            <label class="form-check-label" for="mismoTurnoCheck">
              Usar el mismo turno para todas las semanas
            </label>
          </div>

          <div id="turnoUnicoContainer" class="mb-3 d-none">
            <label class="form-label">Turno para todas las semanas</label>
            <select class="form-select" id="turnoUnicoSelect" name="turnoUnico">
              <option value="">Seleccionar turno</option>
              <option value="m1">Turno Mañana (m1)</option>
              <option value="m2">Turno Tarde (m2)</option>
            </select>
          </div>

          <div class="mb-3" id="contenedorSemanas">
            <!-- Aquí se generarán los turnos por semana -->
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary" id="btnConfirmarAsignacion">Asignar</button>
      </div>
    </div>
  </div>
</div>
<script>

const fechaInicioInput = document.getElementById('fechaInicioAsignacion');
const fechaFinInput = document.getElementById('fechaFinAsignacion');
const contenedorSemanas = document.getElementById('contenedorSemanas');
const checkMismoTurno = document.getElementById('mismoTurnoCheck');
const turnoUnicoContainer = document.getElementById('turnoUnicoContainer');
const turnoUnicoSelect = document.getElementById('turnoUnicoSelect');
const sucursalIdInput = document.getElementById('sucursalId');

// Detectar cambios en fechas
fechaInicioInput.addEventListener('change', generarTablaTurnos);
fechaFinInput.addEventListener('change', generarTablaTurnos);
checkMismoTurno.addEventListener('change', () => {
  turnoUnicoContainer.classList.toggle('d-none', !checkMismoTurno.checked);
  generarTablaTurnos();
});

async function cargarTurnosDisponibles() {
  const sucursalId = sucursalIdInput.value;
  const inicio = fechaInicioInput.value;
  const fin = fechaFinInput.value;

  const url = `assets/php/get-turnos-sucursal.php?sucursal_id=${sucursalId}&fecha_inicio=${inicio}&fecha_fin=${fin}`;
  if (!sucursalId || !inicio || !fin) return [];

  try {  
    const response = await fetch(`assets/php/get-turnos-sucursal.php?sucursal_id=${sucursalId}&fecha_inicio=${inicio}&fecha_fin=${fin}`);
    const data = await response.json();
  
    return data;
  } catch (error) {
    console.log(url);
    console.error('Error al cargar turnos:', error);
    return [];
  }
  

}

async function generarTablaTurnos() {
  const inicio = new Date(fechaInicioInput.value);
  const fin = new Date(fechaFinInput.value);

  if (isNaN(inicio) || isNaN(fin) || inicio > fin) {
    contenedorSemanas.innerHTML = '';
    return;
  }

  const turnosDisponibles = await cargarTurnosDisponibles();

  let html = '';
  let semanaIndex = 1;
  const fechaActual = new Date(inicio);
  const usarMismoTurno = checkMismoTurno.checked;

  if (usarMismoTurno) {
    html += `<input type="hidden" name="usarMismoTurno" value="1">`;

    // Actualizar opciones del <select> único
    turnoUnicoSelect.innerHTML = '<option value="">Seleccionar turno</option>' + 
      turnosDisponibles.map(turno => `<option value="${turno}">Turno ${turno}</option>`).join('');
  }

  const opciones = turnosDisponibles.map(turno => `<option value="${turno}">Turno ${turno}</option>`).join('');

  while (fechaActual <= fin) {
    const inicioSemana = new Date(fechaActual);
    const finSemana = new Date(fechaActual);
    finSemana.setDate(finSemana.getDate() + 6);
    if (finSemana > fin) finSemana.setTime(fin.getTime());

    const fechaStr = inicioSemana.toISOString().split('T')[0];

    html += `<input type="hidden" name="fechasInicio[]" value="${fechaStr}">`;

    if (!usarMismoTurno) {
      html += `
        <tr>
          <td>Semana ${semanaIndex} (${inicioSemana.toLocaleDateString()} - ${finSemana.toLocaleDateString()})</td>
          <td>
            <select class="form-select" name="turnos[]" required>
              <option value="">Seleccionar turno</option>
              ${opciones}
            </select>
          </td>
        </tr>
      `;
    }

    fechaActual.setDate(fechaActual.getDate() + 7);
    semanaIndex++;
  }

  if (usarMismoTurno) {
    contenedorSemanas.innerHTML = '';
  } else {
    contenedorSemanas.innerHTML = `
      <label class="form-label">Asignar turno por semana:</label>
      <table class="table table-sm">
        <thead>
          <tr><th>Semana</th><th>Turno</th></tr>
        </thead>
        <tbody>${html}</tbody>
      </table>
    `;
  }
}

// abrir modal de asignación
document.querySelectorAll('.btn-rol').forEach(btn => {
  btn.addEventListener('click', function() {
    const fila = this.closest('tr');
    const colabId = fila.dataset.id; 

    document.getElementById('colabId').value = colabId;
    document.getElementById('fechaInicioAsignacion').value = '';
    document.getElementById('fechaFinAsignacion').value = '';
    contenedorSemanas.innerHTML = '';
    turnoUnicoSelect.innerHTML = '<option value="">Seleccionar turno</option>';

    const modal = new bootstrap.Modal(document.getElementById('modalAsignarTurno'));
    modal.show();
  });
});

// Asignar evento a los botones "Asignar fecha"
document.querySelectorAll('.btn-dates').forEach(btn => {
  btn.addEventListener('click', function() {
    const fila = this.closest('tr');
    const turnoId = fila.dataset.turnoId; 
    
    document.getElementById('turnoId').value = turnoId;
    document.getElementById('horaEntrada').value = fila.cells[2].textContent.trim();
    document.getElementById('horaSalida').value = fila.cells[3].textContent.trim();
    document.getElementById('patronJornada').value = fila.dataset.jornada;
    
    const hoy = new Date();
    document.getElementById('fechaInicio').valueAsDate = hoy;
    
    const fin = new Date();
    fin.setMonth(fin.getMonth() + 1);
    document.getElementById('fechaTermino').valueAsDate = fin;
    
    const modal = new bootstrap.Modal(document.getElementById('modalHorario'));
    modal.show();
  });
});


let calendar; 
// Guardar horario
document.getElementById('guardarHorario').addEventListener('click', async function() {
  const btn = this;
  try {
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Guardando...';

      const form = document.getElementById('formHorario');
      if (!form.checkValidity()) {
          form.reportValidity();
          return;
      }

      const fechaInicio = new Date(document.getElementById('fechaInicio').value);
      const fechaTermino = new Date(document.getElementById('fechaTermino').value);
      if (fechaTermino < fechaInicio) {
          alert('La fecha de término no puede ser anterior a la de inicio');
          return;
      }

      const datos = {
          sucursal_id: document.getElementById('sucursalId').value,
          turno_id: document.getElementById('turnoId').value,
          hora_entrada: document.getElementById('horaEntrada').value,
          hora_salida: document.getElementById('horaSalida').value,
          fecha_inicio: document.getElementById('fechaInicio').value,
          fecha_fin: document.getElementById('fechaTermino').value,
          patron_jornada: document.getElementById('patronJornada').value
      };

      const response = await fetch('assets/php/test.php', {
          method: 'POST',
          headers: {
              'Content-Type': 'application/json',
          },
          body: JSON.stringify(datos)
      });

      const data = await response.json();

      if (!response.ok || !data.success) {
          throw new Error(data.message || 'Error al guardar el horario');
      }

      alert(data.message);

      // 🔁 Refrescar eventos del calendario
      if (calendar) {
          calendar.refetchEvents();
      } else {
          window.location.reload();
      }

      // Cerrar modal
      bootstrap.Modal.getInstance(document.getElementById('modalHorario')).hide();

  } catch (error) {
      console.error('Error:', error);
      alert(`Error: ${error.message}`);
  } finally {
      btn.disabled = false;
      btn.innerHTML = 'Guardar Horario';
  }
});

// CALENDARIO
document.addEventListener('DOMContentLoaded', function() {
  const sucursalId = document.getElementById('sucursalId').value;
  const calendarEl = document.getElementById('calendar');

  calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    locale: 'es',
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
    },
    displayEventTime: false,
    events: `assets/php/listar-horarios.php?sucursal_id=${sucursalId}`,
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