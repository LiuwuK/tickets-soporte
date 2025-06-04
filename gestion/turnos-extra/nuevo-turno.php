<?php
session_start();
include("../../checklogin.php");
include BASE_PATH . 'dbconnection.php';
include("../assets/php/create-extra.php");
date_default_timezone_set('America/Santiago'); 
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
<!-- sweetalert -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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
            <div class="main-div col-xl-12 col-sm-12">
                <div class="extra-title">
                    <h3>Crear turnos</h3>
                    <div class="excel">
                        <button class="btn btn-updt" data-bs-toggle="modal" data-bs-target="#newSuper">Importar Turnos extra</button>
                    </div>
                </div>
                <!-- Formulario nuevos turnos -->
                <form class="form-horizontal form-div p-3" name="form" method="POST" action="" >
                    <table class="table table-hover" id="tabla-turnos">
                        <thead>
                            <tr>
                                <th scope="col" class="align-middle text-center">Motivo</th>
                                <th scope="col" class="align-middle text-center">Instalacion</th>
                                <th scope="col" class="align-middle text-center">Fecha del Turno</th>
                                <th scope="col" class="align-middle text-center">Hora entrada</th>
                                <th scope="col" class="align-middle text-center">Hora salida</th>
                                <th scope="col" class="align-middle text-center">Monto</th>
                                <th scope="col" class="align-middle text-center">RUT</th>
                                <th scope="col" class="align-middle text-center">Nombre Completo</th>
                                <th scope="col" class="align-middle text-center">Nacionalidad</th>
                                <th scope="col" class="align-middle text-center">Banco</th>
                                <th scope="col" class="align-middle text-center">RUT Cuenta</th>
                                <th scope="col" class="align-middle text-center">Numero de Cuenta</th>
                                <th scope="col" class="align-middle text-center">Persona del Motivo</th>
                                <th scope="col" class="align-middle text-center">Contratado</th>
                                <th scope="col" class="align-middle text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="cuerpo-tabla">
                            <tr>
                                <td class="align-middle text-center">
                                    <select name="nuevos_turnos[][motivo]" class="form-control form-control-sm" required>
                                        <option value="">Motivos</option>
                                        <?php
                                        foreach ($motivo AS $row) {
                                            echo '<option value="'.htmlspecialchars($row['id']).'">'
                                                .htmlspecialchars($row['motivo'])
                                                .'</option>';
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td class="align-middle text-center">
                                    <select name="nuevos_turnos[][instalacion]" class="form-control form-control-sm">
                                        <option value="">Instalaciones</option>
                                        <?php
                                        foreach ($inst AS $row) {
                                            echo '<option value="'.htmlspecialchars($row['id']).'">'
                                                .htmlspecialchars($row['nombre'])
                                                .'</option>';
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td class="align-middle text-center">
                                    <input type="date" name="nuevos_turnos[][fecha]" class="form-control form-control-sm" required>
                                </td>
                                <td class="align-middle text-center">
                                    <input type="time" name="nuevos_turnos[][hora_entrada]" class="form-control form-control-sm" required>
                                </td>
                                <td class="align-middle text-center">
                                    <input type="time" name="nuevos_turnos[][hora_salida]" class="form-control form-control-sm" required>
                                </td>
                                <td class="align-middle text-center">
                                    <input type="text" name="nuevos_turnos[][monto]" class="form-control form-control-sm" required>
                                </td>
                                <td class="align-middle text-center">
                                    <input type="text" name="nuevos_turnos[][rut]" class="form-control form-control-sm" required>
                                </td>
                                <td class="align-middle text-center">
                                    <input type="text" name="nuevos_turnos[][nombre]" class="form-control form-control-sm" required>
                                </td>
                                <td class="align-middle text-center">
                                    <select name="nuevos_turnos[][nacionalidad]" class="form-control form-control-sm" required>
                                        <option value="">Nacionalidad</option>
                                        <option value="Chileno">Chileno</option>
                                        <option value="Extranjero">Extranjero</option>
                                    </select>
                                </td>
                                <td class="align-middle text-center">
                                    <select name="nuevos_turnos[][banco]" class="form-control form-control-sm" required>
                                        <option value="">Bancos</option>
                                        <?php
                                        foreach ($bancos AS $row) {
                                            echo '<option value="'.htmlspecialchars($row['id']).'">'
                                                .htmlspecialchars($row['nombre_banco'])
                                                .'</option>';
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td class="align-middle text-center">
                                    <input type="text" name="nuevos_turnos[][rut_cuenta]" class="form-control form-control-sm" required>
                                </td>
                                <td class="align-middle text-center">
                                    <input type="text" name="nuevos_turnos[][numero_cuenta]" class="form-control form-control-sm" required>
                                </td>
                                <td class="align-middle text-center">
                                    <input type="text" name="nuevos_turnos[][persona_motivo]" class="form-control form-control-sm">
                                </td>
                                <td class="align-middle text-center">
                                    <select name="nuevos_turnos[][contratado]" class="form-control form-control-sm" required>
                                        <option value="Si">Si</option>
                                        <option value="No">No</option>
                                    </select>
                                </td>
                                <td class="align-middle text-center">
                                    <button type="button" class="btn btn-danger eliminar-fila">X</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-end">
                        <button type="button" id="agregar-fila" class="btn btn-default">Agregar otro turno</button>
                        <button type="submit" name="newExtra" class="btn btn-updt">Enviar</button>
                    </div>
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
        <form class="mb-2" name="form" method="post" enctype="multipart/form-data">
            <div id="loading" style="display:none ;">
                <div class="loading-spinner"></div>
                <p>Procesando...</p>
            </div>  
            <div class="modal-body mt-3 d-flex justify-content-center">
                <input class="mb-3" type="file" name="file" required>
            </div>
            <div class="modal-footer form-row-modal d-flex justify-content-end">
                <a href="../assets/excel-ejemplos/turnos.xlsx" download class="btn btn-default">
                    Excel Ejemplo
                </a> 
                <button class="btn btn-updt" name="carga" type="submit">Cargar Datos</button>
            </div>
        </form> 
      </div>
  </div>
</div>

<?php if (isset($_SESSION['alert'])): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: '<?= $_SESSION['alert']['type'] ?>',
        title: '<?= $_SESSION['alert']['title'] ?>',
        html: '<?= $_SESSION['alert']['message'] ?>',
        confirmButtonText: 'Aceptar',
        customClass: {
            popup: 'animated bounceIn'
        }
    });
    <?php unset($_SESSION['alert']); ?>
});
</script>
<?php endif; ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Agregar nueva fila
    document.getElementById('agregar-fila').addEventListener('click', function() {
        agregarFilaTurno();
    });
    
    // Eliminar fila
    document.getElementById('cuerpo-tabla').addEventListener('click', function(e) {
        if (e.target.classList.contains('eliminar-fila')) {
            e.target.closest('tr').remove();
        }
    });
});

function agregarFilaTurno() {
    const cuerpoTabla = document.getElementById('cuerpo-tabla');
    const primeraFila = cuerpoTabla.querySelector('tr');
    
    if (!primeraFila) return;
    
    // Clonar la primera fila
    const nuevaFila = primeraFila.cloneNode(true);
    
    // Limpiar valores de los inputs
    nuevaFila.querySelectorAll('input').forEach(input => {
        if (input.type !== 'button') input.value = '';
    });
    
    // Limpiar selects
    nuevaFila.querySelectorAll('select').forEach(select => {
        select.selectedIndex = 0;
    });
    
    cuerpoTabla.appendChild(nuevaFila);
}
</script>
<!-- sweetalert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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