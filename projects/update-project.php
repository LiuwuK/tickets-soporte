<?php
session_start();
include("../checklogin.php");
include("../dbconnection.php");
include("assets/php/update-project.php");
check_login();

// Función helper para generar opciones de select
function renderOptions($array, $selectedValue = null, $valueKey = 'id', $textKey = 'nombre') {
    if (!empty($array)) {
        foreach ($array as $row) {
            $selected = ($selectedValue !== null && $row[$valueKey] == $selectedValue) ? 'selected' : '';
            echo "<option value=\"{$row[$valueKey]}\" $selected>" . htmlspecialchars($row[$textKey]) . "</option>";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Editar Proyecto</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- CSS personalizados -->
    <link href="../assets/css/sidebar.css" rel="stylesheet" />
    <link href="assets/css/create-project.css" rel="stylesheet" />
    <!-- Toast notificaciones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
</head>
<body class="test">
<div class="page-container">
    <div class="sidebar"><?php include("../header-test.php"); ?></div>
    <div class="page-content"><?php include("../leftbar-test.php"); ?>
        <div class="content">
            <div class="page-title d-flex justify-content-between">
                <h2><i class="bi bi-clipboard2"></i> Editar Proyecto</h2>
                <button class="btn btn-back" onclick="window.location.href='view-projects.php';"><i class="bi bi-arrow-left"></i></button>
            </div>

            <form name="form" id="updtProject" method="post">
                <?php
                $row_p = $projectData;
                $fecha_cierre = $row_p['fecha_cierre_documental'];
                $fecha_adjudicacion = $row_p['fecha_adjudicacion'];
                $fecha_finCt = $row_p['fecha_fin_contrato'];
                ?>
                <div id="loading" style="display:none;">
                    <div class="loading-spinner"></div>
                    <p>Procesando...</p>
                </div>

                <div class="project-main"><br><br>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Nombre Proyecto</label>
                            <input type="text" class="form-control form-control-sm" name="name" value="<?= htmlspecialchars($row_p['nombre']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Cliente</label>
                            <select name="client" id="client" class="form-select form-select-sm" required>
                                <?php renderOptions($clientsArray, $row_p['cliente'], 'id', 'nombre'); ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Tipo de proyecto</label>
                            <select name="pType" id="pType" class="form-select form-select-sm" disabled>
                                <?php renderOptions($typesArray, $row_p['tipo'], 'id', 'nombre'); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Clasificación</label>
                            <select name="pClass" id="pClass" class="form-select form-select-sm">
                                <option value="">Seleccionar</option>
                                <?php renderOptions($classArray, $row_p['clasificacion'], 'id', 'nombre'); ?>
                            </select>
                        </div>
                    </div>

                    <?php if(isset($licData)): ?>
                    <div class="form-row mt-3">
                        <div class="form-group">
                            <label class="form-label">ID Licitación</label>
                            <input type="text" class="form-control form-control-sm" name="licID" value="<?= htmlspecialchars($licData['licitacion_id']); ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Portal</label>
                            <select name="portal" class="form-select form-select-sm" required>
                                <option value="">Sin asignar</option>
                                <?php renderOptions($portalArray, $licData['portal'], 'id', 'nombre_portal'); ?>
                            </select>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Ciudad</label>
                            <select name="city" class="form-select form-select-sm" required>
                                <?php renderOptions($citiesArray, $row_p['ciudad'], 'id', 'nombre_ciudad'); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Vertical</label>
                            <select name="vertical" class="form-select form-select-sm">
                                <option value="">Sin asignar</option>
                                <?php renderOptions($verticalArray, $row_p['vertical'], 'id', 'nombre'); ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Ingeniero responsable</label>
                            <select name="ingeniero" class="form-select form-select-sm">
                                <option value="">Sin asignar</option>
                                <?php renderOptions($ingeArray, $row_p['ingeniero_responsable'], 'id', 'name'); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Distribuidor</label>
                            <select name="dist" class="form-select form-select-sm">
                                <option value="">Sin asignar</option>
                                <?php renderOptions($distribuidorArray, $row_p['distribuidor'], 'id', 'nombre'); ?>
                            </select>
                        </div>
                    </div>

                    <?php if($row_p['estado_id'] == 21): ?>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Competidor</label>
                            <select name="competidor" class="form-select form-select-sm">
                                <option value="">Seleccionar</option>
                                <?php renderOptions($competidoresArray, $row_p['competidor'], 'id', 'nombre_competidor'); ?>
                            </select>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Estado</label>
                            <select name="status" class="form-select form-select-sm">
                                <?php renderOptions($statusArray, $row_p['estado_id'], 'id', 'nombre'); ?>
                            </select>
                        </div>
                        <?php if($row_p['estado_id'] == 19): ?>
                        <div class="form-group">
                            <label class="form-label">Etapa</label>
                            <select name="etapaEst" class="form-select form-select-sm">
                                <?php renderOptions($etData, $row_p['estado_etapa'], 'id', 'nombre_etapa'); ?>
                            </select>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Fechas y monto -->
                    <div class="form-row">
                        <div class="form-group">
                            <label>Monto Proyecto</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text">$</span>
                                <input name="montoP" type="number" class="form-control form-control-sm" value="<?= htmlspecialchars($row_p['monto']); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Fecha de Cierre Documental</label>
                            <input name="cierreDoc" type="date" class="form-control form-control-sm" value="<?= htmlspecialchars($fecha_cierre); ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Fecha de Adjudicación</label>
                            <input name="fAdj" type="date" class="form-control form-control-sm" value="<?= htmlspecialchars($fecha_adjudicacion); ?>">
                        </div>
                        <div class="form-group">
                            <label>Fecha fin de Contrato</label>
                            <input name="finContrato" type="date" class="form-control form-control-sm" value="<?= htmlspecialchars($fecha_finCt); ?>">
                        </div>
                    </div>

                    <!-- Actividades -->
                    <div class="form-row">
                        <div class="form-group">
                            <div class="d-flex justify-content-between align-items-center">
                                <label>Actividades</label>
                                <button type="button" class="btn btn-add-task" data-bs-toggle="modal" data-bs-target="#actividadModal">
                                    <i class="bi bi-calendar-plus"></i>
                                </button>
                            </div>
                            <div id="events-list">
                                <ul id="listadoActividades" class="list-group">
                                    <?php if(isset($actividades)):
                                        foreach($actividades as $actividad):
                                            $fInicio = strftime('%e de %B, %H:%M', strtotime($actividad['fecha_inicio']));
                                            $fTermino = strftime('%e de %B, %H:%M', strtotime($actividad['fecha_termino']));
                                    ?>
                                    <li class="list-group-item">
                                        <h6><?= htmlspecialchars($actividad['nombre']); ?></h6>
                                        <p><?= $fInicio; ?> - <?= $fTermino; ?></p>
                                        <p><?= htmlspecialchars($actividad['descripcion']); ?></p>
                                    </li>
                                    <?php endforeach; endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Resumen -->
                    <div class="form-row">
                        <div class="form-group">
                            <label>Resumen</label>
                            <textarea class="form-control form-control-sm" name="desc" rows="4"><?= htmlspecialchars($row_p['resumen']); ?></textarea>
                        </div>
                    </div>

                    <div class="footer">
                        <button type="reset" class="btn btn-reset">Resetear</button>
                        <button type="submit" name="updtProject" class="btn btn-primary">Actualizar</button>
                    </div>
                </div>
            </form>

            <!-- Modal Actividades -->
            <div class="modal fade" id="actividadModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form id="formActividad">
                        <div class="modal-header">
                            <h5 class="modal-title">Agregar Actividad</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label>Nombre de la Actividad</label>
                                <input type="text" name="nombreActividad" class="form-control form-control-sm" required>
                            </div>
                            <div class="form-row d-flex gap-2 mb-3">
                                <div class="form-group">
                                    <label>Fecha inicio</label>
                                    <input type="datetime-local" name="fechaInicio" class="form-control form-control-sm" required>
                                </div>
                                <div class="form-group">
                                    <label>Fecha termino</label>
                                    <input type="datetime-local" name="fechaTermino" class="form-control form-control-sm" required>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label>Tipo Actividad</label>
                                <select name="areaAct" class="form-select form-select-sm" required>
                                    <option value="">Seleccionar</option>
                                    <?php renderOptions($cargosArray, null, 'id', 'nombre'); ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Descripción</label>
                                <textarea name="descripcionActividad" class="form-control form-control-sm" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Agregar</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Bootstrap 5 JS y dependencias -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<!-- Toastr notificaciones -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="assets/js/create-project.js"></script>
<script src="assets/js/updt-project.js"></script>
</body>
</html>
