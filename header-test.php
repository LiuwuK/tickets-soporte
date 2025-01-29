<?php include("assets/php/dashboard.php"); ?>
<div class="header navbar navbar-inverse ">
  <div class="navbar-inner">

    <div class="header-quick-nav">
      <div class="nav-title d-flex ml-2 pull-left" style="padding-top: 0.5em;padding-left: 1em">
        <button class="btn nav-icon sidebar-toggle">☰</button>
        <strong>SafeTeck - Acceso Cliente</strong>
        <ul class="nav quick-section">

        </ul>
      </div>
      

        <!-- Mostrar notificaciones 
      <div class="pull-right notification" id="nt">
        <i class="fa fa-solid fa-bell head-icons" ></i>
        <div class="arrow-down" id="arrow"></div>
      </div>

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
      </div>-->
      
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
  <!-- <script src="assets/js/new_notifications.js" type="text/javascript"></script>  -->