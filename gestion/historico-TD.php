<?php
session_start();
include("../checklogin.php");
check_login();
include("../dbconnection.php");
include("assets/php/historico-td.php");

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Historico Traslados y Desvinculaciones</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="../assets/css/sidebar.css" rel="stylesheet"/>
  <link href="assets/css/historico-TD.css" rel="stylesheet"/>
</head>

<body class="test">
<div class="page-container">
  <div class="sidebar">
    <?php include("../header-test.php"); ?>
    <?php include("../assets/php/phone-sidebar.php"); ?>
  </div>

  <div class="page-content">
    <?php include("../leftbar-test.php"); ?>

    <div class="page-title d-flex justify-content-between">
      <h2>Historico Traslado y Desvinculacion</h2>
      <button class="btn-back" onclick="window.location.href='main.php';"> 
        <i class="bi bi-arrow-left"></i>
      </button>
    </div>
    <br><br>

    <form method="GET" class="">
      <div class="filtros d-flex justify-content-between form-f">
        <div class="d-flex justify-content-arround mb-3">
          <div class="all-fil">
            <label for="filtroTexto">Buscar</label>
            <input type="text" id="filtroTexto" name="texto" value="<?= htmlspecialchars($texto) ?>" class="form-control form-control-sm fil" placeholder="Buscar por nombre, tipo, etc.">
          </div>
          <div class="all-fil">
            <label for="filtroTipo">Tipo</label>
            <select id="filtroTipo" name="tipo" class="form-select form-select-sm fil">
              <option value="">Todos los tipos</option>
              <option value="traslado" <?= $tipo=='traslado' ? 'selected' : '' ?>>Traslados</option>
              <option value="desvinculación" <?= $tipo=='desvinculación' ? 'selected' : '' ?>>Desvinculaciones</option>
            </select>
          </div>
          <div class="all-fil">
            <label for="filtroEstado">Estado</label>
            <select id="filtroEstado" name="estado" class="form-select form-select-sm fil">
              <option value="">Todos los estados</option>
              <option value="en gestión" <?= $estado=='en gestión' ? 'selected' : '' ?>>En Gestión</option>
              <option value="realizado" <?= $estado=='realizado' ? 'selected' : '' ?>>Realizado</option>
              <option value="anulado" <?= $estado=='anulado' ? 'selected' : '' ?>>Anulado</option>
            </select>
          </div>
          <div class="all-fil">
            <label for="filtroFechaInicio">Fecha Inicio</label>
            <input type="datetime-local" name="fecha_inicio" value="<?= htmlspecialchars($fecha_inicio) ?>" class="form-control form-control-sm fil">
          </div>
          <div class="all-fil">
            <label for="filtroFechaFin">Fecha Fin</label>
            <input type="datetime-local" name="fecha_fin" value="<?= htmlspecialchars($fecha_fin) ?>" class="form-control form-control-sm fil">
          </div>
        </div>

        <div class="d-flex align-items-end">
          <button type="submit" class="btn btn-updt btn-lg me-2">Filtrar</button>
          <button type="submit" class="btn btn-excel" formaction="assets/php/exportar-historico.php">
            <i class="bi bi-file-earmark-excel"></i> Exportar a Excel
          </button>
        </div>
      </div>
    </form>

    <div id="resultadoHistorico" class="content mt-3">
      <?php if(count($historico) > 0): ?>
        <?php foreach($historico as $item): 
          $fechaFormateada = date("d-m-Y H:i", strtotime($item['fecha']));
        ?>
        <div class="h-container" onclick="window.location.href='detalle-historico.php?id=<?= $item['id'] ?>&tipo=<?= $item['tipo'] ?>';">
          <div class="h-header d-flex justify-content-between">
            <div class="colab">
              <strong><?= $item['colaborador'] ?></strong>
              <p>Rut: <?= $item['rut'] ?></p>
            </div>
            <div class="estado mt-2">
              <span class="label label-estado"><?= $item['estado'] ?></span>
            </div>
          </div>
          <div class="h-body">
            <p>Fecha: <?= $fechaFormateada ?></p>
            <p>Tipo: <?= ucfirst(strtolower($item['tipo'])) ?></p>
            <p>Creado por: <?= $item['solicitante'] ?></p>
            <p>Observación SSPP: <?= $item['observacion'] ?: 'No hay observación' ?></p>
            <p>Observación RRHH: <?= $item['obs_rrhh'] ?: 'No hay observación' ?></p>
          </div>
        </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="alert alert-info">No se encontraron registros</div>
      <?php endif; ?>
    </div>

    <!-- Paginación -->
    <?php if($totalPages > 1): ?>
    <nav aria-label="Paginación Historico">
      <ul class="pagination justify-content-center mt-3">
        <?php if($pagina > 1): ?>
          <li class="page-item">
            <a class="page-link" href="?pagina=<?= $pagina-1 ?><?= !empty($texto) ? '&texto='.urlencode($texto) : '' ?><?= !empty($tipo) ? '&tipo='.urlencode($tipo) : '' ?><?= !empty($estado) ? '&estado='.urlencode($estado) : '' ?><?= !empty($fecha_inicio) ? '&fecha_inicio='.urlencode($fecha_inicio) : '' ?><?= !empty($fecha_fin) ? '&fecha_fin='.urlencode($fecha_fin) : '' ?>">
              &laquo; Anterior
            </a>
          </li>
        <?php endif; ?>
        
        <?php 
        $startPage = max(1, $pagina - 2);
        $endPage = min($totalPages, $startPage + 4);
        if($endPage - $startPage < 4) {
            $startPage = max(1, $endPage - 4);
        }
        
        for($i = $startPage; $i <= $endPage; $i++): ?>
          <li class="page-item <?= ($i==$pagina) ? 'active' : '' ?>">
            <a class="page-link" href="?pagina=<?= $i ?><?= !empty($texto) ? '&texto='.urlencode($texto) : '' ?><?= !empty($tipo) ? '&tipo='.urlencode($tipo) : '' ?><?= !empty($estado) ? '&estado='.urlencode($estado) : '' ?><?= !empty($fecha_inicio) ? '&fecha_inicio='.urlencode($fecha_inicio) : '' ?><?= !empty($fecha_fin) ? '&fecha_fin='.urlencode($fecha_fin) : '' ?>">
              <?= $i ?>
            </a>
          </li>
        <?php endfor; ?>
        
        <?php if($pagina < $totalPages): ?>
          <li class="page-item">
            <a class="page-link" href="?pagina=<?= $pagina+1 ?><?= !empty($texto) ? '&texto='.urlencode($texto) : '' ?><?= !empty($tipo) ? '&tipo='.urlencode($tipo) : '' ?><?= !empty($estado) ? '&estado='.urlencode($estado) : '' ?><?= !empty($fecha_inicio) ? '&fecha_inicio='.urlencode($fecha_inicio) : '' ?><?= !empty($fecha_fin) ? '&fecha_fin='.urlencode($fecha_fin) : '' ?>">
              Siguiente &raquo;
            </a>
          </li>
        <?php endif; ?>
      </ul>
    </nav>
    <?php endif; ?>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/sidebar.js"></script>

</body>
</html>