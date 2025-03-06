<?php
session_start();
include("../checklogin.php");
include BASE_PATH . 'dbconnection.php';
include("../admin/phpmail.php");
include("assets/php/create-ticket.php");

check_login();
?>
<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
  <meta charset="utf-8" />
  <title>Tickets</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <meta content="" name="description" />
  <meta content="" name="author" />

<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<!-- CSS personalizados -->
<link href="../assets/css/sidebar.css" rel="stylesheet" type="text/css" />
<link href="../projects/assets/css/create-project.css" rel="stylesheet" type="text/css" />

<!-- Toast notificaciones -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
</head>

<body class="test" >
    <!-- Sidebar -->
<div class="sidebar-overlay"></div> 
  <div class="page-container ">

    <div class="sidebar">
    <?php include("../header-test.php"); ?>
    <?php include("../assets/php/phone-sidebar.php"); ?>
      
    </div>
    <div class="page-content">
    <?php include("../leftbar-test.php"); ?>
        <div class="content">
            <div class="page-title d-flex justify-content-between">
                <h2>Crear Ticket</h2>
                <button class=" btn-back" onclick="window.location.href='tickets-main.php';"> 
                    <i class="bi bi-arrow-left" ></i>
                </button>
            </div>
            <!-- Formulario crear Ticket -->
            <form class="form-horizontal" name="form" method="POST" action=""  onSubmit="return valid();" enctype="multipart/form-data">
                <div id="loading" style="display:none ;">
                    <div class="loading-spinner"></div>
                    <p>Procesando...</p>
                </div>  
                <div class="ticket-main  col-xl-8 col-sm-12">
                    <br>
                    <?php if (isset($_SESSION['msg1'])) : ?>
                        <p align="center" style="color:#FF0000"><?= $_SESSION['msg1']; ?><?= $_SESSION['msg1'] = ""; ?></p>
                    <?php endif; ?>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="subject" class="form-label">Asunto</label>
                            <input type="text" name="subject" id="subject" value="" required class="form-control form-control-sm" />
                        </div>
                    </div>


                    <div class="form-row">
                        <div class="form-group">
                            <label for="tasktype" class="form-label">Departamento Asociado</label>
                                <select id="tasktype" name="tasktype" class="form-select form-select-sm" required>
                                    <option value="">Seleccionar</option>
                                    <?php
                                    while ($row = mysqli_fetch_assoc($deptos)) {
                                        echo "<option value=". $row['id'] .">". $row['nombre'] ."</option>";
                                    };
                                    ?>
                                </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="priority" class="form-label">Prioridad</label>
                            <select id="priority" name="priority" class="form-select form-select-sm">
                            <?php
                            while ($row = mysqli_fetch_assoc($prioridad)) {
                                echo "<option value=". $row['id'] .">". $row['nombre'] ."</option>";
                            };
                            ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="ticketImage" class="form-label">Subir Imagen</label>
                            <input class="form-control form-control-sm" type="file" id="ticketImage" name="ticketImage" accept="image/*">
                        </div>
                    </div>       
                    <div class="form-row">
                        <div class="form-group">
                            <label for="description" class="form-label">Descripción</label>
                            <textarea id="description" name="description" required class="form-control form-control-sm" rows="5"></textarea>
                        </div>
                    </div>

                    <div class="footer">
                        <button class="btn btn-default">Resetear</button>
                        <button type="submit" name="send" class="btn btn-updt">Enviar</button>
                    </div>
                </div>
            </form>
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