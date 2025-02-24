<?php
session_start();
include("../../checklogin.php");
include("../../dbconnection.php");
include("assets/php/jornadas.php");
check_login();
?>
<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
  <meta charset="utf-8" />
  <title>Jornadas</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <meta content="" name="description" />
  <meta content="" name="author" />

<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<!-- CSS personalizados -->
<link href="../../assets/css/sidebar.css" rel="stylesheet" type="text/css" />
<link href="../assets/css/general-crud.css" rel="stylesheet" type="text/css"/>

<!-- Toast notificaciones -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
</head>

<body class="test" >
    <!-- Sidebar -->
  <div class="page-container ">

    <div class="sidebar">
    <?php include("../../header-test.php"); ?>
    </div>
    <div class="page-content">
    <?php include("../../leftbar-test.php"); ?>
        <div class="content">
            <div class="page-title d-flex justify-content-between">
                <h2>Jornadas</h2>
                <button class=" btn-back" onclick="window.location.href='gestion-main.php';"> 
                    <i class="bi bi-arrow-left" ></i>
                </button>
            </div>
            <div class="main-crud col-md-11 col-sm-12">
              <div class="head-crud d-flex justify-content-between mb-3">
                <h4>Administrar Jornadas</h4>
                <div class="btns">
                  <button class="btn btn-updt"  data-bs-toggle="modal" data-bs-target="#newSuper">Nueva Jornada</button>
                  <button type="submit" form="editJornada" class="btn btn-default" id="btnUpdt" name="btnUpdt" disabled>Actualizar</button>
                </div>
              </div>
              <hr>
              <div class="body-crud">
                <table class="table table-striped">
                    <thead>
                        <th>Tipo de jornada</th>
                        <th>Hora de Entrada</th>
                        <th>Hora de Salida</th>
                        <th>Opciones</th>
                    </thead>
                    <tbody>
                      <form name="form" id="editJornada" method="post">
                        <div id="loading" style="display:none ;">
                            <div class="loading-spinner"></div>
                            <p>Procesando...</p>
                        </div>  
                        <?php while ($row = mysqli_fetch_assoc($result)) { ?>    
                            <tr>
                                <td>
                                  <input type="text" class="form-control form-control-sm" id="name" name="name[]" value="<?php echo $row['tipo_jornada'];?>" onchange="enableUpdateButton()" required>
                                  <input type="hidden" name="id[]" value="<?php echo $row['id']; ?>">
                                </td>
                                <td>
                                  <input type="time" class="form-control form-control-sm" id="entrada" name="entrada[]" value="<?php echo $row['hora_entrada'];?>" onchange="enableUpdateButton()" required>
                                </td>
                                <td>
                                  <input type="time" class="form-control form-control-sm" id="salida" name="salida[]" value="<?php echo $row['hora_salida'];?>" onchange="enableUpdateButton()" required>
                                </td>
                                <td>
                                  <button type="button" class="btn btn-del del-btn" data-bs-toggle="modal" data-bs-target="#delSuper" data-sup-id="<?php echo $row['id'];?>">Eliminar</button>
                                </td>
                            </tr>
                        <?php } ?>
                      </form>
                    </tbody>    
                </table>
              </div>
            </div>
        </div>   
    <br><br>
    </div>
  </div>

<!-- Modal jornada nueva -->
<div class="modal fade" id="newSuper" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="newSuperLabel" aria-hidden="true">>
  <div class="modal-dialog">
      <div class="modal-content">
      <div class="modal-header">
          <h5 class="modal-title" id="newSuperLabel">Nueva Jornada</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="newForm"  method="post" enctype="multipart/form-data">
        <div class="modal-body">
          <div class="form-row-modal mb-3 mx-auto">
            <div class="form-group">
                <label for="tipoJornada" class="form-label">Tipo Jornada<span>*</span></label>
                <input type="text" class="form-control form-control-sm" id="tipoJornada" name="tipoJornada" required>
            </div>
          </div>

          <div class="form-row-modal mb-3">
            <div class="form-group">
                <label for="entrada" class="form-label">Hora de Entrada<span>*</span></label>
                <input type="time" class="form-control form-control-sm" id="entrada" name="entrada" required>
            </div>
            <div class="form-group">
                <label for="salida" class="form-label">Hora de Salida <span>*</span></label>
                <input type="time" class="form-control form-control-sm" id="salida" name="salida" required>
            </div>
          </div>
          <div class="form-row-modal justify-content-end">
            <button type="submit"  name="newSup" class="btn pull-right btn-updt">Agregar</button>
          </div>
        </div>
        </form>
        <form class="mb-2 mv" method="post" enctype="multipart/form-data">
          <div class="modal-footer mv-form">
            <h5 class="text-start">Carga masiva</h5>
            <input class="mb-3" type="file" name="file" required >
            <div class="form-row-modal d-flex justify-content-end">
              <a href="assets/excel-ejemplos/jornada.xlsx" download class="btn btn-default">
                Excel Ejemplo
              </a>
              <button class="btn btn-updt" name="carga" type="submit">Cargar Datos</button>
            </div>
          </div>
        </form> 
      </div>
  </div>
</div>
<!-- modal eliminar jornada -->
<div class="modal fade" id="delSuper" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="delSuperLabel" aria-hidden="true">
  <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="delSuperLabel">Eliminar Jornada</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>¿Estás seguro de que quieres eliminar esta Jornada?</p>
          <form id="delForm" method="POST">
            <input type="hidden" name="idSup" id="idSup">
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" name="delSup" class="btn pull-right btn-del">Eliminar</button>
            </div>
          </form>
        </div>
      </div>
  </div>
</div>

<!-- Popper.js (para tooltips y otros componentes) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<!-- Bootstrap Bundle (con Popper.js) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<!-- Complementos/Plugins-->    
<!-- Scripts propios -->
<script src="../../assets/js/sidebar.js"></script>
<script src="assets/js/supervisor.js"></script>
<script src="assets/js/general.js"></script>
</body>

</html>