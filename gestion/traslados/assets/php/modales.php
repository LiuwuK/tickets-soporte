<!-- Modal eliminar traslado -->
<div class="modal fade" id="delTraslado" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Eliminar Traslado</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        ¿Está seguro de eliminar este traslado?
      </div>
      <div class="modal-footer">
        <form method="post" action="assets/php/traslados-back.php">
          <input type="hidden" name="del_id" value="">
          <button type="submit" name="delTraslado" class="btn btn-del den-btn">Eliminar</button>
        </form>
        <button type="button" class="btn btn-updt" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal eliminar desvinculación -->
<div class="modal fade" id="delDesv" tabindex="-1" aria-hidden="true">
  <div class="modal-d
