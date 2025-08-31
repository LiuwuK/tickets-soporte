<!-- 
  CARGOS
  1 Ingeniero

  2 Comercial

  3 Contabilidad y Finanzas

  4 Gerencia

  5 Técnico

  6 Compras  
-->

<div class="sidebar" id="sidebar">
    
  <ul class="nav flex-column sidebar-nav">
    <!-- Dashboard -->
     <br>
    <li class="nav-item">
      <a href="<?php echo BASE_URL;?>dashboard.php" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Inicio">
        <i class="bi bi-house-door"></i>
      </a>
    </li>
    <?php
    if($_SESSION['cargo'] == '3' or $_SESSION['cargo'] == '2' or $_SESSION['cargo'] == '4'  ){ 
    ?>
    <!-- Modulo PROYECTOS -->
    <li class="nav-item">
      <a href="<?php echo BASE_URL;?>projects/projects-main.php" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Proyectos">
        <i class="bi bi-clipboard2"></i>
      </a>
    </li>
    <?php  
      }
    ?>
    <?php
    if (
      in_array($_SESSION['cargo'], ['6', '2', '4'], true) 
      || 
      (
        isset($_SESSION['deptos']) 
        && 
        is_array($_SESSION['deptos']) 
        && 
        array_intersect([19], $_SESSION['deptos'])
    )
  ){
    ?>
    <!-- Modulo Gestion?? -->
    <li class="nav-item">
      <a href="<?php echo BASE_URL;?>gestion/main.php" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Gestión">
        <i class="bi bi-folder"></i>
      </a>
    </li>
    <?php
    }
    ?>
    <!-- Modulo CONFIGURACION -->
    <li class="nav-item">
      <a href="<?php echo BASE_URL;?>configs/config.php" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Configuraciónes">
        <i class="bi bi-gear"></i> 
      </a>
    </li>
    <!-- Logout -->
    <li class="nav-item logout-item">
      <a href="<?php echo BASE_URL;?>logout.php" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Cerrar Sesión">
        <i class="bi bi-box-arrow-right"></i> 
      </a>
    </li>
    <!--  WorkLog -->
    <li class="nav-item">
      <a href="<?php echo BASE_URL;?>work-log.php" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Bitácora de Trabajo">
        <i class="bi bi-journal-text"></i> 
      </a>
      <img class="mt-2 img-sidebar" style="width: 35px;" src="<?php echo BASE_URL; ?>assets/img/admin.jpg" alt="" >
    </li>
    
  </ul>
</div>


    