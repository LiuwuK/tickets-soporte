<?php include("assets/php/dashboard.php"); ?>
<div class="header navbar navbar-inverse ">
  <div class="navbar-inner">
    <div class="header-seperation text-center">
      <ul class="nav pull-left notifcation-center" id="main-menu-toggle-wrapper" style="display:none">
        <li class="dropdown"> <a id="main-menu-toggle" href="#main-menu" class="">
            <div class="iconset top-menu-toggle-white"></div>
          </a> </li>
      </ul>
      <h2>
        <a href="dashboard.php" class="text-white"><strong>Test</strong></a>
      </h2>
    </div>
    <div class="header-quick-nav">
      <div class="ml-2 pull-left" style="padding-top: 0.5em;padding-left: 1em">

        <h4>
          <a href="dashboard.php" class="text-reset"><strong>Sistema de Generación de Tickets - Acceso Cliente</strong></a>
        </h4>
        <ul class="nav quick-section">

        </ul>
      </div>
      
      <div class="pull-right">
        <ul class="nav quick-section ">
          <li class="quicklinks"> <a data-toggle="dropdown" class="dropdown-toggle  pull-right " href="#" id="user-options">
              <i class="fa fa-solid fa-gear head-icons" style="font-size:large"></i>
            </a>
            <ul class="dropdown-menu  pull-right" role="menu" aria-labelledby="user-options">
              <li class="divider"></li>
              <li><a href="logout.php"><i class="fa fa-power-off"></i>&nbsp;&nbsp;Cerrar Sesión</a></li>
            </ul>
          </li>

        </ul>
      </div>

      <!-- Mostrar notificaciones -->
      <div class="pull-right notification" id="nt">
        <i class="fa fa-solid fa-bell head-icons" ></i>
        <div class="arrow-down" id="arrow"></div>
      </div>
      <!-- Container de las notificaciones -->
      <div class="noti-box" display="none" id="nt-div">
        <div class="noti-h">
          <p class="noti-title">Notificaciones</p>
          <i class="pull-right fa fa-solid fa-times-circle" id="close-btn"></i>    
        </div>
        <div class="noti-b">
            <?php 
              if (mysqli_num_rows($noti) > 0) {
                while ($row = mysqli_fetch_assoc($noti)) { ?>
                  <a class="query-link" data-ticketid="<?php echo $row['ticket_id']; ?>" href="http://localhost/tickets-soporte/view-tickets.php<?php echo $row['url']?>">
                    <div class="card" data-creada-en="<?php echo $row['creada_en']; ?>">
                      <div class="img">
                        <i class="fa fa-solid fa-user"></i>
                      </div>
                      
                      <div class="bdy">
                        <p class="msg"><?php echo $row['mensaje']; ?></p>
                        <h6 class="time-elapsed"></h6>
                      </div>
                    <?php 
                      if ($row['leida'] == false ) { ?>
                        <div class="new">
                          <p>!</p>
                        </div>
                    <?php }
                    ?> 
                    </div>
                  </a>
            <?php
                }
              } else{
                //echo "No hay notificaciones ";
              }
            ?>
        </div>
      </div>
      
      <!-- END CHAT TOGGLER -->
    </div>
    <!-- END TOP NAVIGATION MENU -->
  </div>
  <!-- END TOP NAVIGATION BAR -->
</div>
<!-- END HEADER -->

 <!-- definir tipo de usuario  -->
 <script>
    const userType = 'client';
    const userId   = <?php  echo $_SESSION['user_id']; ?>;
 </script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
  <script src="assets/js/new_notifications.js" type="text/javascript"></script>