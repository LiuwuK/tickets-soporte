<?php
session_start();
include("../../checklogin.php");
include("../../dbconnection.php");
check_login();
include("../assets/php/detalle-turno.php");

$puede_editar = (
  ($_SESSION['cargo'] == 11 && $_SESSION['id'] == $row['idAuto'] && $row['estado'] == 'rechazado') || 
  (array_intersect([6], $_SESSION['deptos']) && $row['estado'] == 'rechazado')
);
?>
<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
  <meta charset="utf-8" />
  <title>Turnos Extra</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <meta content="" name="description" />
  <meta content="" name="author" />
<!-- CSS de Choices.js -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<!-- CSS personalizados -->
<link href="../../assets/css/sidebar.css" rel="stylesheet" type="text/css"/>
<link href="../assets/css/historico-TD.css" rel="stylesheet" type="text/css"/>
<!-- Toast notificaciones -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />

<!-- Graficos -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="test" >
    <!-- Sidebar -->
  <div class="page-container ">
    <div class="sidebar">
      <?php include("../../header-test.php"); ?>
    </div>
    <div class="page-content">
    <?php include("../../leftbar-test.php"); ?>
        <div class="page-title d-flex justify-content-between">
          <h2 class="det-view">Detalle del Turno</h2>
          <button class=" btn-back" onclick="window.location.href='ver-turnos.php';"> 
              <i class="bi bi-arrow-left" ></i>
          </button>
        </div> <br><br>
        <div class="d-container col-md-9 mx-auto">
          <!-- Datos Colaborador -->
          <div class="d-flex justify-content-between mt-3 mb-2">
            <h4 class="">Datos colaborador</h4>
            <div class="tags-container">
              <span class="label label-estado"><?php echo $row['estado']; ?></span>
              <?php if($row['justificado'] > 0){ echo '<span class="label label-rechazo">Justificado</span>';} ?>
            </div>
          </div>
          <hr width="90%" class="mx-auto">
          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="colaborador" >Nombre Colaborador</label>
              <input type="text" name="colaborador" id="colaborador" value="<?php echo $row['colaborador']; ?>" class="form-control form-control-sm " readonly/>
            </div>
            <div class="form-group">
              <label class="form-label" for="rutC" >Rut Colaborador</label>
              <input type="text" name="rutC" id="rutC" value="<?php echo $row['rut']; ?>" class="form-control form-control-sm " readonly/>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="nacionalidad" >Nacionalidad</label>
              <input type="text" name="nacionalidad" id="nacionalidad" value="<?php echo $row['nacionalidad']; ?>" class="form-control form-control-sm " readonly/>
            </div>
          </div>
          <br>
          <!-- DATOS GENERALES -->
          <h4>Datos del Turno</h4>
          <hr width="90%" class="mx-auto">
          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="instalacion" >Instalación</label>
              <input type="text" name="instalacion" id="instalacion" value="<?php echo $row['instalacion']; ?>" class="form-control form-control-sm " readonly/>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="horario" >Horario Cubierto</label>
              <input type="text" name="horario" id="horario" value="<?php echo $row['horario']; ?>" class="form-control form-control-sm " readonly/>
            </div>
            <div class="form-group">
              <label class="form-label" for="horas" >Horas Cubiertas</label>
              <input type="number" name="horas" id="horas" value="<?php echo $row['horas']; ?>" class="form-control form-control-sm " readonly/>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="fechaTurno" >Fecha del Turno</label>
              <input type="text" name="fechaTurno" id="fechaTurno" value="<?php echo $row['fechaTurno']; ?>" class="form-control form-control-sm " readonly/>
            </div>
            <div class="form-group">
              <label class="form-label" for="monto" >Monto</label>
              <input type="text" name="monto" id="monto" value="$<?php echo number_format($row['monto'], 0, ',', '.'); ?>"  class="form-control form-control-sm " readonly/>
            </div>
          </div>

          <div class="form-row mt-3">
            <div class="form-group">
              <label class="form-label" for="motivo" >Motivo</label>
              <input type="text" name="motivo" id="motivo" value="<?php echo $row['motivo']; ?>" class="form-control form-control-sm " readonly/>
            </div>
            <div class="form-group">
              <label class="form-label" for="personaMotivo" >Persona del Motivo</label>
              <input type="text" name="personaMotivo" id="personaMotivo" value="<?php echo $row['persona_motivo']; ?>" class="form-control form-control-sm " readonly/>
            </div>
          </div>

          <div class="form-row mt-3">
            <div class="form-group">
              <label class="form-label" for="contratado" >Contratado</label>
              <input type="text" name="contratado" id="contratado" value="<?php echo ($row['contratado'] == 1) ? "SI" : "NO"; ?> " class="form-control form-control-sm " readonly/>
            </div>
            <div class="form-group">
              <label class="form-label" for="autorizadoPor" >Autorizado Por</label>
              <input type="text" name="autorizadoPor" id="autorizadoPor" value="<?php echo $row['autorizadoPor']; ?>" class="form-control form-control-sm " readonly/>
            </div>
          </div>
                    
          <br>
          <!-- Datos del pago -->
          <h4>Datos de Pago</h4>
          <hr width="90%" class="mx-auto">
          <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="banco" >Banco</label>
                <input type="text" name="banco" id="banco" value="<?php echo $row['banco']; ?>" class="form-control form-control-sm " readonly/>
            </div>
            <div class="form-group">
                <label for="rutCta" class="form-label">Rut de la Cuenta</label>
                <input name="rutCta" id="rutCta" type="text" class="form-control form-control-sm" value="<?php echo $row['RUTcta'];?>"  readonly>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="numCuenta" >Numero de la cuenta</label>
                <input type="text" name="numCuenta" id="numCuenta" value="<?php echo $row['numCuenta']; ?>" class="form-control form-control-sm " readonly/>
            </div>
          </div>
          <br>
          <?php 
          if(isset($row['motivoN']) && $row['estado'] != "aprobado"){
          ?>
            <!-- Informacion General -->
            <h4>Informacion General</h4>
            <hr width="90%" class="mx-auto">
            
            <div class="form-row">
              <div class="form-group">
                <label for="motivoN" class="form-label">Motivo del rechazo</labe>
                <textarea class="form-control form-control-sm" id="motivoR" name="motivoN" rows="3" readonly><?php echo $row['motivoN'];?></textarea>     
              </div>
            </div>
            <?php
            if ($puede_editar){?>
            <div id="justificacion-container" style="display: none;" class="form-row">
              <form method="post">
                <input type="hidden" name="id_turno" value="<?= $row['id'] ?>">
                
                <input type="hidden" name="colab" id="hidden_colaborador" value="<?= $row['colaborador'] ?>">
                <input type="hidden" name="rutC" id="hidden_rutC" value="<?= $row['rut'] ?>">
                <input type="hidden" name="numCta" id="hidden_numCuenta" value="<?= $row['numCuenta'] ?>">
                <input type="hidden" name="rutCta" id="hidden_rutCta" value="<?= $row['RUTcta'] ?>">
                <input type="hidden" name="pMotivo" id="hidden_personaMotivo" value="<?= $row['persona_motivo'] ?>">
                <input type="hidden" name="mt" id="hidden_monto" value="<?= $row['monto'] ?>">
                <input type="hidden" name="fturno" id="hidden_fechaTurno" value="<?= $row['fechaTurno'] ?>">

                <div class="form-group mb-3">
                  <label>Justificación de los cambios <span class="text-danger">*</span></label>
                  <textarea name="justificacion" class="form-control" required></textarea>
                </div>
                <button type="submit" name="guardar_cambios" class="btn btn-updt">
                  Guardar Cambios y Justificar
                </button>
              </form>
            </div>
            <button id="btn-editar" class="btn btn-default mb-3" onclick="habilitarEdicion()">Editar Turno</button>
            <?php
            }else{
            ?>
            <div class="form-row">
              <div class="form-group" id="justificacion-container">
                <label for="justi" class="form-label">Justificación</labe>
                <textarea class="form-control form-control-sm" id="justi" name="justi" rows="3" readonly><?php echo $row['justificacion'];?></textarea>     
              </div>
            </div>
          <?php
            }
          }
          
          if((in_array(6, $_SESSION['deptos'], true)) && $row['estado'] != "aprobado" && $row['justificado'] > 0 ){
          ?>
            <!-- CAMBIOS REALIZADOS -->
            <h4>Cambios Realizados</h4>
            <hr width="90%" class="mx-auto">
            <div class="form-row">
              <table class="table table-hover" id="tabla-turnos">
                  <thead>
                      <tr>
                          <th scope="col" class="align-middle text-center" >Fecha</th>
                          <th scope="col" class="align-middle text-center">Editado Por</th>
                          <th scope="col" class="align-middle text-center">Cambios Realizados</th>
                          <th scope="col" class="align-middle text-center">Justificacion</th>
                      </tr>
                  </thead>
                  <tbody id="cuerpo-tabla">
                   <?php if (!empty($historico)): ?>
                    <?php foreach ($historico as $registro): ?>
                        <tr>
                          <td class="align-middle text-center">
                            <?= date('d/m/Y H:i', strtotime($registro['fecha'])) ?>
                          </td>
                          <td class="align-middle text-center">
                            <?= htmlspecialchars($registro['usuario']) ?>
                          </td>
                          <td class="align-middle text-justify ">
                            <?php 
                            $cambios = json_decode($registro['cambios'], true);
                            if ($cambios) {
                              foreach ($cambios as $campo => $detalle) {
                                echo "<strong>" . ucfirst(str_replace('_', ' ', $campo)) . ":</strong> ";
                                if (is_array($detalle) && $campo !== 'datos_bancarios') {
                                  echo "De '{$detalle['antes']}' a '{$detalle['despues']}'<br>";
                                } else if ($campo === 'datos_bancarios'){
                                  echo '<ul class="list-unstyled">';
                                    foreach (['rut_cta', 'digito_verificador', 'numero_cuenta'] as $subcampo) {
                                      if ($detalle['antes'][$subcampo] != $detalle['despues'][$subcampo]) {
                                        $nombreSubcampo = ucfirst(str_replace('_', ' ', $subcampo));
                                        echo "<li>{$nombreSubcampo}: De '{$detalle['antes'][$subcampo]}' a '{$detalle['despues'][$subcampo]}'</li>";
                                      }
                                    }
                                  echo '</ul>';
                                }
                                else 
                                {
                                  echo $detalle."<br>";
                                }
                              }
                            }
                            ?>
                          </td>
                          <td class="align-middle text-center">
                            <?= nl2br(htmlspecialchars($registro['justificacion'])) ?>
                          </td>
                        </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="4" class="text-center">No hay registros históricos para este turno</td>
                    </tr>
                  <?php endif; ?>
                  </tbody>
              </table>
            </div>
          <?php
          }
          if($_SESSION['cargo'] != 11 && $row['estado'] != "aprobado"){
          ?>
            <hr>
            <div class="form-row">
              <div class="form-group">
                <button type="button" class="btn btn-del del-btn den-btn ms-auto" data-bs-toggle="modal" data-bs-target="#denied" data-sup-id="<?php echo $row['id'];?>">Rechazar</button>
              </div>
              <div class="form-group">
              <form method="post" enctype="multipart/form-data">
                <button class="btn btn-updt btn-acpt" name="approved">Aprobar</button>
              </form>
              </div>
            </div>
          <?php
          }else if (array_intersect([10], $_SESSION['deptos'])){
          ?>
            <hr>
            <div class="form-row">
              <div class="form-group">
                <button type="button" class="btn btn-del del-btn den-btn ms-auto" data-bs-toggle="modal" data-bs-target="#denied" data-sup-id="<?php echo $row['id'];?>">Rechazar</button>
              </div>
              <div class="form-group">
              <form method="post" enctype="multipart/form-data">
                <button class="btn btn-updt btn-acpt" name="pago">Aprobar pago</button>
              </form>
              </div>
            </div>
          <?php  
          } ?>
        </div>
    </div>
  </div>
