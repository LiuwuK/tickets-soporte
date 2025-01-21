<?php
session_start();
include("checklogin.php");
check_login();
include("assets/php/edit-user.php");
?>
<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
  <meta charset="utf-8" />
  <title>Editar usuario</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <meta content="" name="description" />
  <meta content="" name="author" />

<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<!-- CSS personalizados -->
<link href="../assets/css/sidebar.css" rel="stylesheet" type="text/css" />
<link href="../assets/css/profile.css" rel="stylesheet" type="text/css" />

<!-- Toast notificaciones -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
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
            <div class="page-title d-flex justify-content-between">
                <h2>
                  <i class="bi bi-person-gear"></i>   
                  Editar Usuario
                </h2>
                <button class="btn btn-back" onclick="window.location.href='manage-users.php';"> 
                    <i class="bi bi-arrow-left" ></i>
                </button>
            </div>
            <?php 
              while ($rw = mysqli_fetch_array($rt)) { ?>
              <div class="form-content">
                <form name="muser" method="post" action="" enctype="multipart/form-data">
                  <div class="panel-body">
                      <div class="form-row">
                        <div class="form-group">
                          <label for="name" class="form-label">Nombre</label>
                          <input type="text" class="form-control form-control-sm" id="name" name="name" value="<?php echo $rw['name']; ?>" required="required">
                        </div>
                      </div>
                    
                      <div class="form-row">
                        <div class="form-group">
                          <label for="email" class="form-label">Correo</label>
                          <input type="email" class="form-control form-control-sm" id="email" name="email" value="<?php echo $rw['email']; ?>" required="required">
                        </div>
                        <div class="form-group">
                          <label for="alt_email" class="form-label">Correo Alternativo</label>
                          <input type="email" class="form-control form-control-sm" id="alt_email" name="alt_email" value="<?php echo $rw['alt_email']; ?>" >
                        </div>
                      </div>

                      <div class="form-row">
                        <div class="form-group">
                          <label for="mobile" class="form-label">Numero de contacto</label>
                          <input type="text" class="form-control form-control-sm" id="mobile" name="mobile" value="<?php echo $rw['mobile']; ?>" required="required">
                        </div>
                        <div class="form-group">
                          <label for="cargo" class="form-label">Cargo</label>
                          <select class="form-select form-select-sm" id="cargo" name="cargo" >
                          <?php
                            while ($row = mysqli_fetch_assoc($cargos)) {
                              if ($rw['cargo'] == $row['id']){
                                echo "<option selected value=".$row['id'].">".$row['nombre'] ."</option>";
                              } else {
                                echo "<option value=".$row['id'].">".$row['nombre'] ."</option>";
                              }
                            };
                          ?>
                          </select>
                        </div>  
                      </div>

                      <div class="form-row">
                        <div class="form-check">
                          <?php 
                            if($rw['status'] == 1){?>
                              <input class="form-check-input" name="status" type="checkbox" value="1" id="status" checked>
                          <?php  
                            }else{ ?>
                              <input class="form-check-input" name="status" type="checkbox" value="1" id="status">
                          <?php   
                            }
                          ?>

                          <label class="form-check-label" for="status">Activar Cuenta?</label>
                        </div>
                      </div>
                      <div class="form-row">
                        <div class="form-group">
                          <label for="address" class="form-label">Direccion</label>
                          <textarea rows="3" class="form-control form-control-sm" id="address" name="address" required="required"><?php echo $rw['address']; ?></textarea>
                        </div>
                      </div>

                      <div class="panel-footer d-flex justify-content-between">
                        <a href="manage-users.php" class="btn btn-default">Volver</a>
                        <button type="submit" name="update" class="btn btn-updt">Actualizar</button>
                      </div>
      
                  </div>
                <?php } ?>
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
  <script src="../assets/js/sidebar.js"></script>
</body>
            