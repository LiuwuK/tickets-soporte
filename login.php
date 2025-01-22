<?php
session_start();
include("assets/php/login.php");
?>
<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
  <meta charset="utf-8" />
  <title>Login</title>
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
  <link href="assets/css/login.css" rel="stylesheet" type="text/css" />
  

</head>

<body>
  <div class="login-div">
    <!-- formulario login -->
    <div class="container loginCtn col-md-5" id="login-form-container">
      <div class="row login-container">
        <div class="col-md-10">
          <h2 class=""><strong>Iniciar Sesión</strong></h2>
        </div>

        <div class="col-md-10"> <br>
          <p style="color:#F00"><?php echo $_SESSION['action1']; ?><?php echo $_SESSION['action1'] = ""; ?></p>
          <form id="login-form" class="login-form" action="" method="post">
            <div class="form-group">
              <label for="email" class="control-label">Email</label>
              <input type="text" class="form-control rounded-0" id="email" name="email" required="required" placeholder="test@safeteck.com"> 
            </div>
            <div class="form-group">
              <label for="password" class="control-label">Contraseña</label>
              <input type="password" class="form-control rounded-0" id="password" name="password" required="required" placeholder="**************">
            </div>
            <div class="form-group text-center">
              <button class="btn btn-login" name="login" type="submit">Acceder</button>
            </div>
          </form>
          <div>
            <br>  
             <p>Aún no tienes cuenta <a href="javascript:void(0);" id="switch-to-register">Regístrate aquí !!</a></p>
          </div>
        </div>
      </div>
    </div>

    <div class="loginImg col-md-7" id="login-img-container">
        
    </div>

    <div class="notactive-form container loginCtn col-md-5" id="register-form-container">
      <div class="row login-container">
        <div class="col-md-10">
          <h2 class=""><strong>Registro de usuarios</strong></h2>
        </div>

        <div class="col-md-10"> <br>
        <form id="signup" name="signup" class="login-form" onsubmit="return checkpass();" method="post">
          <div class="form-group">
            <label for="name" class="control-label">Nombre</label>
            <input type="text" class="form-control rounded-0" id="name" name="name" required="required">
          </div>
          <div class="form-group">
            <label for="email" class="control-label">Correo</label>
            <input type="text" class="form-control rounded-0" id="email" name="email" required="required">
          </div>
          <div class="double-container">
            <div class="form-group ">
              <label for="password" class="control-label">Contraseña</label>
              <input type="password" class="form-control rounded-0" id="password" name="password" required="required">
            </div>
            
            <div class="form-group ">  
              <label for="password" class="control-label">Confirmar Contraseña</label>
              <input type="password" class="form-control rounded-0" id="cpassword" name="cpassword" required="required">
            </div>
          </div>
          
          <div class="double-container">
            <div class="form-group">
              <label for="phone" class="control-label">Número de Contacto</label>
              <input type="text" class="form-control rounded-0" id="phone" name="phone" required="required">
            </div>

            <div class="form-group">
              <label for="gender" class="control-label">Cargo</label>
              <select class="form-control" style="width: 185px;" name="cargo" id="gender" required>
              <?php
                while ($row = mysqli_fetch_assoc($cargo)) {
                  echo "<option value=".$row['id'].">".$row['nombre']."</option>";
                };
                ?>
              </select>
            </div>
          </div>
          <div class="form-group text-center">
            <button class="btn btn-login rounded-pill" name="registro" type="submit">Crear Cuenta</button>
          </div>

          <div>
            <br>  
             <p>Ya tienes cuenta? <a href="javascript:void(0);" id="switch-to-login">Inicia sesión aquí !!</a></p>
          </div>
        </form>
        </div>

      </div>
    </div>
  </div>


  <script src="assets/plugins/jquery-1.8.3.min.js" type="text/javascript"></script>
  <script src="assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
  <script src="assets/plugins/pace/pace.min.js" type="text/javascript"></script>
  <script src="assets/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
  <script src="assets/js/login.js" type="text/javascript"></script>

  <script>
    // Mostrar formulario de registro 
    document.getElementById("switch-to-register").addEventListener("click", function() {
      document.getElementById("login-form-container").classList.add("notactive-form");
      document.getElementById("login-form-container").classList.remove("active-form");

      document.getElementById("register-form-container").classList.add("active-form");
      document.getElementById("register-form-container").classList.remove("notactive-form");
    });

    // Mostrar formulario login
    document.getElementById("switch-to-login").addEventListener("click", function() {
      document.getElementById("register-form-container").classList.add("notactive-form");
      document.getElementById("register-form-container").classList.remove("active-form");

      document.getElementById("login-form-container").classList.add("active-form");
      document.getElementById("login-form-container").classList.remove("notactive-form");
    });

    
  </script>
</body>

</html>