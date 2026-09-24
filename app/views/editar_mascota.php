<?php
require_once __DIR__ . '/../models/Mascota.php';

$idMascota = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

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
  <link rel="stylesheet" href="css/estilos_base.css">
</head>
<body>

  <header>
    <div class="marca">
      <img src="img/logo.svg" alt="Veterinaria PetCare">
    </div>

    <div class="sesion">
      <span>Empleado: Nombre del Empleado</span>
      <a href="#">Cerrar sesión</a>
    </div>
  </header>

  <nav>
    <ul>
      <li><a href="mascotas.php" class="activo">Mascotas</a></li>
      <li><a href="clientes.php">Clientes</a></li>
      <li><a href="citas.html">Citas</a></li>
      <li><a href="#" class="deshabilitado">Empleados</a></li>
      <li><a href="#" class="deshabilitado">Proveedores</a></li>
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
    <h1>Editar mascota</h1>

    <form action="actualizar_mascota.php" method="POST" enctype="multipart/form-data">

      <input type="hidden" id="id_mascota" name="id_mascota" value="<?= v($mascota['id_mascota']) ?>">

      <fieldset>
        <legend>Datos Básicos</legend>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
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
            <label class="label-campo" for="sexo">Sexo *:</label>
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

        <div style="margin-top: 12px;">
          <label class="label-campo" for="fotografia">Fotografía:</label>
          <input type="file" class="campo" id="fotografia" name="fotografia" accept="image/*">
          <p style="font-size: 13px; color: var(--texto-suave); margin: 6px 0 0;">Deja este campo vacío si no quieres cambiar la foto actual.</p>
        </div>
      </fieldset>

      <fieldset>
        <legend>Datos Clínicos</legend>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
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
        </div>
      </fieldset>

      <button type="submit">Guardar cambios</button>
      <a class="btn" href="mascotas.php">Cancelar</a>
    </form>
  </main>

  <footer>
    <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
  </footer>

</body>
</html>