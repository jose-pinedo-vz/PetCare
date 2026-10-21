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
          <?php $especieActual = $old['sexo'] ?? $registro['sexo'] ?? ''; ?>
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
        <input type="text" class="campo" id="raza" name="raza" maxlength="30" value="<?= htmlspecialchars($old['raza'] ?? $registro['raza'] ?? '') ?>">
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
        <label class="label-campo" for="color">Color *:</label>
        <input type="text" class="campo" id="color" name="color" maxlength="20" value="<?= htmlspecialchars($old['color'] ?? $registro['color'] ?? '') ?>" required>
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
        <input type="number" step="0.01" min="0" class="campo" id="costo_de_adquisicion" name="costo_de_adquisicion" value="<?= htmlspecialchars($old['costo_de_adquisicion'] ?? $registro['costo_de_adquisicion'] ?? '') ?>" required>
    </div>

    <div>
        <label class="label-campo" for="precio_de_venta">Precio de venta *:</label>
        <input type="number" step="0.01" min="0" class="campo" id="precio_de_venta" name="precio_de_venta" value="<?= htmlspecialchars($old['precio_de_venta'] ?? $registro['precio_de_venta'] ?? '') ?>" required>
    </div>

    <div>
        <label class="label-campo" for="estado">Estado:</label>
        <select class="campo" id="estado" name="estado">
            <?php $estadoActual = $old['estado'] ?? $registro['estado'] ?? ''; ?>
            <option value="Disponible" <?= ($estadoActual === 'Venta legal') ? 'selected' : '' ?>>Venta legal</option>
            <option value="Reservado" <?= ($estadoActual === 'Adopcion') ? 'selected' : '' ?>>Adopcion</option>
        </select>
    </div>

    <div class="campo-completo">
      <label class="label-campo" for="caracteristicas">Características *:</label>
      <textarea class="campo" id="caracteristicas" name="caracteristicas" rows="2" required><?= htmlspecialchars($old['caracteristicas'] ?? $registro['caracteristicas'] ?? '') ?></textarea>
    </div>

    <div class="campo-completo">
      <label class="label-campo" for="procedencia">Procedencia *:</label>
      <textarea class="campo" id="procedencia" name="procedencia" rows="2" required><?= htmlspecialchars($old['procedencia'] ?? $registro['procedencia'] ?? '') ?></textarea>
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
      <textarea class="campo" id="vacunas" name="vacunas" rows="2" required><?= htmlspecialchars($old['vacunas'] ?? $registro['vacunas'] ?? '') ?></textarea>
    </div>

    <div>
        <label class="label-campo" for="desparasitacion">Desparasitación *:</label>
        <input type="date" class="campo" id="desparasitacion" name="desparasitacion" value="<?= htmlspecialchars($old['desparasitacion'] ?? $registro['desparasitacion'] ?? '') ?>" required>
    </div>

    <div class="campo-completo">
        <label class="label-campo" for="observaciones">Observaciones *:</label>
        <textarea class="campo" id="observaciones" name="observaciones" rows="2" required><?= htmlspecialchars($old['observaciones'] ?? $registro['observaciones'] ?? '') ?></textarea>
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

</body>
</html>