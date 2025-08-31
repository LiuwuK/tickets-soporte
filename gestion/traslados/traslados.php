<?php
session_start();
include("../../checklogin.php");
check_login();
include("../../dbconnection.php");
include("assets/php/traslados.php");

$usRol = $_SESSION['cargo'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Traslados y Desvinculaciones</title>

  <!-- CSS externos -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <!-- sweetalert notificaciones -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

  <!-- CSS internos -->
  <link href="../../assets/css/sidebar.css" rel="stylesheet" type="text/css"/>
  <link href="../assets/css/traslados.css" rel="stylesheet" type="text/css"/>
</head>
<body class="test">
  <div class="page-container">
    <div class="sidebar">
      <?php include("../../header-test.php"); ?>
    </div>

    <div class="page-content">
      <?php include("../../leftbar-test.php"); ?>

      <div class="page-title d-flex justify-content-between">
        <h2>Traslado y Desvinculacion</h2>
        <button class="btn-back" onclick="window.location.href='../main.php';">
          <i class="bi bi-arrow-left"></i>
        </button>
      </div>

      <div class="content">
        <div class="col-md-10 form-data mx-auto">
          <!-- Solicitante y fecha -->
          <div class="form-row d-flex justify-content-between">
            <div class="form-group">
              <label class="form-label">Solicitante</label>
              <input type="text" class="form-control form-control-sm" value="<?= htmlspecialchars($_SESSION['name']); ?>" disabled>
            </div>
            <div class="form-group">
              <label class="form-label">Fecha <span>*</span></label>
              <input type="date" class="form-control form-control-sm" value="<?= date('Y-m-d'); ?>" disabled>
            </div>
          </div>

          <!-- Selección de formulario -->
          <div class="form-row d-flex">
            <div class="form-group">
              <div class="form-check">
                <input class="form-check-input" type="radio" name="forms" id="traslado">
                <label class="form-check-label" for="traslado">Formulario de Traslado</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="forms" id="desvinculacion">
                <label class="form-check-label" for="desvinculacion">Formulario de Desvinculacion</label>
              </div>
            </div>
          </div>
        </div>

        <!-- Loading spinner -->
        <div id="loading" style="display:none;">
          <div class="loading-spinner"></div>
          <p>Procesando...</p>
        </div>

        <!-- Formulario Traslado -->
        <?php include("assets/php/traslado-form.php"); ?>
        <!-- Formulario Desvinculación -->
        <?php include("assets/php/desv-form.php"); ?>
        <!-- Traslados list -->
        <?php include("assets/php/traslados-list.php"); ?>
        <!-- Desvinculaciones list -->
        <?php include("assets/php/desvinculaciones-list.php"); ?>
        
      </div>
    </div>
  </div>

  <!-- Modales -->
  <?php include("assets/php/modales.php"); ?>

  <!-- JS externos -->
  <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- JS custom -->
  <script src="assets/js/traslados.js"></script>
  <?php if(isset($_SESSION['swal'])): ?>
    <script>
    document.addEventListener("DOMContentLoaded", function(){
        Swal.fire({
            icon: '<?= $_SESSION['swal']['tipo'] ?>',
            title: '<?= $_SESSION['swal']['tipo']=='success' ? "¡Éxito!" : "Error" ?>',
            text: '<?= addslashes($_SESSION['swal']['msg']) ?>',
            confirmButtonColor: '<?= $_SESSION['swal']['tipo']=='success' ? "#33435e" : "#d33" ?>'
        });
    });
    </script>
  <?php 
  unset($_SESSION['swal']); // Limpiar mensaje para no repetirlo
  endif; 
  ?>
</body>
</html>