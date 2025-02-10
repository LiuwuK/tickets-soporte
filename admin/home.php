<?php
session_start();
include("../checklogin.php");
include("dbconnection.php");
include("assets/php/home.php");
check_login();

?>
<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
  <meta charset="utf-8" />
  <title>home</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <meta content="" name="description" />
  <meta content="" name="author" />

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <!-- Calendario CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/litepicker@0.6.6/dist/css/litepicker.css"/>
  <!-- CSS personalizados -->
  <link href="../assets/css/sidebar.css" rel="stylesheet" type="text/css" />
  <!-- Toast notificaciones -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
  <!-- chartjs 
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  -->
</head>


<body class="test" >
    <!-- Sidebar --> 
  <div class="page-container ">
    <div class="sidebar">
    <?php include("header.php"); ?>
    </div>
    <div class="page-content">
    <?php include("leftbar.php"); ?>
        <div class="content">
          <div id="calendar">
          </div>
        </div>   
    </div>
  </div>

  <!-- Popper.js (para tooltips y otros componentes) -->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <!-- Bootstrap Bundle (con Popper.js) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Calendario -->
  <script src="https://cdn.jsdelivr.net/npm/litepicker/dist/litepicker.js"></script>
  <!-- Scripts propios -->
  <script src="../assets/js/sidebar.js"></script>

</body>

</body>

</html>