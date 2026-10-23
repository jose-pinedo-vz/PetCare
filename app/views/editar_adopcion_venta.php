<?php
session_start();
require_once __DIR__ . '/../models/adopcion_venta.php';
$errores=$_SESSION['errores'] ?? [];
$old=$_SESSION['old'] ?? [];
unset($_SESSION['errores']);
unset($_SESSION['old']);
$id=$_GET['id'] ?? null;
if (!$id)
{
    header('Location: ../views/adopcion_venta.php');
    exit;
}
$registro=obtenerPorId($id);

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
      <div class="migaja"><a href="adopcion_venta.php" class="link-tenue">Gestión de adopción y venta</a> / Editar</div>
      <div class="encabezado-pagina">
        <h1>Editar adopción y venta</h1>
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
    <form action="../controllers/actualizar_adopcion_venta.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= htmlspecialchars($registro['id_animal']) ?>">
        <input type="hidden" name="fotografia_actual" value="<?= htmlspecialchars($registro['fotografia'] ?? '') ?>">

<fieldset>
  <legend> Datos generales</legend>

  <div class="grid-formulario">
    <div>
        <label class="label-campo" for="especie">Especie:</label>
        <select class="campo" id="especie" name="especie" required>
          <?php $especieActual = $old['especie'] ?? $registro['especie'] ?? ''; ?>
          <option value="Perro" <?= ($especieActual === 'Perro') ? 'selected' : '' ?>>Perro</option>
          <option value="Gato" <?= ($especieActual === 'Gato') ? 'selected' : '' ?>>Gato</option>
          <option value="Conejo" <?= ($especieActual === 'Conejo') ? 'selected' : '' ?>>Conejo</option>
          <option value="Hamster" <?= ($especieActual === 'Hamster') ? 'selected' : '' ?>>Hamster</option>
          <option value="Hurón" <?= ($especieActual === 'Hurón') ? 'selected' : '' ?>>Hurón</option>
          <option value="Erizo" <?= ($especieActual === 'Erizo') ? 'selected' : '' ?>>Erizo</option>
          <option value="Tortuga" <?= ($especieActual === 'Tortuga') ? 'selected' : '' ?>>Tortuga</option>
          <option value="Ave" <?= ($especieActual === 'Ave') ? 'selected' : '' ?>>Ave</option>
        </select>
    </div>

    <div>
        <label class="label-campo" for="raza">Raza:</label>
        <select class="campo" id="raza" name="raza">
        </select>
    </div>

    <div>
        <label class="label-campo" for="sexo">Sexo:</label>
        <select class="campo" id="sexo" name="sexo">
            <?php $sexoActual = $old['sexo'] ?? $registro['sexo'] ?? ''; ?>
            <option value="Macho" <?= ($sexoActual === 'Macho') ? 'selected' : '' ?>>Macho</option>
            <option value="Hembra" <?= ($sexoActual === 'Hembra') ? 'selected' : '' ?>>Hembra</option>
        </select>
    </div>

    <div>
        <label class="label-campo" for="fecha_de_nacimiento">Fecha de nacimiento *:</label>
        <input type="date" class="campo" id="fecha_de_nacimiento" name="fecha_de_nacimiento" value="<?= htmlspecialchars($old['fecha_de_nacimiento'] ?? $registro['fecha_de_nacimiento'] ?? '') ?>" required>
    </div>

    <div>
