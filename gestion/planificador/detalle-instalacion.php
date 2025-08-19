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

$diasSemana = ['lunes','martes','miércoles','jueves','viernes','sábado','domingo'];
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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

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
                <?php echo $row['puestos']; ?>
              </p>
              <p>
                <strong>Dotacion Optima: </strong>
                <?php echo $row['dotacion_optima']; ?>
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
          <div class="d-container col-md-6 info-turnos table-responsive-custom">
            <form id="formTurnos" action="assets/php/guardar-turnos.php" method="post">
              <input type="hidden" name="sucursal_id" value="<?= $_GET['id'] ?>">
              <div class="table-responsive">
                <table class="table table-hover" id="tabla-turnos">
                  <thead>
                    <tr>
                      <th scope="col" class="align-middle text-center">Turno</th>
                      <th scope="col" class="align-middle text-center">Tipo Jornada</th>
                      <th scope="col" class="align-middle text-center">Hora Entrada</th>
                      <th scope="col" class="align-middle text-center">Hora Salida</th>
                      <th scope="col" class="align-middle text-center">Código</th>
                      <th scope="col" class="align-middle text-center">Acciones</th>
                    </tr>
                  </thead>
                  <tbody id="cuerpo-tabla">
                    <!-- Mostrar turnos existentes -->
                    <?php if (!empty($turnosExistentes)): ?>
                      <?php foreach ($turnosExistentes as $index => $turno): ?>
                        
                        <tr data-turno-id="<?= $turno['id'] ?>" data-jornada="<?= htmlspecialchars($turno['nJo']) ?>">
                          <input type="hidden" name="turnos[<?= $index ?>][id]" value="<?= $turno['id'] ?>">
                          <td class="align-middle text-center">
                            <input type="text" name="turnos[<?= $index ?>][nombre]" 
                                  class="form-control" style="min-width: 170px;" value="<?= htmlspecialchars($turno['nombre_turno']) ?>">
                          </td>
                          
                          <td class="align-middle text-center">
                            <select style="min-width: 70px;" name="turnos[<?= $index ?>][jornada_id]" class="form-control">
                              <option value="">Seleccione una jornada</option>
                              <?php mysqli_data_seek($jornadas, 0); ?>
                              <?php while ($row = mysqli_fetch_assoc($jornadas)): ?>
                                <option value="<?= htmlspecialchars($row['id']) ?>" 
                                  <?= $row['id'] == $turno['jornada_id'] ? 'selected' : '' ?>>
                                  <?= htmlspecialchars($row['tipo_jornada']) ?>
                                </option>
                              <?php endwhile; ?>
                            </select>
                          </td>
                          
                          <td class="align-middle text-center">
                            <table class="table table-sm mb-0">
                                <?php foreach ($diasSemana as $dia): 
                                  $entrada = $turno['dias'][$dia]['entrada'] ?? '';
                                  $salida = $turno['dias'][$dia]['salida'] ?? '';
                                  if (empty($entrada) && empty($salida)) continue;
                                ?>
                                  <tr>
                                    <td class="text-nowrap align-middle"><strong><?= ucfirst($dia) ?>:</strong></td>
                                    <td>
                                      <input type="time" class="form-control"
                                            name="turnos[<?= $index ?>][dias][<?= $dia ?>][entrada]"
                                            value="<?= $entrada ?>">
                                    </td>
                                  </tr>
                                <?php endforeach; ?>
                            </table>
                          </td>
                          
                          <td class="align-middle text-center">
                            <table class="table table-sm mb-0">
                              <?php foreach ($diasSemana as $dia): 
                                $entrada = $turno['dias'][$dia]['entrada'] ?? '';
                                $salida = $turno['dias'][$dia]['salida'] ?? '';
                                if (empty($entrada) && empty($salida)) continue;
                              ?>
                                <tr>
                                  <td>
                                    <input type="time" class="form-control"
                                          name="turnos[<?= $index ?>][dias][<?= $dia ?>][salida]"
                                          value="<?= $salida ?>">
                                  </td>
                                </tr>
                              <?php endforeach; ?>
                            </table>
                          </td>
                          
                          <td class="align-middle text-center">
                            <span style="min-width: 70px;" class="form-control-plaintext"><?= htmlspecialchars($turno['codigo']) ?></span>
                            <input type="hidden" name="turnos[<?= $index ?>][codigo]" value="<?= htmlspecialchars($turno['codigo']) ?>">
                          </td>
                          
                          <td class="align-middle text-center">
                            <button type="button" class="btn btn-updt btn-dates">Asignar fecha</button>
                            <button type="button" class="btn btn-danger" onclick="eliminarTurno(this)" 
                              <?= count($turnosExistentes) <= 1 ? 'disabled' : '' ?>>X</button>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <!-- Plantilla para nuevas filas (hidden) -->
                    <tr id="plantilla-fila" style="display: none;">
                      <td><input type="text" name="nuevos_turnos[][nombre]" class="form-control"></td>
                      <td>
                        <select name="nuevos_turnos[][jornada_id]" class="form-control">
                          <option value="">Seleccione una jornada</option>
                          <?php mysqli_data_seek($jornadas, 0); ?>
                          <?php while ($row = mysqli_fetch_assoc($jornadas)): ?>
                            <option value="<?= htmlspecialchars($row['id']) ?>">
                              <?= htmlspecialchars($row['tipo_jornada']) ?>
                            </option>
                          <?php endwhile; ?>
                        </select>
                      </td>
                      <td>
                        <table class="table table-sm mb-0">
                          <?php foreach ($diasSemana as $dia): ?>
                            <tr>
                              <td class="text-nowrap align-middle"><strong><?= ucfirst($dia) ?>:</strong></td>
                              <td>
                                <input type="time" class="form-control" 
                                      name="nuevos_turnos[][dias][<?= $dia ?>][entrada]">
                              </td>
                            </tr>
                          <?php endforeach; ?>
                        </table>
                      </td>
                      <td>
                        <table class="table table-sm mb-0">
                          <?php foreach ($diasSemana as $dia): ?>
                            <tr>
                              <td>
                                <input type="time" class="form-control" 
                                      name="nuevos_turnos[][dias][<?= $dia ?>][salida]">
                              </td>
                            </tr>
                          <?php endforeach; ?>
                        </table>
                      </td>
                      <td>
                        <span class="form-control-plaintext">Nuevo</span>
                        <input type="hidden" name="nuevos_turnos[][codigo]" value="">
                      </td>
                      <td><button type="button" class="btn btn-danger" onclick="eliminarTurno(this)">X</button></td>
                    </tr>
                  </tbody>
                </table>
              </div>
              
              <div class="mb-3">
                <button type="button" class="btn btn-updt" id="btn-agregar-turno">Agregar Turno</button>
                <button type="submit" class="btn btn-default">Guardar Cambios</button>
              </div>
            </form>
          </div>
          <!-- COLABORADORES -->
          <div class="col-md-6">
            <div class="d-container h-auto tt table-responsive-custom">
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
                      <tr data-id="<?= $colab['id'] ?>" data-nombre="<?= $colab['name'].' '.$colab['fname'].' '.$colab['mname'] ?>" data-sucursal-id="<?= $_GET['id'] ?>">
                        <td class="align-middle text-center">
                          <?= $colab['codigos_turnos'] ?>
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
          </div>
        </div>
        
      <!-- Calendario -->
      <div class="main-colab col-md-9 d-flex flex-column d-container"> 
        <div class="mb-3 d-flex justify-content-between"> 
          <div class="select col-md-5">
            <label class="form-label" for="colaborador">Filtrar por Colaborador</label>
            <select class="form-select form-select-sm" name="colaborador" id="filtroColaborador">
                <option value="">Todos los turnos</option>
              <?php foreach ($colaboradorAsociado as $index => $colab): ?>
                <option value="<?= $colab['id'] ?>">
                  <?= $colab['name'].' '.$colab['fname'].' '.$colab['mname'] ?>
                </option>
              <?php endforeach;?> 

            </select>
          </div>
          <div class="btns col-md-5 d-flex justify-content-end p-3">
            <button type="button" class="excel-btn btn ">
              <i class="bi bi-file-earmark-excel"></i>
            </button>
            <button type="button" class="pdf-btn btn">
              <i class="bi bi-file-earmark-pdf"></i>
            </button>
          </div>
        </div>
        <div class="col-md-12 calendario" id="calendar">
        </div>
      </div>           
    </div>
  </div>

