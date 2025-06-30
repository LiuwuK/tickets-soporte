<?php
session_start();
include("../checklogin.php");
include("../dbconnection.php");
check_login();
include("assets/php/manage-dist.php");


?>
<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
  <meta charset="utf-8" />
  <title>Distribuidores</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <meta content="" name="description" />
  <meta content="" name="author" />

<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<!-- Calendario CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/litepicker@0.6.6/dist/css/litepicker.css"/>
<!-- CSS personalizados -->
<link href="../assets/css/sidebar.css" rel="stylesheet" type="text/css" />
<link href="assets/css/manage-dist.css" rel="stylesheet" type="text/css" />
<!-- Toast notificaciones -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
</head>

<body class="test" >
    <!-- Sidebar -->
  <div class="page-container ">
    <div class="sidebar">
    <?php include("../header-test.php"); ?>
    </div>
    <div class="page-content">
    <?php include("../leftbar-test.php"); ?>
        <div class="content">
            <div class="page-title d-flex justify-content-between">
                <h2>
                  <i class="bi bi-truck"></i>  
                  Distribuidores
                </h2>
                <button class=" btn-back" onclick="window.location.href='main.php';"> 
                    <i class="bi bi-arrow-left" ></i>
                </button>
            </div>
            <div class="main">
                <form name="form" id="form" method="post" >
                    <div id="loading" style="display:none ;">
                        <div class="loading-spinner"></div>
                        <p>Procesando...</p>
                    </div>
                    <table class="table table-striped">
                        <thead>
                            <th>Distribuidor</th>
                            <th>Monto Asignado</th>
                            <th>Monto Restante</th>
                            <th>Opciones</th>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($distribuidorData)) { ?>    
                                <tr>
                                    <td>
                                        <?php echo $row['nombre'] ?>
                                        <input type="hidden" name="id[]" value="<?php echo $row['id']; ?>">
                                    </td>
                                    <td>
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">$</span>
                                            <input 
                                                type="number" 
                                                name="monto[]" 
                                                class="form-control form-control-sm" 
                                                value="<?php echo $row['monto']; ?>" 
                                                onchange="enableUpdateButton()"
                                            >
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">$</span>
                                            <input 
                                                type="number" 
                                                name="monto_restante[]" 
                                                class="form-control form-control-sm" 
                                                value="<?php echo $row['monto_restante'];?>" 
                                                onchange="enableUpdateButton()"
                                            >
                                        </div>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-updt" onclick="window.location.href='dist-details.php?id=<?php echo $row['id']; ?>';">
                                            <i class="bi bi-info-circle">Ver detalles</i>
                                        </button>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>    
                    </table>
                    <div class="text-end">
                        <button 
                            id="updateButton" 
                            class="btn btn-updt" 
                            type="submit" 
                            disabled
                            name="updt-dist"
                        >
                           Actualizar
                        </button>
                    </div>
                </form>
            </div>
        </div>   
    </div>
  </div>

<script>
    
</script>

<!-- Popper.js (para tooltips y otros componentes) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<!-- Bootstrap Bundle (con Popper.js) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<!-- Calendario -->
<script src="https://cdn.jsdelivr.net/npm/litepicker/dist/litepicker.js"></script>
<!-- Scripts propios -->
<script src="../assets/js/sidebar.js"></script>
<script src="assets/js/dist-details.js"></script>
</body>
</html>