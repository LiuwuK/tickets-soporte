<?php
session_start();
include("../../../checklogin.php");
include("../../dbconnection.php");
check_login();
?>
<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
  <meta charset="utf-8" />
  <title>Configuración</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <meta content="" name="description" />
  <meta content="" name="author" />

<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<!-- CSS personalizados -->
<link href="../../../assets/css/sidebar.css" rel="stylesheet" type="text/css"/>
<link href="../../../assets/css/main.css" rel="stylesheet" type="text/css"/>

<!-- Toast notificaciones -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
</head>

<body class="test" >
    <!-- Sidebar -->
<div class="sidebar-overlay"></div> 
  <div class="page-container ">

    <div class="sidebar">
    <?php include("../../header.php"); ?>
      
    </div>
    <div class="page-content">
    <?php include("../../leftbar.php"); ?>
        <div class="content">
            <div class="page-title d-flex justify-content-between">
                <h2>Usuario</h2>
                <button class=" btn-back" onclick="window.location.href='../config.php';"> 
                    <i class="bi bi-arrow-left" ></i>
                </button>
            </div><br><br>

            <div class="content-body mx-auto col-xl-10">
                <div class="crd mx-auto" onclick="window.location.href='manage-users.php';">
                    <i class="bi bi-person-fill-gear"></i>
                    <h4>Editar usuarios</h4>
                </div>
                <div class="crd mx-auto" onclick="window.location.href='change-password.php';">
                    <i class="bi bi-unlock-fill"></i>
                    <h4>Cambiar Contraseña</h4>
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
<script src="../../../assets/js/sidebar.js"></script>
</body>

</html>