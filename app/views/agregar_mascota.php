<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veterinaria PetCare - Agregar mascota</title>
  <link rel="stylesheet" href="css/estilos_base_v2.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
</head>
<body>

  <div class="layout">

  <?php $paginaActiva = 'mascotas'; include __DIR__ . '/layout/sidebar.php'; ?>


  <main>
      <div class="migaja"><a href="mascotas.php" class="link-tenue">Gestión de Mascotas</a> / Agregar</div>
      <div class="encabezado-pagina">
        <h1>Registrar mascota</h1>
      </div>

      <div class="tarjeta">
    

    <form action="guardar_mascota.php" method="POST" enctype="multipart/form-data">

<fieldset>
            <legend> Datos básicos</legend>

            <div class="grid-formulario">
              <div>
                <label class="label-campo" for="nombre" >Nombre:</label>
                <input type="text" class="campo" id="nombre" name="nombre" required >
              </div>

              <div>
                <label class="label-campo" for="especie" >Especie:</label>
                <input type="text" class="campo" id="especie" name="especie" placeholder="Ej. Perro, Gato" required >
              </div>

              <div>
                <label class="label-campo" for="raza" >Raza:</label>
                <input type="text" class="campo" id="raza" name="raza" >
              </div>

              <div>
                <label class="label-campo" for="sexo" >Sexo:</label>
                <select class="campo" id="sexo" name="sexo" required >
                  <option value="Macho">Macho</option>
                  <option value="Hembra">Hembra</option>
                </select>
              </div>

              <div>
                <label class="label-campo" for="edad" >Edad / Fecha nacimiento:</label>
                <input type="date" class="campo" id="edad" name="edad" required >
              </div>

              <div>
                <label class="label-campo" for="color" >Color:</label>
                <input type="text" class="campo" id="color" name="color" >
              </div>

              <div>
                <label class="label-campo" for="peso" >Peso (kg):</label>
                <input type="number" step="0.01" class="campo" id="peso" name="peso" >
              </div>

              <div>
                <label class="label-campo" for="tamanio" >Tamaño:</label>
                <select class="campo" id="tamanio" name="tamanio" >
                  <option value="">Seleccionar...</option>
                  <option value="Pequeño">Pequeño</option>
                  <option value="Mediano">Mediano</option>
                  <option value="Grande">Grande</option>
                  <option value="Gigante">Gigante</option>
                </select>
              </div>

              <div>
                <label class="label-campo" for="id_cliente" >Dueño (ID cliente):</label>
                <input type="number" class="campo" id="id_cliente" name="id_cliente" required placeholder="ID del Cliente" value="<?php echo htmlspecialchars((string)($_GET['id_cliente'] ?? '')); ?>" >
              </div>

              <div>
                <label class="label-campo" for="id_veterinario" >Veterinario asignado (ID):</label>
                <input type="number" class="campo" id="id_veterinario" name="id_veterinario" placeholder="ID del Veterinario" >
              </div>
            </div>

            <div class="campo-completo-sep">
              <label class="label-campo" for="fotografia" >Fotografía:</label>
              <input type="file" class="campo" id="fotografia" name="fotografia" accept="image/*" >
              <img id="preview-crop" alt="Vista previa recortada (cuadrado)">
              <p class="nota-campo">Al elegir una foto se abrirá el cuadrado de recorte.</p>
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

          <!-- 2. Datos clínicos -->
          <fieldset>
            <legend> Datos clínicos</legend>

            <div class="grid-formulario">
              <div>
                <label class="label-campo" for="alergias" >Alergias:</label>
                <textarea class="campo" id="alergias" name="alergias" rows="2" ></textarea>
              </div>

              <div>
                <label class="label-campo" for="enfermedades" >Enfermedades:</label>
                <textarea class="campo" id="enfermedades" name="enfermedades" rows="2" ></textarea>
              </div>

              <div>
                <label class="label-campo" for="medicamentos" >Medicamentos:</label>
                <textarea class="campo" id="medicamentos" name="medicamentos" rows="2" ></textarea>
              </div>

              <div>
                <label class="label-campo" for="condiciones_especiales" >Condiciones especiales:</label>
                <textarea class="campo" id="condiciones_especiales" name="condiciones_especiales" rows="2" ></textarea>
              </div>

              <div>
                <label class="label-campo" for="vacunas" >Vacunas:</label>
                <textarea class="campo" id="vacunas" name="vacunas" rows="2" ></textarea>
              </div>

              <div>
                <label class="label-campo" for="ultima_desparasitacion" >Última desparasitación:</label>
                <input type="date" class="campo" id="ultima_desparasitacion" name="ultima_desparasitacion" >
              </div>

              <div>
                <label class="label-campo" for="temperamento">Temperamento:</label>
                <textarea class="campo" id="temperamento" name="temperamento" rows="2" placeholder="Ej. Dócil, juguetón, tímido"></textarea>
              </div>

              <div>
                <label class="label-campo" for="restricciones_para_manejo">Restricciones para manejo:</label>
                <textarea class="campo" id="restricciones_para_manejo" name="restricciones_para_manejo" rows="2" placeholder="Ej. Cuidado con las patas"></textarea>
              </div>

              <div class="campo-completo">
                <label class="label-campo" for="observaciones">Observaciones:</label>
                <textarea class="campo" id="observaciones" name="observaciones" rows="2" placeholder="Observaciones generales"></textarea>
              </div>
            </div>
          </fieldset>

      <div class="acciones-formulario">
        <a class="btn-secundario" href="mascotas.php">Cancelar</a>
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
  <script src="js/crop-mascota.js"></script>

</body>
</html>