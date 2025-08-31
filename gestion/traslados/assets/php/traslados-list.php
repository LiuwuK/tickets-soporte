<div class="form-data col-md-10 mx-auto">
  <h3>Traslados</h3>
  <?php if ($num > 0): ?>
    <button class="btn btn-updt" onclick="window.location.href='assets/php/excel-traslados.php';">Descargar</button>

    <?php while ($row = $traslados->fetch_assoc()): ?>
      <div class="mt-3 card col-md-11 mx-auto p-3 card-t">
        <div class="colab-data">
          <strong>Colaborador: <?= htmlspecialchars($row['nombre_colaborador']) ?></strong>
          <p>Solicitante: <?= htmlspecialchars($row['soliN']) ?></p>
          <p>Fecha: <?= date("Y-m-d H:i", strtotime($row['fecha_registro'])) ?></p>
          <?php if ($usRol == '11'): ?>
            <p>Estado: <?= htmlspecialchars($row['estado']) ?></p>
          <?php else: ?>
            <p>Estado:
              <select class="form-select form-select-sm estado-select" data-id="<?= $row['id'] ?>">
                <option value="En gestión" <?= $row['estado']=='En gestión'?'selected':'' ?>>En gestión</option>
                <option value="Realizado" <?= $row['estado']=='Realizado'?'selected':'' ?>>Realizado</option>
                <option value="Anulado" <?= $row['estado']=='Anulado'?'selected':'' ?>>Anulado</option>
              </select>
            </p>
          <?php endif; ?>
        </div>

        <div class="origen-data">
          <strong>Instalacion de Origen:
            <?= !empty($row['inOrigen_nombre']) ? htmlspecialchars($row['inOrigen_nombre']) : htmlspecialchars($row['suOrigen']) ?>
          </strong>
          <p>Supervisor: <?= htmlspecialchars($row['supOrigen']) ?></p>
          <p>Razón Social: <?= htmlspecialchars($row['raOrigen'] ?? 'Sin razón social') ?></p>
          <p>Jornada Origen: <?= htmlspecialchars($row['joOrigen']) ?></p>
          <p>Rol: <?= htmlspecialchars($row['rolOrigen']) ?></p>
        </div>

        <div class="destino-data">
          <strong>Instalacion de Destino:
            <?= !empty($row['inDestino_nombre']) ? htmlspecialchars($row['inDestino_nombre']) : htmlspecialchars($row['suDestino']) ?>
          </strong>
          <p>Supervisor: <?= htmlspecialchars($row['supDestino']) ?></p>
          <p>Razón Social: <?= htmlspecialchars($row['raDestino'] ?? 'Sin razón social') ?></p>
          <p>Jornada Destino: <?= htmlspecialchars($row['joDestino']) ?></p>
          <p>Rol: <?= htmlspecialchars($row['rolDestino']) ?></p>
        </div>

        <?php if ($_SESSION['id'] == 38): ?>
          <div class="delete-btns">
            <button class="btn btn-del del-btn" data-bs-toggle="modal" data-bs-target="#delTraslado" data-id="<?= $row['id'] ?>">Eliminar</button>
          </div>
        <?php endif; ?>
      </div>
    <?php endwhile; ?>

  <?php else: ?>
    <p>No hay traslados registrados el dia de hoy</p>
  <?php endif; ?>
</div>
