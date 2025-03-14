<?php
session_start();
include("../../checklogin.php");
include BASE_PATH . 'dbconnection.php';
include("../assets/php/create-extra.php");

check_login();
?>
<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
  <meta charset="utf-8" />
  <title>Turnos extra</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <meta content="" name="description" />
  <meta content="" name="author" />

<!-- CSS de Choices.js -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<!-- CSS personalizados -->
<link href="../../assets/css/sidebar.css" rel="stylesheet" type="text/css" />
<link href="../../projects/assets/css/create-project.css" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" href="../assets/css/nuevo-turno.css">

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
        <div class="content">
            <div class="page-title d-flex justify-content-between">
                <h2>Turnos Extra</h2>
                <button class=" btn-back" onclick="window.location.href='../turnos-extras.php';"> 
                    <i class="bi bi-arrow-left" ></i>
                </button>
            </div>
            <div class="main-div col-xl-8 col-sm-12">
                <div class="extra-title">
                    <h3>Crear turno</h3>
                    <div class="excel">
                        <button class="btn btn-updt" data-bs-toggle="modal" data-bs-target="#newSuper">Importar Turnos extra</button>
                    </div>                
                </div>
                <!-- Formulario nuevos turnos -->

                <form class="form-horizontal form-div" name="form" method="POST" action="" >
                    <div id="loading" style="display:none ;">
                        <div class="loading-spinner"></div>
                        <p>Procesando...</p>
                    </div>  
                    <br>
                    <div class="form-row">
                        <div class="form-group col-md-6">
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
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="fecha_turno" class="form-label">Fecha del Turno <span>*</span></label>
                            <input type="date" name="fecha_turno" id="fecha_turno" class="form-control form-control-sm" required min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="horas_cubiertas" class="form-label">Hora Cubiertas <span>*</span></label>
                            <input type="number" name="horas_cubiertas" id="horas_cubiertas" class="form-control form-control-sm" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="monto" class="form-label">Monto <span>*</span></label>
                            <div class="input-group">
                                <span class="input-group-text" id="montoP">$</span>
                                <input type="number" name="monto" id="monto" class="form-control form-control-sm" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="nombre_colaborador" class="form-label">Nombre del Colaborador <span>*</span></label>
                            <input type="text" name="nombre_colaborador" id="nombre_colaborador" class="form-control form-control-sm" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="rut" class="form-label">RUT <span>*</span></label>
                            <input type="text" name="rut" id="rut" class="form-control form-control-sm" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="motivo_turno" class="form-label">Motivo del Turno <span>*</span></label>
                            <select name="motivo_turno" id="motivo_turno" class="form-select form-select-sm" required>
                                <option value="">Seleccione un motivo</option>
                                <?php
                                foreach ($motivo AS $row) {
                                    echo "<option value=".$row['id'].">".$row['motivo'] ."</option>";
                                };
                                ?>
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="autorizado_por" class="form-label">Autorizado por <span>*</span></label>
                            <input type="text" class="form-control form-control-sm"  name="autorizado_por" value="<?php echo $_SESSION['name'];?>" readonly>
                        </div>
                    </div>

                    <!-- Datos bancarios -->
                    <div class="form-row mb-3">
                    <h4>Datos bancarios</h4>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">

                            <label for="banco" class="form-label">Banco <span>*</span></label>
                            <input type="text" name="banco" id="banco" class="form-control form-control-sm" required>   
                        </div>
                        <div class="form-group col-md-6">
                            <div class="d-flex msg">
                                <label for="rut" class="form-label">RUT Cta <span>*</span></label>
                                <p> (Sin puntos)</p>
                            </div>
                            <input type="text" class="form-control form-control-sm" id="rut" name="rutCta" maxlength="12" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="numCta" class="form-label">Numero de Cuenta <span>*</span></label>
                            <input type="number" name="numCta" id="numCta" class="form-control form-control-sm" required>   
                        </div>
                    </div>
                    <br>
                    <div class="footer">
                        <button type="submit" name="newExtra" class="btn btn-updt">Enviar</button>
                    </div>
                </form>
            </div>
                   
        </div>   
    </div>
  </div>
<!-- Modal new -->
<div class="modal fade" id="newSuper" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="newSuperLabel" aria-hidden="true">>
  <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="newSuperLabel">Importar turnos</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form class="mb-2" method="post" enctype="multipart/form-data">
            <div class="modal-body mt-3 d-flex justify-content-center">
                <input class="mb-3" type="file" name="file" required>
            </div>
            <div class="modal-footer form-row-modal d-flex justify-content-end">
              <button class="btn btn-updt" name="carga" type="submit">Cargar Datos</button>
            </div>
        </form> 
      </div>
  </div>
</div>

<!-- JS de Choices.js -->
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<!-- Popper.js (para tooltips y otros componentes) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<!-- Bootstrap Bundle (con Popper.js) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<!-- Complementos/Plugins-->
<!-- Scripts propios -->
<script src="../assets/js/turno-extra.js"></script>
<script src="../../assets/js/sidebar.js"></script>
</body>

</html>