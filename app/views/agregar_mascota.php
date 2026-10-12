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
  <link rel="stylesheet" href="css/estilos_base.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
  <style>
    #preview-crop { max-width: 200px; border-radius: 8px; display: none; margin-top: 8px; }
    #modal-crop { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.7); z-index: 99; align-items: center; justify-content: center; }
    #modal-crop .caja { background: #fff; padding: 15px; border-radius: 8px; max-width: 500px; width: 90%; }
    #imagen-a-recortar { max-width: 100%; max-height: 60vh; display: block; }

    /* Estilo para el contenedor de errores */
    .alerta-errores {
      background-color: #f8d7da;
      color: #842029;
      border: 1px solid #f5c2c7;
      padding: 15px 20px;
      border-radius: 8px;
      margin-bottom: 20px;
    }
    .alerta-errores h4 {
      margin: 0 0 8px 0;
      font-size: 16px;
    }
    .alerta-errores ul {
      margin: 0;
      padding-left: 20px;
    }
  </style>
</head>
<body>

  <header>
    <div class="marca">
      <img src="img/logo3.svg" alt="Veterinaria PetCare">
    </div>

    <div class="sesion">
      <span>Empleado: Nombre del empleado</span>
      <a href="#">Cerrar sesión</a>
    </div>
  </header>

  <nav>
    <ul>
      <li><a href="mascotas.php" class="activo">Mascotas</a></li>
      <li><a href="citas.php">Citas</a></li>
      <li><a href="clientes.php">Clientes</a></li>
      <li><a href="empleados.php">Empleados</a></li>
      <li><a href="proveedores.php">Proveedores</a></li>

      
      <li><a href="#" class="deshabilitado">Inventario</a></li>
      <li><a href="#" class="deshabilitado">Ventas</a></li>
      <li><a href="#" class="deshabilitado">Servicios</a></li>
      <li><a href="#" class="deshabilitado">Adopción y venta</a></li>
      <li><a href="#" class="deshabilitado">Veterinaria</a></li>
      <li><a href="#" class="deshabilitado">Pagos</a></li>
      <li><a href="#" class="deshabilitado">Pagos con tarjeta</a></li>
    </ul>
  </nav>

  <main>
    <h1>Registrar mascota</h1>

    <!-- BLOQUE DE ALERTAS: Muestra los errores guardados en la sesión -->
    <?php if (!empty($errores)): ?>
      <div class="alerta-errores">
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
            <legend style="font-weight: bold; color: #333; padding: 0 5px;"> Datos básicos</legend>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
              <div>
                <label class="label-campo" class="label-campo" for="nombre" >Nombre:</label>
                <input type="text" class="campo" id="nombre" name="nombre" value="<?= htmlspecialchars($old['nombre'] ?? '') ?>" required >
              </div>

              <div>
                <label class="label-campo" for="especie" >Especie:</label>
                <input type="text" class="campo" id="especie" name="especie" placeholder="Ej. Perro, Gato" value="<?= htmlspecialchars($old['especie'] ?? '') ?>" required >
              </div>

              <div>
                <label class="label-campo" for="raza" >Raza:</label>
                <input type="text" class="campo" id="raza" name="raza" value="<?= htmlspecialchars($old['raza'] ?? '') ?>" >
              </div>

              <div>
                <label class="label-campo" for="sexo" >Sexo:</label>
                <select class="campo" id="sexo" name="sexo" required >
                  <option value="Macho" <?= ($old['sexo'] ?? '') === 'Macho' ? 'selected' : '' ?>>Macho</option>
                  <option value="Hembra" <?= ($old['sexo'] ?? '') === 'Hembra' ? 'selected' : '' ?>>Hembra</option>
                </select>
              </div>

              <div>
                <label class="label-campo" for="edad" >Edad / Fecha nacimiento:</label>
                <input type="date" class="campo" id="edad" name="edad" value="<?= htmlspecialchars($old['edad'] ?? '') ?>" required >
              </div>

              <div>
                <label class="label-campo" for="color" >Color:</label>
                <input type="text" class="campo" id="color" name="color" value="<?= htmlspecialchars($old['color'] ?? '') ?>">
              </div>

              <div>
                <label class="label-campo" for="peso" >Peso (kg):</label>
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
                <label class="label-campo" for="id_cliente" >Dueño (ID cliente):</label>
                <input type="number" class="campo" id="id_cliente" name="id_cliente" required placeholder="ID del Cliente" value="<?= htmlspecialchars($old['id_cliente'] ?? '') ?>" >
              </div>

              <div>
                <label class="label-campo" for="id_veterinario" >Veterinario asignado (ID):</label>
                <input type="number" class="campo" id="id_veterinario" name="id_veterinario" placeholder="ID del Veterinario" value="<?= htmlspecialchars($old['id_veterinario'] ?? '') ?>">
              </div>
            </div>

            <div style="margin-top: 12px;">
              <label class="label-campo" for="fotografia" >Fotografía:</label>
              <input type="file" class="campo" id="fotografia" name="fotografia" accept="image/*" >
              <img id="preview-crop" alt="Vista previa recortada (cuadrado)">
              <p style="font-size: 13px; color: var(--texto-suave); margin: 6px 0 0;">Al elegir una foto se abrirá el cuadrado de recorte.</p>
            </div>

            <div id="modal-crop">
              <div class="caja">
                <h3 style="margin-top:0;">Recortar foto (cuadrado)</h3>
                <img id="imagen-a-recortar" alt="Imagen a recortar">
                <div style="margin-top:10px; text-align:right; display:flex; gap:8px; justify-content:flex-end;">
                  <button type="button" id="btn-cancelar-crop">Cancelar</button>
                  <button type="button" id="btn-recortar">Recortar y usar</button>
                </div>
              </div>
            </div>
          </fieldset>

          <!-- 2. Datos clínicos -->
          <fieldset>
            <legend> Datos clínicos</legend>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
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

              <div style="grid-column: 1 / -1;">
                <label class="label-campo" for="observaciones">Observaciones:</label>
                <textarea class="campo" id="observaciones" name="observaciones" rows="2" placeholder="Observaciones generales"><?= htmlspecialchars($old['observaciones'] ?? '') ?></textarea>
              </div>
            </div>
          </fieldset>

      <button type="submit">Guardar</button>
      <a class="btn" href="mascotas.php">Cancelar</a>
    </form>
  </main>

  <footer>
    <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
  </footer>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
  <script src="js/crop-mascota.js"></script>

</body>
</html>
