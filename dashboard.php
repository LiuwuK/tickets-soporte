<?php
session_start();
include("checklogin.php");
check_login();
include("assets/php/dashboard.php");

?>
<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
  <meta charset="utf-8" />
  <title>Dashboard </title>
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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"/>

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
        <h3>Dashboard</h3>
        <div class="row 2col">
          
          <div class="d-flex col-md-3 col-sm-3 spacing-bottom-sm spacing-bottom">
            <div class="tiles blue added-margin">
              <div class="tiles-body">
                <div class="controller"> <a href="javascript:;" class="reload"></a> <a href="javascript:;" class="remove"></a> </div>
                <div class="heading"><a href="view-tickets.php" style="color:#FFF"> Total de Tickets</a></div>
                <h3 class="text-right text-white"><span class="animate-number" data-value="<?php echo $general; ?>" data-animation-duration="1200"><?= $general ?></span></h3>
              </div>
            </div>
          </div>

          <div class="d-flex col-md-3 col-sm-3 spacing-bottom-sm spacing-bottom">
            <div class="tiles bg-green added-margin">
              <div class="tiles-body">
                <div class="controller"> <a href="javascript:;" class="reload"></a> <a href="javascript:;" class="remove"></a> </div>
                <div class="heading"><a href="view-tickets.php?statusF=11" style="color:#FFF">Tickets Abiertos</a></div>
                <h3 class="text-right text-white"><span class="animate-number" data-value="<?php echo $abi; ?>" data-animation-duration="1200"><?= $abi?></span></h3>
              </div>
            </div>
          </div>
          
          <div class="d-flex col-md-3 col-sm-3 spacing-bottom-sm spacing-bottom">
            <div class="tiles bg-yellow added-margin">
              <div class="tiles-body">
                <div class="controller"> <a href="javascript:;" class="reload"></a> <a href="javascript:;" class="remove"></a> </div>
                <div class="heading"><a href="view-tickets.php?statusF=10" style="color:#FFF">Tickets en revisión</a></div>
                <h3 class="text-right text-white"><span class="animate-number" data-value="<?php echo $revi; ?>" data-animation-duration="1200"><?= $revi?></span></h3>
              </div>
            </div>
          </div>

          <div class="d-flex col-md-3 col-sm-3 spacing-bottom-sm spacing-bottom">
            <div class="tiles bg-red added-margin">
              <div class="tiles-body">
                <div class="controller"> <a href="javascript:;" class="reload"></a> <a href="javascript:;" class="remove"></a> </div>
                <div class="heading"><a href="view-tickets.php?statusF=12" style="color:#FFF">Tickets Resueltos</a></div>
                <h3 class="text-right text-white"><span class="animate-number" data-value="<?php echo $cerr; ?>" data-animation-duration="1200"><?= $cerr?></span></h3>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="noti-box">
      <div class="noti-h">
        <p class="noti-title">Notificaciones</p>
        <i class="pull-right fa fa-solid fa-times-circle"></i>    
      </div>
      <div class="noti-b">
          <div class="card">
            <p>El ticket # fue actualizado</p>
          </div>
          <div class="card">
            <p>El ticket # fue actualizado</p>
          </div>
          <div class="card">
            <p>El ticket # fue actualizado</p>
          </div>
          <div class="card">
            <p>El ticket # fue actualizado</p>
          </div>
          <div class="card">
            <p>El ticket # fue actualizado</p>
          </div>
          
      </div>
    </div>
  <br><br>
  </div>



  
  <style>
    .noti-b{
      overflow-y: scroll;
      flex-grow: 1;       
    }
    .card{
      height:65px;
      border-bottom: 1px solid #fc7d1c;
      
    }
    .noti-h{
        border-radius: 12px 12px 0px 0px;
        background-color: #fc7d1c;
        height: 55px;
        text-align: start;
        align-items: center;
        display: flex;
        justify-content: space-between;
        padding: 0;              
      }
    .noti-title {
      color: white;
      margin: 20px;
      font-weight: bold;  
      font-size: 15px;
    }
    .noti-h i{
      color: white;
      font-size: medium;
      margin: 20px;
      cursor: pointer;
    }

    .noti-box{
      margin: 0 auto;
      background-color: white;
      box-shadow: 0 0 16px rgba(0, 0, 0, 0.3);
      border-radius: 12px;
      width: 30%;
      height: 350px; 
      display: flex;
      flex-direction: column;   
    }
  </style>


  
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


</body>

</html>