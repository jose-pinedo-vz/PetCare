<?php
require_once __DIR__ . '/../models/Mascota.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$idMascota = filter_input(INPUT_GET, 'id_mascota', FILTER_VALIDATE_INT);

if ($idMascota === false || $idMascota === null) {
    header('Location: mascotas.php');
    exit();
}

$mascota = obtenerMascotaPorId($idMascota);

if (!$mascota) {
    header('Location: mascotas.php');
    exit();
}

$errores = $_SESSION['errores'] ?? [];
$old     = $_SESSION['old'] ?? [];

unset($_SESSION['errores']);
unset($_SESSION['old']);

// actualizar_mascota.php tambien puede mandar los errores por ?error=a | b | c
if (!empty($_GET['error'])) {
    foreach (explode(' | ', (string)$_GET['error']) as $msg) {
        $errores[] = $msg;
    }
}

// Valor del formulario: lo que se escribio antes del error o, si no hay, lo de la BD
function dato(array $old, array $mascota, string $campo): string
{
    $valor = array_key_exists($campo, $old) ? $old[$campo] : ($mascota[$campo] ?? '');
    return htmlspecialchars((string)($valor ?? ''));
}

$sexoActual = $old['sexo'] ?? $mascota['sexo'] ?? '';
$tamActual  = $old['tamanio'] ?? $mascota['tamanio'] ?? '';
$activo     = (int)($old['esta_activo'] ?? $mascota['esta_activo'] ?? 1) === 1;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veterinaria PetCare - Editar mascota</title>
  <link rel="stylesheet" href="css/estilos_base_v2.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
  <style>
    /* Switch Activo / Inactivo (si otra vista lo necesita, moverlo a css/v2/base.css) */
    .switch-fila { display: flex; align-items: center; gap: 12px; margin-top: 4px; }
    .switch { position: relative; display: inline-block; width: 46px; height: 26px; flex-shrink: 0; }
    .switch input { opacity: 0; width: 0; height: 0; }
    .slider {
      position: absolute; inset: 0; cursor: pointer;
      background-color: var(--gray-light);
      border: 1px solid var(--border-light);
      border-radius: 26px;
      transition: background-color 0.2s;
    }
    .slider::before {
      content: ""; position: absolute; left: 2px; top: 2px;
      width: 20px; height: 20px; border-radius: 50%;
      background-color: var(--white);
      box-shadow: 0 1px 2px rgba(0, 0, 0, 0.25);
      transition: transform 0.2s;
    }
    .switch input:checked + .slider { background-color: var(--accent-teal); }
    .switch input:checked + .slider::before { transform: translateX(20px); }
    .switch input:focus-visible + .slider { outline: 2px solid var(--primary-dark); outline-offset: 2px; }
  </style>
