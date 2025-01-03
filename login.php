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
  <script type="text/javascript">
    function checkpass() {
      //Validar numero
      var numero =  document.signup.phone.value;
      const regex = /^\+56\d{9}$/;
    
      if (!regex.test(numero) ){
        alert('Debe ser un numero valido');
        document.signup.phone.focus();
        return false;
      }

      //validar contraseñas
      if (document.signup.password.value != document.signup.cpassword.value) {
        alert('Los campos contraseña y confirmar contraseña no coinciden');
        document.signup.cpassword.focus();
        return false;
      }
      return true;
    }
  </script>
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
              <label for="email" class="control-label">Usuario</label>
              <input type="text" class="form-control rounded-0" id="email" name="email" required="required" placeholder="Username/Email"> 
            </div>
            <div class="form-group">
              <label for="password" class="control-label">Contraseña</label>
              <input type="password" class="form-control rounded-0" id="password" name="password" required="required" placeholder="**************">
            </div>
            <div class="form-group">
                <label for="role" class="control-label">Rol</label>
                <label class="radio-inline">
                    <input type="radio" name="role" value="admin" id="role_admin" required> Admin
                </label>
                <label class="radio-inline">
                    <input type="radio" name="role" value="user" id="role_user" required> User
                </label>
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
        <h2>IMAGEN</h2>
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
              <label for="gender" class="control-label">Genero</label>
              <select class="form-control" style="width: 185px;" name="gender" id="gender" required>
                <option value="male">Male</option>
                <option value="female">Female</option>
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

  <style>
    body {
      display: flex;
      justify-content: center;
      align-items: center;
      margin: 0;
      background-image: url('assets/img/bg.jpg') no-repeat center center / cover; 
      background-color: #f0f0f0; 
    }
    .login-div {
      display: flex;
      width: 95%;
      height: 95%;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
      border-radius: 8px;
      overflow: hidden;
      background-color: white;
    }

    .loginImg {
      display: flex;
      justify-content: center;
      align-items: center;
      flex: 1;
      background: url('assets/img/bgimg.jpg') no-repeat center center / cover;
    }

    .loginCtn {
      color: #2C3E50;
      padding: 20px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .notactive-form {
      display: none;
      opacity: 0;
    }

    .active-form {
      display: flex;
      opacity: 1;
    }

    .btn-login{
      background-color: #34435e;
      color: white;
      width: 100%;
    }

    .double-container{
      display: flex;
      justify-content: space-between;
    }

  </style>

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