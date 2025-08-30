<?php
session_start();
include("../../checklogin.php");
include("../../dbconnection.php");
include("assets/php/colaboradores.php");
check_login();
?>
<!DOCTYPE html>
<html>

<head>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
  <meta charset="utf-8" />
  <title>Colaboradores</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <meta content="" name="description" />
  <meta content="" name="author" />

<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<!-- CSS personalizados -->
<link href="../../assets/css/sidebar.css" rel="stylesheet" type="text/css" />
<link href="../assets/css/general-crud.css" rel="stylesheet" type="text/css"/>

<!-- sweetalert -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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
                <h2>Colaboradores</h2>
                <button class=" btn-back" onclick="window.location.href='gestion-main.php';"> 
                    <i class="bi bi-arrow-left" ></i>
                </button>
            </div>
            <div class="main-crud col-md-12 col-sm-12">
              <div class="head-crud d-flex justify-content-between mb-3">
                <h4>Administrar Colaboradores</h4>
                <div class="btns">
                  <button class="btn btn-updt"  data-bs-toggle="modal" data-bs-target="#newSuper" style="width:180px;">Nuevo Colaborador</button>
                  <button type="submit" form="editSu" class="btn btn-default" id="btnUpdt" name="btnUpdt" disabled>Actualizar</button>
                </div>
              </div>
              <hr>
              <div class="body-crud">
                <!-- Campo de búsqueda -->
                <div class="search-container mb-3">
                    <form method="GET" action="">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" placeholder="Buscar por nombre del colaborador" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                            <button type="submit" class="btn btn-updt">Buscar</button>
                        </div>
                    </form>
                </div>

                <!-- Tabla de colaboradores -->
                <table class="table table-striped">
                    <thead>
                        <th>Rut</th>
                        <th>Nombre</th>
                        <th>Apellido Paterno</th>
                        <th>Apellido Materno</th>
                        <th>Razon social</th>
                        <th>Nacionalidad</th>
                        <th>Fecha de Ingreso</th>
                        <th>Celular</th>
                        <th>Email</th>
                        <th>Tipo Contrato</th>
                        <th>Sucursal</th>
                        <th>Vigente</th>
                        <th>Opciones</th>
                    </thead>
                    <tbody>
                        <form name="form" id="editSu" method="post">
                            <div id="loading" style="display:none ;">
                                <div class="loading-spinner"></div>
                                <p>Procesando...</p>
                            </div>
                            <?php while ($row_s = mysqli_fetch_assoc($result)) { ?>
                                <tr>
                                    <td>
                                      <input type="text" class="form-control form-control-sm" id="rut" name="rut[]" value="<?php echo $row_s['rut']; ?>" onchange="enableUpdateButton()" required>
                                      <input type="hidden" name="id[]" value="<?php echo $row_s['id']; ?>">
                                    </td>
                                    <td>
                                      <input type="text" class="form-control form-control-sm" id="name" name="name[]" value="<?php echo $row_s['name']; ?>" onchange="enableUpdateButton()" required>
                                    </td>
                                    <td>
                                      <input type="text" class="form-control form-control-sm" id="fname" name="fname[]" value="<?php echo $row_s['fname']; ?>" onchange="enableUpdateButton()" required>
                                    </td>
                                    <td>
                                      <input type="text" class="form-control form-control-sm" id="mname" name="mname[]" value="<?php echo $row_s['mname']; ?>" onchange="enableUpdateButton()" required>
                                    </td>
                                    <td>
                                      <input type="text" class="form-control form-control-sm" id="rsocial" name="rsocial[]" value="<?php echo $row_s['rsocial']; ?>" onchange="enableUpdateButton()" required>
                                    </td>
                                    <td>
                                      <input type="text" class="form-control form-control-sm" id="nacionality" name="nacionality[]" value="<?php echo $row_s['nacionality']; ?>" onchange="enableUpdateButton()" required>
                                    </td>
                                    <td>
                                      <input type="date" class="form-control form-control-sm" id="entry_date" name="entry_date[]" value="<?php echo $row_s['entry_date']; ?>" onchange="enableUpdateButton()" required>
                                    </td>
                                    <td>
                                      <input type="text" class="form-control form-control-sm" id="phone" name="phone[]" value="<?php echo $row_s['phone']; ?>" onchange="enableUpdateButton()" required>
                                    </td>
                                    <td>
                                      <input type="text" class="form-control form-control-sm" id="email" name="email[]" value="<?php echo $row_s['email']; ?>" onchange="enableUpdateButton()" required>
                                    </td>
                                    <td>
                                      <input type="text" class="form-control form-control-sm" id="ctype" name="ctype[]" value="<?php echo $row_s['contract_type']; ?>" onchange="enableUpdateButton()" required>
                                    </td>
                                    <td>
                                        <select name="depto[]" id="depto" class="form-select form-select-sm" onchange="enableUpdateButton()" required>
                                            <?php     
                                            foreach ($su as $row) {
                                              $selected = ($row['id'] == $row_s['facility']) ? 'selected' : '';
                                              echo "<option value=\"{$row['id']}\" $selected>{$row['nombre']}</option>";
                                            };
                                            ?>
                                        </select>
                                    </td>
                                    <td>
                                        <select name="estado[]" id="estado" class="form-select form-select-sm" onchange="enableUpdateButton()" required>
                                            <option value="1" <?php if ($row_s['vigente'] == '1') echo 'selected'; ?>>Activo</option>
                                            <option value="0" <?php if ($row_s['vigente'] == '0') echo 'selected'; ?>>Inactivo</option>
                                        </select>
                                    </td>
                                    <td>
                                        <button type="button" style="width: 100%;" class="btn btn-del del-btn" data-bs-toggle="modal" data-bs-target="#delSuper" data-sup-id="<?php echo $row['id']; ?>">Eliminar</button>
                                    </td>
                                </tr>
                            <?php } ?>
                        </form>
                    </tbody>
                </table>

                <!-- Controles de paginación -->
                <nav aria-label="Page navigation">
                  <ul class="pagination justify-content-center">
                      <?php
                      // Mostrar siempre el botón "Anterior"
                      echo '<li class="page-item ' . ($page == 1 ? 'disabled' : '') . '">';
                      echo '<a class="page-link" href="?page=' . ($page - 1) . '&search=' . urlencode($search) . '">Anterior</a>';
                      echo '</li>';

                      // Calcular rango de páginas a mostrar
                      $maxPagesToShow = 5;
                      $startPage = max(1, $page - floor($maxPagesToShow / 2));
                      $endPage = min($totalPages, $startPage + $maxPagesToShow - 1);
                      
                      // Ajustar si estamos cerca del final
                      if ($endPage - $startPage < $maxPagesToShow - 1) {
                          $startPage = max(1, $endPage - $maxPagesToShow + 1);
                      }

                      // Mostrar primera página si no está en el rango
                      if ($startPage > 1) {
                          echo '<li class="page-item"><a class="page-link" href="?page=1&search=' . urlencode($search) . '">1</a></li>';
                          if ($startPage > 2) {
                              echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                          }
                      }

                      // Mostrar páginas en el rango calculado
                      for ($i = $startPage; $i <= $endPage; $i++) {
                          $active = $page == $i ? 'active' : '';
                          echo '<li class="page-item ' . $active . '"><a class="page-link" href="?page=' . $i . '&search=' . urlencode($search) . '">' . $i . '</a></li>';
                      }

                      // Mostrar última página si no está en el rango
                      if ($endPage < $totalPages) {
                          if ($endPage < $totalPages - 1) {
                              echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                          }
                          echo '<li class="page-item"><a class="page-link" href="?page=' . $totalPages . '&search=' . urlencode($search) . '">' . $totalPages . '</a></li>';
                      }

                      // Mostrar siempre el botón "Siguiente"
                      echo '<li class="page-item ' . ($page == $totalPages ? 'disabled' : '') . '">';
                      echo '<a class="page-link" href="?page=' . ($page + 1) . '&search=' . urlencode($search) . '">Siguiente</a>';
                      echo '</li>';
                      ?>
                  </ul>
                </nav>
              </div>
            </div>
        </div>   
    <br><br>
    </div>
  </div>

