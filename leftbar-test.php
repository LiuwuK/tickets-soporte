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
          <li class="nav-item">
            <a href="dashboard.php" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Inicio">
              <i class="bi bi-house-door"></i>
            </a>
          </li>
          <?php 
            if($_SESSION['cargo'] == '2'){ ?>
              <li class="nav-item active">
                <a href="create-project.php" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Crear Proyectos">
                  <i class="bi bi-clipboard2-plus"></i> 
                </a>
              </li>
              <li class="nav-item">
                <a href="view-projects.php" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Ver Proyectos">
                  <i class="bi bi-clipboard2"></i>
                </a>
              </li>
            <?php  
          }
          ?>
          <li class="nav-item">
            <a href="view-tickets.php" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Ver Tickets">
              <i class="bi bi-ticket-perforated"></i>
            </a>
          </li>
          <li class="nav-item">
            <a href="profile.php" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Mi Perfil">
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

            <!-- <button class="btn" id="toggleButton">
                <i class="bi bi-arrow-right"></i>
            </button>
              <script>
                document.getElementById('toggleButton').addEventListener('click', function() {
                  document.getElementById('sidebar').classList.toggle('expanded');
              });
              </script>
            -->
          </li>
        </ul>
    </div>



    