<div class="form-data col-md-10 mx-auto">
  <h3>Desvinculaciones</h3>
  <?php if ($num_des > 0): ?>
    <button class="btn btn-updt" onclick="window.location.href='assets/php/excel-desvinculaciones.php';">Descargar</button>

    <?php while ($row = $desvinculaciones->fetch_assoc()): ?>
      <div class="mt-3 card col-md-11 mx-auto p-3 card-t">
        <div class="colab-data">
          <strong>Colaborador: <?= htmlspecialchars($row['colaborador']) ?></strong>
          <p>Solicitante: <?= htmlspecialchars($row['soliN']) ?></p>
          <p>Fecha: <?= date("Y-m-d H:i", strtotime($row['fecha_registro'])) ?></p>
          <?php if ($usRol == '11'): ?>
            <p>Estado: <?= htmlspecialchars($row['estado']) ?></p>
          <?php else: ?>
            <p>Estado:
              <select class="form-select form-select-sm desv-select" data-id="<?= $row['id'] ?>">
                <option value="En gestión" <?= $row['estado']=='En gestión'?'selected':'' ?>>En gestión</option>
                <option value="Realizado" <?= $row['estado']=='Realizado'?'selected':'' ?>>Realizado</option>
                <option value="Anulado" <?= $row['estado']=='Anulado'?'selected':'' ?>>Anulado</option>
              </select>
            </p>
          <?php endif; ?>
        </div>

        <div class="origen-data">
          <strong>Instalación Origen:
            <?= !empty($row['sucN']) ? htmlspecialchars($row['sucN']) : htmlspecialchars($row['instalacion']) ?>
          </strong>
          <p>Supervisor: <?= htmlspecialchars($row['supN']) ?></p>
          <p>Razón Social: <?= htmlspecialchars($row['razon'] ?? 'Sin razón social') ?></p>
          <p>Rol: <?= !empty($row['rolN']) ? htmlspecialchars($row['rolN']) : 'Sin Asignar' ?></p>
        </div>

        <div class="destino-data">
          <strong>Motivo de Egreso: <?= htmlspecialchars($row['motivoEgreso']) ?></strong>
          <p>Observación: <?= !empty($row['observacion']) ? htmlspecialchars($row['observacion']) : 'No tiene observación' ?></p>
        </div>

        <?php if ($_SESSION['id'] == 38): ?>
          <div class="delete-btns">
            <button class="btn btn-del del-btn" data-bs-toggle="modal" data-bs-target="#delDesv" data-id="<?= $row['id'] ?>">Eliminar</button>
          </div>
        <?php endif; ?>
      </div>
    <?php endwhile; ?>

  <?php else: ?>
    <p>No hay desvinculaciones registradas el dia de hoy</p>
  <?php endif; ?>
</div>