<!-- modal asignacion fecha de turno -->
<div class="modal fade" id="modalHorario" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg"> 
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Asignar Fechas a jornada</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formHorario">
          <input type="hidden" id="sucursalId" value="<?= $_GET['id'] ?>">
          <input type="hidden" id="turnoId">
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
          <div class="row mb-3">
            <div class="mb-3">
              <label class="form-label">Bloque</label>
              <select class="form-select" id="bloqueSelect" name="bloqueSelect">
                <option value="AM">AM</option>
                <option value="PM">PM</option>
              </select>
            </div>
          </div>
          
          <!-- Tabla de horarios por día -->
          <div class="table-responsive">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>Día</th>
                  <th>Hora Entrada</th>
                  <th>Hora Salida</th>
                  <th>Aplicar</th>
                </tr>
              </thead>
              <tbody id="horariosDias">
              </tbody>
            </table>
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
<!-- modala asignacion colaborador -->
<div class="modal fade" id="modalAsignarTurno">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Asignar Turno a Colaborador</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formAsignarTurno">
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Fecha de inicio *</label>
              <input type="date" class="form-control" id="fechaInicioAsignacion" required>
              <small class="text-muted">Primer día de asignación</small>
            </div>
            <div class="col-md-6">
              <label class="form-label">Fecha de finalización *</label>
              <input type="date" class="form-control" id="fechaFinAsignacion" required>
              <small class="text-muted">Último día de asignación</small>
            </div>
          </div>
          
          <input type="hidden" id="sucursalId" value="<?= $_GET['id'] ?>">
          <input type="hidden" id="colabId" name="colabId">
          <input type="hidden" id="colabNombre" name="colabNombre">
          
          <div class="alert alert-info mb-3">
            <i class="bi bi-info-circle"></i> Asignando turno a: <strong id="nombreColaborador"></strong>
          </div>
          
          <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" id="mismoTurnoCheck">
            <label class="form-check-label" for="mismoTurnoCheck">
              Mismo turno para todas las semanas
            </label>
          </div>
          
          <div id="turnoUnicoContainer" class="mb-3 d-none">
            <label class="form-label">Seleccionar turno *</label>
            <select class="form-select" id="turnoUnicoSelect" name="turnoUnico" disabled>
              <option value="">Seleccionar...</option>
            </select>
          </div>
          
          <div id="contenedorSemanas" class="table-responsive">
            <div class="alert alert-warning">
              Seleccione un rango de fechas para ver los turnos disponibles
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="btnConfirmarAsignacion">
          <span id="btnText">Asignar</span>
          <span id="btnSpinner" class="spinner-border spinner-border-sm d-none"></span>
        </button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Elementos del DOM
  const fechaInicioInput = document.getElementById('fechaInicioAsignacion');
  const fechaFinInput = document.getElementById('fechaFinAsignacion');
  const contenedorSemanas = document.getElementById('contenedorSemanas');
  const checkMismoTurno = document.getElementById('mismoTurnoCheck');
  const turnoUnicoContainer = document.getElementById('turnoUnicoContainer');
  const turnoUnicoSelect = document.getElementById('turnoUnicoSelect');
  const sucursalIdInput = document.getElementById('sucursalId');
  const btnConfirmar = document.getElementById('btnConfirmarAsignacion');
  const btnSpinner = document.getElementById('btnSpinner');
  const btnText = document.getElementById('btnText');
  const nombreColabSpan = document.getElementById('nombreColaborador');

  // Fechas por defecto
  const hoy = new Date();
  fechaInicioInput.valueAsDate = hoy;
  const finDefault = new Date();
  finDefault.setMonth(hoy.getMonth() + 1);
  fechaFinInput.valueAsDate = finDefault;

  // Event Listeners
  fechaInicioInput.addEventListener('change', validarFechasYGenerarTabla);
  fechaFinInput.addEventListener('change', validarFechasYGenerarTabla);
  
  checkMismoTurno.addEventListener('change', function() {
    turnoUnicoContainer.classList.toggle('d-none', !this.checked);
    if (this.checked) {
      cargarTurnosUnicoSelect();
    }
    validarFechasYGenerarTabla();
  });

  // Botones de asignación en la tabla
  document.querySelectorAll('.btn-rol').forEach(btn => {
    btn.addEventListener('click', async function() {
      const fila = this.closest('tr');
      const colabId = fila.dataset.id;
      const colabNombre = fila.dataset.nombre;
      
      document.getElementById('colabId').value = colabId;
      document.getElementById('colabNombre').value = colabNombre;
      nombreColabSpan.textContent = colabNombre;
      
      // Resetear formulario
      checkMismoTurno.checked = false;
      turnoUnicoContainer.classList.add('d-none');
      contenedorSemanas.innerHTML = '<div class="alert alert-info">Seleccione las fechas y opciones de asignación</div>';
      
      const modal = new bootstrap.Modal(document.getElementById('modalAsignarTurno'));
      modal.show();
    });
  });

  // Confirmar asignación
  btnConfirmar.addEventListener('click', async function() {
    if (!validarFormulario()) return;
    try {
      mostrarCarga(true);
      const datos = prepararDatosAsignacion();
      const response = await enviarAsignacion(datos);
      
      if (response.success) {
        mostrarExito(response.message);
        bootstrap.Modal.getInstance(document.getElementById('modalAsignarTurno')).hide();
        if (typeof actualizarVista === 'function') actualizarVista();
      } else {
        throw new Error(response.message || 'Error al asignar turnos');
      }
    } catch (error) {
      mostrarError(error.message);
      console.error('Error:', error);
    } finally {
      mostrarCarga(false);
    }
  });

  // Funciones auxiliares
  async function validarFechasYGenerarTabla() {
    const inicio = new Date(fechaInicioInput.value);
    const fin = new Date(fechaFinInput.value);

    if (isNaN(inicio) || isNaN(fin) || inicio > fin) {
      contenedorSemanas.innerHTML = '<div class="alert alert-warning">Seleccione un rango de fechas válido</div>';
      return;
    }

    if (checkMismoTurno.checked) {
      await generarTablaTurnoUnico();
    } else {
      await generarTablaTurnosPorSemana();
    }
  }

  async function cargarTurnosUnicoSelect() {
    const turnos = await cargarTurnosDisponibles();
    turnoUnicoSelect.innerHTML = '<option value="">Seleccionar turno...</option>';
    
    turnos.forEach(turno => {
      const letraBloque = turno.bloque_id.split('_')[0];
      const option = document.createElement('option');
      option.value = turno.turno_id;
      option.dataset.bloque = turno.bloque_id;
      option.textContent = `${turno.codigo} (${turno.nombre_turno}) - ${letraBloque}`;
      turnoUnicoSelect.appendChild(option);
    });
    
    turnoUnicoSelect.disabled = turnos.length === 0;
  }

  async function generarTablaTurnoUnico() {
    if (!turnoUnicoSelect.value) {
      contenedorSemanas.innerHTML = '<div class="alert alert-warning">Seleccione un turno</div>';
      return;
    }

    const inicio = new Date(fechaInicioInput.value);
    const fin = new Date(fechaFinInput.value);
    
    // Verificar disponibilidad del turno
    const disponible = await verificarDisponibilidadTurno(
      turnoUnicoSelect.value, 
      inicio.toISOString().split('T')[0], 
      fin.toISOString().split('T')[0]
    );

    if (!disponible) {
      contenedorSemanas.innerHTML = `
        <div class="alert alert-danger">
          El turno seleccionado no tiene horarios asignados para el período seleccionado
        </div>
      `;
      return;
    }

    contenedorSemanas.innerHTML = `
      <div class="alert alert-success">
        Se asignará el turno seleccionado semanalmente desde 
        ${inicio.toLocaleDateString()} hasta ${fin.toLocaleDateString()}
      </div>
    `;
  }

  async function generarTablaTurnosPorSemana() {
    const inicio = new Date(fechaInicioInput.value);
    const fin = new Date(fechaFinInput.value);
    const turnos = await cargarTurnosDisponibles();
      
    let html = `
      <div class="table-responsive">
        <table class="table table-sm table-hover">
          <thead class="table-light">
            <tr>
              <th width="30%">Semana</th>
              <th>Turno</th>
              <th>Disponibilidad</th>
            </tr>
          </thead>
          <tbody>
    `;

    let fechaActual = new Date(inicio);
    let semanaIndex = 1;

    while (fechaActual <= fin) {
      const inicioSemana = new Date(fechaActual);
      let finSemana = new Date(fechaActual);
      finSemana.setDate(finSemana.getDate() + 6);
      
      if (finSemana > fin) finSemana = new Date(fin);

      // Verificar disponibilidad para esta semana
      const turnosDisponibles = await verificarTurnosDisponiblesSemana(
        inicioSemana.toISOString().split('T')[0],
        finSemana.toISOString().split('T')[0]
      );

      const opciones = turnosDisponibles.length > 0
        ? turnosDisponibles.map(t => 
          `<option value="${t.id}">${t.codigo} (${t.nombre_turno})</option>`
          ).join('')
        : '<option value="">No hay turnos disponibles</option>';

      const estado = turnosDisponibles.length > 0
        ? '<span class="badge bg-success">Disponible</span>'
        : '<span class="badge bg-danger">No disponible</span>';

      html += `
          <tr>
            <td>
              <strong>Semana ${semanaIndex}</strong><br>
              <small class="text-muted">${inicioSemana.toLocaleDateString()} - ${finSemana.toLocaleDateString()}</small>
            </td>
            <td>
              <select class="form-select form-select-sm" name="turnos[]" 
                ${turnosDisponibles.length === 0 ? 'disabled' : ''}>
                <option value="">${turnosDisponibles.length === 0 ? 'No disponibles' : 'Seleccionar...'}</option>
                ${opciones}
              </select>
            </td>
            <td>${estado}</td>
          </tr>
      `;

      fechaActual.setDate(fechaActual.getDate() + 7);
      semanaIndex++;
    }

    html += `</tbody></table></div>`;
    contenedorSemanas.innerHTML = html;
  }

  async function cargarTurnosDisponibles() {
    try {
      const response = await fetch(`assets/php/get-turnos-sucursal.php?sucursal_id=${sucursalIdInput.value}`);
      const data = await response.json();      
      if (!data || !data.success || !Array.isArray(data.data)) {
        throw new Error('Formato de datos incorrecto');
      }
      
      return data.data;
      } catch (error) {
        console.error('Error al cargar turnos:', error);
        mostrarError('No se pudieron cargar los turnos disponibles');
        return []; 
      }
  }

  async function verificarDisponibilidadTurno(turnoId, fechaInicio, fechaFin) {
    try {
      const response = await fetch(`assets/php/verificar-disponibilidad.php?turno_id=${turnoId}&fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`);
      const data = await response.json();
      return data.success && data.disponible;
    } catch (error) {
      console.error('Error verificando disponibilidad:', error);
      return false;
    }
  }

  async function verificarTurnosDisponiblesSemana(fechaInicio, fechaFin) {
    try {
      const response = await fetch(`assets/php/get-turnos-disponibles.php?sucursal_id=${sucursalIdInput.value}&fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`);
      const data = await response.json();
      return data.success ? data.data : [];
    } catch (error) {
      console.error('Error verificando turnos semanales:', error);
      return [];
    }
  }

  function validarFormulario() {
    const form = document.getElementById('formAsignarTurno');
    if (!form.checkValidity()) {
      form.reportValidity();
      return false;
    }
      
    if (checkMismoTurno.checked && !turnoUnicoSelect.value) {
      mostrarError('Debe seleccionar un turno para asignación recurrente');
      return false;
    }
      
    if (!checkMismoTurno.checked) {
      const turnosSeleccionados = Array.from(document.querySelectorAll('[name="turnos[]"]')).filter(sel => sel.value);
      if (turnosSeleccionados.length === 0) {
        mostrarError('Debe seleccionar al menos un turno para alguna semana');
        return false;
      }
    }
      
      return true;
  }

  function prepararDatosAsignacion() {
  const esRecurrente = checkMismoTurno.checked;
  const datos = {
    colaborador_id: document.getElementById('colabId').value,
    colaborador_nombre: document.getElementById('colabNombre').value,
    fecha_inicio: fechaInicioInput.value,
    fecha_fin: fechaFinInput.value,
    es_recurrente: esRecurrente
  };

 if (esRecurrente) {
    datos.turno_id = turnoUnicoSelect.value;
    datos.bloque_id = turnoUnicoSelect.selectedOptions[0].dataset.bloque; 
  } else {
    datos.turnos_semanas = Array.from(document.querySelectorAll('[name="turnos[]"]')).map(sel => ({
      turno_id: sel.value,
      bloque_id: sel.selectedOptions[0]?.dataset.bloque || null
    }));
  }

  return datos;
}

  async function enviarAsignacion(datos) {
    console.log(datos)
    const response = await fetch('assets/php/asignar-turno.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(datos)
    });
    return await response.json();
  }

  function mostrarCarga(mostrar) {
    btnConfirmar.disabled = mostrar;
    btnSpinner.classList.toggle('d-none', !mostrar);
    btnText.textContent = mostrar ? 'Procesando...' : 'Asignar';
  }

  function mostrarExito(mensaje) {
    Swal.fire({
      icon: 'success',
      title: 'Éxito',
      text: mensaje,
      timer: 2000,
      showConfirmButton: false
    });
  }

  function mostrarError(mensaje) {
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: mensaje
    });
  }
});

let calendar; 

// Contador para nuevos turnos
let contadorNuevos = <?= !empty($turnosExistentes) ? count($turnosExistentes) : 0 ?>;
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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