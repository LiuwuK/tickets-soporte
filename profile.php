<?php
session_start();
include("checklogin.php");
include("dbconnection.php");

include("assets/php/profile.php");

check_login();
?>
<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
  <meta charset="utf-8" />
  <title>Projects </title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <meta content="" name="description" />
  <meta content="" name="author" />

<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<!-- CSS personalizados -->
<link href="assets/css/sidebar.css" rel="stylesheet" type="text/css" />
<link href="assets/css/profile.css" rel="stylesheet" type="text/css" />

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
                <h2>Mi Perfil</h2>
            </div>
            <?php
            while ($row = mysqli_fetch_assoc($usr)) {
            ?>
            <div class="form-content">
                <form class="form-horizontal" method="post" enctype="multipart/form-data">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div align="left">
                                <h3><?php echo $row['name']?></h3>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label" for="name" >Nombre </label>
                                    <input type="text" name="name" id="name" value="<?php echo $row['name']; ?>" class="form-control form-control-sm " />
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label" for="email">Correo Principal </label>
                                    <input type="text" name="email" value="<?php echo $row['email']; ?>" class="form-control form-control-sm" />
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="alt_email">Correo Alternativo </label>
                                    <input type="text" name="alt_email" value="<?php echo $row['alt_email']; ?>" class="form-control form-control-sm" />
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label" for="phone">Contacto </label>
                                    <input type="text" name="phone" id="phone" value="<?php echo $row['mobile']; ?>" maxlength="10" class="form-control form-control-sm" />
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="cargo">Cargo</label>
                                    <input class="form-control form-control-sm" type="text" name="cargo" id="cargo" value="<?php echo $row['cargoUser'];?>" disabled>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Dirección</label>
                                    <textarea class="form-control form-control-sm" name="address" rows="5"><?php echo $row['address']; ?></textarea>
                                </div>
                            </div>
                        </div>
                <?php } ?>
                        <div class="panel-footer d-flex justify-content-between">
                            <button class="btn btn-default" name="reset" type="reset">Resetear</button>
                            <button type="submit" name="update" class="btn btn-updt">Actualizar</button>
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
</body>

</html>