<?php
session_start();
include("../../checklogin.php");
include BASE_PATH.'dbconnection.php';
include("../assets/php/recuperaciones.php");
check_login();
?>
<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
  <meta charset="utf-8" />
  <title>Gestión</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <meta content="" name="description" />
  <meta content="" name="author" />

<!-- CSS de Choices.js -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<!-- Calendario CSS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
<!-- CSS personalizados -->
<link href="../../assets/css/sidebar.css" rel="stylesheet" type="text/css"/>
<link href="../../assets/css/main.css" rel="stylesheet" type="text/css"/>
<link href="../assets/css/recuperaciones.css" rel="stylesheet" type="text/css"/>
<!-- Toast notificaciones -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
</head>

<body class="test" >
    <!-- Sidebar -->
<div class="sidebar-overlay"></div> 
  <div class="page-container ">

    <div class="sidebar">
    <?php include("../../header-test.php"); ?>
    </div>
    <div class="page-content">
    <?php include("../../leftbar-test.php"); ?>
        <div class="content row d-flex justify-content-around">
            <div class="page-title">
                <h2>Recuperaciones</h2>
            </div><br><br>

            <div class="rec-form col-md-4">
                <form class="form-horizontal" name="form" method="POST" action="" >
                    <div id="loading" style="display:none ;">
                        <div class="loading-spinner"></div>
                        <p>Procesando...</p>
                    </div>
                    <h3>Ingresar Recuperación</h3>
                    <br>
                    <div class="form-group">
                        <label class="form-label">Instalación <span>*</span></label>
                        <div>
                            <select name="instalacion" id="instalacion" class="form-select form-select-sm search-form" required>
                                <option value="">Seleccionar</option>
                                <?php
                                foreach ($inst AS $row) {
                                    echo "<option value='".$row['id']."'>".$row['nombre']."</option>";
                                };
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="monto" class="form-label">Monto <span>*</span></label>
                        <div class="input-group">
                            <span class="input-group-text" id="montoP">$</span>
                            <input type="number" name="monto" id="monto" class="form-control form-control-sm" required>
                        </div>
                    </div>
                    <div class="form-group">
                    <label for="fecha" class="form-label">Fecha de recuperación <span>*</span></label>
                        <input type="date" name="fecha" id="fecha" class="form-control form-control-md" required >
                    </div>
                    <div class="btn-div d-flex justify-content-end mt-4 mb-2">
                        <button class="btn btn-updt" type="submit" name="send">Enviar</button>
                    </div>
                </form>
            </div>
            <div class="calendario col-md-7">
                <div class="filtros mb-4">
                    <label>Sucursal:</label>
                    <select id="filtro-sucursal" class="form-select ">
                        <option value="">Todas las sucursales</option>
                        <?php 
                            foreach ($inst AS $row) {
                                echo "<option value='".$row['id']."'>".$row['nombre']."</option>";
                            };
                        ?>
                    </select>
                </div>
                <div class="calendar-main" id="calendario">
                </div>
            </div>
        </div>   
    </div>
  </div>


  <script>
    document.addEventListener("DOMContentLoaded", function() {
        var calendarEl = document.getElementById('calendario');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'es',
            events: function(fetchInfo, successCallback, failureCallback) {
                var sucursalId = document.getElementById('filtro-sucursal').value;
                fetch(`../assets/php/get_recuperaciones.php?sucursal_id=${sucursalId}`)
                    .then(response => response.json())
                    .then(data => successCallback(data))
                    .catch(error => failureCallback(error));
            },
            eventDidMount: function(info) {
                const tooltip = document.createElement('div');
                tooltip.className = 'fc-custom-tooltip';
                tooltip.textContent = info.event.title;
                info.el.appendChild(tooltip);

                info.el.addEventListener('mouseenter', () => tooltip.style.opacity = '1');
                info.el.addEventListener('mouseleave', () => tooltip.style.opacity = '0');
            },
            headerToolbar: {
                left:'dayGridMonth,dayGridDay',
                center: 'title',
                right: 'prev,next',
            },
            buttonText: {
                dayGridMonth: 'Mes',
                dayGridDay: 'Día'
            }
        });
        calendar.render();
        // Filtra eventos al cambiar la sucursal
        document.getElementById('filtro-sucursal').addEventListener('change', function() {
            calendar.refetchEvents();
        });

        document.querySelectorAll("select.search-form").forEach(selectElement => {
            const choices = new Choices(selectElement, {
                searchEnabled: true,
                itemSelectText: "",
                placeholder: true
            });
        });
    });

  </script>
  
<!-- JS de Choices.js -->
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<!-- Popper.js (para tooltips y otros componentes) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<!-- Calendario -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/google-calendar/main.min.js"></script>
<!-- Bootstrap Bundle (con Popper.js) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<!-- Complementos/Plugins-->
<!-- Scripts propios -->
<script src="../../assets/js/sidebar.js"></script>
</body>

</html>