<!-- Modal new -->
<div class="modal fade" id="newSuper" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="newSuperLabel" aria-hidden="true">>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
          <h5 class="modal-title" id="newSuperLabel">Nuevo colaborador</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form class="mb-2 mv" method="post" enctype="multipart/form-data">
        <div class="modal-footer mv-form">
          <h5 class="text-start">Carga masiva</h5>
          <input class="mb-3" type="file" name="file" required >
          <div class="form-row-modal d-flex justify-content-end">
            <button class="btn btn-updt" name="carga" type="submit">Cargar Datos</button>
          </div>
        </div>
      </form> 
    </div>
  </div>
</div>

<!-- modal eliminar  -->
<div class="modal fade" id="delSuper" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="delSuperLabel" aria-hidden="true">
  <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="delSuperLabel">Eliminar Colaborador</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>¿Estás seguro de que quieres eliminar a este Colaborador?</p>
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

<!-- sweetalert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (isset($_SESSION['swal'])): ?>
  <script>
    Swal.fire({
      title: <?= json_encode($_SESSION['swal']['title']) ?>,
      html: <?= json_encode($_SESSION['swal']['html']) ?>,
      icon: <?= json_encode($_SESSION['swal']['icon']) ?>,
      confirmButtonText: <?= json_encode($_SESSION['swal']['confirmButtonText']) ?>,
      <?= isset($_SESSION['swal']['showCancelButton']) && $_SESSION['swal']['showCancelButton'] ? 'showCancelButton: true,' : '' ?>
      <?= isset($_SESSION['swal']['cancelButtonText']) ? 'cancelButtonText: ' . json_encode($_SESSION['swal']['cancelButtonText']) . ',' : '' ?>
      footer: <?= json_encode($_SESSION['swal']['footer']) ?>
    }).then((result) => {
      <?php if (!empty($_SESSION['swal']['details'])): ?>
        if (result.dismiss === Swal.DismissReason.cancel) {
          Swal.fire({
            title: 'Detalles',
            html: <?= json_encode(nl2br($_SESSION['swal']['details'])) ?>,
            icon: 'info',
            confirmButtonText: 'Cerrar'
          });
        }
      <?php endif; ?>
    });
  </script>
  <?php unset($_SESSION['swal']); ?>
<?php endif; ?>

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