<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veterinaria PetCare - Agregar mascota</title>
  <link rel="stylesheet" href="css/estilos_base.css">
</head>
<body>

  <header>
    <div class="marca">
      <img src="/img/logo.svg" alt="Veterinaria PetCare">
    </div>

    <div class="sesion">
      <span>Empleado: Nombre del Empleado</span>
      <a href="#">Cerrar sesión</a>
    </div>
  </header>

  <nav>
    <ul>
      <li><a href="mascotas.php" class="activo">Mascotas</a></li>

      <li><a href="#" class="deshabilitado">Clientes</a></li>
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
    <h1>Registrar mascota</h1>

    <form action="guardar_mascota.php" method="POST" enctype="multipart/form-data">

<fieldset>
            <legend style="font-weight: bold; color: #333; padding: 0 5px;"> Datos Básicos</legend>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
              <div>
                <label class="label-campo" class="label-campo" for="nombre" >Nombre *:</label>
                <input type="text" class="campo" id="nombre" name="nombre" required >
              </div>

              <div>
                <label class="label-campo" for="especie" >Especie *:</label>
                <input type="text" class="campo" id="especie" name="especie" placeholder="Ej. Perro, Gato" required >
              </div>

              <div>
                <label class="label-campo" for="raza" >Raza:</label>
                <input type="text" class="campo" id="raza" name="raza" >
              </div>

              <div>
                <label class="label-campo" for="sexo" >Sexo *:</label>
                <select class="campo" id="sexo" name="sexo" required >
                  <option value="Macho">Macho</option>
                  <option value="Hembra">Hembra</option>
                </select>
              </div>

              <div>
                <label class="label-campo" for="edad" >Edad / Fecha nacimiento *:</label>
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
                <label class="label-campo" for="id_cliente" >Dueño (ID Cliente) *:</label>
                <input type="number" class="campo" id="id_cliente" name="id_cliente" required placeholder="ID del Cliente" >
              </div>

              <div>
                <label class="label-campo" for="id_veterinario" >Veterinario Asignado (ID):</label>
                <input type="number" class="campo" id="id_veterinario" name="id_veterinario" placeholder="ID del Veterinario" >
              </div>
            </div>

            <div style="margin-top: 12px;">
              <label class="label-campo" for="fotografia" >Fotografía:</label>
              <input type="file" class="campo" id="fotografia" name="fotografia" accept="image/*" >
            </div>
          </fieldset>

          <!-- 2. Datos clínicos -->
          <fieldset>
            <legend> Datos Clínicos</legend>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
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
                <label class="label-campo" for="condiciones_especiales" >Condiciones Especiales:</label>
                <textarea class="campo" id="condiciones_especiales" name="condiciones_especiales" rows="2" ></textarea>
              </div>

              <div>
                <label class="label-campo" for="vacunas" >Vacunas:</label>
                <textarea class="campo" id="vacunas" name="vacunas" rows="2" ></textarea>
              </div>

              <div>
                <label class="label-campo" for="ultima_desparasitacion" >Última Desparasitación:</label>
                <input type="date" class="campo" id="ultima_desparasitacion" name="ultima_desparasitacion" >
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

</body>
</html>
