<?php
session_start();
include("../../checklogin.php");
include("../../dbconnection.php");
include("assets/php/instalaciones.php");
check_login();
$userID = $_SESSION['id'];


?>
<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
  <meta charset="utf-8" />
  <title>Sucursales</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <meta name="google" content="notranslate">
  <meta content="" name="description" />
  <meta content="" name="author" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link href="../../assets/css/sidebar.css" rel="stylesheet" type="text/css"/>
  <link href="../assets/css/historico-TD.css" rel="stylesheet" type="text/css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="test" >
  <div class="page-container ">
    <div class="sidebar">
      <?php include("../../header-test.php"); ?>
    </div>
    <div class="page-content">
      <?php include("../../leftbar-test.php"); ?>
      <div class="page-title d-flex justify-content-between">
        <h2>Sucursales</h2>
        <button class=" btn-back" onclick="window.location.href='main.php';"> 
          <i class="bi bi-arrow-left" ></i>
        </button>
      </div> <br><br>

      <form method="GET" action="" id="filtrosForm">
        <div class="filtros d-flex justify-content-between form-f">
          <div class="d-flex justify-content-between mb-3">
            <div class="all-fil">
              <label for="filtroTexto">Buscar</label>
              <input type="text" id="filtroTexto" name="texto"
                value="<?= htmlspecialchars($filtros['texto']) ?>"
                class="form-control form-control-sm fil"
                placeholder="Buscar..."
                autocomplete="off">
            </div>
            <div class="all-fil">  
              <label for="filtroCentro">Centro de costos</label>
              <select id="filtroCentro" name="centro" class="form-select form-select-sm">
                <option value="">Todos los Centros</option>
                <?php foreach ($departamentos as $cc): ?>
                  <option value="<?= $cc['id'] ?>" <?= $filtros['centro'] == $cc['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cc['nombre_departamento']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="all-fil">  
              <label for="filtroSupervisor">Supervisor</label>
              <select id="filtroSupervisor" name="supervisor" class="form-select form-select-sm">
                <option value="">Todos los supervisores</option>
                <?php foreach ($supervisores as $supervisor): ?>
                  <option value="<?= $supervisor['id'] ?>" <?= $filtros['supervisor'] == $supervisor['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($supervisor['nombre_supervisor']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div>
            <button type="submit" class="btn btn-updt btn-lg me-2">
              <i class="bi bi-filter"></i> Filtrar
            </button>
            <button type="button" class="btn btn-excel" onclick="window.location.href='assets/php/descargar-calendario.php?formato=all&';">
              <i class="bi bi-file-earmark-excel"></i> Exportar Calendario por Instalacion
            </button>
            <?php if($userID == 50 ){?>
            <!--  if($userID == 50 ){-->
              <button type="button" class="btn btn-excel" onclick="window.location.href='assets/php/descargar-calendario.php?formato=all&userID=<?= $userID ?>';">
                <i class="bi bi-file-earmark-excel"></i> Exportar Calendario Conjunto
              </button>

            <?php } ?>
          </div>
        </div>
        <input type="hidden" name="pagina" value="<?= $pagina ?>">
      </form>

      <div id="resultadoSucursal" class="content">
        <?php if (count($sucursal) === 0): ?>
          <div class="alert alert-info">No se encontraron sucursales</div>
        <?php else: ?>
          <?php foreach ($sucursal as $item): ?>
            <div class="h-container" onclick="window.location.href='detalle-instalacion.php?id=<?= $item['id'] ?>';">
              <div class="h-header d-flex justify-content-between">
                <div class="colab">
                  <strong><?= htmlspecialchars($item['nombre']) ?></strong>
                  <p>Razón Social: <?= $item['razon_social'] ? htmlspecialchars($item['razon_social']) : 'Sin definir' ?></p>
                  <p>Centro de Costos: <?= htmlspecialchars($item['cost_center']) ?></p>
                </div>
                <div class="estado mt-2">
                  <span class="label label-estado" style="text-transform: capitalize;"><?= htmlspecialchars($item['estado']) ?></span>
                </div>
              </div>
              <div class="h-body">
                <p>Supervisor: <?= htmlspecialchars($item['nSup']) ?></p>
                <p>Ciudad: <?= htmlspecialchars($item['nCiudad']) ?></p>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <?php if ($total_pages > 1): ?>
        <nav aria-label="Paginación de sucursales">
          <ul class="pagination justify-content-center mt-4">
            <?php if ($pagina > 1): ?>
              <li class="page-item">
                <a class="page-link" href="?<?= http_build_query(array_merge($filtros, ['pagina' => $pagina - 1])) ?>">
                  &laquo; Anterior
                </a>
              </li>
            <?php endif; ?>

            <?php
            $startPage = max(1, $pagina - 2);
            $endPage = min($total_pages, $startPage + 4);
            if ($endPage - $startPage < 4) {
                $startPage = max(1, $endPage - 4);
            }
            
            for ($i = $startPage; $i <= $endPage; $i++): ?>
              <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
                <a class="page-link" href="?<?= http_build_query(array_merge($filtros, ['pagina' => $i])) ?>">
                  <?= $i ?>
                </a>
              </li>
            <?php endfor; ?>

            <?php if ($pagina < $total_pages): ?>
              <li class="page-item">
                <a class="page-link" href="?<?= http_build_query(array_merge($filtros, ['pagina' => $pagina + 1])) ?>">
                  Siguiente &raquo;
                </a>
              </li>
            <?php endif; ?>
          </ul>
        </nav>
      <?php endif; ?>
    </div>
  </div>

  <script>
    document.getElementById('filtrosForm').addEventListener('submit', function() {
      this.querySelector('input[name="pagina"]').value = 1;
    });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>