<!-- modal eliminar  -->
<div class="modal fade" id="denied" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deniedLabel" aria-hidden="true">
  <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="deniedLabel">Rechazar turno</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="delForm" method="POST">
        <div class="modal-body">
          <label for="motivoR" class="form-label">Motivo del Rechazo</label>
          <textarea class="form-control form-control-sm" id="motivoR" name="motivoR" rows="4"></textarea>
          <input type="hidden" name="idDen" id="idDen">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-updt" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" name="denTurno" class="btn pull-right btn-del">Rechazar</button>
        </div>
        </form>
      </div>
  </div>
</div>
<!-- Popper.js (para tooltips y otros componentes) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<!-- Bootstrap Bundle (con Popper.js) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<!-- Scripts propios -->
<script src="../../assets/js/sidebar.js"></script>
<script src="../assets/js/detalle-turno.js"></script>
<style>

input.editable, textarea.editable {
  background-color: #fff;
  border: 1px solid #ced4da;
  box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
}


#justificacion-container {
  width: 83%;
  margin: 0 auto;
  padding: 15px;
  background-color: #f8f9fa;
  border-radius: 5px;
}
</style>
<?php
if($puede_editar){
?>
<script>
  function habilitarEdicion() {
    const excludedIds = [
      'motivoR', 'autorizadoPor', 'motivo', 'banco',
      'contratado', 'horario', 'horas', 'instalacion', 'nacionalidad'
    ];
    
    document.querySelectorAll('input[readonly], textarea[readonly], select[readonly]').forEach(input => {
      if (!excludedIds.includes(input.id)) {
        input.removeAttribute('readonly');
        input.classList.add('editable');

        input.addEventListener('change', function() {
          document.getElementById('hidden_' + this.id).value = this.value;
        });
        
        if (input.tagName === 'SELECT') {
          input.disabled = false;
        }
      }
    });
    
    document.getElementById('btn-editar').style.display = 'none';
    document.getElementById('justificacion-container').style.display = 'block';
}
</script>
<?php }; ?>
</body>

</html>