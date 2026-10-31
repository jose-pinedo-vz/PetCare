<?php
session_start();

$errores = $_SESSION['errores'] ?? [];
$old     = $_SESSION['old'] ?? [];

unset($_SESSION['errores']);
unset($_SESSION['old']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veterinaria PetCare - Agregar adopción y venta</title>
  <link rel="stylesheet" href="css/estilos_base_v2.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
</head>
<body>

  <div class="layout">

  <?php $paginaActiva = 'adopcion'; include __DIR__ . '/layout/sidebar.php'; ?>


  <main>
      <div class="migaja"><a href="adopcion_venta.php" class="link-tenue">Gestión de adopción y venta</a> / Agregar</div>
      <div class="encabezado-pagina">
        <h1>Registrar adopción y venta</h1>
      </div>

      <div class="tarjeta">
    
    <?php if (!empty($errores)): ?>
      <div class="msg-error">
        <h4>Por favor corrige los siguientes errores:</h4>
        <ul>
          <?php foreach ($errores as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form action="guardar_adopcion_venta.php" method="POST" enctype="multipart/form-data">

<fieldset>
  <legend> Datos generales</legend>

  <div class="grid-formulario">
    <div>
      <label class="label-campo" for="especie">Especie *:</label>
      <select class="campo" id="especie" name="especie" required>
        <option value="Perro" <?= (($old['especie'] ?? '') === 'Perro') ? 'selected' : '' ?>>Perro</option>
        <option value="Gato" <?= (($old['especie'] ?? '') === 'Gato') ? 'selected' : '' ?>>Gato</option>
        <option value="Conejo" <?= (($old['especie'] ?? '') === 'Conejo') ? 'selected' : '' ?>>Conejo</option>
        <option value="Hamster" <?= (($old['especie'] ?? '') === 'Hamster') ? 'selected' : '' ?>>Hamster</option>
        <option value="Hurón" <?= (($old['especie'] ?? '') === 'Hurón') ? 'selected' : '' ?>>Hurón</option>
        <option value="Erizo" <?= (($old['especie'] ?? '') === 'Erizo') ? 'selected' : '' ?>>Erizo</option>
        <option value="Tortuga" <?= (($old['especie'] ?? '') === 'Tortuga') ? 'selected' : '' ?>>Tortuga</option>
        <option value="Ave" <?= (($old['especie'] ?? '') === 'Ave') ? 'selected' : '' ?>>Ave</option>
      </select>
    </div>

    <div>
      <label class="label-campo" for="raza">Raza:</label>
      <input type="text" class="campo" id="raza" name="raza" minlength="2" maxlength="30"
        pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñ0-9]{2,}([\s\-][A-Za-zÁÉÍÓÚáéíóúÑñ0-9]{2,})*$"
        title="Solo letras, números y guiones (sin símbolos especiales)"
        value="<?= htmlspecialchars($old['raza'] ?? '') ?>">
    </div>

    <div>
      <label class="label-campo" for="sexo">Sexo:</label>
      <select class="campo" id="sexo" name="sexo">
        <option value="Macho" <?= (($old['sexo'] ?? '') === 'Macho') ? 'selected' : '' ?>>Macho</option>
        <option value="Hembra" <?= (($old['sexo'] ?? '') === 'Hembra') ? 'selected' : '' ?>>Hembra</option>
      </select>
    </div>

    <div>
      <label class="label-campo" for="fecha_de_nacimiento">Fecha de nacimiento *:</label>
      <input type="date" class="campo" id="fecha_de_nacimiento" name="fecha_de_nacimiento"
        min="<?= date('Y-m-d', strtotime('-35 years')) ?>" max="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($old['fecha_de_nacimiento'] ?? '') ?>" required>
    </div>

    <div>
      <label class="label-campo" for="color">Color *:</label>
      <?php 
        $coloresDisponibles = [
          'Blanco', 'Negro', 'Gris', 'Café', 'Amarillo', 'Verde', 'Rojo',
          'Blanco y negro', 'Café y blanco', 'Café y amarillo', 'Blanco y gris',
          'Verde y amarillo', 'Blanco, amarillo y negro'
        ];
        $colorOld = $old['color'] ?? '';
        $colorEspecOld = $old['color_espec'] ?? '';
        $esOtroColor = ($colorOld === 'Otro') || (!empty($colorOld) && !in_array(mb_strtolower($colorOld), array_map('mb_strtolower', $coloresDisponibles)));
        if ($esOtroColor && empty($colorEspecOld) && $colorOld !== 'Otro') {
          $colorEspecOld = $colorOld;
        }
      ?>
      <select class="campo" id="color" name="color" required>
        <option value="">Seleccionar...</option>
        <?php foreach ($coloresDisponibles as $opcionColor): ?>
          <option value="<?= htmlspecialchars($opcionColor) ?>" <?= (!$esOtroColor && strcasecmp($colorOld, $opcionColor) === 0) ? 'selected' : '' ?>>
            <?= htmlspecialchars($opcionColor) ?>
          </option>
        <?php endforeach; ?>
        <option value="Otro" <?= $esOtroColor ? 'selected' : '' ?>>Otro (Especificar)</option>
      </select>
    </div>

    <div id="contenedor-color-espec" style="<?= $esOtroColor ? '' : 'display: none;' ?>">
      <label class="label-campo" for="color_espec">Especifique el color *:</label>
      <input type="text" class="campo" id="color_espec" name="color_espec" maxlength="50" placeholder="Ej. Marrón rojizo" value="<?= htmlspecialchars($colorEspecOld) ?>">
    </div>

    <div>
      <label class="label-campo" for="peso">Peso (kg) *:</label>
      <input type="number" step="0.01" min="0.01" max="150" class="campo" id="peso" name="peso"
        placeholder="Ej. 8.5" value="<?= htmlspecialchars($old['peso'] ?? '') ?>" required>
    </div>

    <div>
      <label class="label-campo" for="fecha_de_ingreso">Fecha de ingreso *:</label>
      <input type="datetime-local" class="campo" id="fecha_de_ingreso" name="fecha_de_ingreso"
        max="<?= date('Y-m-d\TH:i') ?>" value="<?= htmlspecialchars($old['fecha_de_ingreso'] ?? '') ?>" required>
    </div>

    <div>
      <label class="label-campo" for="costo_de_adquisicion">Costo de adquisición:</label>
      <input type="number" step="0.01" min="0" max="500000" class="campo" id="costo_de_adquisicion" name="costo_de_adquisicion"
        placeholder="Ej. 1500.00" value="<?= htmlspecialchars($old['costo_de_adquisicion'] ?? '') ?>">
    </div>

    <div>
      <label class="label-campo" for="precio_de_venta">Precio de venta *:</label>
      <input type="number" step="0.01" min="0" max="500000" class="campo" id="precio_de_venta" name="precio_de_venta"
        placeholder="Ej. 2500.00" value="<?= htmlspecialchars($old['precio_de_venta'] ?? '') ?>" required>
    </div>

    <div>
      <label class="label-campo" for="estado">Estado:</label>
      <select class="campo" id="estado" name="estado">
        <option value="Disponible" <?= in_array(($old['estado'] ?? ''), ['Disponible', 'Venta legal', 'Venta Legal']) ? 'selected' : '' ?>>Venta legal</option>
        <option value="Adopcion" <?= in_array(($old['estado'] ?? ''), ['Adopcion', 'Adopción', 'Reservado']) ? 'selected' : '' ?>>Adopción</option>
      </select>
    </div>

    <div class="campo-completo">
      <label class="label-campo" for="caracteristicas">Características *:</label>
      <textarea class="campo" id="caracteristicas" name="caracteristicas" rows="2" minlength="5" maxlength="500" required
        placeholder="Describe temperamento, aspecto y detalles de la mascota"><?= htmlspecialchars($old['caracteristicas'] ?? '') ?></textarea>
    </div>

    <div class="campo-completo">
      <label class="label-campo" for="procedencia">Procedencia *:</label>
      <textarea class="campo" id="procedencia" name="procedencia" rows="2" minlength="5" maxlength="500" required
        placeholder="Origen o criadero"><?= htmlspecialchars($old['procedencia'] ?? '') ?></textarea>
    </div>
  </div>

  <div class="campo-completo-sep">
    <label class="label-campo" for="fotografia">Fotografía:</label>
    <input type="file" class="campo" id="fotografia" name="fotografia" accept="image/*">
    <img id="preview-crop" alt="Vista previa recortada (cuadrado)">
    <p class="nota-campo">Al elegir una foto se abrirá el cuadrado de recorte.</p>
  </div>

  <div id="modal-crop">
    <div class="caja">
      <h3>Recortar foto (cuadrado)</h3>
      <img id="imagen-a-recortar" alt="Imagen a recortar">
      <div class="crop-acciones">
        <button type="button" id="btn-cancelar-crop" class="btn btn-secundario btn-chico">Cancelar</button>
        <button type="button" id="btn-recortar" class="btn btn-chico">Recortar y usar</button>
      </div>
    </div>
  </div>
</fieldset>

<fieldset>
  <legend> Datos clínicos</legend>

  <div class="grid-formulario">
    <div>
      <label class="label-campo" for="vacunas">Vacunas *:</label>
      <textarea class="campo" id="vacunas" name="vacunas" rows="2" minlength="3" maxlength="500" required
        placeholder="Esquema de vacunas recibidas"><?= htmlspecialchars($old['vacunas'] ?? '') ?></textarea>
    </div>

    <div>
      <label class="label-campo" for="desparasitacion">Desparasitación *:</label>
      <input type="date" class="campo" id="desparasitacion" name="desparasitacion"
        min="<?= date('Y-m-d', strtotime('-35 years')) ?>" max="<?= date('Y-m-d') ?>"
        value="<?= htmlspecialchars($old['desparasitacion'] ?? '') ?>" required>
    </div>

    <div class="campo-completo">
      <label class="label-campo" for="observaciones">Observaciones *:</label>
      <textarea class="campo" id="observaciones" name="observaciones" rows="2" minlength="5" maxlength="500" required
        placeholder="Observaciones generales"><?= htmlspecialchars($old['observaciones'] ?? '') ?></textarea>
    </div>
  </div>
</fieldset>

      <div class="acciones-formulario">
        <a class="btn btn-secundario btn-chico" href="adopcion_venta.php">Cancelar</a>
        <button type="submit" class="btn btn-chico">Guardar</button>
      </div>
    </form>
  
      </div>
    </main>

  </div>

  <footer>
    <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
  </footer>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
  <script src="js/crop-adopcion_venta.js"></script>
  <script>
    (function() {
      const selectColor = document.getElementById('color');
      const contenedorColorEspec = document.getElementById('contenedor-color-espec');
      const inputColorEspec = document.getElementById('color_espec');

      if (selectColor && contenedorColorEspec) {
        function toggleColorEspec() {
          if (selectColor.value === 'Otro') {
            contenedorColorEspec.style.display = 'block';
            if (inputColorEspec) inputColorEspec.required = true;
          } else {
            contenedorColorEspec.style.display = 'none';
            if (inputColorEspec) {
              inputColorEspec.required = false;
            }
          }
        }
        selectColor.addEventListener('change', toggleColorEspec);
        toggleColorEspec();
      }

      const fechaNacInput = document.getElementById('fecha_de_nacimiento');
      const fechaDespInput = document.getElementById('desparasitacion');
      if (fechaNacInput && fechaDespInput) {
        function sincronizarFechasDesp() {
          if (fechaNacInput.value) {
            fechaDespInput.min = fechaNacInput.value;
          } else {
            fechaDespInput.min = '<?= date('Y-m-d', strtotime('-35 years')) ?>';
          }
        }
        fechaNacInput.addEventListener('change', sincronizarFechasDesp);
        sincronizarFechasDesp();
      }
    })();
  </script>

</body>
</html>