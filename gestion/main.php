<?php
session_start();
include("../checklogin.php");
include BASE_PATH.'dbconnection.php';
check_login();
?>
<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
  <meta charset="utf-8" />
  <title>Gestión</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <meta content="" name="description" />
  <meta content="" name="author" />

<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<!-- CSS personalizados -->
<link href="../assets/css/sidebar.css" rel="stylesheet" type="text/css"/>
<link href="../assets/css/main.css" rel="stylesheet" type="text/css"/>

<!-- Toast notificaciones -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
</head>

<body class="test" >
    <!-- Sidebar -->
<div class="sidebar-overlay"></div> 
  <div class="page-container ">

    <div class="sidebar">
    <?php include("../header-test.php"); ?>
    <?php include("../assets/php/phone-sidebar.php"); ?>
      
    </div>
    <div class="page-content">
    <?php include("../leftbar-test.php"); ?>
        <div class="content">
            <div class="page-title">
                <h2>Gestión Clientes</h2>
            </div><br><br>

            <div class="content-body mx-auto col-xl-10">
                <?php
                if($_SESSION['cargo'] == 6 ){
                ?>
                    <div class="crd mx-auto" onclick="window.location.href='manage-dist.php';">
                        <i class="bi bi-truck"></i>
                        <h4>Distribuidores</h4>
                    </div>
                <?php
                }
                if(isset($_SESSION['deptos']) 
                && 
                is_array($_SESSION['deptos']) 
                && 
                array_intersect([17, 19], $_SESSION['deptos'])){
                ?>
                    <div class="crd mx-auto" onclick="window.location.href='traslados.php';">
                        <i class="bi bi-truck-front-fill"></i>
                        <h4>Traslado y Desvinculacion</h4>
                    </div>
                    <div class="crd mx-auto" onclick="window.location.href='historico-TD.php';">
                        <i class="bi bi-people-fill"></i>
                        <h4> Historico</h4>
                    </div>
                <?php    
                }
                if($_SESSION['cargo'] == 4){
                ?>
                    <div class="crd mx-auto" onclick="window.location.href='clients.php';">
                        <i class="bi bi-people-fill"></i>
                        <h4>Historico Clientes</h4>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>   
    </div>
  </div>


<!-- Popper.js (para tooltips y otros componentes) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<!-- Bootstrap Bundle (con Popper.js) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<!-- Complementos/Plugins-->
<!-- Scripts propios -->
<script src="../assets/js/sidebar.js"></script>
</body>

</html>