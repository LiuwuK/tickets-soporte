<?php
session_start();
include("../../checklogin.php");
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
<link href="../../assets/css/sidebar.css" rel="stylesheet" type="text/css"/>
<link href="../../assets/css/main.css" rel="stylesheet" type="text/css"/>

<!-- Toast notificaciones -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
</head>

<body class="test" >
    <!-- Sidebar -->
<div class="sidebar-overlay"></div> 
<div class="page-container ">
    <div class="sidebar">
    <?php include("../../header-test.php"); ?>
    </div>
    <div class="page-content">
    <?php include("../../leftbar-test.php"); ?>
        <div class="content">
            <div class="page-title d-flex justify-content-between">
                <h2>Planificador</h2>
                <button class=" btn-back" onclick="window.location.href='../main.php';"> 
                    <i class="bi bi-arrow-left" ></i>
                </button>
            </div><br><br>

            <div class="content-body mx-auto col-xl-10">
                <?php    
                if(isset($_SESSION['deptos']) && in_array(238, $_SESSION['deptos'])){
                ?>
                <div class="crd mx-auto" onclick="window.location.href='colaboradores.php';">
                    <i class="bi bi-people-fill"></i>
                    <h4>Colaboradores</h4>
                </div>  
               <?php
                }
                ?>
                <div class="crd mx-auto" onclick="window.location.href='instalaciones.php';">
                    <i class="bi bi-buildings-fill"></i>
                    <h4>Sucursales</h4>
                </div> 
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