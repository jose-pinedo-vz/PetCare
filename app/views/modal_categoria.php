<dialog class="categoria-dialog" id="modal-nueva-categoria" aria-labelledby="titulo-modal-categoria">
  <div class="categoria-dialog-encabezado">
    <h2 id="titulo-modal-categoria">Nueva categoría</h2>
    <button type="button" class="categoria-dialog-cerrar" data-cerrar-modal-categoria aria-label="Cerrar">×</button>
  </div>

  <div class="categoria-dialog-contenido">
    <div class="categoria-dialog-grid">
      <div>
        <label class="label-campo" for="modal-id-categoria">Id categoría:</label>
        <input class="campo" id="modal-id-categoria" type="text" value="Automático" disabled>
      </div>

      <div>
        <label class="label-campo" for="modal-nombre-categoria">Nombre:</label>
        <input class="campo" id="modal-nombre-categoria" type="text" placeholder="Nombre de la categoría">
      </div>

      <div class="categoria-dialog-completo">
        <label class="label-campo" for="modal-descripcion-categoria">Descripción:</label>
        <textarea class="campo" id="modal-descripcion-categoria" rows="3" placeholder="Descripción de la categoría"></textarea>
      </div>

      <div>
        <label class="label-campo" for="modal-estado-categoria">Estado:</label>
        <select class="campo" id="modal-estado-categoria">
          <option value="Activo">Activo</option>
          <option value="Inactivo">Inactivo</option>
        </select>
      </div>

    </div>

    <p class="categoria-dialog-nota">La creación de categorías aún no está habilitada.</p>

    <div class="categoria-dialog-acciones">
      <button type="button" class="btn-secundario btn-chico" data-cerrar-modal-categoria>Cancelar</button>
      <button type="button" class="btn btn-chico" disabled>Guardar categoría</button>
    </div>
  </div>
</dialog>
