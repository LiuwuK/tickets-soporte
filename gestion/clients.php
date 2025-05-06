<?php
session_start();
include("../checklogin.php");
include("../dbconnection.php");
include("assets/php/clients.php");
check_login();

?>
<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
  <meta charset="utf-8" />
  <title>Clientes</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <meta content="" name="description" />
  <meta content="" name="author" />

<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<!-- CSS personalizados -->
<link href="../assets/css/sidebar.css" rel="stylesheet" type="text/css"/>
<link href="assets/css/clients.css" rel="stylesheet" type="text/css"/>
<!-- Toast notificaciones -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
<!-- Graficos -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="test" >
    <!-- Sidebar -->
  <div class="page-container ">
    <div class="sidebar">
      <?php include("../header-test.php"); ?>
      <?php include("../assets/php/phone-sidebar.php"); ?>
    </div>
    <div class="page-content">
    <?php include("../leftbar-test.php"); ?>
        <div class="page-title d-flex justify-content-between">
          <div class="d-flex">
            <h2>Clientes</h2>
            <button type="button" class="btn btn-sm btn-add-client"  data-bs-toggle="modal" data-bs-target="#clientsModal">
                <i class="bi bi-plus"></i> 
            </button>
          </div>
          <button class=" btn-back" onclick="window.location.href='main.php';"> 
              <i class="bi bi-arrow-left" ></i>
          </button>
        </div> <br><br>
        <div class="content">
        <?php
          if($num_cl > 0){
            while($row = $clientes->fetch_assoc()){
              $img = !empty($row['img_perfil']) && file_exists($row['img_perfil']) ? $row['img_perfil'] : '../assets/img/user.png';
        ?>
              <div class="card" onclick="window.location.href='client-info.php?clientID=<?php echo $row['id']; ?>';">
                <div class="profile-img mt-3">
                  <img src="<?php echo $img ?>" alt="">
                </div>
                <div class="card-body text-center">
                    <strong><?php echo $row['nombre'] ?></strong>
                    <p>Vertical: <?php echo $row['verticalN'] ?></p>
                </div>
                <div class="card-footer text-center">
                    <strong>Ver Historico</strong>
                </div>
              </div>
        <?php
            }
          }else{
            echo "<h4 class='text-center'>No se han encontrado clientes registrados</h4>";
          }
        ?>
        </div>   
    </div>
  </div>

  <!-- Modal Clientes -->
  <div class="modal fade" id="clientsModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="clientsModalLabel" aria-hidden="true">>
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="clientsModalLabel">Nuevo Cliente</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form id="form" method="post" enctype="multipart/form-data">
              <div class="form-row mb-3">
                  <div class="form-group">
                      <label for="nombreCliente" class="form-label">Nombre Cliente</label>
                      <input type="text" class="form-control form-control-sm" id="nombreCliente" name="nombreCliente" required>
                  </div>
                  <div class="form-group">
                      <label class="form-label">Vertical</label>
                      <div>
                          <select name="vertical" id="vertical" class="form-select form-select-sm" required>
                              <option value="">Seleccionar</option>
                              <?php
                              while ($row = mysqli_fetch_assoc($result)) {
                                  echo "<option value=".$row['id'].">".$row['nombre']."</option>";
                              };
                              ?>  
                          </select>
                      </div>
                  </div>
              </div>

              <div class="form-row mb-3">
                <div class="form-group">
                  <label for="nombreEnc" class="form-label">Nombre Encargado</label>
                  <input type="text" class="form-control form-control-sm" id="nombreEnc" name="nombreEnc" required>
                </div>
                <div class="form-group">
                  <label for="cargo" class="form-label">Cargo</label>
                  <input type="text" class="form-control form-control-sm" id="cargo" name="cargo" required>
                </div>
              </div>

              <div class="form-row mb-3">
                <div class="form-group">
                  <label for="correo" class="form-label">Correo</label>
                  <input type="email" class="form-control form-control-sm" id="correo" name="correo" required>
                </div>
              </div>

              <div class="form-row mb-3">
                <div class="form-group">
                    <label for="clientImg" class="form-label">Subir Imagen</label>
                    <input class="form-control form-control-sm" type="file" id="clientImg" name="clientImg" accept="image/*">
                </div>
              </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" form="form" name="addClient" class="btn pull-right btn-updt">Agregar</button>
        </div>
        </div>
    </div>
  </div>

<!-- Popper.js (para tooltips y otros componentes) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<!-- Bootstrap Bundle (con Popper.js) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<!-- Scripts propios -->
<script src="../assets/js/sidebar.js"></script>

</body>

</html>