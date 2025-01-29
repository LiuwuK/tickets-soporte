<?php
session_start();
include("checklogin.php");
include("dbconnection.php");

include("../assets/php/work-log.php");

check_login();
?>
<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
  <meta charset="utf-8" />
  <title>Bitácora de trabajo</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <meta content="" name="description" />
  <meta content="" name="author" />

<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<!-- CSS personalizados -->
<link href="../assets/css/sidebar.css" rel="stylesheet" type="text/css"/>
<link href="../assets/css/work-log.css" rel="stylesheet" type="text/css"/>


<!-- Toast notificaciones -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
</head>

<body class="test" >
    <!-- Sidebar -->
  <div class="page-container ">

    <div class="sidebar">
    <?php include("header.php"); ?>
    </div>
    <div class="page-content">
    <?php include("leftbar.php"); ?>
        <div class="content">
            <div class="page-title">
                <h2>Bitácora de Trabajo</h2>
            </div>
           <div class="row d-flex justify-content-between">
                <div class="add-log new-logs  col-lg-12 col-xl-5">
                    <h5>Ingresar Actividad</h5>
                    <form name="form" id="workLog" method="post">
                        <div class="group mt-3">
                            <label for="title" class="form-label">Titulo</label>
                            <input type="text" class="form-control form-control-sm" id="title" name="title" required="required">
                        </div>
                        <div class="group mt-3">
                            <label class="form-label">Tipo de Actividad</label>
                            <div>
                                <select name="tAct" class="form-select form-select-sm" required>
                                    <option value="">Seleccionar</option>
                                    <?php
                                        while ($row = mysqli_fetch_assoc($tipoAct)) {
                                            echo "<option value=".$row['id'].">".$row['nombre_actividad'] ."</option>";
                                        };
                                    ?>  
                                </select>
                            </div>
                        </div>
                        <div class="group mt-3">
                            <label for="hora" class="form-label">Hora</label>
                            <input type="time" class="form-control form-control-sm" id="hora" name="hora" required="required">
                            <input type="hidden" id="fechaFinal" name="fecha">
                        </div>
                        <div class="group mt-3 mb-3">
                            <label for="desc" class="form-label">Descripción</label>
                            <textarea class="form-control form-control-sm" id="desc" name="desc" rows="6"></textarea>   
                        </div>

                        <div class="group">
                            <button class="btn btn-updt" name="addLog">Añadir</button>
                        </div>
                    </form>
                </div>
                <div class="add-log col-lg-12 col-xl-6 h-100">
                    <div class="title mb-3 d-flex justify-content-between">
                        <h5 id="day"></h5>
                        <button class='btn btn-default' onclick="window.location.href='../assets/php/pdf-log.php?userId=<?php echo $_SESSION['id'];?>';">Generar registro</button>
                    </div>
                    <div class="logs see-logs">
                       <?php
                        if($num > 0){
                            foreach($act as $actividad){
                                $hora = date('h:i A', strtotime($actividad['fecha']));// H:i = formato 24hrs
                        ?>
                            <div class="card card-log ">
                                <strong><?php echo $actividad['titulo'];?></strong>
                                <p class="time">Tipo Actividad: <?php echo $actividad['tAct'];?></p>
                                <p class="time">Hora: <?php echo $hora;?></p>
                                <label class="card-desc">Descripción:</label>
                                <p><?php echo $actividad['descripcion'];?></p>
                            </div>           
                        <?php
                            }
                        }else{
                            echo "<h3 class='text-center'>Hoy no has realizado actividades</h3>";
                        }
                       ?>             
                    </div>
                </div>
           </div>      
        </div>   
    </div>
  </div>
  <script>
    //obtener dia actual 
    const today = new Date();
    const currentDay = new Intl.DateTimeFormat('es-ES', { weekday: 'long' }).format(today);
    document.getElementById('day').textContent = `${currentDay.charAt(0).toUpperCase()}${currentDay.slice(1)}`;
    //convertir en fecha completa y pasar a campo oculto
    const timeInput = document.getElementById('hora');
    timeInput.addEventListener('change', () => {
        const selectedTime = timeInput.value;
        const currentDate = new Date().toISOString().split('T')[0];
        const finalDateTime = `${currentDate}T${selectedTime}:00`; 
        console.log(finalDateTime);

        document.getElementById('fechaFinal').value = finalDateTime;
    });
</script>

<!-- Popper.js (para tooltips y otros componentes) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<!-- Bootstrap Bundle (con Popper.js) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<!-- Complementos/Plugins-->    
<!-- Scripts propios -->
<script src="../assets/js/sidebar.js"></script>
</body>

</html>