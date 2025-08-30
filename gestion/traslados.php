<?php
session_start();
include("../checklogin.php");
check_login();
include("../dbconnection.php");
include("assets/php/traslados.php");

$usRol = $_SESSION['cargo'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Traslados y Desvinculacion</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  <link href="../assets/css/sidebar.css" rel="stylesheet" type="text/css"/>
  <link href="assets/css/traslados.css" rel="stylesheet" type="text/css"/>
</head>

<body class="test">
  <div class="page-container">
    <div class="sidebar">
      <?php include("../header-test.php"); ?>
      <?php include("../assets/php/phone-sidebar.php"); ?>
    </div>
    
    <div class="page-content">
      <?php include("../leftbar-test.php"); ?>

      <div class="page-title d-flex justify-content-between">
        <h2>Traslado y Desvinculacion</h2>
        <button class="btn-back" onclick="window.location.href='main.php';">
          <i class="bi bi-arrow-left"></i>
        </button>
      </div>

      <div class="content">
        <div class="col-md-10 form-data mx-auto">
          <div class="form-row mx-auto d-flex justify-content-between">
            <div class="form-group">
              <label class="form-label">Solicitante</label>
              <input type="text" class="form-control form-control-sm" value="<?php echo htmlspecialchars($_SESSION['name']); ?>" disabled>
            </div>
            <div class="form-group">
              <label class="form-label">Fecha <span>*</span></label>
              <input type="date" class="form-control form-control-sm" value="<?php echo date('Y-m-d'); ?>" disabled>
            </div>
          </div>

          <div class="form-row mx-auto d-flex">
            <div class="form-group">
              <div class="form-check">
                <input class="form-check-input" type="radio" name="forms" id="traslado" onclick="mostrarFormulario('trasladoForm')">
                <label class="form-check-label" for="traslado">Formulario de Traslado</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="forms" id="desvinculacion" onclick="mostrarFormulario('desvinculacionForm')">
                <label class="form-check-label" for="desvinculacion">Formulario de Desvinculacion</label>
              </div>
            </div>
          </div>
        </div>

        <!-- Formulario de Traslado -->
        <form method="POST" enctype="multipart/form-data">
          <div id="loading" style="display:none;">
            <div class="loading-spinner"></div>
            <p>Procesando...</p>
          </div>

          <div class="col-md-10 form-data mx-auto" id="trasladoForm" style="display: none;">
            <h3>Formulario de Traslado</h3>

            <div class="form-row mx-auto">
              <div class="form-group">
                <div class="d-flex msg">
                  <label class="form-label">Supervisor Origen <span>*</span></label>
                  <p>(Supervisor a cargo de Instalación de origen)</p>
                </div>
                <select name="supervisor" class="form-select form-select-sm search-form" required>
                  <option value="">Seleccionar</option>
                  <?php foreach ($sup as $row): ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['nombre']); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div class="form-row mx-auto">
              <div class="form-group">
                <label class="form-label">Nombre Colaborador <span>*</span></label>
                <input type="text" class="form-control form-control-md" name="colaborador" required>
              </div>
              <div class="form-group">
                <div class="d-flex msg">
                  <label class="form-label">Rut <span>*</span></label>
                  <p>(Sin puntos)</p>
                </div>
                <input type="text" class="form-control form-control-md" name="rut" maxlength="12" required>
              </div>
            </div>

            <div class="form-row mx-auto">
              <div class="form-group">
                <label class="form-label">Instalación de Origen <span>*</span></label>
                <select name="instalacion" class="form-select form-select-sm search-form" required>
                  <option value="">Seleccionar</option>
                  <?php foreach ($inst as $row): ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['nombre']); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">Jornada de Origen <span>*</span></label>
                <select name="jornada" class="form-select form-select-sm search-form" required>
                  <option value="">Seleccionar</option>
                  <?php foreach ($jornada as $row): ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['nombre']); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div class="form-row mx-auto insOrigen">
              <div class="form-group">
                <label class="form-label">Nombre Instalacion Origen<span>*</span></label>
                <input type="text" class="form-control form-control-md" name="inOrigen" required>
              </div>
            </div>

            <div class="form-row mx-auto">
              <div class="form-group">
                <label class="form-label">Rol de Origen <span>*</span></label>
                <select name="rolOrigen" class="form-select form-select-sm search-form" required>
                  <option value="">Seleccionar</option>
                  <?php foreach ($rol as $row): ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['nombre']); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">Motivo de traslado</label>
                <select name="motivo" class="form-select form-select-sm search-form" required>
                  <option value="">Seleccionar</option>
                  <?php foreach ($motivoT as $row): ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['nombre']); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div class="form-row mx-auto">
              <div class="form-group">
                <label class="form-label">Instalacion de Destino <span>*</span></label>
                <select name="inDestino" class="form-select form-select-sm search-form" required>
                  <option value="">Seleccionar</option>
                  <?php foreach ($inst as $row): ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['nombre']); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">Jornada de Destino <span>*</span></label>
                <select name="joDestino" class="form-select form-select-sm search-form" required>
                  <option value="">Seleccionar</option>
                  <?php foreach ($jornada as $row): ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['nombre']); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div class="form-row mx-auto insDestino">
              <div class="form-group">
                <label class="form-label">Nombre Instalacion Destino<span>*</span></label>
                <input type="text" class="form-control form-control-sm" name="iDestino" required>
              </div>
            </div>

            <div class="form-row mx-auto">
              <div class="form-group">
                <label class="form-label">Rol de Destino<span>*</span></label>
                <select name="rolDestino" class="form-select form-select-sm search-form" required>
                  <option value="">Seleccionar</option>
                  <?php foreach ($rol as $row): ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['nombre']); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">Fecha de Inicio de Turno <span>*</span></label>
                <input type="date" class="form-control form-control-md" name="fechaInicio" required min="<?php echo date('Y-m-d'); ?>">
              </div>
            </div>

            <div class="form-row mx-auto">
              <div class="form-group">
                <div class="d-flex msg">
                  <label class="form-label">Supervisor Destino <span>*</span></label>
                  <p>(Supervisor a cargo de instalación de destino)</p>
                </div>
                <select name="supervisorDestino" class="form-select form-select-sm search-form" required>
                  <option value="">Seleccionar</option>
                  <?php foreach ($sup as $row): ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['nombre']); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div class="form-row mx-auto">
              <div class="form-group">
                <label class="form-label">Observación</label>
                <textarea class="form-control form-control-sm" name="observacionT" rows="6"></textarea>
              </div>
            </div>

            <div class="footer">
              <button type="submit" name="trasladoForm" class="btn btn-updt">Enviar</button>
            </div>
          </div>
        </form>

        <!-- Formulario Desvinculacion -->
        <form method="POST" enctype="multipart/form-data">
          <div id="loading" style="display:none;">
            <div class="loading-spinner"></div>
            <p>Procesando...</p>
          </div>

          <div class="col-md-10 form-data mx-auto" id="desvinculacionForm" style="display: none;">
            <h3>Formulario de Desvinculación</h3>

            <div class="form-row mx-auto">
              <div class="form-group">
                <div class="d-flex msg">
                  <label class="form-label">Supervisor Encargado <span>*</span></label>
                  <p>(Supervisor a cargo de Instalación)</p>
                </div>
                <select name="supervisorEncargado" class="form-select form-select-sm search-form" required>
                  <option value="">Seleccionar</option>
                  <?php foreach ($sup as $row): ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['nombre']); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">Instalacion de Origen <span>*</span></label>
                <select name="instalacion" class="form-select form-select-sm search-form" required>
                  <option value="">Seleccionar</option>
                  <?php foreach ($inst as $row): ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['nombre']); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div class="form-row mx-auto in_origen">
              <div class="form-group">
                <label class="form-label">Nombre Instalacion<span>*</span></label>
                <input type="text" class="form-control form-control-sm" name="inNombre" required>
              </div>
            </div>

            <div class="form-row mx-auto">
              <div class="form-group">
                <label class="form-label">Nombre Colaborador <span>*</span></label>
                <input type="text" class="form-control form-control-sm" name="colaborador" required>
              </div>
              <div class="form-group">
                <div class="d-flex msg">
                  <label class="form-label">Rut <span>*</span></label>
                  <p>(Sin puntos ni guion)</p>
                </div>
                <input type="text" class="form-control form-control-sm" name="rut" maxlength="12" required>
              </div>
            </div>

            <div class="form-row mx-auto">
              <div class="form-group">
                <label class="form-label">Rol <span>*</span></label>
                <select name="rol" class="form-select form-select-sm search-form" required>
                  <option value="">Seleccionar</option>
                  <?php foreach ($rol as $row): ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['nombre']); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">Motivo de Egreso <span>*</span></label>
                <select name="motivo" class="form-select form-select-sm search-form" required>
                  <option value="">Seleccionar</option>
                  <?php foreach ($motivoE as $row): ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['nombre']); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div class="form-row mx-auto">
              <div class="form-group">
                <label class="form-label">Subir Archivos</label>
                <input class="form-control form-control-sm" type="file" name="desvDocs">
              </div>
            </div>

            <div class="form-row mx-auto">
              <div class="form-group">
                <label class="form-label">Observación</label>
                <textarea class="form-control form-control-sm" name="observacion" rows="6"></textarea>
              </div>
            </div>

            <div class="footer">
              <button type="submit" name="desvForm" class="btn btn-updt">Enviar</button>
            </div>
          </div>
        </form>

        <!-- Traslados -->
        <div class="form-data col-md-10 mx-auto">
          <h3>Traslados</h3>
          <?php if ($num > 0): ?>
            <button class="btn btn-updt" onclick="window.location.href='assets/php/excel-traslados.php';">Descargar</button>
            
            <?php while ($row = $traslados->fetch_assoc()): ?>
              <div class="mt-3 card col-md-11 mx-auto p-3 card-t">
                <div class="colab-data">
                  <strong>Colaborador: <?php echo htmlspecialchars($row['nombre_colaborador']); ?></strong>
                  <p>Solicitante: <?php echo htmlspecialchars($row['soliN']); ?></p>
                  <p>Fecha: <?php echo date("Y-m-d H:i", strtotime($row['fecha_registro'])); ?></p>
                  
                  <?php if ($usRol == '11'): ?>
                    <p>Estado: <?php echo htmlspecialchars($row['estado']); ?></p>
                  <?php else: ?>
                    <p>Estado:
                      <select class="form-select form-select-sm estado-select" data-id="<?php echo $row['id']; ?>">
                        <option value="En gestión" <?php if ($row['estado'] == 'En gestión') echo 'selected'; ?>>En gestión</option>
                        <option value="Realizado" <?php if ($row['estado'] == 'Realizado') echo 'selected'; ?>>Realizado</option>
                        <option value="Anulado" <?php if ($row['estado'] == 'Anulado') echo 'selected'; ?>>Anulado</option>
                      </select>
                    </p>
                  <?php endif; ?>
                </div>

                <div class="origen-data">
                  <strong>Instalacion de Origen:
                    <?php
                    if (!empty(trim($row['inOrigen_nombre'] ?? ''))) {
                      echo htmlspecialchars($row['inOrigen_nombre']);
                    } elseif (!empty(trim($row['suOrigen'] ?? ''))) {
                      echo htmlspecialchars($row['suOrigen']);
                    }
                    ?>
                  </strong>
                  <p>Supervisor: <?php echo htmlspecialchars($row['supOrigen']); ?></p>
                  <p>Razón Social: <?php echo htmlspecialchars($row['raOrigen'] ?? 'Sin razón social'); ?></p>
                  <p>Jornada Origen: <?php echo htmlspecialchars($row['joOrigen']); ?></p>
                  <p>Rol: <?php echo htmlspecialchars($row['rolOrigen']); ?></p>
                </div>

                <div class="destino-data">
                  <strong>Instalacion de Destino:
                    <?php
                    if (!empty(trim($row['inDestino_nombre'] ?? ''))) {
                      echo htmlspecialchars($row['inDestino_nombre']);
                    } elseif (!empty(trim($row['suDestino'] ?? ''))) {
                      echo htmlspecialchars($row['suDestino']);
                    }
                    ?>
                  </strong>
                  <p>Supervisor: <?php echo htmlspecialchars($row['supDestino']); ?></p>
                  <p>Razón Social: <?php echo htmlspecialchars($row['raDestino'] ?? 'Sin razón social'); ?></p>
                  <p>Jornada Destino: <?php echo htmlspecialchars($row['joDestino']); ?></p>
                  <p>Rol: <?php echo htmlspecialchars($row['rolDestino']); ?></p>
                </div>

                <?php if ($_SESSION['id'] == 38): ?>
                  <div class="delete-btns">
                    <button class="btn btn-del del-btn" data-bs-toggle="modal" data-bs-target="#delTraslado" data-id="<?php echo $row['id']; ?>">Eliminar</button>
                  </div>
                <?php endif; ?>
              </div>
            <?php endwhile; ?>
            
          <?php else: ?>
            <p>No hay traslados registrados el dia de hoy</p>
          <?php endif; ?>
        </div>

        <!-- Desvinculaciones -->
        <div class="form-data col-md-10 mx-auto">
          <h3>Desvinculaciones</h3>
          <?php if ($num_des > 0): ?>
            <button class="btn btn-updt" onclick="window.location.href='assets/php/excel-desvinculaciones.php';">Descargar</button>
            
            <?php while ($row = $desvinculaciones->fetch_assoc()): ?>
              <div class="mt-3 card col-md-11 mx-auto p-3 card-t">
                <div class="colab-data">
                  <strong>Colaborador: <?php echo htmlspecialchars($row['colaborador']); ?></strong>
                  <p>Solicitante: <?php echo htmlspecialchars($row['soliN']); ?></p>
                  <p>Fecha: <?php echo date("Y-m-d H:i", strtotime($row['fecha_registro'])); ?></p>
                  
                  <?php if ($usRol == '11'): ?>
                    <p>Estado: <?php echo htmlspecialchars($row['estado']); ?></p>
                  <?php else: ?>
                    <p>Estado:
                      <select class="form-select form-select-sm desv-select" data-id="<?php echo $row['id']; ?>">
                        <option value="En gestión" <?php if ($row['estado'] == 'En gestión') echo 'selected'; ?>>En gestión</option>
                        <option value="Realizado" <?php if ($row['estado'] == 'Realizado') echo 'selected'; ?>>Realizado</option>
                        <option value="Anulado" <?php if ($row['estado'] == 'Anulado') echo 'selected'; ?>>Anulado</option>
                      </select>
                    </p>
                  <?php endif; ?>
                </div>

                <div class="origen-data">
                  <strong>Instalación Origen:
                    <?php
                    if (!empty(trim($row['in_nombre'] ?? ''))) {
                      echo htmlspecialchars($row['in_nombre']);
                    } elseif (!empty(trim($row['instalacion'] ?? ''))) {
                      echo htmlspecialchars($row['instalacion']);
                    }
                    ?>
                  </strong>
                  <p>Supervisor: <?php echo htmlspecialchars($row['supervisor']); ?></p>
                  <p>Razón Social: <?php echo htmlspecialchars($row['razon'] ?? 'Sin razón social'); ?></p>
                  <p>Rol: <?php echo !empty($row['rolN']) ? htmlspecialchars($row['rolN']) : 'Sin Asignar'; ?></p>
                </div>

                <div class="destino-data">
                  <strong>Motivo de Egreso: <?php echo htmlspecialchars($row['motivoEgreso']); ?></strong>
                  <p>Observación: <?php echo !empty($row['observacion']) ? htmlspecialchars($row['observacion']) : 'No tiene observación'; ?></p>
                </div>

                <?php if ($_SESSION['id'] == 38): ?>
                  <div class="delete-btns">
                    <button class="btn btn-del del-btn" data-bs-toggle="modal" data-bs-target="#delDesv" data-id="<?php echo $row['id']; ?>">Eliminar</button>
                  </div>
                <?php endif; ?>
              </div>
            <?php endwhile; ?>
            
          <?php else: ?>
            <p>No hay desvinculaciones registradas el dia de hoy</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal eliminar traslado -->
  <div class="modal fade" id="delTraslado" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Eliminar Traslado</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          <p>¿Estás seguro de que quieres eliminar este Traslado?</p>
          <form method="POST" id="delTr">
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

  <!-- Modal eliminar desvinculacion -->
  <div class="modal fade" id="delDesv" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Eliminar Desvinculación</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          <p>¿Estás seguro de que quieres eliminar esta Desvinculación?</p>
          <form method="POST" id="delDesvForm">
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

  <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
  <script src="../assets/js/sidebar.js"></script>
  
  <script>
    // Inicialización
    document.addEventListener("DOMContentLoaded", function() {
      // Fecha actual
      const fechaSoli = document.querySelector("input[type='date'][disabled]");
      if (fechaSoli) {
        fechaSoli.value = "<?php echo date('Y-m-d'); ?>";
      }

      // Inicializar Choices.js
      if (typeof Choices !== 'undefined') {
        document.querySelectorAll("select.search-form").forEach(function(el) {
          new Choices(el, { 
            removeItemButton: false, 
            shouldSort: false, 
            searchPlaceholderValue: "Buscar..." 
          });
        });
      }

      // Gestión de formularios
      window.mostrarFormulario = function(formId) {
        document.querySelectorAll('.form-data.mx-auto[id]').forEach(form => {
          form.style.display = 'none';
        });
        document.getElementById(formId).style.display = 'block';
      };

      // Configurar modales
      const delTrasladoModal = document.getElementById('delTraslado');
      if (delTrasladoModal) {
        delTrasladoModal.addEventListener('show.bs.modal', function(event) {
          const button = event.relatedTarget;
          document.getElementById('idTr').value = button.getAttribute('data-id');
        });
      }

      const delDesvModal = document.getElementById('delDesv');
      if (delDesvModal) {
        delDesvModal.addEventListener('show.bs.modal', function(event) {
          const button = event.relatedTarget;
          document.getElementById('idDesv').value = button.getAttribute('data-id');
        });
      }

      // Validación de formularios
      const forms = document.querySelectorAll('form');
      forms.forEach(form => {
        form.addEventListener('submit', function(e) {
          const requiredFields = form.querySelectorAll('[required]');
          let valid = true;
          
          requiredFields.forEach(field => {
            if (!field.value.trim()) {
              field.classList.add('is-invalid');
              valid = false;
            } else {
              field.classList.remove('is-invalid');
            }
          });
          
          if (!valid) {
            e.preventDefault();
            toastr.error('Por favor, complete todos los campos obligatorios');
          }
        });
      });
    });
  </script>
</body>
</html>