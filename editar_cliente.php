<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veterinaria PetCare - Editar cliente</title>
  <link rel="stylesheet" href="estilos_base.css">
</head>
<body>

  <header>
    <div class="marca">
      <img src="logo.svg" alt="Veterinaria PetCare">
    </div>

    <div class="sesion">
      <span>Empleado: Nombre del Empleado</span>
      <a href="#">Cerrar sesión</a>
    </div>
  </header>

  <nav>
    <ul>
      <li><a href="mascotas.php" class="activo">Mascotas</a></li>

      <li><a href="#clientes.php" class="activo">Clientes</a></li>
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
    <h1>Editar cliente</h1>

    <!--
    -->
    <form action="actualizar_mascota.php" method="POST" enctype="multipart/form-data">

      <input type="hidden" id="id_mascota" name="id_mascota" value="1">

      <fieldset>
        <legend>Datos Básicos</legend>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
          <div>
            <label class="label-campo" for="nombre">Nombre *:</label>
            <input type="text" class="campo" id="nombre" name="nombre" value="Firulais" required>
          </div>

          <div>
            <label class="label-campo" for="especie">Especie *:</label>
            <input type="text" class="campo" id="especie" name="especie" value="Perro" required>
          </div>

          <div>
            <label class="label-campo" for="raza">Raza:</label>
            <input type="text" class="campo" id="raza" name="raza" value="Labrador">
          </div>

          <div>
            <label class="label-campo" for="sexo">Sexo *:</label>
            <select class="campo" id="sexo" name="sexo" required>
              <option value="Macho" selected>Macho</option>
              <option value="Hembra">Hembra</option>
            </select>
          </div>

          <div>
            <label class="label-campo" for="edad">Edad / Fecha nacimiento *:</label>
            <input type="date" class="campo" id="edad" name="edad" value="2023-05-10" required>
          </div>

          <div>
            <label class="label-campo" for="color">Color:</label>
            <input type="text" class="campo" id="color" name="color" value="Café">
          </div>

          <div>
            <label class="label-campo" for="peso">Peso (kg):</label>
            <input type="number" step="0.01" class="campo" id="peso" name="peso" value="28.5">
          </div>

          <div>
            <label class="label-campo" for="tamanio">Tamaño:</label>
            <select class="campo" id="tamanio" name="tamanio">
              <option value="">Seleccionar...</option>
              <option value="Pequeño">Pequeño</option>
              <option value="Mediano">Mediano</option>
              <option value="Grande" selected>Grande</option>
              <option value="Gigante">Gigante</option>
            </select>
          </div>

          <div>
            <label class="label-campo" for="id_cliente">Dueño (ID Cliente) *:</label>
            <input type="number" class="campo" id="id_cliente" name="id_cliente" value="12" required placeholder="ID del Cliente">
          </div>

          <div>
            <label class="label-campo" for="id_veterinario">Veterinario Asignado (ID):</label>
            <input type="number" class="campo" id="id_veterinario" name="id_veterinario" value="3" placeholder="ID del Veterinario">
          </div>
        </div>

        <div style="margin-top: 12px;">
          <label class="label-campo" for="fotografia">Fotografía:</label>
          <input type="file" class="campo" id="fotografia" name="fotografia" accept="image/*">
          <p style="font-size: 13px; color: var(--texto-suave); margin: 6px 0 0;">Deja este campo vacío si no quieres cambiar la foto actual.</p>
        </div>
      </fieldset>

      <!-- 2. Datos clínicos -->
      <fieldset>
        <legend>Datos Clínicos</legend>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
          <div>
            <label class="label-campo" for="alergias">Alergias:</label>
            <textarea class="campo" id="alergias" name="alergias" rows="2">Ninguna conocida</textarea>
          </div>

          <div>
            <label class="label-campo" for="enfermedades">Enfermedades:</label>
            <textarea class="campo" id="enfermedades" name="enfermedades" rows="2"></textarea>
          </div>

          <div>
            <label class="label-campo" for="medicamentos">Medicamentos:</label>
            <textarea class="campo" id="medicamentos" name="medicamentos" rows="2"></textarea>
          </div>

          <div>
            <label class="label-campo" for="condiciones_especiales">Condiciones Especiales:</label>
            <textarea class="campo" id="condiciones_especiales" name="condiciones_especiales" rows="2"></textarea>
          </div>

          <div>
            <label class="label-campo" for="vacunas">Vacunas:</label>
            <textarea class="campo" id="vacunas" name="vacunas" rows="2">Rabia, Parvovirus</textarea>
          </div>

          <div>
            <label class="label-campo" for="ultima_desparasitacion">Última Desparasitación:</label>
            <input type="date" class="campo" id="ultima_desparasitacion" name="ultima_desparasitacion" value="2026-06-15">
          </div>

          <!-- campos que faltaron -->
              <div>
                <label class="label-campo" for="nombre_del_campo">Temperamento:</label>
                <textarea class="campo" id="temperamento" name="temperamento" rows="2"></textarea>
              </div>

              <div>
                <label class="label-campo" for="nombre_del_campo">Restricciones para manejo:</label>
                <textarea class="campo" id="restricciones_para_manejo" name="restricciones_para_manejo" rows="2"></textarea>
              </div>

              <div>
                <label class="label-campo" for="nombre_del_campo">Observaciones:</label>
                <textarea class="campo" id="observaciones" name="observaciones" rows="2"></textarea>
              </div>

              <div>
                <label class="label-campo">Estado:</label>
                <div style="display: flex; align-items: center; gap: 10px; margin-top: 5px;">
                  
                  <!-- Botón Switch -->
                  <label class="switch">
                    <input type="checkbox" id="esta_activo" onchange="cambiarEstado(this)" checked>
                    <span class="slider round"></span>
                  </label>

                  <!-- Caja de texto visual NO editable -->
                  <input type="text" id="texto_estado" class="campo" value="Activo" readonly disabled style="width: 100px; text-align: center; font-weight: bold; background-color: #e9ecef; cursor: not-allowed;">

                  <!-- Campo oculto para enviar 1 o 0 a PHP -->
                  <input type="hidden" id="activo" name="activo" value="1">

                </div>
        </div>
      </fieldset>

      <button type="submit">Guardar cambios</button>
      <a class="btn" href="clientes.php">Cancelar</a>
    </form>
  </main>

  <footer>
    <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
  </footer>

</body>
</html>
