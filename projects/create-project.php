<?php
session_start();
include("../checklogin.php");
include("../dbconnection.php");
include("../admin/phpmail.php");
include("assets/php/create-project.php");

check_login();
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Proyectos</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- CSS personalizados -->
  <link href="../assets/css/sidebar.css" rel="stylesheet" type="text/css" />
  <link href="assets/css/create-project.css" rel="stylesheet" type="text/css" />

  <!-- Toast notificaciones -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
</head>

<body class="test">
  <div class="page-container">
    <div class="sidebar">
      <?php include("../header-test.php"); ?>
    </div>

    <div class="page-content">
      <?php include("../leftbar-test.php"); ?>
      <div class="content">
        <div class="page-title d-flex justify-content-between">
          <h2>Crear Proyecto</h2>
          <button class="btn-back" onclick="window.location.href='projects-main.php';">
            <i class="bi bi-arrow-left"></i>
          </button>
        </div>

        <form name="form" id="newProject" method="post">
          <div id="loading" style="display:none;">
            <div class="loading-spinner"></div>
            <p>Procesando...</p>
          </div>

          <div class="project-main"><br><br>
            <!-- Nombre y Cliente -->
            <div class="form-row">
              <div class="form-group">
                <label for="name" class="form-label">Nombre Proyecto <span>*</span></label>
                <input type="text" class="form-control form-control-sm" id="name" name="name" required>
              </div>
              <div class="form-group">
                <label class="form-label">Cliente <span>*</span></label>
                <select name="client" id="client" class="form-select form-select-sm" required>
                  <option value="">Seleccionar</option>
                  <?php foreach ($catalogs['clientes'] as $row) : ?>
                    <option value="<?= $row['id'] ?>"><?= $row['nombre'] ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <!-- Tipo y Clasificación -->
            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Tipo de proyecto <span>*</span></label>
                <select name="pType" id="pType" class="form-select form-select-sm" required>
                  <option value="">Seleccionar</option>
                  <?php foreach ($catalogs['types'] as $row) : ?>
                    <option value="<?= $row['id'] ?>"><?= $row['nombre'] ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">Clasificación <span>*</span></label>
                <select name="pClass" id="pClass" class="form-select form-select-sm" required>
                  <option value="">Seleccionar</option>
                  <?php foreach ($catalogs['class'] as $row) : ?>
                    <option value="<?= $row['id'] ?>"><?= $row['nombre'] ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <!-- Licitación -->
            <div class="form-row" id="licitacionT" style="display:none;">
              <strong>Datos Licitación</strong>
            </div>
            <div class="form-row" id="licitacion" style="display:none;">
              <div class="form-group">
                <label for="licID" class="form-label">ID licitación</label>
                <input type="text" class="form-control form-control-sm" id="licID" name="licID">
              </div>
              <div class="form-group">
                <label class="form-label">Portal</label>
                <select name="portal" class="form-select form-select-sm">
                  <option value="">Sin asignar</option>
                  <?php foreach ($catalogs['portales'] as $row) : ?>
                    <option value="<?= $row['id'] ?>"><?= $row['nombre_portal'] ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <!-- Contactos -->
            <div class="form-row justify-content-start" id="contactoT" style="display:none;">
              <strong>Datos de Contacto</strong>
              <button type="button" class="btn btn-add-task" data-bs-toggle="modal" data-bs-target="#contactoModal">
                <i class="bi bi-plus-circle"></i>
              </button>
            </div>
            <div class="d-flex contactos" id="contacto" style="display: hidden;"></div>

            <!-- Ciudad y Vertical -->
            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Ciudad <span>*</span></label>
                <select name="city" class="form-select form-select-sm" required>
                  <option value="">Seleccionar</option>
                  <?php foreach ($catalogs['cities'] as $row) : ?>
                    <option value="<?= $row['id'] ?>"><?= $row['nombre_ciudad'] ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">Vertical <span>*</span></label>
                <select name="vertical" class="form-select form-select-sm" required>
                  <option value="">Seleccionar</option>
                  <?php foreach ($catalogs['verticales'] as $row) : ?>
                    <option value="<?= $row['id'] ?>"><?= $row['nombre'] ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <!-- Monto y Fechas -->
            <div class="form-row">
              <div class="form-group">
                <label for="">Monto Proyecto</label>
                <div class="input-group">
                  <span class="input-group-text" id="montoP">$</span>
                  <input name="montoP" type="number" class="form-control form-control-sm" placeholder="1,000,000">
                </div>
              </div>
              <div class="form-group">
                <label for="">Fecha de Cierre Documental</label>
                <div class="input-group">
                  <span class="input-group-text" id="cierreDoc"><i class="bi bi-exclamation-lg"></i></span>
                  <input name="cierreDoc" type="date" class="form-control form-control-sm">
                </div>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label for="">Fecha de Adjudicación</label>
                <div class="input-group">
                  <span class="input-group-text" id="fAdj"><i class="bi bi-exclamation-lg"></i></span>
                  <input name="fAdj" type="date" class="form-control form-control-sm">
                </div>
              </div>
              <div class="form-group">
                <label for="">Fecha fin de Contrato</label>
                <div class="input-group">
                  <span class="input-group-text" id="finContrato"><i class="bi bi-exclamation-lg"></i></span>
                  <input name="finContrato" type="date" class="form-control form-control-sm">
                </div>
              </div>
            </div>

            <!-- Resumen -->
            <div class="form-row">
              <div class="form-group">
                <label for="desc" class="form-label">Resumen</label>
                <textarea class="form-control form-control-sm" id="desc" name="desc" rows="4"></textarea>
              </div>
            </div>

            <!-- Actividades -->
            <div class="form-row">
              <div class="form-group">
                <div class="label-container">
                  <label class="label">Actividades</label>
                  <button type="button" class="btn btn-add-task" data-bs-toggle="modal" data-bs-target="#actividadModal">
                    <i class="bi bi-calendar-plus"></i>
                  </button>
                </div>
                <div id="events-list">
                  <ul id="listadoActividades" class="list-group"></ul>
                </div>
              </div>
            </div>

            <div class="form-row footer">
              <button class="btn btn-reset">Resetear</button>
              <button type="submit" id="newProject" name="newProject" class="btn pull-right">Crear</button>
            </div>

          </div>
        </form>

      </div>
    </div>
  </div>

  <!-- Modales (Actividades y Contactos) se mantienen igual -->
  <?php include("modales.php"); ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/contactos.js"></script>
  <script src="assets/js/create-project.js"></script>
</body>

</html>
