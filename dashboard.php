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
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
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
            <div class="first-row col-md-12 col-xl-5" id="calendar">
              <?php
                if(!$_SESSION['refresh_token']){
              ?>
                <button class="btn btn-updt mt-3" onclick="window.location.href='assets/php/gauth/oauth-init.php';">Vincular calendario de google</button>
              <?php  
                } 
              ?>
            </div> 
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
                <?php
                  if($num_t > 0){
                ?>
                  <div class="t-body">
                    <div class="d-flex flex-row align-items-center pl-3 mb-3 ">
                      <div class="flex-grow-1 text-center">
                        <strong>Asunto</strong>
                      </div>
                      <div class="text-center" style="width: 50%;">
                        <strong>Resumen</strong>
                      </div>
                      <div class="text-center" style="width: 25%;">
                        <strong>Fecha de subida</strong>
                      </div>
                      <div class="text-end" style="width: 10%;">
                        <p></p>
                      </div>
                    </div>
                    <?php                     
                      while ($row = $tickets_pendiente->fetch_assoc()){
                        $posting_date = $row['posting_date'];
                        $date_posting = new DateTime($posting_date);
                        $today = new DateTime();
                        $interval = $today->diff($date_posting);
                        $days_passed = $interval->days;
                        $months_passed = $interval->m + ($interval->y * 12);

                        if ($days_passed > 30) {
                            $time_passed = "Hace ".$months_passed." Meses";
                        } else if($days_passed <= 0){
                          $time_passed = "Hoy";
                        } else {
                            $time_passed = "Hace". $days_passed . " Días";
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
                          <p><?php echo $time_passed ?></p>
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
                <?php 
                  }else{
                    echo "<h4 class='text-center' >No hay tickets pendientes</h4>";
                  }
                ?>
              </div>
            <?php
              }
              // User comercial 
              else if($_SESSION['cargo'] == 2 ){
                
              ?>
                <div class="first-row">
                  <div class="t-head">
                    <h3>Top 5 Proyectos</h3>
                  </div>
                  <?php
                    if($num_top > 0){
                  ?>
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
                        while ($row = $top_proyectos -> fetch_assoc()){
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
                          <div class="text-end" style="width: 15%;">
                            <p><button class="btn btn-updt" onclick="window.location.href='view-projects.php?textSearch=<?php echo $row['id'];?>&statusF=<?php echo $row['estado_id'];?>';">Ver</button></p>
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
                  <?php
                    }else{
                      echo "<div class't-body'><h4>No existen proyectos</h4></div>";
                    }
                  ?>
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
                        <?php
                          if($num_xf){
                        ?>
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
                          while ($row =$xfacturar ->fetch_assoc()){
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
                              <p><button class="btn btn-updt" onclick="window.location.href='bill-projects.php?textSearch=<?php echo $row['id'];?>&statusF=<?php echo $row['estado_id'];?>';">Ver</button></p>
                            </div>
                          </div>
                        <?php
                          $counter++; 
                            if ($counter >= 5) { 
                                break;
                            }
                          }
                        ?>
                        <?php
                          }else{
                            echo "<h4 class='text-center'>No hay proyectos por facturar</h4>";
                          }
                        ?>
                    </div>
                  </div>
                <?php
                }
            ?>
          </div>
            <!-- segunda fila -->
          <div class="row">
            <?php
              if($_SESSION['cargo'] == 4){
                
            ?>
              <div class="gerencia  col-md-12 col-xl-5">
                <div class="t-head">
                  <h3>Top 5 Proyectos</h3>
                </div>
                <?php
                  if($num_top > 0){
                ?>
                  <div class="t-body">
                    <div class="d-flex flex-row align-items-center pr-3 mb-2">
                        <div class="flex-grow-1 text-center" style="width: 20%;">
                          <strong>Nombre</strong>
                        </div>
                        <div class="flex-grow-1 text-center" style="width: 30%; overflow:hidden;">
                          <strong>Clasificación</strong>
                        </div>
                        <div class="flex-grow-1 text-center" style="width: 20%;">
                          <strong>Monto proyecto</strong>
                        </div>
                        <div class="flex-grow-1 text-center" style="width: 20%;">
                          <strong>Estado</strong>
                        </div>
                        <div class="flex-grow-1 text-end" style="width: 15%;">
                        </div>
                    </div>
                    <?php                     
                      while ($row = $top_proyectos -> fetch_assoc()){
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
                        <div class="text-end" style="width: 15%;">
                          <p><button class="btn btn-updt" onclick="window.location.href='view-projects.php?textSearch=<?php echo $row['id'];?>&statusF=<?php echo $row['estado_id'];?>';">Ver</button></p>
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
                <?php
                  }else{
                    echo "<div class't-body'><h4>No existen proyectos</h4></div>";
                  }
                ?>
              </div>

              <div class="col-sm-12 col-md-12 col-lg-8 col-xl-6 graficos">
                <div class="d-flex justify-content-between mb-3">
                  <h3>Proyectos registrados</h3>
                  <div class="btn-group" role="group" aria-label="Filtro por Trimestre">
                    <button type="button" class="btn btn-updt btn-num" id="btnQ1" data-trimestre="1">Q1</button>
                    <button type="button" class="btn btn-updt btn-num" id="btnQ2" data-trimestre="2">Q2</button>
                    <button type="button" class="btn btn-updt btn-num" id="btnQ3" data-trimestre="3">Q3</button>
                    <button type="button" class="btn btn-updt btn-num" id="btnQ4" data-trimestre="4">Q4</button>
                  </div>
                </div>
                <!-- Botones de filtro -->
                <canvas id="lineTotalProjects"></canvas>
              </div>
            <?php
              }
            ?>
          </div>
          <!-- tercera fila  -->  
           <div class="row">
            <?php
              if($_SESSION['cargo'] == 4){
            ?>  
              <div class="col-sm-12 col-md-12 col-lg-8 col-xl-6 graficos">
                <div class="d-flex justify-content-between mb-3">
                  <h3>Total Monto Proyectos</h3>
                  <select id="mesSelector" class="form-select form-select-sm">
                    <option value="0" selected>Ver todo</option>
                  </select>
                  <div class="btn-group q-group" role="group" aria-label="Filtro por Trimestre">
                    <button type="button" class="btn btn-updt btn-q" id="bQ1" data-trimestre="1">Q1</button>
                    <button type="button" class="btn btn-updt btn-q" id="bQ2" data-trimestre="2">Q2</button>
                    <button type="button" class="btn btn-updt btn-q" id="bQ3" data-trimestre="3">Q3</button>
                    <button type="button" class="btn btn-updt btn-q" id="bQ4" data-trimestre="4">Q4</button>
                  </div>
                </div>
                <canvas id="projects"></canvas>
              </div>
              
              <div class="col-sm-12 col-md-12 col-lg-8 col-xl-5 graficos">
                <h3>Total Monto  por vertical</h3>
                <canvas id="totalProjects"></canvas>
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
<!-- Google Api (para el calendario ) -->

<!-- Calendario -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/google-calendar/main.min.js"></script>
<!-- Scripts propios -->
<script src="assets/js/sidebar.js"></script>
<script src="assets/js/calendar.js"></script>
<?php
  if($_SESSION['cargo'] == 4 ){
?>
  <script>
    //grafico total monto
    const tProjects = <?php echo json_encode($tProject); ?>;
    const tProjectsData = <?php echo json_encode($tProjectData); ?>;
    //grafico cantidad proyectos
    const mp = <?php echo $mp_json; ?>;
    const datap = <?php echo $datap_json; ?>;
    // grafico total proyectos registrados  
    const datasets = <?php echo $datasets_json; ?>;
    const trimestres = <?php echo $trimestres_json;?>;
    const maxnum = <?php echo $maxnum;?>;
    const maximo = <?php echo $maximo;?>;

  </script>
  <script src="assets/js/charts.js"></script>';  
<?php  
}
?>

</body>

</html>