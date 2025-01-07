 <!-- BEGIN SIDEBAR -->
 <div class="page-sidebar" id="main-menu">
   <!-- BEGIN MINI-PROFILE -->
   <div class="page-sidebar-wrapper scrollbar-dynamic" id="main-menu-wrapper">
     <div class="user-info-wrapper">
       <div class="profile-wrapper" >
         <img src="../assets/img/user.png" alt="" data-src="../assets/img/user.png" data-src-retina="../assets/img/user.png" class="side-user-img" />
       </div>
       <div class="user-info">
         <div class="greeting" style="font-size:14px;">Bienvenid@</div>
         <div class="username" style="font-size:12px;"><?php echo $_SESSION["name"]; ?></div>

       </div>
     </div>
     <!-- END MINI-PROFILE -->
     <!-- BEGIN SIDEBAR MENU -->
     <p class="menu-title">Opciones <span class="pull-right"><a href="#" onclick="location.reload()"><i class="fa fa-refresh"></i></a></span></p>

     <ul>
       <li class="start"> <a href="home.php"> <i class="icon-custom-home"></i><span class="title">Dashboard</span> </a>
       </li>
       <li><a href="manage-tickets.php"><span class="fa fa-ticket"></span> Gestionar Ticket</a></li>
       <li><a href="manage-users.php"><span class="fa fa-users"></span> Usuarios</a></li>
       <li><a href="change-password.php"><span class="fa fa-file-text-o"></span> Cambiar Contraseña</a></li>
     </ul>