<?php
require_once __DIR__ . '/../models/Mascota.php';

$idMascota = filter_input(INPUT_GET, 'id_mascota', FILTER_VALIDATE_INT);

if ($idMascota === false || $idMascota === null) {
    header('Location: mascotas.php');
    exit;
}

$mascota = obtenerMascotaPorId($idMascota);

if (!$mascota) {
    header('Location: mascotas.php');
    exit;
}

// Función chiquita para no repetir htmlspecialchars() en cada campo
function v($valor) {
    return htmlspecialchars((string)($valor ?? ''));
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veterinaria PetCare - Editar mascota</title>
  <link rel="stylesheet" href="css/estilos_base_v2.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
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
    

    <form action="../controllers/actualizar_mascota.php" method="POST" enctype="multipart/form-data">

      <input type="hidden" id="id_mascota" name="id_mascota" value="<?= v($mascota['id_mascota']) ?>">

      <fieldset>
        <legend>Datos básicos</legend>

        <div class="grid-formulario">
          <div>
            <label class="label-campo" for="nombre">Nombre *:</label>
            <input type="text" class="campo" id="nombre" name="nombre" value="<?= v($mascota['nombre']) ?>" required>
          </div>

          <div>
            <label class="label-campo" for="especie">Especie *:</label>
            <input type="text" class="campo" id="especie" name="especie" value="<?= v($mascota['especie']) ?>" required>
          </div>

          <div>
            <label class="label-campo" for="raza">Raza:</label>
            <input type="text" class="campo" id="raza" name="raza" value="<?= v($mascota['raza']) ?>">
          </div>

          <div>
            <label class="label-campo" for="sexo">Sexo:</label>
            <select class="campo" id="sexo" name="sexo" required>
              <option value="Macho" <?= $mascota['sexo'] === 'Macho' ? 'selected' : '' ?>>Macho</option>
              <option value="Hembra" <?= $mascota['sexo'] === 'Hembra' ? 'selected' : '' ?>>Hembra</option>
            </select>
          </div>

          <div>
            <label class="label-campo" for="edad">Edad / Fecha nacimiento *:</label>
            <input type="date" class="campo" id="edad" name="edad" value="<?= v($mascota['edad']) ?>" required>
          </div>

          <div>
            <label class="label-campo" for="color">Color:</label>
            <input type="text" class="campo" id="color" name="color" value="<?= v($mascota['color']) ?>">
          </div>

          <div>
            <label class="label-campo" for="peso">Peso (kg):</label>
            <input type="number" step="0.01" class="campo" id="peso" name="peso" value="<?= v($mascota['peso']) ?>">
          </div>

          <div>
            <label class="label-campo" for="tamanio">Tamaño:</label>
            <select class="campo" id="tamanio" name="tamanio">
              <option value="">Seleccionar...</option>
              <option value="Pequeño" <?= $mascota['tamanio'] === 'Pequeño' ? 'selected' : '' ?>>Pequeño</option>
              <option value="Mediano" <?= $mascota['tamanio'] === 'Mediano' ? 'selected' : '' ?>>Mediano</option>
              <option value="Grande" <?= $mascota['tamanio'] === 'Grande' ? 'selected' : '' ?>>Grande</option>
              <option value="Gigante" <?= $mascota['tamanio'] === 'Gigante' ? 'selected' : '' ?>>Gigante</option>
            </select>
          </div>

          <div>
            <label class="label-campo" for="id_cliente">Dueño (ID Cliente) *:</label>
            <input type="number" class="campo" id="id_cliente" name="id_cliente" value="<?= v($mascota['id_cliente']) ?>" required placeholder="ID del Cliente">
          </div>

          <div>
            <label class="label-campo" for="id_veterinario">Veterinario Asignado (ID):</label>
            <input type="number" class="campo" id="id_veterinario" name="id_veterinario" value="<?= v($mascota['id_veterinario']) ?>" placeholder="ID del Veterinario">
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
            <textarea class="campo" id="alergias" name="alergias" rows="2"><?= v($mascota['alergias']) ?></textarea>
          </div>

          <div>
            <label class="label-campo" for="enfermedades">Enfermedades:</label>
            <textarea class="campo" id="enfermedades" name="enfermedades" rows="2"><?= v($mascota['enfermedades']) ?></textarea>
          </div>

          <div>
            <label class="label-campo" for="medicamentos">Medicamentos:</label>
            <textarea class="campo" id="medicamentos" name="medicamentos" rows="2"><?= v($mascota['medicamentos']) ?></textarea>
          </div>

          <div>
            <label class="label-campo" for="condiciones_especiales">Condiciones Especiales:</label>
            <textarea class="campo" id="condiciones_especiales" name="condiciones_especiales" rows="2"><?= v($mascota['condiciones_especiales']) ?></textarea>
          </div>

          <div>
            <label class="label-campo" for="vacunas">Vacunas:</label>
            <textarea class="campo" id="vacunas" name="vacunas" rows="2"><?= v($mascota['vacunas']) ?></textarea>
          </div>

          <div>
            <label class="label-campo" for="ultima_desparasitacion">Última Desparasitación:</label>
            <input type="date" class="campo" id="ultima_desparasitacion" name="ultima_desparasitacion" value="<?= v($mascota['ultima_desparasitacion']) ?>">
          </div>

          <!-- campos que faltaron -->
              <div>
                <label class="label-campo" for="temperamento">Temperamento:</label>
                <textarea class="campo" id="temperamento" name="temperamento" rows="2"><?= v($mascota['temperamento'] ?? '') ?></textarea>
              </div>

              <div>
                <label class="label-campo" for="restricciones_para_manejo">Restricciones para manejo:</label>
                <textarea class="campo" id="restricciones_para_manejo" name="restricciones_para_manejo" rows="2"><?= v($mascota['restricciones_para_manejo'] ?? '') ?></textarea>
              </div>

              <div>
                <label class="label-campo" for="observaciones">Observaciones:</label>
                <textarea class="campo" id="observaciones" name="observaciones" rows="2"><?= v($mascota['observaciones'] ?? '') ?></textarea>
              </div>

              <div>
                <label class="label-campo">Estado:</label>
                <div class="estado-fila">
                  <?php $activo = !isset($mascota['esta_activo']) || $mascota['esta_activo'] == 1; ?>
                  <!-- Botón Switch -->
                  <label class="switch">
                    <input type="checkbox" id="esta_activo" onchange="cambiarEstado(this)" <?= $activo ? 'checked' : '' ?>>
                    <span class="slider round"></span>
                  </label>

                  <!-- Caja de texto visual NO editable -->
                  <input type="text" id="texto_estado" value="<?= $activo ? 'Activo' : 'Inactivo' ?>" readonly disabled class="campo estado-texto">

                  <!-- Campo oculto para enviar 1 o 0 a PHP -->
                  <input type="hidden" id="activo" name="activo" value="<?= $activo ? '1' : '0' ?>">

                </div>
              </div>
            </div>
          </fieldset>

      <div class="acciones-formulario">
        <a class="btn-secundario" href="mascotas.php">Cancelar</a>
        <button type="submit">Guardar cambios</button>
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
    function cambiarEstado(check) {
      document.getElementById('activo').value = check.checked ? '1' : '0';
      document.getElementById('texto_estado').value = check.checked ? 'Activo' : 'Inactivo';
    }
  </script>

</body>
</html>