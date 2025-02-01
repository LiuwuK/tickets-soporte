<?php
session_start();
include("checklogin.php");
include("dbconnection.php");
include("assets/php/clients.php");
check_login();

$row = $clientes->fetch_assoc()
?>
<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
  <meta charset="utf-8" />
  <title>Historico Cliente</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <meta content="" name="description" />
  <meta content="" name="author" />

<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<!-- CSS personalizados -->
<link href="assets/css/sidebar.css" rel="stylesheet" type="text/css"/>
<link href="assets/css/clients.css" rel="stylesheet" type="text/css"/>
<!-- Toast notificaciones -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
<!-- Graficos -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="test" >
    <!-- Sidebar -->
  <div class="page-container ">
    <div class="sidebar">
      <?php include("header-test.php"); ?>
      <?php include("assets/php/phone-sidebar.php"); ?>
    </div>
    <div class="page-content">
    <?php include("leftbar-test.php"); ?>
        <div class="page-title">
          <h2>Historico cliente</h2>
        </div> 
        <div class="col-md-10 client-card mx-auto d-flex">
          <img src="assets/img/admin.jpg" alt="">
          <div class="info-body">
            <h4><?php echo $row['nombre'];?></h4>
            <p>Vertical: <?php echo $row['verticalN'];?></p>
            <h5>Monto total Proyectos: <?php echo '$'.number_format($row['monto_proyectos'], 0, '.', ',');?></h5>
          </div>
        </div>   

        <div class="col-md-10 compe-card mx-auto">
          <h4>Lista de competidores</h4>
          <div class="competidores">
            <!-- while competidores -->
            <div class="list d-flex">
              <div class="img">
                <img src="assets/img/admin.jpg" alt="">
              </div>
              <div class="info-comp">
                <p>Competidor 1</p>
                <p>Sector: Tecnologia</p>
              </div>
            </div>

            <div class="list d-flex">
              <div class="img">
                <img src="assets/img/admin.jpg" alt="">
              </div>
              <div class="info-comp">
                <p>Competidor 2</p>
                <p>Sector: Tecnologia</p>
              </div>
            </div>
            
            <div class="list d-flex">
              <div class="img">
                <img src="assets/img/admin.jpg" alt="">
              </div>
              <div class="info-comp">
                <p>Competidor 3</p>
                <p>Sector: Tecnologia</p>
              </div>
            </div>

            <div class="list d-flex">
              <div class="img">
                <img src="assets/img/admin.jpg" alt="">
              </div>
              <div class="info-comp">
                <p>Competidor 4</p>
                <p>Sector: Tecnologia</p>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-10 compe-card mx-auto">
          <h4>Licitaciones </h4>
          <div class="licitacion">
            <div class="lici-info d-flex">
              <div class="lici-left">
                <strong >Licitacion 1 </strong>
                <span class="label label-success">$24,000,000</span>
                <p class="mt-3">Clasificacion: Tecnologia</p>
                <p>Competidor Ganador: Competidor 1</p>
                <p>Fecha Adjudicacion: 03/02/2025</p>
                <p>Fecha Renovacion: 12/02/2026</p>
              </div>
              <div class="lici-desc">
                  <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Assumenda minima repellendus consectetur distinctio, autem repellat iure accusantium quos qui, veritatis illum unde tenetur officiis dolorem soluta, placeat reprehenderit ducimus esse?</p>
              </div>
            </div>

            <div class="lici-info d-flex">
              <div class="lici-left">
                <strong>Licitacion 2 </strong>
                <span class="label label-success">$24,000,000</span>
                <p class="mt-3">Clasificacion: Guardias</p>
                <p>Fecha Adjudicacion: 03/02/2025</p>
                <p>Fecha Renovacion: 12/02/2026</p>
              </div>

              <div class="lici-desc">
                  <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Assumenda minima repellendus consectetur distinctio, autem repellat iure accusantium quos qui, veritatis illum unde tenetur officiis dolorem soluta, placeat reprehenderit ducimus esse?</p>
              </div>
            </div>

            <div class="lici-info d-flex">
              <div class="lici-left">
                <strong>Licitacion 3 </strong>
                <span class="label label-success">$24,000,000</span>
                <p class="mt-3">Clasificacion: Guardias</p>
                <p>Fecha Adjudicacion: 03/02/2025</p>
                <p>Fecha Renovacion: 12/02/2026</p>
              </div>

              <div class="lici-desc">
                  <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Assumenda minima repellendus consectetur distinctio, autem repellat iure accusantium quos qui, veritatis illum unde tenetur officiis dolorem soluta, placeat reprehenderit ducimus esse?</p>
              </div>
            </div>

            <div class="lici-info d-flex">
              <div class="lici-left">
                <strong>Licitacion 4 </strong>
                <span class="label label-success">$24,000,000</span>
                <p class="mt-3">Clasificacion: Guardias</p>
                <p>Fecha Adjudicacion: 03/02/2025</p>
                <p>Fecha Renovacion: 12/02/2026</p>
              </div>

              <div class="lici-desc">
                  <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Assumenda minima repellendus consectetur distinctio, autem repellat iure accusantium quos qui, veritatis illum unde tenetur officiis dolorem soluta, placeat reprehenderit ducimus esse?</p>
              </div>
            </div>
          </div>
        </div>
    </div>
  </div>

<!-- Popper.js (para tooltips y otros componentes) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<!-- Bootstrap Bundle (con Popper.js) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<!-- Scripts propios -->
<script src="assets/js/sidebar.js"></script>

</body>

</html>