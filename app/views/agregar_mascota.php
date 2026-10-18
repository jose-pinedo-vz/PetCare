<?php
// Iniciar la sesión al principio
session_start();

// Obtener errores y datos enviados previamente (si existen)
$errores = $_SESSION['errores'] ?? [];
$old     = $_SESSION['old'] ?? [];

// Limpiar la sesión para que las alertas no reaparezcan al recargar
unset($_SESSION['errores']);
unset($_SESSION['old']);
?>

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
    

    <!-- BLOQUE DE ALERTAS: Muestra los errores guardados en la sesión -->
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

    <form action="guardar_mascota.php" method="POST" enctype="multipart/form-data">

<fieldset>
            <legend> Datos básicos</legend>

            <div class="grid-formulario">
              <div>
                <label class="label-campo" class="label-campo" for="nombre" >Nombre *:</label>
                <input type="text" class="campo" id="nombre" name="nombre" value="<?= htmlspecialchars($old['nombre'] ?? '') ?>" required >
              </div>

              <div>
                <label class="label-campo" for="especie" >Especie *:</label>
                <!-- <input type="text" class="campo" id="especie" name="especie" placeholder="Ej. Perro, Gato" value="" required >-->
                <select class="campo" id="especie" name="especie" required >
                  <option value="">Seleccionar...</option>
                  <option value="Perro" <?= ($old['especie'] ?? '') === 'Perro' ? 'selected' : '' ?>>Perro</option>
                  <option value="Gato" <?= ($old['especie'] ?? '') === 'Gato' ? 'selected' : '' ?>>Gato</option>
                  <option value="Conejo" <?= ($old['especie'] ?? '') === 'Conejo' ? 'selected' : '' ?>>Conejo</option>
                  <option value="Hamster" <?= ($old['especie'] ?? '') === 'Hamster' ? 'selected' : '' ?>>Hamster</option>
                  <option value="Hurón" <?= ($old['especie'] ?? '') === 'Hurón' ? 'selected' : '' ?>>Hurón</option>
                  <option value="Erizo" <?= ($old['especie'] ?? '') === 'Erizo' ? 'selected' : '' ?>>Erizo</option>
                  <option value="Tortuga" <?= ($old['especie'] ?? '') === 'Tortuga' ? 'selected' : '' ?>>Tortuga</option>
                  <option value="Ave" <?= ($old['especie'] ?? '') === 'Ave' ? 'selected' : '' ?>>Ave</option>
                </select>
              </div>

              <div>
                <label class="label-campo" for="raza" >Raza *:</label>
                <input type="text" class="campo" id="raza" name="raza" value="<?= htmlspecialchars($old['raza'] ?? '') ?>" >
              </div>

              <div>
                <label class="label-campo" for="sexo" >Sexo *:</label>
                <select class="campo" id="sexo" name="sexo" required >
                  <option value="">Seleccionar...</option>
                  <option value="Macho" <?= ($old['sexo'] ?? '') === 'Macho' ? 'selected' : '' ?>>Macho</option>
                  <option value="Hembra" <?= ($old['sexo'] ?? '') === 'Hembra' ? 'selected' : '' ?>>Hembra</option>
                </select>
              </div>

              <div>
                <label class="label-campo" for="edad" >Edad / Fecha nacimiento *:</label>
                <input type="date" class="campo" id="edad" name="edad" value="<?= htmlspecialchars($old['edad'] ?? '') ?>" required >
              </div>

              <div>
                <label class="label-campo" for="color" >Color:</label>
                <!-- <input type="text" class="campo" id="color" name="color" value=""> -->
                 <select class="campo" id="color" name="color" required>
                  <option value="">Seleccionar...</option>
                  <option value="Blanco" <?= ($old['color'] ?? '') === 'Blanco' ? 'selected' : '' ?>>Blanco</option>
                  <option value="Negro" <?= ($old['color'] ?? '') === 'Negro' ? 'selected' : '' ?>>Negro</option>
                  <option value="Gris" <?= ($old['color'] ?? '') === 'Gris' ? 'selected' : '' ?>>Gris</option>
                  <option value="Café" <?= ($old['color'] ?? '') === 'Café' ? 'selected' : '' ?>>Café</option>
                  <option value="Amarillo" <?= ($old['color'] ?? '') === 'Amarillo' ? 'selected' : '' ?>>Amarillo</option>
                  <option value="Verde" <?= ($old['color'] ?? '') === 'Verde' ? 'selected' : '' ?>>Verde</option>
                  <option value="Rojo" <?= ($old['color'] ?? '') === 'Rojo' ? 'selected' : '' ?>>Rojo</option>
                  <option value="Blanco y negro" <?= ($old['color'] ?? '') === 'Blanco y negro' ? 'selected' : '' ?>>Blanco y negro</option>
                  <option value="Café y blanco" <?= ($old['color'] ?? '') === 'Café y blanco' ? 'selected' : '' ?>>Café y blanco</option>
                  <option value="Café y amarillo" <?= ($old['color'] ?? '') === 'Café y amarillo' ? 'selected' : '' ?>>Café y amarillo</option>
                  <option value="Blanco y gris" <?= ($old['color'] ?? '') === 'Blanco y gris' ? 'selected' : '' ?>>Blanco y gris</option>
                  <option value="Verde y amarillo" <?= ($old['color'] ?? '') === 'Verde y amarillo' ? 'selected' : '' ?>>Verde y amarillo</option>
                  <option value="Blanco, amarillo y negro" <?= ($old['color'] ?? '') === 'Blanco, amarillo y negro' ? 'selected' : '' ?>>Blanco, amarillo y negro</option>
                  <option value="Otro" <?= ($old['color'] ?? '') === 'Otro' ? 'selected' : '' ?>>Otro (Especificar)</option>
                </select>
              </div>

              <div>
                <label class="label-campo" for="peso" >Peso (kg) *:</label>
                <input type="number" step="0.01" class="campo" id="peso" name="peso" value="<?= htmlspecialchars($old['peso'] ?? '') ?>">
              </div>

              <div>
                <label class="label-campo" for="tamanio" >Tamaño:</label>
                <?php $tam = $old['tamanio'] ?? ''; ?>
                <select class="campo" id="tamanio" name="tamanio" >
                  <option value="">Seleccionar...</option>
                  <option value="Pequeño" <?= $tam === 'Pequeño' ? 'selected' : '' ?>>Pequeño</option>
                  <option value="Mediano" <?= $tam === 'Mediano' ? 'selected' : '' ?>>Mediano</option>
                  <option value="Grande" <?= $tam === 'Grande' ? 'selected' : '' ?>>Grande</option>
                  <option value="Gigante" <?= $tam === 'Gigante' ? 'selected' : '' ?>>Gigante</option>
                </select>
              </div>

              <div>
                <label class="label-campo" for="id_cliente" >Dueño (ID cliente) *:</label>
                <input type="number" class="campo" id="id_cliente" name="id_cliente" required placeholder="ID del Cliente" value="<?= htmlspecialchars($old['id_cliente'] ?? $_GET['id_cliente'] ?? '') ?>" >
              </div>

              <div>
                <label class="label-campo" for="id_veterinario" >Veterinario asignado (ID):</label>
                <input type="number" class="campo" id="id_veterinario" name="id_veterinario" placeholder="ID del Veterinario" value="<?= htmlspecialchars($old['id_veterinario'] ?? '') ?>">
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
                <textarea class="campo" id="alergias" name="alergias" rows="2" ><?= htmlspecialchars($old['alergias'] ?? '') ?></textarea>
              </div>

              <div>
                <label class="label-campo" for="enfermedades" >Enfermedades:</label>
                <textarea class="campo" id="enfermedades" name="enfermedades" rows="2" ><?= htmlspecialchars($old['enfermedades'] ?? '') ?></textarea>
              </div>

              <div>
                <label class="label-campo" for="medicamentos" >Medicamentos:</label>
                <textarea class="campo" id="medicamentos" name="medicamentos" rows="2" ><?= htmlspecialchars($old['medicamentos'] ?? '') ?></textarea>
              </div>

              <div>
                <label class="label-campo" for="condiciones_especiales" >Condiciones especiales:</label>
                <textarea class="campo" id="condiciones_especiales" name="condiciones_especiales" rows="2" ><?= htmlspecialchars($old['condiciones_especiales'] ?? '') ?></textarea>
              </div>

              <div>
                <label class="label-campo" for="vacunas" >Vacunas:</label>
                <textarea class="campo" id="vacunas" name="vacunas" rows="2" ><?= htmlspecialchars($old['vacunas'] ?? '') ?></textarea>
              </div>

              <div>
                <label class="label-campo" for="ultima_desparasitacion" >Última desparasitación:</label>
                <input type="date" class="campo" id="ultima_desparasitacion" name="ultima_desparasitacion" value="<?= htmlspecialchars($old['ultima_desparasitacion'] ?? '') ?>">
              </div>

              <div>
                <label class="label-campo" for="temperamento">Temperamento:</label>
                <textarea class="campo" id="temperamento" name="temperamento" rows="2" placeholder="Ej. Dócil, juguetón, tímido"><?= htmlspecialchars($old['temperamento'] ?? '') ?></textarea>
              </div>

              <div>
                <label class="label-campo" for="restricciones_para_manejo">Restricciones para manejo:</label>
                <textarea class="campo" id="restricciones_para_manejo" name="restricciones_para_manejo" rows="2" placeholder="Ej. Cuidado con las patas"><?= htmlspecialchars($old['restricciones_para_manejo'] ?? '') ?></textarea>
              </div>

              <div class="campo-completo">
                <label class="label-campo" for="observaciones">Observaciones:</label>
                <textarea class="campo" id="observaciones" name="observaciones" rows="2" placeholder="Observaciones generales"><?= htmlspecialchars($old['observaciones'] ?? '') ?></textarea>
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
