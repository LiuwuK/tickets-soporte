<?php
session_start();
include("../checklogin.php");
check_login();
include("../dbconnection.php");
include("assets/php/view-projects.php");
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <meta charset="utf-8" />
    <title>Proyectos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta content="" name="description" />
    <meta content="" name="author" />

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- CSS personalizados -->
    <link href="../assets/css/sidebar.css" rel="stylesheet" type="text/css" />
    <link href="../tickets/assets/css/manage_tickets.css" rel="stylesheet" type="text/css"/>
    <link href="assets/css/view-projects.css" rel="stylesheet" type="text/css"/>
    <!-- Toast notificaciones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
</head>
<body class="test">
  <div class="page-container">
    <div class="sidebar">
        <?php include("../header-test.php"); ?>
    </div>
    <div class="page-content">
        <?php include("../leftbar-test.php"); ?>
        <div class="content">
            <div class="page-title d-flex justify-content-between">
                <h2>Proyectos</h2>
                <button class="btn-back" onclick="window.location.href='projects-main.php';">
                    <i class="bi bi-arrow-left"></i>
                </button>
            </div>

            <!-- filtros -->
            <div>
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" class="d-flex flex-wrap align-items-end gap-2">
                        <!-- Buscar -->
                        <div style="min-width:180px; flex:1">
                            <label class="form-label">Buscar</label>
                            <input type="text" class="form-control form-control-sm" name="textSearch" placeholder="Nombre/ID" value="<?= htmlspecialchars($searchText) ?>">
                        </div>

                        <!-- Estado -->
                        <div style="min-width:120px">
                            <label class="form-label">Estado</label>
                            <select name="statusF" class="form-select form-select-sm">
                                <option value="">Ver todo</option>
                                <?php while ($st = mysqli_fetch_assoc($statusF)): ?>
                                    <option value="<?= $st['id'] ?>" <?= isset($_GET['statusF']) && $_GET['statusF']==$st['id'] ? 'selected' : '' ?>><?= $st['nombre'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <!-- Vertical -->
                        <div style="min-width:120px">
                            <label class="form-label">Vertical</label>
                            <select name="verticalF" class="form-select form-select-sm">
                                <option value="">Ver todo</option>
                                <?php foreach ($verticales as $row): ?>
                                    <option value="<?= $row['id'] ?>" <?= isset($_GET['verticalF']) && $_GET['verticalF']==$row['id'] ? 'selected' : '' ?>><?= $row['nombre'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Portal -->
                        <div style="min-width:120px">
                            <label class="form-label">Portal</label>
                            <select name="portalF" class="form-select form-select-sm">
                                <option value="">Ver todo</option>
                                <?php foreach($portal as $pt): ?>
                                    <option value="<?= $pt['id'] ?>" <?= isset($_GET['portalF']) && $_GET['portalF']==$pt['id'] ? 'selected' : '' ?>><?= $pt['nombre_portal'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Tipo de Proyecto -->
                        <div style="min-width:120px">
                            <label class="form-label">Tipo Proyecto</label>
                            <select name="tipoprjF" class="form-select form-select-sm">
                                <option value="">Ver todo</option>
                                <?php foreach($tipoProyecto as $tprj): ?>
                                    <option value="<?= $tprj['id'] ?>" <?= isset($_GET['tipoprjF']) && $_GET['tipoprjF']==$tprj['id'] ? 'selected' : '' ?>><?= $tprj['nombre'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Clasificación -->
                        <div style="min-width:120px">
                            <label class="form-label">Clasificación</label>
                            <select name="clasif" class="form-select form-select-sm">
                                <option value="">Ver todo</option>
                                <?php foreach($clasif as $cl): ?>
                                    <option value="<?= $cl['id'] ?>" <?= isset($_GET['clasif']) && $_GET['clasif']==$cl['id'] ? 'selected' : '' ?>><?= $cl['nombre'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Proyectos por página -->
                        <div style="min-width:100px">
                            <label class="form-label">Mostrar</label>
                            <select name="limit" class="form-select form-select-sm">
                                <option value="10" <?= $limit==10 ? 'selected' : '' ?>>10</option>
                                <option value="20" <?= $limit==20 ? 'selected' : '' ?>>20</option>
                                <option value="50" <?= $limit==50 ? 'selected' : '' ?>>50</option>
                            </select>
                        </div>

                        <!-- Botón Filtrar -->
                        <div class="align-self-end">
                            <button type="submit" class="btn btn-updt btn-sm">Filtrar</button>
                        </div>
                    </form>
                </div>
            </div>

            </div>

            <?php 
            if ($num > 0) {
                while ($row = $rt->fetch_assoc()) { ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="grid simple no-border">

                                <!-- Vista cerrada -->
                                <div class="grid-title no-border descriptive clickable">
                                    <h4 class="semi-bold"><?= $row['nombre']; ?></h4>
                                    <p>
                                        <span class="text-success bold">Proyecto #<?= $row['projectId']; ?></span>
                                        - Fecha de Creación <?= $row['fecha_creacion']; ?> 
                                        <?php if ($row['estado_id'] == 20): ?>
                                            <span class="label label-success"><?= $row['estado']; ?></span>
                                        <?php elseif ($row['estado_id'] == 21): ?>
                                            <span class="label label-important"><?= $row['estado']; ?></span>
                                        <?php else: ?>
                                            <span class="label label-warning"><?= $row['estado']; ?></span>
                                            <?php if($row['etapaN']): ?>
                                                <span class="label label-et"><?= $row['etapaN']; ?></span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <span class="label label-success">$<?= number_format($row['monto'],0,'.',','); ?></span>
                                    </p>
                                    <div class="actions"> 
                                        <a class="view" href="javascript:;"><i class="bi bi-caret-down-fill"></i></a> 
                                    </div>
                                    <p>
                                        <strong>Fecha de Cierre Documental:</strong> 
                                        <span><?= !empty($row['fecha_cierre_documental']) ? $row['fecha_cierre_documental'] : 'Sin Asignar'; ?></span>
                                        <strong style="margin-left:5px">Fecha Adjudicacion:</strong>
                                        <span><?= !empty($row['fecha_adjudicacion']) ? $row['fecha_adjudicacion'] : 'Sin Asignar'; ?></span>
                                    </p>
                                    <p>
                                        <strong>Ciudad:</strong> <span><?= $row['ciudadN']; ?></span>
                                        <strong style="margin-left:5px">Cliente:</strong> <span><?= !empty($row['clienteN']) ? $row['clienteN'] : 'Sin Asignar'; ?></span>
                                        <strong style="margin-left:5px">Ingeniero Responsable:</strong> <span><?= $row['ingeniero'] ?: 'Sin asignar'; ?></span>
                                    </p>
                                </div>

                                <!-- Vista completa -->
                                <div class="grid-body no-border" style="display:none">
                                    <hr>
                                    <div class="post">
                                        <div class="info-wrapper">
                                            <div class="info d-flex">
                                                <div class="left-bar"></div>
                                                <div class="main-info">
                                                    <!-- Datos básicos -->
                                                    <div class="pr-row">
                                                        <div class="group">
                                                            <strong>Ciudad:</strong> 
                                                            <p><?= $row['ciudadN']; ?></p>
                                                        </div>
                                                        <div class="group">
                                                            <strong>Cliente:</strong>
                                                            <p><?= $row['clienteN'] ?: 'Sin Asignar'; ?></p>
                                                        </div>
                                                        <div class="group">
                                                            <strong>Ingeniero:</strong>
                                                            <p><?= $row['ingeniero'] ?: 'Sin Asignar'; ?></p>
                                                        </div>
                                                        <div class="group">
                                                            <strong>Comercial:</strong>
                                                            <p><?= $row['comercial'] ?: 'Sin Asignar'; ?></p>
                                                        </div>
                                                        <div class="group">
                                                            <strong>Distribuidor:</strong>
                                                            <p><?= $row['distribuidorN'] ?: 'Sin Asignar'; ?></p>
                                                        </div>
                                                        <div class="group">
                                                            <strong>Tipo de Proyecto:</strong>
                                                            <p><?= $row['tipoP'] ?: 'Sin Asignar'; ?></p>
                                                        </div>
                                                    </div>

                                                    <!-- Actividades -->
                                                    <div class="pr-row">
                                                        <div class="group">
                                                            <strong>Actividades</strong>
                                                            <ul>
                                                                <?php
                                                                if($row['actividades']){
                                                                    $acts = explode(';;', $row['actividades']);
                                                                    foreach($acts as $act){
                                                                        [$nombre, $fecha] = explode('||',$act);
                                                                        $fecha_form = strftime('%e de %B %Y, %H:%M', strtotime($fecha));
                                                                        echo "<li>$nombre -- $fecha_form</li>";
                                                                    }
                                                                } else echo "<li>Sin tareas asignadas</li>";
                                                                ?>
                                                            </ul>
                                                        </div>
                                                    </div>

                                                    <!-- Contactos -->
                                                    <div class="pr-row">
                                                        <div class="group">
                                                            <strong>Contactos</strong>
                                                            <?php
                                                            if($row['contactos']){
                                                                $cts = explode(';;', $row['contactos']);
                                                                foreach($cts as $c){
                                                                    [$nombre, $correo, $cargo, $numero] = explode('||',$c);
                                                                    echo "<div class='contacto-item'>
                                                                            <strong>$nombre</strong> - $correo - $cargo - $numero
                                                                        </div>";
                                                                }
                                                            } else echo "<p>No hay contactos</p>";
                                                            ?>
                                                        </div>
                                                    </div>

                                                    <!-- BOM -->
                                                    <div class="pr-row">
                                                        <div class="group">
                                                            <strong>BOM</strong>
                                                            <?php
                                                            $totalBOM = 0;
                                                            if($row['bom']){
                                                                $boms = explode(';;',$row['bom']);
                                                                echo "<ul>";
                                                                foreach($boms as $b){
                                                                    [$nombre, $cantidad, $total] = explode('||',$b);
                                                                    $totalBOM += $total;
                                                                    echo "<li>$nombre x$cantidad - $".number_format($total,0,'.',',')."</li>";
                                                                }
                                                                echo "</ul>";
                                                            } else echo "<p>No se han asignado materiales</p>";
                                                            ?>
                                                            <strong>Total BOM:</strong> $<?= number_format($totalBOM,0,'.',','); ?>
                                                        </div>
                                                    </div>

                                                    <!-- Licitación (si aplica) -->
                                                    <?php if($row['tipoP'] == 1 && $row['licitacion_id']): ?>
                                                        <div class="pr-row">
                                                            <strong>ID Licitación:</strong> <?= $row['licitacion_id']; ?>
                                                            <strong>Portal:</strong> <?= $row['portal_id'] ?: 'Sin asignar'; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!------------------>
                            </div>
                        </div>
                    </div>
                <?php }
                ?>
                <!-- PAGINACIÓN -->
               <?php if ($totalPages > 1): ?>
                <nav aria-label="Paginación de proyectos">
                    <ul class="pagination justify-content-center mt-4">
                        <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">
                                &laquo; Anterior
                            </a>
                        </li>
                        <?php endif; ?>

                        <?php
                        $startPage = max(1, $page - 2);
                        $endPage   = min($totalPages, $startPage + 4);

                        if ($endPage - $startPage < 4) {
                            $startPage = max(1, $endPage - 4);
                        }

                        for ($i = $startPage; $i <= $endPage; $i++): ?>
                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">
                                Siguiente &raquo;
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <?php endif; 

            } else { ?>
                <h3 align="center" style="color:red;">Sin proyectos que mostrar</h3>
            <?php } ?>
        </div>
    </div>
  </div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
<script src="../assets/js/support_ticket.js" type="text/javascript"></script>
<script src="../assets/js/general.js"></script>
<script src="../assets/js/sidebar.js"></script>
<script src="assets/js/view-projects.js"></script>
</body>
</html>