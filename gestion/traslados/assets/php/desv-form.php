<?php

$motivoE = array_filter($datos['motivos_gestion'], fn($m) => $m['tipo_motivo']==='egreso');
?>

<form method="POST" enctype="multipart/form-data">
  <input type="hidden" name="tipo" value="desvinculacion">

  <div class="col-md-10 form-data mx-auto" id="desvinculacionForm" style="display:none;">
    <h3>Formulario de Desvinculación</h3>

    <div class="form-row mx-auto">
      <div class="form-group">
        <label class="form-label">Instalación de Origen <span>*</span></label>
        <select name="instalacion" id="inst_origen_desv" class="form-select" required>
          <option value="">Seleccionar</option>
          <?php foreach( $datos['sucursales'] as $s): ?>
            <option value="<?= $s['id']; ?>" data-supervisor="<?= htmlspecialchars($s['supervisor_nombre']); ?>" data-supervisorID="<?= htmlspecialchars($s['supervisorID']); ?>">
              <?= htmlspecialchars($s['nombre']); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label class="form-label">Supervisor Encargado <span>*</span></label>
        <input type="text" name="supervisor" id="sup_origen_desv" class="form-control " readonly required>
        <input type="text" name="supervisorID" id="sup_origendesv_ID" class="form-control " hidden>
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
        <label class="form-label">Rol <span>*</span></label>
        <select name="rol" class="form-select" required>
          <option value="">Seleccionar</option>
          <?php foreach($datos['roles'] as $r): ?>
            <option value="<?= $r['id']; ?>"><?= htmlspecialchars($r['nombre']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label class="form-label">Motivo de Egreso <span>*</span></label>
        <select name="motivo" class="form-select" required>
          <option value="">Seleccionar</option>
          <?php foreach($motivoE as $m): ?>
            <option value="<?= $m['id']; ?>"><?= htmlspecialchars($m['nombre']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <div class="form-row mx-auto">
      <div class="form-group">
        <label class="form-label">Subir Archivos</label>
        <input type="file" name="desvDocs" class="form-control ">
      </div>
    </div>

    <div class="form-row mx-auto">
      <div class="form-group">
        <label class="form-label">Observación</label>
        <textarea name="observacion" class="form-control " rows="6"></textarea>
      </div>
    </div>

    <div class="footer">
      <button type="submit" name="desvForm" class="btn btn-updt">Enviar</button>
    </div>
  </div>
</form>

<script>
// Autocompletar supervisor según la instalación seleccionada
document.getElementById('inst_origen_desv').addEventListener('change', function(){
    const sup = this.selectedOptions[0].dataset.supervisor || '';
    const supID = this.selectedOptions[0].dataset.supervisorid ;
    document.getElementById('sup_origen_desv').value = sup;
    document.getElementById('sup_origendesv_ID').value = supID;
});
</script>
