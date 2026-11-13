<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['csrf_categoria'])) {
    $_SESSION['csrf_categoria'] = bin2hex(random_bytes(32));
}

$erroresCategoria = isset($erroresCategoria) && is_array($erroresCategoria)
    ? $erroresCategoria
    : (is_array($_SESSION['errores_categoria'] ?? null) ? $_SESSION['errores_categoria'] : []);
$oldCategoria = isset($oldCategoria) && is_array($oldCategoria)
    ? $oldCategoria
    : (is_array($_SESSION['old_categoria'] ?? null) ? $_SESSION['old_categoria'] : []);
?>
<dialog class="categoria-dialog" id="modal-nueva-categoria" aria-labelledby="titulo-modal-categoria" data-csrf="<?= htmlspecialchars($_SESSION['csrf_categoria'], ENT_QUOTES, 'UTF-8') ?>">
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
        <input class="campo" id="modal-nombre-categoria" type="text" maxlength="50" placeholder="Nombre de la categoría" value="<?= htmlspecialchars($oldCategoria['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
      </div>

      <div class="categoria-dialog-completo">
        <label class="label-campo" for="modal-descripcion-categoria">Descripción:</label>
        <textarea class="campo" id="modal-descripcion-categoria" rows="3" maxlength="255" placeholder="Descripción de la categoría"><?= htmlspecialchars($oldCategoria['descripcion'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
      </div>

      <div>
        <label class="label-campo" for="modal-estado-categoria">Estado:</label>
        <select class="campo" id="modal-estado-categoria">
          <option value="Activo" <?= ($oldCategoria['estado'] ?? 'Activo') === 'Activo' ? 'selected' : '' ?>>Activo</option>
          <option value="Inactivo" <?= ($oldCategoria['estado'] ?? '') === 'Inactivo' ? 'selected' : '' ?>>Inactivo</option>
        </select>
      </div>
    </div>

    <div class="msg-error" id="errores-modal-categoria" role="alert" aria-live="assertive" <?= empty($erroresCategoria) ? 'hidden' : '' ?>>
      <h4>Por favor corrige los siguientes errores:</h4>
      <ul id="lista-errores-modal-categoria">
        <?php
        foreach ($erroresCategoria as $error) {
            echo '<li>' . htmlspecialchars((string)$error, ENT_QUOTES, 'UTF-8') . '</li>';
        }
        ?>
      </ul>
    </div>
    <p class="categoria-dialog-nota" id="mensaje-modal-categoria" role="status" aria-live="polite" hidden></p>

    <div class="categoria-dialog-acciones">
      <button type="button" class="btn-secundario btn-chico" data-cerrar-modal-categoria>Cancelar</button>
      <button type="button" class="btn btn-chico" id="btn-guardar-categoria">Guardar categoría</button>
    </div>
  </div>
</dialog>
