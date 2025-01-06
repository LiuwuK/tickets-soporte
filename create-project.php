<?php
session_start();
include("checklogin.php");
include("dbconnection.php");
include("assets/php/create-project.php");
check_login();


?>
<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
  <meta charset="utf-8" />
  <title>Projects </title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <meta content="" name="description" />
  <meta content="" name="author" />

  <link href="assets/plugins/pace/pace-theme-flash.css" rel="stylesheet" type="text/css" media="screen" />
  <link href="assets/plugins/boostrapv3/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
  <link href="assets/plugins/boostrapv3/css/bootstrap-theme.min.css" rel="stylesheet" type="text/css" />
  <link href="assets/plugins/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css" />
  <link href="assets/css/animate.min.css" rel="stylesheet" type="text/css" />
  <link href="assets/plugins/jquery-scrollbar/jquery.scrollbar.css" rel="stylesheet" type="text/css" />
  <link href="assets/css/style.css" rel="stylesheet" type="text/css" />
  <link href="assets/css/responsive.css" rel="stylesheet" type="text/css" />
  <link href="assets/css/custom-icon-set.css" rel="stylesheet" type="text/css" />
  <!-- notificaciones -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"/>
  <!-- PIKADAY CALENDAR -->
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/pikaday/css/pikaday.css">
  <script src="https://cdn.jsdelivr.net/npm/pikaday/pikaday.js"></script>

</head>

<body class="" >
  <?php include("header.php"); ?>
  <div class="page-container row-fluid">
    <?php include("leftbar.php"); ?>
    <div class="clearfix"></div>
  </div>
  </div>
  <div class="page-content">
    <div class="clearfix"></div>
    <div class="content">
      <div class="page-title">
        <h3>Crear Proyecto</h3>
      </div>
      <form action="" id="newProject">
        <div class="project-main" >  <br><br>      
            <div class="form-row">
              <div class="form-group">
                <label for="name" class="control-label">Nombre</label>
                <input type="text" class="form-control rounded-0" id="name" name="name" required="required">
              </div>
                
              <div class="form-group">
                <label for="client" class="control-label">Cliente</label>
                <input type="text" class="form-control rounded-0" id="client" name="client" required="required">          
              </div>
            </div>
            <div class="form-row">
              <div class ="form-group">
                <label>Ciudad</label>
                <div >
                  <select name="city" class="form-control select" required>
                      <option value="">Seleccionar</option>
                  </select>
                </div>
              </div> 
              
              <div class ="form-group">
                <label>Estatus</label>
                <div >
                  <select name="status" class="form-control select" required>
                      <?php
                        while ($row = mysqli_fetch_assoc($status)) {
                            echo "<option value=". $row['id'].">".$row['nombre'] ."</option>";
                        };
                      ?>
                  </select>
                </div>
              </div>
            </div>
            <div class="form-row">
              <div class ="form-group">
                <label>Comercial responsable</label>
                <div >
                  <select name="comercial" class="form-control select" required>
                      <option value="">Seleccionar</option>
                  </select>
                </div>
              </div> 
              
              <div class ="form-group">
                <label>Ingeniero responsable</label>
                <div >
                  <select name="ingeniero" class="form-control select" required>
                      <option value="">Seleccionar</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label for="bom" class="control-label">BOM</label>
                <input type="text" class="form-control rounded-0" id="bom" name="bom" required="required">
              </div>
                
              <div class="form-group">
                <label for="dist" class="control-label">Distribuidor</label>
                <input type="text" class="form-control rounded-0" id="dist" name="dist" required="required">          
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label for="software" class="control-label">Costo Software</label>
                <input type="text" class="form-control rounded-0" id="software" name="software" placeholder="USD 0">
              </div>
              <div class="form-group">
                <label for="hardware" class="control-label">Costo Hardware</label>
                <input type="text" class="form-control rounded-0" id="hardware" name="hardware" placeholder="USD 0">          
              </div>
            </div>     
            <div class="form-row">
                <div class="form-group">
                  <div class="label-container">
                    <label class="control-label">Actividades</label>
                    <button class="btn-add-task" id="datepicker-icon">
                      <i class="fa fa-calendar-o"></i> 
                    </button>
                    <input type="text" id="datepicker" style="display:none;">
                  </div>
                  <ul>
                      <li>Actividad ejemplo  -  06-01-2025</li>
                  </ul>

                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                  <label for="desc" class="control-label">Resumen</labe>
                  <textarea class="form-control rounded-0" id="desc" name="desc" ></textarea>     
                </div>
            </div>       
            <div class="footer">
                <button class="btn btn-default">Resetear</button>
                <input type="submit" value="newProject" name="newProject" class="btn btn-primary pull-right">
            </div>
        </div>
      </form>
    </div>   

    <style>
      .calendar{
        height: 250px;
        width: 250px;
      }
  
      .project-main{
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        background-color: #fff;
        border-radius: 5px;
      }
      .form-row {
        display: flex;
        justify-content: space-between;
        width: 60%;
        gap: 20px; 
      }

      .form-group {
        display: flex;
        flex-direction: column;
        flex: 1; 
      }

      .footer{
        display: flex;
        width: 80%;
        justify-content: space-between;
        margin-bottom: 30px;
      }

      .label-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 20%; 
        margin-bottom: 20px;
      }

      .btn-add-task {
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        margin-bottom: 5px ;
        cursor: pointer;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      }

      .btn-add-task:hover {
        background-color: #e0e0e0;
      }

      .btn-add-task:active{
        transform: scale(0.9);
      }
    </style>
        
  <br><br>
  </div>
  
  </div>
  </div>

  <script src="assets/plugins/jquery-1.8.3.min.js" type="text/javascript"></script>
  <script src="assets/plugins/jquery-ui/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
  <script src="assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
  <script src="assets/plugins/breakpoints.js" type="text/javascript"></script>
  <script src="assets/plugins/jquery-unveil/jquery.unveil.min.js" type="text/javascript"></script>
  <script src="assets/plugins/jquery-block-ui/jqueryblockui.js" type="text/javascript"></script>
  <script src="assets/plugins/jquery-scrollbar/jquery.scrollbar.min.js" type="text/javascript"></script>
  <script src="assets/plugins/pace/pace.min.js" type="text/javascript"></script>
  <script src="assets/plugins/jquery-numberAnimate/jquery.animateNumbers.js" type="text/javascript"></script>
  <script src="assets/js/core.js" type="text/javascript"></script>
  <script src="assets/js/chat.js" type="text/javascript"></script>
  <script src="assets/js/demo.js" type="text/javascript"></script>
  <script src="assets/js/calendar.js" type="text/javascript"></script>
      
</body>

</html>