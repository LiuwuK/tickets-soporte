<?php
session_start();
include("../checklogin.php");
include("../dbconnection.php");
include("assets/php/dist-details.php");


check_login();
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
                  Editar Distribuidores
                </h2>
                <button class="btn btn-back" onclick="window.location.href='manage-dist.php';"> 
                    <i class="bi bi-arrow-left" ></i>
                </button>
            </div><br>
            
            <div class="main">
                <h3><?php echo $distribuidor['nombre']; ?></h3>
                <?php 
                  while ($row = mysqli_fetch_assoc($proyectos)){
                  $fecha_original = $row['fecha_creacion']; 
                  setlocale(LC_TIME, 'es_ES.UTF-8', 'spanish');
                  // Formatear la fecha
                  $timestamp = strtotime($fecha_original);
                  $fecha = strftime('%e de %B %Y', $timestamp);
                ?>
                <div class="maincard" onclick="toggleDetails(this)">
                  <div class="card d-flex flex-row align-items-center p-3 mb-2">
                    <div class="flex-grow-1">
                      <strong><?php echo $row['nombre']; ?></strong>
                    </div>
                    <div class="text-center" style="width: 200px;">
                      <p><?php echo $fecha; ?></p>
                    </div>
                    <div class="text-end" style="width: 150px;">
                      <p><?php echo '$' . number_format($row['monto'], 0, '.', ','); ?></p>
                    </div>
                  </div>
                  <div class="additional-info">
                    <div class="title">
                      <h4>Lista de materiales</h4>
                    </div>
                    <div class="info">
                      <?php
                        $total = 0;
                        $id =  $row['id'];
                        $query = "SELECT * 
                                    FROM bom
                                    WHERE proyecto_id = $id";    
                        $bom = $con->prepare($query);
                        $bom->execute();
                        $result = $bom->get_result();
                        $num = $result->num_rows;
                       if ($num > 0){
                        while($material = $result->fetch_assoc() ){
                          $total = $total + $material['total'];
                      ?>       
                          <div class="material-item list-group-item">
                              <div><?php echo $material['nombre'];?></div>
                              <div><i class="bi bi-x-lg"></i></div>
                              <div><?php echo $material['cantidad'];?></div>
                              <div><?php echo '$'.number_format($material['total'], 0, '.', ',');;?></div>
                          </div>
                      <?php
                        }
                        echo "<div class='material-total'>Total: $".number_format($total, 0, '.', ',')."</div>";  
                       }else{
                        echo "<p>Aun no tiene materiales asociados</p>";
                       }
                      ?>
                    </div>
                  </div>
                </div>
                <?php
                  }
                ?>
            </div>
        </div>   
    </div>
  </div>

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