<<<<<<< HEAD
      <label class="label-campo" for="color">Color *:</label>
      <select class="campo" id="color" name="color" required>
        <option value="Blanco" <?= (($old['color'] ?? $registro['color']) === 'Blanco') ? 'selected' : '' ?>>Blanco</option>
        <option value="Negro" <?= (($old['color'] ?? $registro['color']) === 'Negro') ? 'selected' : '' ?>>Negro</option>
        <option value="Café / Marrón" <?= (($old['color'] ?? $registro['color']) === 'Café / Marrón') ? 'selected' : '' ?>>Café / Marrón</option>
        <option value="Gris" <?= (($old['color'] ?? $registro['color']) === 'Gris') ? 'selected' : '' ?>>Gris</option>
        <option value="Beige / Crema" <?= (($old['color'] ?? $registro['color']) === 'Beige / Crema') ? 'selected' : '' ?>>Beige / Crema</option>
        <option value="Dorado / Leonado" <?= (($old['color'] ?? $registro['color']) === 'Dorado / Leonado') ? 'selected' : '' ?>>Dorado / Leonado</option>
        <option value="Canela / Rojo" <?= (($old['color'] ?? $registro['color']) === 'Canela / Rojo') ? 'selected' : '' ?>>Canela / Rojo</option>
        <option value="Verde" <?= (($old['color'] ?? $registro['color']) === 'Verde') ? 'selected' : '' ?>>Verde</option>
        <option value="Manchado / Overo" <?= (($old['color'] ?? $registro['color']) === 'Manchado / Overo') ? 'selected' : '' ?>>Manchado / Overo</option>
        <option value="Atigrado" <?= (($old['color'] ?? $registro['color']) === 'Atigrado') ? 'selected' : '' ?>>Atigrado</option>
        <option value="Bicolor" <?= (($old['color'] ?? $registro['color']) === 'Bicolor') ? 'selected' : '' ?>>Bicolor</option>
        <option value="Tricolor" <?= (($old['color'] ?? $registro['color']) === 'Tricolor') ? 'selected' : '' ?>>Tricolor</option>
        <option value="Moteado / Jaspeado" <?= (($old['color'] ?? $registro['color']) === 'Moteado / Jaspeado') ? 'selected' : '' ?>>Moteado / Jaspeado</option>
      </select>
=======
        <label class="label-campo" for="color">Color *:</label>
        <?php 
          $coloresDisponibles = [
            'Blanco', 'Negro', 'Gris', 'Café', 'Amarillo', 'Verde', 'Rojo',
            'Blanco y negro', 'Café y blanco', 'Café y amarillo', 'Blanco y gris',
            'Verde y amarillo', 'Blanco, amarillo y negro'
          ];
          $colorActual = $old['color'] ?? $registro['color'] ?? '';
          $colorEspecActual = $old['color_espec'] ?? '';
          $esOtroColor = ($colorActual === 'Otro') || (!empty($colorActual) && !in_array(mb_strtolower($colorActual), array_map('mb_strtolower', $coloresDisponibles)));
          if ($esOtroColor && empty($colorEspecActual) && $colorActual !== 'Otro') {
            $colorEspecActual = $colorActual;
          }
        ?>
        <select class="campo" id="color" name="color" required>
          <option value="">Seleccionar...</option>
          <?php foreach ($coloresDisponibles as $opcionColor): ?>
            <option value="<?= htmlspecialchars($opcionColor) ?>" <?= (!$esOtroColor && strcasecmp($colorActual, $opcionColor) === 0) ? 'selected' : '' ?>>
              <?= htmlspecialchars($opcionColor) ?>
            </option>
          <?php endforeach; ?>
          <option value="Otro" <?= $esOtroColor ? 'selected' : '' ?>>Otro (Especificar)</option>
        </select>
    </div>

    <div id="contenedor-color-espec" style="<?= $esOtroColor ? '' : 'display: none;' ?>">
        <label class="label-campo" for="color_espec">Especifique el color *:</label>
        <input type="text" class="campo" id="color_espec" name="color_espec" maxlength="50" placeholder="Ej. Marrón rojizo" value="<?= htmlspecialchars($colorEspecActual) ?>">
