<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['csrf_hotel'])) {
    $_SESSION['csrf_hotel'] = bin2hex(random_bytes(32));
}
?>
<dialog class="jaula-dialog" id="modal-nueva-jaula" aria-labelledby="titulo-modal-jaula" data-csrf="<?= htmlspecialchars($_SESSION['csrf_hotel'], ENT_QUOTES, 'UTF-8') ?>">
    <div class="jaula-dialog-encabezado">
        <h2 id="titulo-modal-jaula">Agregar jaula</h2>
        <button type="button" class="jaula-dialog-cerrar" data-cerrar-modal-jaula aria-label="Cerrar">×</button>
    </div>
    <div class="jaula-dialog-contenido">
        <label class="label-campo" for="modal-tamano-jaula">Tamaño:</label>
        <select class="campo" id="modal-tamano-jaula" required>
            <option value="">Selecciona el tamaño</option>
            <option value="chico">Chico</option>
            <option value="mediano">Mediano</option>
            <option value="grande">Grande</option>
        </select>

        <div class="msg-error" id="errores-modal-jaula" role="alert" aria-live="assertive" hidden>
            <h4>No fue posible registrar la jaula:</h4>
            <ul id="lista-errores-modal-jaula"></ul>
        </div>
        <p class="jaula-dialog-nota" id="mensaje-modal-jaula" role="status" aria-live="polite" hidden></p>

        <div class="jaula-dialog-acciones">
            <button type="button" class="btn-secundario btn-chico" data-cerrar-modal-jaula>Cancelar</button>
            <button type="button" class="btn btn-chico" id="btn-guardar-jaula">Guardar jaula</button>
        </div>
    </div>
</dialog>
