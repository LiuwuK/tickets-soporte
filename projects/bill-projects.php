<?php
session_start();
include("../checklogin.php");
include("../dbconnection.php");
check_login();
include("assets/php/bill-projects.php");
header('Content-Type: text/html; charset=utf-8');


?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Proyectos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="../assets/css/sidebar.css" rel="stylesheet" />
    <link href="../tickets/assets/css/manage_tickets.css" rel="stylesheet"/>
    <link href="assets/css/view-projects.css" rel="stylesheet"/>
</head>
<body class="test">
<div class="page-container">
    <div class="sidebar"><?php include("../header-test.php"); ?></div>
    <div class="page-content">
        <?php include("../leftbar-test.php"); ?>
        <div class="content">
            <div class="page-title d-flex justify-content-between">
                <h2>Proyectos por facturar</h2>
                <button class="btn-back" onclick="window.location.href='projects-main.php';"><i class="bi bi-arrow-left"></i></button>
            </div>

            <!-- Filtros -->
            <form method="GET" class="mt-3 d-flex gap-2 flex-wrap align-items-end">
                <input type="text" class="form-control form-control-sm" name="textSearch" placeholder="Nombre/ID" value="<?= htmlspecialchars($_GET['textSearch'] ?? '') ?>">

                <select name="verticalF" class="form-select form-select-sm">
                    <option value="">Vertical (Todo)</option>
                    <?php foreach($verticales as $v): ?>
                        <option value="<?= $v['id'] ?>" <?= (($_GET['verticalF'] ?? '') == $v['id'])?'selected':'' ?>><?= $v['nombre'] ?></option>
                    <?php endforeach; ?>
                </select>

                <select name="distribuidorF" class="form-select form-select-sm">
                    <option value="">Distribuidor (Todo)</option>
                    <?php foreach($distData as $d): ?>
                        <option value="<?= $d['id'] ?>" <?= (($_GET['distribuidorF'] ?? '') == $d['id'])?'selected':'' ?>><?= $d['nombre'] ?></option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
            </form>

            <br>

            <?php if ($num > 0): while ($row = $rt->fetch_assoc()): ?>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="grid simple no-border">
                            <!-- Vista cerrada -->
                            <div class="grid-title no-border descriptive clickable">
                                <h4 class="semi-bold"><?= htmlspecialchars($row['nombre']) ?></h4>
                                <p>
                                    <span class="text-success bold">Proyecto #<?= $row['id'] ?></span> - Fecha Creación <?= $row['fecha_creacion'] ?>
                                    <span class="label <?= $row['estado_id']==20?'label-success':($row['estado_id']==21?'label-important':'label-warning') ?>"><?= $row['estado'] ?></span>
                                    <span class="label label-success">$<?= number_format($row['monto'],0,'.',',') ?></span>
                                </p>
                                <p>Ciudad: <?= $row['ciudadN'] ?> | Distribuidor: <?= $row['distribuidorN'] ?> | Ingeniero: <?= $row['ingeniero'] ?: 'Sin asignar' ?></p>
                                <div class="actions"><a class="view" href="javascript:;"><i class="bi bi-caret-down-fill"></i></a></div>
                            </div>

                            <!-- Vista completa -->
                            <div class="grid-body no-border" style="display:none">
                                <hr>
                                <h5>Lista de materiales</h5>
                                <?php 
                                $total = 0;
                                if (!empty($boms[$row['id']])): 
                                    foreach($boms[$row['id']] as $mat):
                                        $total += $mat['total']; ?>
                                        <div class="materials list-group-item d-flex justify-content-between">
                                            <span><?= htmlspecialchars($mat['nombre']) ?> x<?= $mat['cantidad'] ?></span>
                                            <span>$<?= number_format($mat['total'],0,'.',',') ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                    <div class="material-total">Total: $<?= number_format($total,0,'.',',') ?></div>
                                <?php else: ?>
                                    <p>Aun no tiene materiales asociados</p>
                                <?php endif; ?>
                                <div class="mt-2 text-end">
                                    <button class="btn btn-updt" data-bs-toggle="modal" data-bs-target="#closeModal" data-id="<?= $row['id'] ?>">Facturar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; else: ?>
                <h3 class="text-center text-danger">Sin proyectos que mostrar</h3>
            <?php endif; ?>

        </div>
    </div>
</div>

<!-- Modal facturar proyecto -->
<div class="modal fade" id="closeModal" tabindex="-1">
    <form id="endBtn" method="post">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-center">El proyecto # será facturado</p>
                    <input type="hidden" name="pId">
                </div>
                <div class="modal-footer">
                    <button type="submit" name="billBtn" class="btn btn-updt">Facturar</button>
                    <button type="button" class="btn btn-del" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/general.js"></script>
<script src="assets/js/bill-projects.js"></script>
</body>
</html>
