<div class="sidebar" id="sidebar">
  <div class="text-center">
    <br> 
    <a href="<?php echo BASE_URL;?>admin/tickets/create-ticket.php" > 
      <button class="add-ticket btn" data-bs-toggle="tooltip" data-bs-placement="right" title="Crear ticket">
        <i class="bi bi-plus"></i>
      </button>
    </a>
    <br><br>
  </div>
  
  <ul class="nav flex-column sidebar-nav">
    <li class="nav-item" >
      <a href="<?php echo BASE_URL;?>admin/home.php" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Dashboard">
        <i class="bi bi-house-door"></i>
      </a>
    </li>
    <!-- modulo tickets -->
    <li class="nav-item">
      <a href="<?php echo BASE_URL;?>admin/tickets/tickets-main.php" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Tickets">
        <i class="bi bi-ticket-perforated"></i>
      </a>
    </li>

    <!-- modulo proyectos -->
    <li class="nav-item active">
          <a href="<?php echo BASE_URL;?>admin/projects/project-main.php" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Proyectos">
            <i class="bi bi-clipboard2"></i> 
          </a>
    </li>
    <!-- Modulo CONFIGURACION -->
    <li class="nav-item">
      <a href="<?php echo BASE_URL;?>admin/configs/config.php" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Configuraciónes">
        <i class="bi bi-gear"></i> 
      </a>
    </li>
    <li class="nav-item logout-item">
      <a href="<?php echo BASE_URL;?>logout.php" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Cerrar Sesión">
        <i class="bi bi-box-arrow-right"></i> 
      </a>
    </li>
    <li class="nav-item">
      <a href="<?php echo BASE_URL;?>work-log.php" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Bitácora de Trabajo">
        <i class="bi bi-journal-text"></i> 
      </a>
      <img src="<?php echo BASE_URL;?>assets/img/admin.jpg" alt="" >
    </li>

    </li>
  </ul>
</div>



    