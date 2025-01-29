<div class="sidebar" id="sidebar">
  <div class="text-center">
    <br> 
    <a href="create-ticket.php" > 
      <button class="add-ticket btn" data-bs-toggle="tooltip" data-bs-placement="right" title="Crear ticket">
        <i class="bi bi-plus"></i>
      </button>
    </a>
    <br><br>
  </div>
  
  <ul class="nav flex-column sidebar-nav">
    <li class="nav-item" >
      <a href="home.php" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Dashboard">
        <i class="bi bi-house-door"></i>
      </a>
    </li>
    <?php 
      if($_SESSION['cargo'] == '1' or $_SESSION['cargo'] == '2' ){ ?>
        <li class="nav-item active">
          <a href="create-project.php" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Crear Proyectos">
            <i class="bi bi-clipboard2-plus"></i> 
          </a>
        </li>
        <li class="nav-item">
          <a href="view-projects.php" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Gestionar Proyectos">
            <i class="bi bi-clipboard2"></i>
          </a>
        </li>
    <?php  
      }
    ?>
    <li class="nav-item">
      <a href="manage-tickets.php" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Gestionar Tickets">
        <i class="bi bi-ticket-perforated"></i>
      </a>
    </li>
    <li class="nav-item">
      <a href="work-log.php" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Bitácora de Trabajo">
        <i class="bi bi-journal-text"></i> 
      </a>
    </li>
    <li class="nav-item">
      <a href="manage-users.php" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Gestionar Usuarios">
        <i class="bi bi-person-gear"></i> 
      </a>
    </li>
    <li class="nav-item" >
      <a href="change-password.php" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Cambiar Contraseña">
        <i class="bi bi-lock"></i>
      </a>
    </li>
    <li class="nav-item logout-item">
      <a href="logout.php" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Cerrar Sesión">
        <i class="bi bi-box-arrow-right"></i> 
      </a>
      <img src="../assets/img/admin.jpg" alt="" >
    </li>

    </li>
  </ul>
</div>



    