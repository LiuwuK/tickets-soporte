<?php
session_start();
include("../../checklogin.php");
include("../../dbconnection.php");
include("assets/php/supervisor.php");
check_login();
?>
<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
  <meta charset="utf-8" />
  <title>Supervisores</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <meta content="" name="description" />
  <meta content="" name="author" />

<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<!-- CSS personalizados -->
<link href="../../assets/css/sidebar.css" rel="stylesheet" type="text/css" />
<link href="../assets/css/general-crud.css" rel="stylesheet" type="text/css"/>

<!-- Toast notificaciones -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
</head>

<body class="test" >
    <!-- Sidebar -->
  <div class="page-container ">

    <div class="sidebar">
    <?php include("../../header-test.php"); ?>
    </div>
    <div class="page-content">
    <?php include("../../leftbar-test.php"); ?>
        <div class="content">
            <div class="page-title d-flex justify-content-between">
                <h2>Supervisores</h2>
                <button class=" btn-back" onclick="window.location.href='gestion-main.php';"> 
                    <i class="bi bi-arrow-left" ></i>
                </button>
            </div>
            <div class="main-crud col-md-11 col-sm-12">
              <div class="head-crud d-flex justify-content-between mb-3">
                <h4>Administrar Supervisores</h4>
                <div class="btns">
                  <button>Nuevo Supervisor</button>
                  <button>Actualizar</button>
                </div>
              </div>
              <hr>
              <div class="body-crud">
                <table class="table table-striped">
                    <thead>
                        <th>Nombre</th>
                        <th>Rut</th>
                        <th>Correo</th>
                        <th>Numero de Contacto</th>
                        <th>Opciones</th>
                    </thead>
                    <tbody>
                      <form name="form" id="editSupervisor" method="post">
                        <div id="loading" style="display:none ;">
                            <div class="loading-spinner"></div>
                            <p>Procesando...</p>
                        </div>  
                        <?php while ($row = mysqli_fetch_assoc($result)) { ?>    
                            <tr>
                                <td>
                                  <input type="text" class="form-control form-control-sm" id="name" name="name" value="<?php echo $row['nombre_supervisor'];?>" required>
                                  <input type="hidden" name="id[]" value="<?php echo $row['id']; ?>">
                                </td>
                                <td>
                                  <input type="text" class="form-control form-control-sm" id="rut" name="rut" value="<?php echo $row['rut'];?>" required>
                                </td>
                                <td>
                                  <input type="email" class="form-control form-control-sm" id="email" name="email" value="<?php echo $row['email'];?>">
                                </td>
                                <td>
                                  <div class="input-group">
                                    <span class="input-group-text" id="numeroC">+56</span>
                                    <input name="numeroC" type="number" class="form-control form-control-sm" value="<?php echo $row['numero_contacto'];?>" aria-label="NumeroContacto" aria-describedby="numeroC">
                                  </div>                              
                                </td>
                                <td>
                                  <button>Eliminar</button>
                                </td>
                            </tr>
                        <?php } ?>
                      </form>
                    </tbody>    
                </table>
              </div>
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
<script src="../../assets/js/sidebar.js"></script>
</body>

</html>