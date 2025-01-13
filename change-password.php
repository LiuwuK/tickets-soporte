<?php
session_start();
include("checklogin.php");
include("dbconnection.php");
include("admin/notificaciones.php");
include("assets/php/change-password.php");

check_login();
?>
<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
  <meta charset="utf-8" />
  <title>Cambiar Contraseña</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <meta content="" name="description" />
  <meta content="" name="author" />

<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<!-- CSS personalizados -->
<link href="assets/css/sidebar.css" rel="stylesheet" type="text/css" />
<link href="assets/css/change-password.css" rel="stylesheet" type="text/css" />

<!-- Toast notificaciones -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
</head>

<body class="test" >
    <!-- Sidebar -->
  <div class="page-container ">

    <div class="sidebar">
    <?php include("header-test.php"); ?>
      
    </div>
    <div class="page-content">
    <?php include("leftbar-test.php"); ?>
        <div class="content">
            <div class="page-title">
                <h2>Cambiar Contraseña</h2>
            </div>
            <div class="form-content">
                <form class="form-horizontal" name="form1" method="post" action="" onSubmit="return valid();">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <?php
                                if (!empty($_SESSION['msg1'])) {
                                    echo '<p style="text-align: center; color: #FF0000;">' . htmlspecialchars($_SESSION['msg1'], ENT_QUOTES, 'UTF-8') . '</p>';
                                    $_SESSION['msg1'] = "";
                                }
                            ?>
                           <div class="form-row">
                                <div class="form-group">
                                    <label for="oldpass" class="form-label">Contraseña Actual</label>
                                    <input type="password" name="oldpass" id="oldpass" value="" class="form-control form-control-sm" placeholder="******************" required/>
                                </div>
                           </div>
                            <div class="form-row">    
                                <div class="form-group">
                                    <label for="newpass" class="form-label">Nueva Contraseña</label>
                                    <input type="password" name="newpass" id="newpass" value="" class="form-control form-control-sm" placeholder="******************" required />
                                </div>
                            </div>
                            <div class="form-row">                                
                                <div class="form-group">
                                    <label class="form-label">Confirmar Contraseña</label>
                                    <input type="password" name="confirmpassword" id="confirmpassword" class="form-control form-control-sm" placeholder="******************" required/>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer d-flex justify-content-between">
                            <button class="btn btn-default">Resetear</button>
                            <button type="submit"  name="change" class="btn btn-updt">Cambiar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>   
    <br><br>
    </div>
  </div>


<!-- Popper.js (para tooltips y otros componentes) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<!-- Bootstrap Bundle (con Popper.js) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<!-- Complementos/Plugins-->    
<!-- Scripts propios -->
<script src="assets/js/sidebar.js"></script>
<script src="assets/js/change-password.js"></script>
</body>

</html>