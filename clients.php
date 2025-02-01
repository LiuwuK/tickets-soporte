<?php
session_start();
include("checklogin.php");
include("dbconnection.php");
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
<link href="assets/css/sidebar.css" rel="stylesheet" type="text/css"/>
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
      <?php include("header-test.php"); ?>
      <?php include("assets/php/phone-sidebar.php"); ?>
    </div>
    <div class="page-content">
    <?php include("leftbar-test.php"); ?>
        <div class="page-title">
          <h2>Clientes</h2>
        </div> <br><br>
        <div class="content">
        <?php
          if($num_cl > 0){
            while($row = $clientes->fetch_assoc()){
        ?>
              <div class="card" onclick="window.location.href='client-info.php?clientID=<?php echo $row['id']; ?>';">
                <div class="profile-img mt-3">
                  <img src="assets/img/admin.jpg" alt="">
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
          }
        ?>
        </div>   
    </div>
  </div>

<!-- Popper.js (para tooltips y otros componentes) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<!-- Bootstrap Bundle (con Popper.js) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<!-- Scripts propios -->
<script src="assets/js/sidebar.js"></script>

</body>

</html>