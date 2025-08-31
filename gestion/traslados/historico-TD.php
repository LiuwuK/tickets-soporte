<?php
session_start();
include("../../checklogin.php");
check_login();
include("../../dbconnection.php");
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
  <link href="../../assets/css/sidebar.css" rel="stylesheet"/>
  <link href="../assets/css/historico-TD.css" rel="stylesheet"/>
</head>
<body class="test">
<div class="page-container">
  <div class="sidebar">
    <?php include("../../header-test.php"); ?>
  </div>

  <div class="page-content">
    <?php include("../../leftbar-test.php"); ?>

    <div class="page-title d-flex justify-content-between">
      <h2>Historico Traslado y Desvinculacion</h2>
      <button class="btn-back" onclick="window.location.href='../main.php';"> 
        <i class="bi bi-arrow-left"></i>
      </button>
    </div>
    <br><br>

    <form method="GET">
      <div class="filtros d-flex justify-content-between form-f">
        <div class="d-flex justify-content-arround mb-3">
          <!-- Buscar -->
          <div class="all-fil">
            <label for="filtroTexto">Buscar</label>
            <input type="text" id="filtroTexto" name="texto" 
                  value="<?= htmlspecialchars($filters['texto'] ?? '') ?>" 
                  class="form-control form-control-sm fil" 
                  placeholder="Buscar por nombre, tipo, etc.">
          </div>

          <!-- Tipo -->
          <div class="all-fil">
            <label for="filtroTipo">Tipo</label>
            <select id="filtroTipo" name="tipo" class="form-select form-select-sm fil">
              <option value="">Todos los tipos</option>
              <option value="traslado" <?= ($filters['tipo'] ?? '') === 'traslado' ? 'selected' : '' ?>>Traslados</option>
              <option value="desvinculacion" <?= ($filters['tipo'] ?? '') === 'desvinculacion' ? 'selected' : '' ?>>Desvinculaciones</option>
            </select>
          </div>

          <!-- Estado -->
          <div class="all-fil">
            <label for="filtroEstado">Estado</label>
            <select id="filtroEstado" name="estado" class="form-select form-select-sm fil">
              <option value="">Todos los estados</option>
              <option value="en gestión" <?= ($filters['estado'] ?? '') === 'en gestión' ? 'selected' : '' ?>>En Gestión</option>
              <option value="realizado" <?= ($filters['estado'] ?? '') === 'realizado' ? 'selected' : '' ?>>Realizado</option>
              <option value="anulado" <?= ($filters['estado'] ?? '') === 'anulado' ? 'selected' : '' ?>>Anulado</option>
            </select>
          </div>

          <!-- Fecha Inicio -->
          <div class="all-fil">
            <label for="filtroFechaInicio">Fecha Inicio</label>
            <input type="datetime-local" name="fecha_inicio" 
                  value="<?= htmlspecialchars($filters['fecha_inicio'] ?? '') ?>" 
                  class="form-control form-control-sm fil">
          </div>

          <!-- Fecha Fin -->
          <div class="all-fil">
            <label for="filtroFechaFin">Fecha Fin</label>
            <input type="datetime-local" name="fecha_fin" 
                  value="<?= htmlspecialchars($filters['fecha_fin'] ?? '') ?>" 
                  class="form-control form-control-sm fil">
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
          $fechaFormateada = date("d-m-Y", strtotime($item['fecha_registro']));
          $tipoLower = strtolower($item['tipo']);
        ?>
        <div class="h-container" onclick="window.location.href='detalle-historico.php?id=<?= $item['id'] ?>&tipo=<?= $tipoLower ?>';">
          <div class="h-header d-flex justify-content-between">
            <div class="colab">
              <strong><?= $item['colaborador'] ?></strong>
              <p>Rut: <?= $item['rutC'] ?></p>
            </div>
            <div class="estado mt-2">
              <span class="label label-estado"><?= $item['estadoN'] ?></span>
            </div>
          </div>
          <div class="h-body">
            <p>Fecha: <?= $fechaFormateada ?></p>
            <p>Tipo: <?= ucfirst($tipoLower) ?></p>
            <p>Creado por: <?= $item['soliN'] ?></p>
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
            <a class="page-link" href="<?= buildQueryPage($pagina-1, $filters) ?>">&laquo; Anterior</a>
          </li>
        <?php endif; ?>

        <?php
          $startPage = max(1, $pagina - 2);
          $endPage = min($totalPages, $startPage + 4);
          if ($endPage - $startPage < 4) {
              $startPage = max(1, $endPage - 4);
          }
          for($i = $startPage; $i <= $endPage; $i++):
        ?>
          <li class="page-item <?= ($i==$pagina) ? 'active' : '' ?>">
            <a class="page-link" href="<?= buildQueryPage($i, $filters) ?>"><?= $i ?></a>
          </li>
        <?php endfor; ?>

        <?php if($pagina < $totalPages): ?>
          <li class="page-item">
            <a class="page-link" href="<?= buildQueryPage($pagina+1, $filters) ?>">Siguiente &raquo;</a>
          </li>
        <?php endif; ?>
      </ul>
    </nav>
    <?php endif; ?>

  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
