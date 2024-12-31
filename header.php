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
      <div class="pull-right notification">
        <i class="fa fa-solid fa-bell head-icons"></i>
      </div>

  

      <style>
        .head-icons{
          font-size: medium;
        }
        .head-icons:hover{
          cursor: pointer;
        }
        .notification{
          display: flex; 
          align-items: center; 
          justify-content: center; 
          height: 97%;
        }
      </style>
      
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