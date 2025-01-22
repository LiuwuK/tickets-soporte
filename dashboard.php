<?php
session_start();
include("checklogin.php");
include("dbconnection.php");

check_login();
?>
<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
  <meta charset="utf-8" />
  <title>home</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <meta content="" name="description" />
  <meta content="" name="author" />

<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<!-- Calendario CSS -->
<!-- CSS personalizados -->
<link href="assets/css/sidebar.css" rel="stylesheet" type="text/css"/>
<link href="assets/css/dashboard.css" rel="stylesheet" type="text/css"/>
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
    </div>
    <div class="page-content">
    <?php include("leftbar-test.php"); ?>
        <div class="content">
          <!-- Primera fila -->
          <div class="row">
            <!-- <div id="calendar">
            </div> -->
            <!-- Dependiendo del usuario cambia el dashboard -->
            <?php
              //  contador
              $counter = 0;
              // User Tecnico
              if($_SESSION['cargo'] == 5){
            ?>
              <div class="first-row">
                <div class="t-head">
                  <h3>Tickets pendientes</h3>
                </div>
                <div class="t-body">
                  <div class="d-flex flex-row align-items-center pl-3 mb-3 ">
                    <div class="flex-grow-1 text-center">
                      <strong>Asunto</strong>
                    </div>
                    <div class="text-center" style="width: 50%;">
                      <strong>Resumen</strong>
                    </div>
                    <div class="text-center" style="width: 25%;">
                      <strong>Tiempo transcurrido</strong>
                    </div>
                    <div class="text-end" style="width: 10%;">
                      <p></p>
                    </div>
                  </div>
                  <?php                     
                    while ($row = mysqli_fetch_assoc($tickets_pendiente)){
                      $posting_date = $row['posting_date'];
                      $date_posting = new DateTime($posting_date);
                      $today = new DateTime();
                      $interval = $today->diff($date_posting);
                      $days_passed = $interval->days;
                      $months_passed = $interval->m + ($interval->y * 12);

                      if ($days_passed > 30) {
                          $time_passed = $months_passed . " Meses";
                      } else if($days_passed <= 0){
                        $time_passed = "Hoy";
                      } else {
                          $time_passed = $days_passed . " Días";
                      }
                  ?>
                    <div class="card d-flex flex-row align-items-center p-3 mb-2">
                      <div class="flex-grow-1 text-center">
                        <strong><?php echo $row['subject']; ?></strong>
                      </div>
                      <div class="text-center" style="width: 50%; overflow:hidden;">
                        <p><?php echo $row['ticket']; ?></p>
                      </div>
                      <div class="text-center" style="width: 20%;">
                        <p>Hace <?php echo $time_passed ?></p>
                      </div>
                      <div class="text-end" style="width: 100px;">
                        <p><button class="btn btn-updt" onclick="window.location.href='tickets-asignados.php?textSearch=<?php echo $row['id']; ?>&priority=<?php echo $row['prioprity']; ?>';">Ver</button></p>
                      </div>
                    </div>
                  <?php
                      $counter++; 
                      if ($counter >= 5) { 
                          break;
                      }
                    }
                  ?>
                </div>
              </div>
            <?php
              }
              // User comercial Y Gerencia
              else if($_SESSION['cargo'] == 2 or $_SESSION['cargo'] == 4){
              ?>
                <div class="first-row">
                  <div class="t-head">
                    <h3>Top 5 Proyectos</h3>
                  </div>
                  <div class="t-body">
                    <div class="d-flex flex-row align-items-center pr-3 mb-2">
                        <div class="flex-grow-1 text-center" style="width: 20%;">
                          <strong>Nombre</strong>
                        </div>
                        <div class="text-center" style="width: 30%; overflow:hidden;">
                          <strong>Clasificación</strong>
                        </div>
                        <div class="text-center" style="width: 20%;">
                          <strong>Monto proyecto</strong>
                        </div>
                        <div class="text-center" style="width: 20%;">
                          <strong>Estado</strong>
                        </div>
                        <div class="text-end" style="width: 12%;">
                        </div>
                    </div>
                    <?php                     
                      while ($row = mysqli_fetch_assoc($top_proyectos)){
                      ?>
                      <div class="card d-flex flex-row align-items-center p-3 mb-2">
                        <div class="flex-grow-1 text-center" style="width: 20%;">
                          <strong><?php echo $row['nombre'] ;?></strong>
                        </div>
                        <div class="text-center" style="width: 30%; overflow:hidden;">
                          <p><?php echo $row['clasiN'] ;?></p>
                        </div>
                        <div class="text-center" style="width: 20%;">
                          <p><?php echo '$'.number_format($row['monto'], 0, '.', ',');?></p>
                        </div>
                        <div class="text-center" style="width: 20%;">
                          <?php
                          if ($row['estado_id'] == '20') {
                          ?>
                            <span class="label label-success"><?php echo $row['estado']; ?></span>
                            <?php
                            }else if ($row['estado_id'] == '21'){
                            ?>
                              <span class="label label-important"><?php echo $row['estado']; ?></span>
                              <?php
                            }else{?>
                              <span class="label label-warning"><?php echo $row['estado']; ?></span>
                              <?php
                          };?>
                        </div>
                        <div class="text-end" style="width: 10%;">
                          <p><button class="btn btn-updt" onclick="window.location.href='view-projects.php';">Ver</button></p>
                        </div>
                      </div>
                    <?php
                      $counter++; 
                        if ($counter >= 5) { 
                            break;
                        }
                      }
                    ?>
                  </div>
                  <div class="t-footer d-flex justify-content-between ">
                      <strong>Proyectos: <?php echo $total_proyectos ?> </strong>
                      <strong>Monto total proyectos: <?php echo '$'.number_format($monto_general, 0, '.', ',');?></strong>
                      <strong>Monto proyectos ganados: <?php echo '$'.number_format($monto_ganados, 0, '.', ',');?> </strong>
                  </div>
                </div>
              <?php
              }
              // user contabilidad y finanzas
              else if($_SESSION['cargo'] == 3){
                ?>
                  <div class="first-row">
                    <div class="t-head">
                      <h3>Proyectos por facturar</h3>
                    </div>
                    <div class="t-body">
                      <div class="d-flex flex-row align-items-center pr-3 mb-2">
                          <div class="flex-grow-1 text-center" style="width: 20%;">
                            <strong>Nombre</strong>
                          </div>
                          <div class="text-center" style="width: 30%; overflow:hidden;">
                            <strong>Clasificación</strong>
                          </div>
                          <div class="text-center" style="width: 20%;">
                            <strong>Costo proyecto</strong>
                          </div>
                          <div class="text-center" style="width: 20%;">
                            <strong>Tiempo transcurrido</strong>
                          </div>
                          <div class="text-end" style="width: 12%;">
                          </div>
                      </div>
                      <?php                     
                        while ($row = mysqli_fetch_assoc($xfacturar)){
                          $posting_date = $row['fecha_actualizacion'];
                          $date_posting = new DateTime($posting_date);
                          $today = new DateTime();
                          $interval = $today->diff($date_posting);
                          $days_passed = $interval->days;
                          $months_passed = $interval->m + ($interval->y * 12);

                          if ($days_passed > 30) {
                              $time_passed = $months_passed . " Meses";
                          } else if($days_passed <= 0){
                            $time_passed = "Hoy";
                          } else {
                              $time_passed = $days_passed . " Días";
                          }
                        ?>
                        <div class="card d-flex flex-row align-items-center p-3 mb-2">
                          <div class="flex-grow-1 text-center" style="width: 20%;">
                            <strong><?php echo $row['nombre'] ;?></strong>
                          </div>
                          <div class="text-center" style="width: 30%; overflow:hidden;">
                            <p><?php echo $row['clasiN'] ;?></p>
                          </div>
                          <div class="text-center" style="width: 20%;">
                            <p><?php echo '$'.number_format($row['costo_real'], 0, '.', ',');?></p>
                          </div>
                          <div class="text-center" style="width: 20%;">
                            <p><?php echo $time_passed ;?></p>
                          </div>
                          <div class="text-end" style="width: 10%;">
                            <p><button class="btn btn-updt" onclick="window.location.href='bill-projects.php';">Ver</button></p>
                          </div>
                        </div>
                      <?php
                        $counter++; 
                          if ($counter >= 5) { 
                              break;
                          }
                        }
                      ?>
                    </div>
                    <div class="t-footer d-flex justify-content-between ">
                       
                    </div>
                  </div>
                <?php
                }
            ?>
          </div>
          <!-- Segunda fila  -->
           <div class="row">
            <?php
              if($_SESSION['cargo'] == 4){
            ?>  
              <div class="col-sm-12 col-md-12 col-lg-8 col-xl-6 graficos">
                <h3>Total Proyectos</h3>
                <canvas id="totalProjects"></canvas>
              </div>
              
              <div class="col-sm-12 col-md-12 col-lg-8 col-xl-5 graficos">
                <h3>Cantidad de Proyectos</h3>
                <canvas id="projects"></canvas>
              </div>
            <?php
              }
            ?>
           </div>
        </div>   
    </div>
  </div>

  <script>
    //datos de php a js
    //grafico total monto
    const tProjects = <?php echo json_encode($tProject); ?>;
    const tProjectsData = <?php echo json_encode($tProjectData); ?>;
    //grafico cantidad proyectos
    const cProjects = <?php echo json_encode($cProject); ?>;
    const cProjectsData = <?php echo json_encode($cProjectData); ?>;
    

    // Grafico total de proyectos generados
    const configCount = {
      type: 'bar', // Tipo de gráfico de barras
      data: {
        labels: cProjects,
        datasets: [{
          label: 'Total Proyectos', 
          data: cProjectsData, 
          backgroundColor: '#0aa699'
        }]
      },
      options: {
        responsive: true, // El gráfico será responsive (se adapta al tamaño de la pantalla)
        scales: {
          x: {
            max: 20, 
            title: {
              display: true,
              text: 'Estado del proyecto',
            }
          },
          y: {
            max: 10, 
            beginAtZero: true // La escala del eje Y comenzará desde cero
          }
        }
      }
    }
    // Grafico PIE total monto proyectos
    const configTotal = {
        type: 'pie',
        data: {
            labels: tProjects,
            datasets: [{
                data: tProjectsData,
                backgroundColor: [
                    '#fdd01c',  '#0aa699', '#f35958'
                ],
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const value = context.raw;
                            const formattedValue = new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD',minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(value);
                            return `${context.label}: ${formattedValue}`;
                        }
                    }
                }
            }
        }
    };

    // Renderizar gráficos
    window.onload = function() {
      //grafico total monto
        const projectTotal = document.getElementById('totalProjects').getContext('2d');
        new Chart(projectTotal, configTotal);
      //grafico cantidad proyectos
        const projects = document.getElementById('projects').getContext('2d');
        new Chart(projects, configCount);
    };
  </script>

<!-- Popper.js (para tooltips y otros componentes) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<!-- Bootstrap Bundle (con Popper.js) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<!-- Calendario -->

<!-- Scripts propios -->
<script src="assets/js/sidebar.js"></script>
<script src="assets/js/calendar.js"></script>
</body>

</html>