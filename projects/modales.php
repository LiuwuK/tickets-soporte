<!-- Modal Actividades -->
<div class="modal fade" id="actividadModal" tabindex="-1" aria-labelledby="actividadModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="actividadModalLabel">Agregar Actividad</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formActividad">
                    <div class="mb-3">
                        <label for="nombreActividad" class="form-label">Nombre de la Actividad</label>
                        <input type="text" class="form-control form-control-sm" id="nombreActividad" name="nombreActividad" required>
                    </div>
                    <div class="form-row-modal mb-3 d-flex">
                        <div class="form-group me-2">
                            <label for="fechaInicio" class="form-label">Fecha inicio</label>
                            <input type="datetime-local" class="form-control form-control-sm" id="fechaInicio" name="fechaInicio" required>
                        </div>
                        <div class="form-group">
                            <label for="fechaTermino" class="form-label">Fecha termino</label>
                            <input type="datetime-local" class="form-control form-control-sm" id="fechaTermino" name="fechaTermino" required>
                        </div>
                    </div>
                    <div class="form-row-modal mb-3 d-flex">
                        <div class="form-group">
                            <label class="form-label">Tipo Actividad</label>
                            <select name="areaAct" id="areaAct" class="form-select form-select-sm" required>
                                <option value="">Seleccionar</option>
                                <?php foreach ($catalogs['cargos'] as $row) : ?>
                                    <option value="<?= $row['id'] ?>"><?= $row['nombre'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="descripcionActividad" class="form-label">Descripción</label>
                        <textarea class="form-control form-control-sm" id="descripcionActividad" name="descripcionActividad" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-reset" data-bs-dismiss="modal">Cerrar</button>
                <button type="submit" form="formActividad" class="btn pull-right">Agregar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Contactos -->
<div class="modal fade" id="contactoModal" tabindex="-1" aria-labelledby="contactoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="contactoModalLabel">Agregar Contacto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formContacto">
                    <div class="form-row-modal">
                        <div class="form-group me-2">
                            <label for="cName" class="form-label">Nombre</label>
                            <input type="text" class="form-control form-control-sm mb-3" id="cName" name="cName">
                            <label for="cEmail" class="form-label">Email</label>
                            <input type="email" class="form-control form-control-sm" id="cEmail" name="cEmail">
                        </div>
                        <div class="form-group">
                            <label for="cargo" class="form-label">Cargo</label>
                            <input type="text" class="form-control form-control-sm mb-3" id="cargo" name="cargo">
                            <label for="cNumero" class="form-label">Número de contacto</label>
                            <input type="text" class="form-control form-control-sm" id="cNumero" name="cNumero">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-reset" data-bs-dismiss="modal">Cerrar</button>
                <button type="submit" form="formContacto" class="btn pull-right">Agregar</button>
            </div>
        </div>
    </div>
</div>
