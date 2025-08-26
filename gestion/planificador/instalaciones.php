<?php
session_start();
include("../../checklogin.php");
include("../../dbconnection.php");
include("assets/php/instalaciones.php");
check_login();
$userID = $_SESSION['id'];

?>
<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
  <meta charset="utf-8" />
  <title>Sucursales</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <meta name="google" content="notranslate">
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
          <h2>Sucursales</h2>
          <button class=" btn-back" onclick="window.location.href='main.php';"> 
              <i class="bi bi-arrow-left" ></i>
          </button>
        </div> <br><br>

          <div class="filtros d-flex justify-content-between form-f">
            <div class="d-flex justify-content-between mb-3">
              <div class="all-fil">
                <label for="filtroTexto">Buscar</label>
                <input type="text" id="filtroTexto" name="texto" class="form-control form-control-sm fil" placeholder="Buscar por nombre, tipo, etc.">
              </div>
              <div class="all-fil">  
                <label for="filtroCentro" >Centro de costos</label>
                <select id="filtroCentro" name="centro" class="form-select form-select-sm">
                  <option value="">Todos los Centros</option>
                  <?php
                    if ($result_dep->num_rows > 0) {
                      while ($cc = $result_dep->fetch_assoc()) {
                          echo '<option value="'.$cc['id'].'">' 
                            . htmlspecialchars($cc['nombre_departamento'], ENT_QUOTES) . '</option>';
                      }
                    } else {
                      echo '<option value="">No hay supervisores</option>';
                    }
                    $stmt_d->close();
                  ?>
                </select>
              </div>
              <div class="all-fil">  
                <label for="filtroSupervisor" >Supervisor</label>
                <select id="filtroSupervisor" name="supervisor" class="form-select form-select-sm">
                  <option value="">Todos los supervisores</option>
                  <?php
                    if ($result_sup->num_rows > 0) {
                      while ($supervisor = $result_sup->fetch_assoc()) {
                          echo '<option value="'.$supervisor['id'].'">' 
                            . htmlspecialchars($supervisor['nombre_supervisor'], ENT_QUOTES) . '</option>';
                      }
                    } else {
                      echo '<option value="">No hay supervisores</option>';
                    }
                    $stmt_s->close();
                  ?>
                </select>
              </div>
            </div>
            <div>
                <button type="submit" class="btn btn-excel" onclick="window.location.href='assets/php/descargar-calendario.php?formato=all&userID=<?php echo $userID;?>';">

                  <i class="bi bi-file-earmark-excel"></i> 
                  Exportar Calendarios
                </button>
              </div>
          </div>
      
        <div id="resultadoSucursal" class="content"></div>
    </div>
  </div>

<!-- Popper.js (para tooltips y otros componentes) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<!-- Bootstrap Bundle (con Popper.js) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<!-- Scripts propios -->
<script src="../../assets/js/sidebar.js"></script>
<script src="assets/js/instalaciones.js"></script>

</body>

</html>