</head>
<body>

  <div class="layout">

  <?php $paginaActiva = 'mascotas'; include __DIR__ . '/layout/sidebar.php'; ?>

  <main>
      <div class="migaja"><a href="mascotas.php" class="link-tenue">Gestión de Mascotas</a> / Editar</div>
      <div class="encabezado-pagina">
        <h1>Editar mascota</h1>
      </div>

      <div class="tarjeta">

    <?php if (!empty($errores)): ?>
      <div class="msg-error">
        <div>Por favor corrige los siguientes errores:</div>
        <?php foreach ($errores as $error): ?>
          <div>• <?= htmlspecialchars($error) ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form action="../controllers/actualizar_mascota.php" method="POST" enctype="multipart/form-data">

      <input type="hidden" id="id_mascota" name="id_mascota" value="<?= (int)$mascota['id_mascota'] ?>">

      <fieldset>
        <legend>Datos básicos</legend>

        <div class="grid-formulario">
          <div>
            <label class="label-campo" for="nombre">Nombre *:</label>
            <input type="text" class="campo" id="nombre" name="nombre" value="<?= dato($old, $mascota, 'nombre') ?>" required>
          </div>

          <div>
            <label class="label-campo" for="especie">Especie *:</label>
            <input type="text" class="campo" id="especie" name="especie" value="<?= dato($old, $mascota, 'especie') ?>" required>
          </div>

          <div>
            <label class="label-campo" for="raza">Raza:</label>
            <input type="text" class="campo" id="raza" name="raza" value="<?= dato($old, $mascota, 'raza') ?>">
          </div>

          <div>
            <label class="label-campo" for="sexo">Sexo:</label>
            <select class="campo" id="sexo" name="sexo" required>
              <option value="Macho"  <?= $sexoActual === 'Macho'  ? 'selected' : '' ?>>Macho</option>
              <option value="Hembra" <?= $sexoActual === 'Hembra' ? 'selected' : '' ?>>Hembra</option>
            </select>
          </div>

          <div>
            <label class="label-campo" for="edad">Edad / Fecha nacimiento *:</label>
            <input type="date" class="campo" id="edad" name="edad" value="<?= dato($old, $mascota, 'edad') ?>" required>
          </div>

          <div>
            <label class="label-campo" for="color">Color:</label>
            <input type="text" class="campo" id="color" name="color" value="<?= dato($old, $mascota, 'color') ?>">
          </div>

          <div>
            <label class="label-campo" for="peso">Peso (kg):</label>
            <input type="number" step="0.01" class="campo" id="peso" name="peso" value="<?= dato($old, $mascota, 'peso') ?>">
          </div>

          <div>
            <label class="label-campo" for="tamanio">Tamaño:</label>
            <select class="campo" id="tamanio" name="tamanio">
              <option value="">Seleccionar...</option>
              <option value="Pequeño" <?= $tamActual === 'Pequeño' ? 'selected' : '' ?>>Pequeño</option>
              <option value="Mediano" <?= $tamActual === 'Mediano' ? 'selected' : '' ?>>Mediano</option>
              <option value="Grande"  <?= $tamActual === 'Grande'  ? 'selected' : '' ?>>Grande</option>
              <option value="Gigante" <?= $tamActual === 'Gigante' ? 'selected' : '' ?>>Gigante</option>
            </select>
          </div>

          <div>
            <label class="label-campo" for="id_cliente">Dueño (ID cliente) *:</label>
            <input type="number" class="campo" id="id_cliente" name="id_cliente" value="<?= dato($old, $mascota, 'id_cliente') ?>" required placeholder="ID del Cliente">
          </div>

          <div>
            <label class="label-campo" for="id_veterinario">Veterinario asignado (ID):</label>
            <input type="number" class="campo" id="id_veterinario" name="id_veterinario" value="<?= dato($old, $mascota, 'id_veterinario') ?>" placeholder="ID del Veterinario">
          </div>
        </div>

        <div class="campo-completo-sep">
          <label class="label-campo" for="fotografia">Fotografía:</label>
          <input type="file" class="campo" id="fotografia" name="fotografia" accept="image/*">
          <img id="preview-crop" alt="Vista previa recortada (cuadrado)">
          <p class="nota-campo">Deja este campo vacío si no quieres cambiar la foto actual.</p>
        </div>

        <div id="modal-crop">
          <div class="caja">
            <h3>Recortar foto (cuadrado)</h3>
            <img id="imagen-a-recortar" alt="Imagen a recortar">
            <div class="crop-acciones">
              <button type="button" id="btn-cancelar-crop" class="btn-secundario">Cancelar</button>
              <button type="button" id="btn-recortar">Recortar y usar</button>
            </div>
          </div>
        </div>
      </fieldset>

      <fieldset>
        <legend>Datos clínicos</legend>

        <div class="grid-formulario">
          <div>
            <label class="label-campo" for="alergias">Alergias:</label>
            <textarea class="campo" id="alergias" name="alergias" rows="2"><?= dato($old, $mascota, 'alergias') ?></textarea>
          </div>

          <div>
            <label class="label-campo" for="enfermedades">Enfermedades:</label>
            <textarea class="campo" id="enfermedades" name="enfermedades" rows="2"><?= dato($old, $mascota, 'enfermedades') ?></textarea>
          </div>

          <div>
            <label class="label-campo" for="medicamentos">Medicamentos:</label>
            <textarea class="campo" id="medicamentos" name="medicamentos" rows="2"><?= dato($old, $mascota, 'medicamentos') ?></textarea>
          </div>

          <div>
            <label class="label-campo" for="condiciones_especiales">Condiciones especiales:</label>
            <textarea class="campo" id="condiciones_especiales" name="condiciones_especiales" rows="2"><?= dato($old, $mascota, 'condiciones_especiales') ?></textarea>
          </div>

          <div>
            <label class="label-campo" for="vacunas">Vacunas:</label>
            <textarea class="campo" id="vacunas" name="vacunas" rows="2"><?= dato($old, $mascota, 'vacunas') ?></textarea>
          </div>

          <div>
            <label class="label-campo" for="ultima_desparasitacion">Última desparasitación:</label>
            <input type="date" class="campo" id="ultima_desparasitacion" name="ultima_desparasitacion" value="<?= dato($old, $mascota, 'ultima_desparasitacion') ?>">
          </div>

          <div>
            <label class="label-campo" for="temperamento">Temperamento:</label>
            <textarea class="campo" id="temperamento" name="temperamento" rows="2"><?= dato($old, $mascota, 'temperamento') ?></textarea>
          </div>

          <div>
            <label class="label-campo" for="restricciones_para_manejo">Restricciones para manejo:</label>
            <textarea class="campo" id="restricciones_para_manejo" name="restricciones_para_manejo" rows="2"><?= dato($old, $mascota, 'restricciones_para_manejo') ?></textarea>
          </div>

          <div>
            <label class="label-campo" for="observaciones">Observaciones:</label>
            <textarea class="campo" id="observaciones" name="observaciones" rows="2"><?= dato($old, $mascota, 'observaciones') ?></textarea>
          </div>

          <div>
            <span class="label-campo">Estado:</span>
            <div class="switch-fila">
              <label class="switch" aria-label="Mascota activa">
                <input type="checkbox" id="esta_activo" <?= $activo ? 'checked' : '' ?>>
                <span class="slider"></span>
              </label>
              <span id="texto_estado" class="estado-chip <?= $activo ? 'estado-atendida' : 'estado-espera' ?>"><?= $activo ? 'Activo' : 'Inactivo' ?></span>
              <!-- Este es el campo que se envia a PHP (1 o 0) -->
              <input type="hidden" id="activo" name="activo" value="<?= $activo ? '1' : '0' ?>">
            </div>
          </div>
        </div>
      </fieldset>

      <div class="acciones-formulario">
        <a class="btn-secundario" href="mascotas.php">Cancelar</a>
        <button type="submit" class="btn">Guardar cambios</button>
      </div>
    </form>

      </div>
    </main>

  </div>

  <footer>
    <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
  </footer>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
  <script src="js/crop-mascota.js"></script>
  <script>
    // Switch Activo / Inactivo: actualiza el texto y el valor que se envia
    (function () {
      var interruptor = document.getElementById('esta_activo');
      var texto = document.getElementById('texto_estado');
      var oculto = document.getElementById('activo');
      if (!interruptor || !texto || !oculto) return;

      interruptor.addEventListener('change', function () {
        var activo = interruptor.checked;
        oculto.value = activo ? '1' : '0';
        texto.textContent = activo ? 'Activo' : 'Inactivo';
        texto.classList.toggle('estado-atendida', activo);
        texto.classList.toggle('estado-espera', !activo);
      });
    })();
  </script>

</body>
</html>
