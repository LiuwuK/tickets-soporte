<?php
session_start();
include("assets/php/login.php");
?>
<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
  <meta charset="utf-8" />
  <title>CWEB Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <meta content="" name="description" />
  <meta content="" name="author" />
  <link href="assets/plugins/pace/pace-theme-flash.css" rel="stylesheet" type="text/css" media="screen" />
  <link href="assets/plugins/boostrapv3/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
  <link href="assets/plugins/boostrapv3/css/bootstrap-theme.min.css" rel="stylesheet" type="text/css" />
  <link href="assets/plugins/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css" />
  <link href="assets/css/animate.min.css" rel="stylesheet" type="text/css" />
  <link href="assets/css/style.css" rel="stylesheet" type="text/css" />
  <link href="assets/css/responsive.css" rel="stylesheet" type="text/css" />
  <link href="assets/css/custom-icon-set.css" rel="stylesheet" type="text/css" />

</head>

<body class="error-body no-top">
  <div class="container">
    <div class="row login-container">
      <div class="col-md-5">
        <h2 class="text-center text-white"><strong>Acceder al Sistema de Tickets</strong></h2>
        <hr style="border-color:#ebe7e7">
        <p class="text-center">
          Aún no tienes cuenta <a href="registration.php">Regístrate aquí !!</a>
        </p>
      </div>
      <div class="col-md-5 "> <br>
        <p style="color:#F00"><?php echo $_SESSION['action1']; ?><?php echo $_SESSION['action1'] = ""; ?></p>
        <form id="login-form" class="login-form" action="" method="post">
          <p style="color: #F00"><?php echo $_SESSION['action1']; ?><?php echo $_SESSION['action1'] = ""; ?></p>
          <div class="form-group">
            <label for="email" class="control-label">Correo</label>
            <input type="text" class="form-control rounded-0" id="email" name="email" required="required">
          </div>
          <div class="form-group">
            <label for="password" class="control-label">Contraseña</label>
            <input type="password" class="form-control rounded-0" id="password" name="password" required="required">
          </div>
          <div class="form-group text-center">
            <button class="btn btn-primary btn-cons pull-right" name="login" type="submit">Acceder</button>
          </div>
        </form>
      </div>


    </div>
  </div>
  <script src="assets/plugins/jquery-1.8.3.min.js" type="text/javascript"></script>
  <script src="assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
  <script src="assets/plugins/pace/pace.min.js" type="text/javascript"></script>
  <script src="assets/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
  <script src="assets/js/login.js" type="text/javascript"></script>
</body>

</html>