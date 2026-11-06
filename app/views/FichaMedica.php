<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Esta vista se carga desde controllers/FichaMedicaController.php,
// asi que puede que estas variables no existan si se entra directo.
$mascotas             = $mascotas ?? [];
$veterinarios         = $veterinarios ?? [];
$idConsulta           = $idConsulta ?? 0;
$idVeterinario        = $idVeterinario ?? 1;
$radiografiasPrevias  = $radiografiasPrevias ?? [];

// Devuelve el valor viejo guardado en sesion (si hubo error) y lo borra
function old(string $clave): string
{
    $valor = (string)($_SESSION[$clave] ?? '');
    unset($_SESSION[$clave]);
    return htmlspecialchars($valor);
}

// El controlador usa 'error' (texto) en un lado y 'errores' (arreglo) en otro
$mensajeError = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
$listaErrores = $_SESSION['errores'] ?? [];
unset($_SESSION['errores']);
$mascotaVieja = old('oldMascota');
$veterinarioViejo = old('oldid_veterinario');
if ($veterinarioViejo === '' && $idVeterinario > 0) {
    $veterinarioViejo = (string)$idVeterinario;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Esta vista se muestra desde /controllers/, asi que las rutas relativas
       (css, imagenes, links del sidebar) se calculan desde /views/ -->
  <base href="../views/">
  <title>Veterinaria PetCare - Ficha veterinaria</title>
  <link rel="stylesheet" href="css/estilos_base_v2.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
</head>
<body>

  <div class="layout">

  <?php $paginaActiva = 'citas'; include __DIR__ . '/layout/sidebar.php'; ?>

  <main>
      <div class="migaja"><a href="citas.php" class="link-tenue">Gestión de Citas</a> / Ficha veterinaria</div>
      <div class="encabezado-pagina">
        <h1>Ficha veterinaria</h1>
      </div>

      <div class="tarjeta">

    <?php if ($mensajeError && empty($listaErrores)): ?>
      <div class="msg-error"><?= htmlspecialchars($mensajeError) ?></div>
    <?php endif; ?>

    <?php if (!empty($listaErrores)): ?>
      <div class="msg-error">
        <?php foreach ($listaErrores as $e): ?>
          <div>• <?= htmlspecialchars($e) ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form action="../controllers/FichaMedicaController.php" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="id_consulta" value="<?= (int)$idConsulta ?>">

      <fieldset>
        <legend>Datos de la consulta</legend>
        <div class="grid-formulario">
          <div>
            <label class="label-campo" for="mascota">Mascota *:</label>
            <select class="campo" name="mascota" id="mascota" required>
              <option value="">Selecciona una mascota</option>
              <?php foreach ($mascotas as $m): ?>
                <option value="<?= (int)$m['id'] ?>" <?= ((string)$m['id'] === (string)$mascotaVieja) ? 'selected' : '' ?>><?= htmlspecialchars($m['nombre']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label class="label-campo" for="id_veterinario">Veterinario *:</label>
            <select class="campo" name="id_veterinario" id="id_veterinario" required>
              <option value="">Selecciona un veterinario</option>
              <?php foreach ($veterinarios as $v): ?>
                <option value="<?= (int)$v['id'] ?>" <?= ((string)$v['id'] === (string)$veterinarioViejo) ? 'selected' : '' ?>><?= htmlspecialchars($v['nombre']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label class="label-campo" for="precio">Costo ($) *:</label>
            <input type="number" step="0.01" min="1" max="100000" class="campo" id="precio" name="precio"
              placeholder="Ingresa el costo (ej. 350.00)" required value="<?= old('oldPrecio') ?>">
          </div>
        </div>
      </fieldset>

      <fieldset>
        <legend>Datos del paciente</legend>
        <div class="grid-formulario">
          <div>
            <label class="label-campo" for="temperatura">Temperatura (°C) *:</label>
            <input type="number" class="campo" id="temperatura" name="temperatura" min="30" max="45" step="0.1"
              placeholder="Ej. 38.5" required value="<?= old('oldTemperatura') ?>">
          </div>

          <div>
            <label class="label-campo" for="peso">Peso (kg) *:</label>
            <input type="number" class="campo" id="peso" name="peso" min="0.1" max="150" step="0.1"
              placeholder="Ej. 10.5" required value="<?= old('oldPeso') ?>">
          </div>

          <div>
            <label class="label-campo" for="frecuenciaC">Frecuencia cardíaca (lpm) *:</label>
            <input type="number" class="campo" id="frecuenciaC" name="frecuenciaC" min="30" max="350"
              placeholder="Ej. 90" required value="<?= old('oldFrecuenciaC') ?>">
          </div>

          <div>
            <label class="label-campo" for="frecuenciaR">Frecuencia respiratoria (rpm) *:</label>
            <input type="number" class="campo" id="frecuenciaR" name="frecuenciaR" min="10" max="150"
              placeholder="Ej. 24" required value="<?= old('oldFrecuenciaR') ?>">
          </div>

          <div class="campo-completo">
            <label class="label-campo" for="sintomas">Síntomas presentados *:</label>
            <textarea class="campo" id="sintomas" name="sintomas" rows="4" minlength="5" maxlength="1000"
              placeholder="Describe los síntomas observados" required><?= old('oldSintomas') ?></textarea>
          </div>
        </div>
      </fieldset>

      <fieldset>
        <legend>Diagnóstico y tratamiento</legend>
        <div class="grid-formulario">
          <div class="campo-completo">
            <label class="label-campo" for="diagnostico">Diagnóstico clínico *:</label>
            <textarea class="campo" id="diagnostico" name="diagnostico" rows="3" minlength="5" maxlength="1000"
              placeholder="Diagnóstico del paciente" required><?= old('oldDiagnostico') ?></textarea>
          </div>

          <div>
            <label class="label-campo" for="tratamiento">Tratamiento *:</label>
            <textarea class="campo" id="tratamiento" name="tratamiento" rows="3" minlength="5" maxlength="1000"
              placeholder="Tratamiento prescrito" required><?= old('oldTratamiento') ?></textarea>
          </div>

          <div>
            <label class="label-campo" for="Medicamento">Medicamentos *:</label>
            <textarea class="campo" id="Medicamento" name="Medicamento" rows="3" minlength="3" maxlength="500"
              placeholder="Medicamentos recetados" required><?= old('oldMedicamento') ?></textarea>
          </div>

          <div>
            <label class="label-campo" for="Dosis">Dosis:</label>
            <textarea class="campo" id="Dosis" name="Dosis" rows="3" maxlength="500"
              placeholder="Ej. 1 tableta cada 12 horas"><?= old('oldDosis') ?></textarea>
          </div>

          <div>
            <label class="label-campo" for="indicaciones">Indicaciones para el dueño *:</label>
            <textarea class="campo" id="indicaciones" name="indicaciones" rows="3" minlength="5" maxlength="1000"
              placeholder="Cuidados y recomendaciones" required><?= old('oldIndicacion') ?></textarea>
          </div>

          <div>
            <label class="label-campo" for="estudiosA">Estudios asociados:</label>
            <textarea class="campo" id="estudiosA" name="estudiosA" rows="3" maxlength="500"
              placeholder="Rayos X, análisis de sangre, etc."><?= old('oldEstudiosaA') ?></textarea>
          </div>

          <div>
            <label class="label-campo" for="fecha">Próxima cita:</label>
            <input type="date" class="campo" id="fecha" name="fecha"
              min="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d', strtotime('+2 years')) ?>" value="<?= old('oldFecha') ?>">
          </div>

          <div class="campo-completo campo-completo-sep">
            <label class="label-campo" for="radiografias">Radiografías / Estudios de imagen:</label>
            <input type="file" class="campo" id="radiografias" name="radiografias[]" accept="image/*" multiple>
            <p style="font-size: 0.95rem; color: #90a4ae; margin-top: 6px;">
              Puedes seleccionar una o varias imágenes. Se abrirá el recortador para ajustar cada una antes de guardar.
            </p>
            <div id="contenedor-previews-radiografias" style="display: none; flex-wrap: wrap; gap: 12px; margin-top: 12px;"></div>
          </div>

          <?php if (!empty($radiografiasPrevias)): ?>
            <div class="campo-completo" style="margin-top: 10px;">
              <label class="label-campo">Radiografías registradas previamente en esta consulta:</label>
              <div style="display: flex; flex-wrap: wrap; gap: 12px; margin-top: 8px;">
                <?php foreach ($radiografiasPrevias as $rx): ?>
                  <div style="background: rgba(0,0,0,0.25); border: 1px solid rgba(255,255,255,0.15); border-radius: 8px; padding: 6px; text-align: center;">
                    <a href="../../<?= htmlspecialchars($rx['ruta']) ?>" target="_blank" title="Ver imagen completa">
                      <img src="../../<?= htmlspecialchars($rx['ruta']) ?>" alt="Radiografía" style="width: 100px; height: 100px; object-fit: cover; border-radius: 6px; display: block;">
                    </a>
                    <span style="font-size: 0.72rem; color: #90a4ae; display: block; margin-top: 4px;"><?= htmlspecialchars(substr($rx['fecha_subida'] ?? '', 0, 10)) ?></span>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>

          <div class="campo-completo">
            <label class="label-campo" for="observaciones">Observaciones:</label>
            <textarea class="campo" id="observaciones" name="observaciones" rows="3" maxlength="1000"
              placeholder="Notas u observaciones adicionales"><?= old('oldObservaciones') ?></textarea>
          </div>
        </div>
      </fieldset>

      <div id="modal-crop">
        <div class="caja">
          <h3 id="crop-titulo" style="color: #1e293b; margin-top: 0;">Recortar radiografía</h3>
          <img id="imagen-a-recortar" alt="Imagen a recortar">
          <div class="crop-acciones">
            <button type="button" id="btn-cancelar-crop" class="btn btn-secundario btn-chico">Omitir / Cancelar</button>
            <button type="button" id="btn-recortar" class="btn btn-chico">Recortar y usar</button>
          </div>
        </div>
      </div>

      <div class="acciones-formulario">
        <a class="btn-secundario" href="citas.php">Cancelar</a>
        <button type="submit" class="btn">Terminar consulta</button>
      </div>
    </form>

      </div>
    </main>

  </div>

  <footer>
    <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
  </footer>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
  <script src="js/crop-radiografias.js"></script>

</body>
</html>
