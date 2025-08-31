<form method="POST" enctype="multipart/form-data">
  <input type="hidden" name="tipo" value="traslado">

  <div class="col-md-10 form-data mx-auto" id="trasladoForm" style="display:none;">
    <h3>Formulario de Traslado</h3>

    <div class="form-row mx-auto">
        <div class="form-group">
            <label class="form-label">Instalación de Origen <span>*</span></label>
            <select name="inst_origen" id="inst_origen" class="form-select" required>
            <option value="">Seleccionar</option>
            <?php foreach($datos['sucursales'] as $s): ?>
                <option value="<?= $s['id']; ?>" data-supervisor="<?= htmlspecialchars($s['supervisor_nombre']); ?>" data-supervisorID="<?= htmlspecialchars($s['supervisorID']); ?>">
                <?= htmlspecialchars($s['nombre']); ?>
                </option>
            <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="form-row mx-auto">
        <div class="form-group">
            <label class="form-label">Nombre Colaborador <span>*</span></label>
            <input type="text" name="colaborador" class="form-control " required>
        </div>
        <div class="form-group">
            <label class="form-label">Rut <span>*</span></label>
            <input type="text" name="rut" id="rut" class="form-control " maxlength="12" required>
        </div>
    </div>

    <div class="form-row mx-auto">
        <div class="form-group">
            <label class="form-label">Rol de Origen <span>*</span></label>
            <select name="rol_origen" class="form-select  search-form" required>
                <option value="">Seleccionar</option>
                <?php foreach ($datos['roles'] as $row): ?>
                <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['nombre']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">Jornada de Origen <span>*</span></label>
            <select name="jor_origen" class="form-select" required>
            <option value="">Seleccionar</option>
            <?php foreach($datos['jornadas'] as $j): ?>
                <option value="<?= $j['id']; ?>"><?= htmlspecialchars($j['nombre']); ?></option>
            <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="form-row mx-auto">
        <div class="form-group">
            <label class="form-label">Supervisor Origen</label>
            <input type="text" id="sup_origen" class="form-control " readonly required>
            <input type="text" name="sup_origen_ID" id="sup_origen_ID" class="form-control " hidden>
        </div>

        <div class="form-group">
            <label class="form-label">Fecha de Inicio de Turno <span>*</span></label>
            <input type="date" class="form-control form-control-md" name="fecha_inicio" required min="<?php echo date('Y-m-d'); ?>">
        </div>
    </div>

    <div class="form-row mx-auto">
        <div class="form-group">
            <label class="form-label">Instalación de Destino <span>*</span></label>
            <select name="inst_destino" id="inst_destino" class="form-select" required>
            <option value="">Seleccionar</option>
            <?php foreach($datos['sucursales'] as $s): ?>
                <option value="<?= $s['id']; ?>" data-supervisor="<?= htmlspecialchars($s['supervisor_nombre']); ?>" data-supervisorID="<?= htmlspecialchars($s['supervisorID']); ?>">
                <?= htmlspecialchars($s['nombre']); ?>
                </option>
            <?php endforeach; ?>
            </select>
        </div>
    </div>

    <!-- Supervisor Destino -->
    <div class="form-row mx-auto">
        <div class="form-group">
            <label class="form-label">Supervisor Destino</label>
            <input type="text" id="sup_destino" class="form-control " readonly required>
            <input type="text" name="sup_destino_ID" id="sup_destino_ID" class="form-control " hidden>
        </div>
        <div class="form-group">
            <label class="form-label">Jornada de Destino <span>*</span></label>
            <select name="jor_destino" class="form-select" required>
            <option value="">Seleccionar</option>
            <?php foreach($datos['jornadas'] as $j): ?>
                <option value="<?= $j['id']; ?>"><?= htmlspecialchars($j['nombre']); ?></option>
            <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="form-row mx-auto">
         <div class="form-group">
            <label class="form-label">Rol de Destino <span>*</span></label>
            <select name="rol_destino" class="form-select  search-form" required>
                <option value="">Seleccionar</option>
                <?php foreach ($datos['roles'] as $row): ?>
                <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['nombre']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">Motivo de traslado <span>*</span></label>
            <select name="motivo" class="form-select" required>
            <option value="">Seleccionar</option>
            <?php foreach($datos['motivos_gestion'] as $m):
                if($m['tipo_motivo']==='traslado'): ?>
                <option value="<?= $m['id']; ?>"><?= htmlspecialchars($m['nombre']); ?></option>
            <?php endif; endforeach; ?>
            </select>
        </div>
    </div>

    <div class="form-row mx-auto">
      <div class="form-group">
        <label class="form-label">Observación</label>
        <textarea name="observacion" class="form-control " rows="4"></textarea>
      </div>
    </div>

    <div class="footer">
      <button type="submit" class="btn btn-updt">Enviar</button>
    </div>
  </div>
</form>

<script>
document.getElementById('inst_origen').addEventListener('change', function(){
    const sup = this.selectedOptions[0].dataset.supervisor || '';
    const supID = this.selectedOptions[0].dataset.supervisorid ;
    document.getElementById('sup_origen').value = sup;
    document.getElementById('sup_origen_ID').value = supID;
});

document.getElementById('inst_destino').addEventListener('change', function(){
    const sup = this.selectedOptions[0].dataset.supervisor || '';
    const supID = this.selectedOptions[0].dataset.supervisorid ;
    document.getElementById('sup_destino').value = sup;
    document.getElementById('sup_destino_ID').value = supID;
});
</script>