>>>>>>> 10fbd84bf8af6c84a1078d66b2b88570e3530377
    </div>

    <div>
        <label class="label-campo" for="peso">Peso (kg) *:</label>
        <input type="number" step="0.01" class="campo" id="peso" name="peso" value="<?= htmlspecialchars($old['peso'] ?? $registro['peso'] ?? '') ?>" required>
    </div>

    <div>
        <label class="label-campo" for="fecha_de_ingreso">Fecha de ingreso *:</label>
        <input type="datetime-local" class="campo" id="fecha_de_ingreso" name="fecha_de_ingreso" value="<?= htmlspecialchars($old['fecha_de_ingreso'] ?? $registro['fecha_de_ingreso'] ?? '') ?>" required>
    </div>

    <div>
        <label class="label-campo" for="costo_de_adquisicion">Costo de adquisición *:</label>
        <input type="number" step="0.01" min="0" max="500000" class="campo" id="costo_de_adquisicion" name="costo_de_adquisicion" value="<?= htmlspecialchars($old['costo_de_adquisicion'] ?? $registro['costo_de_adquisicion'] ?? '') ?>" required>
    </div>

    <div>
        <label class="label-campo" for="precio_de_venta">Precio de venta *:</label>
        <input type="number" step="0.01" min="0" max="500000" class="campo" id="precio_de_venta" name="precio_de_venta" placeholder="Ej. 1500.00" value="<?= htmlspecialchars($old['precio_de_venta'] ?? $registro['precio_de_venta'] ?? '') ?>" required>
    </div>

    <div>
        <label class="label-campo" for="estado">Estado:</label>
        <select class="campo" id="estado" name="estado">
            <?php $estadoActual = $old['estado'] ?? $registro['estado'] ?? ''; ?>
            <option value="Venta legal" <?= ($estadoActual === 'Venta legal') ? 'selected' : '' ?>>Venta legal</option>
            <option value="Adopcion" <?= ($estadoActual === 'Adopcion') ? 'selected' : '' ?>>Adopcion</option>
        </select>
    </div>

    <div class="campo-completo">
      <label class="label-campo" for="caracteristicas">Características *:</label>
      <textarea class="campo" id="caracteristicas" name="caracteristicas" rows="2" minlength="5" maxlength="500" required placeholder="Describe temperamento, aspecto y detalles de la mascota"><?= htmlspecialchars($old['caracteristicas'] ?? $registro['caracteristicas'] ?? '') ?></textarea>
    </div>

    <div class="campo-completo">
      <label class="label-campo" for="procedencia">Procedencia *:</label>
      <textarea class="campo" id="procedencia" name="procedencia" rows="2" minlength="5" maxlength="500" required placeholder="Origen o criadero"><?= htmlspecialchars($old['procedencia'] ?? $registro['procedencia'] ?? '') ?></textarea>
    </div>
  </div>

  <div class="campo-completo-sep">
    <label class="label-campo" for="fotografia">Fotografía:</label>
    <?php if (!empty($registro['fotografia'])): ?>
        <div style="margin-bottom: 10px;">
          <p class="nota-campo">Imagen actual:</p>
          <img src="../../uploads/mascotas/<?= htmlspecialchars($registro['fotografia']) ?>" alt="Foto actual" style="max-width: 120px; border-radius: 4px;">
        </div>
    <?php endif;?>
    <input type="file" class="campo" id="fotografia" name="fotografia" accept="image/*">
    <img id="preview-crop" alt="Vista previa recortada (cuadrado)">
    <p class="nota-campo">Al elegir una foto nueva se abrirá el cuadrado de recorte.</p>
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
  <legend> Datos clínicos</legend>

  <div class="grid-formulario">
    <div>
      <label class="label-campo" for="vacunas">Vacunas *:</label>
      <textarea class="campo" id="vacunas" name="vacunas" rows="2" minlength="3" maxlength="500" required placeholder="Esquema de vacunas recibidas"> <?= htmlspecialchars($old['vacunas'] ?? $registro['vacunas'] ?? '') ?></textarea>
    </div>

    <div>
        <label class="label-campo" for="desparasitacion">Desparasitación *:</label>
        <input type="date" class="campo" id="desparasitacion" name="desparasitacion" max="<?= date('Y-m-d') ?>"  value="<?= htmlspecialchars($old['desparasitacion'] ?? $registro['desparasitacion'] ?? '') ?>" required>
    </div>

    <div class="campo-completo">
        <label class="label-campo" for="observaciones">Observaciones *:</label>
        <textarea class="campo" id="observaciones" name="observaciones" rows="2" minlength="5" maxlength="500" required placeholder="Observaciones generales"><?= htmlspecialchars($old['observaciones'] ?? $registro['observaciones'] ?? '') ?></textarea>
    </div>
  </div>
</fieldset>

      <div class="acciones-formulario">
        <a class="btn-secundario" href="adopcion_venta.php">Cancelar</a>
        <button type="submit">Guardar</button>
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
    })();
  </script>

</body>
</html>