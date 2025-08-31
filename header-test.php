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
    const BASE_PATH = 'http://localhost/tickets-soporte/';
 </script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>