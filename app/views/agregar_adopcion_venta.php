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
    

    <form action="guardar_adopcion_venta.php" method="POST" enctype="multipart/form-data">

<fieldset>
  <legend> Datos generales</legend>

  <div class="grid-formulario">
    <div>
      <label class="label-campo" for="especie">Especie:</label>
      <input type="text" class="campo" id="especie" name="especie" maxlength="30" placeholder="Ej. Perro, Gato">
    </div>

    <div>
      <label class="label-campo" for="raza">Raza:</label>
      <input type="text" class="campo" id="raza" name="raza" maxlength="30">
    </div>

    <div>
      <label class="label-campo" for="sexo">Sexo:</label>
      <select class="campo" id="sexo" name="sexo">
        <option value="Macho">Macho</option>
        <option value="Hembra">Hembra</option>
      </select>
    </div>

    <div>
      <label class="label-campo" for="fecha_de_nacimiento">Fecha de nacimiento *:</label>
      <input type="date" class="campo" id="fecha_de_nacimiento" name="fecha_de_nacimiento" required>
    </div>

    <div>
      <label class="label-campo" for="color">Color *:</label>
      <input type="text" class="campo" id="color" name="color" maxlength="20" required>
    </div>

    <div>
      <label class="label-campo" for="peso">Peso (kg) *:</label>
      <input type="number" step="0.01" class="campo" id="peso" name="peso" required>
    </div>

    <div>
      <label class="label-campo" for="fecha_de_ingreso">Fecha de ingreso *:</label>
      <input type="datetime-local" class="campo" id="fecha_de_ingreso" name="fecha_de_ingreso" required>
    </div>

    <div>
      <label class="label-campo" for="costo_de_adquisicion">Costo de adquisición:</label>
      <input type="number" step="0.01" min="0" class="campo" id="costo_de_adquisicion" name="costo_de_adquisicion">
    </div>

    <div>
      <label class="label-campo" for="precio_de_venta">Precio de venta *:</label>
      <input type="number" step="0.01" min="0" class="campo" id="precio_de_venta" name="precio_de_venta" required>
    </div>

    <div>
      <label class="label-campo" for="estado">Estado:</label>
      <select class="campo" id="estado" name="estado">
        <option value="">Seleccionar...</option>
        <option value="Disponible">Disponible</option>
        <option value="Reservado">Reservado</option>
        <option value="Vendido">Vendido</option>
      </select>
    </div>

    <div class="campo-completo">
      <label class="label-campo" for="caracteristicas">Características *:</label>
      <textarea class="campo" id="caracteristicas" name="caracteristicas" rows="2" required></textarea>
    </div>

    <div class="campo-completo">
      <label class="label-campo" for="procedencia">Procedencia *:</label>
      <textarea class="campo" id="procedencia" name="procedencia" rows="2" required></textarea>
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
      <textarea class="campo" id="vacunas" name="vacunas" rows="2" required></textarea>
    </div>

    <div>
      <label class="label-campo" for="desparasitacion">Desparasitación *:</label>
      <input type="date" class="campo" id="desparasitacion" name="desparasitacion" required>
    </div>

    <div class="campo-completo">
      <label class="label-campo" for="observaciones">Observaciones *:</label>
      <textarea class="campo" id="observaciones" name="observaciones" rows="2" required placeholder="Observaciones generales"></textarea>
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

</body>
</html>