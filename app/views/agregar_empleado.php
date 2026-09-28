<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veterinaria PetCare - Agregar empleado</title>
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
      <li><a href="mascotas.php">Mascotas</a></li>
      <li><a href="citas.php">citas</a></li>
      <li><a href="clientes.php">Clientes</a></li>
      <li><a href="empleados.php" class="activo">Empleados</a></li>

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

    <h1>Agregar empleado</h1>

    <form action="guardar_empleado.php" method="POST">

      <fieldset>

        <legend>Datos Personales</legend>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">

          <div>
            <label class="label-campo" for="id_empleado">
              ID Empleado *:
            </label>

            <input
              type="number"
              class="campo"
              id="id_empleado"
              name="id_empleado"
              required>
          </div>


          <div>
            <label class="label-campo" for="nombre">
              Nombre *:
            </label>

            <input
              type="text"
              class="campo"
              id="nombre"
              name="nombre"
              maxlength="20"
              required>
          </div>


          <div>
            <label class="label-campo" for="apellido">
              Apellido *:
            </label>

            <input
              type="text"
              class="campo"
              id="apellido"
              name="apellido"
              maxlength="40"
              required>
          </div>

        </div>

      </fieldset>

      <fieldset>

        <legend>Datos de Contacto</legend>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">

          <div>
            <label class="label-campo" for="telefono">
              Teléfono *:
            </label>

            <input
              type="tel"
              class="campo"
              id="telefono"
              name="telefono"
              maxlength="15"
              required>
          </div>


          <div>
            <label class="label-campo" for="correo">
              Correo Electrónico *:
            </label>

            <input
              type="email"
              class="campo"
              id="correo"
              name="correo"
              maxlength="100"
              required>
          </div>

        </div>

      </fieldset>

      <fieldset>

        <legend>Domicilio</legend>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">

          <div>
            <label class="label-campo" for="calle">
              Calle *:
            </label>

            <input
              type="text"
              class="campo"
              id="calle"
              name="calle"
              maxlength="100"
              required>
          </div>


          <div>
            <label class="label-campo" for="numero_exterior">
              Número Exterior *:
            </label>

            <input
              type="text"
              class="campo"
              id="numero_exterior"
              name="numero_exterior"
              maxlength="15"
              required>
          </div>


          <div>
            <label class="label-campo" for="numero_interior">
              Número Interior:
            </label>

            <input
              type="text"
              class="campo"
              id="numero_interior"
              name="numero_interior"
              maxlength="15">
          </div>


          <div>
            <label class="label-campo" for="colonia">
              Colonia *:
            </label>

            <input
              type="text"
              class="campo"
              id="colonia"
              name="colonia"
              maxlength="60"
              required>
          </div>


          <div>
            <label class="label-campo" for="ciudad">
              Ciudad *:
            </label>

            <input
              type="text"
              class="campo"
              id="ciudad"
              name="ciudad"
              maxlength="60"
              required>
          </div>


          <div>
            <label class="label-campo" for="estado">
              Estado *:
            </label>

            <input
              type="text"
              class="campo"
              id="estado"
              name="estado"
              maxlength="60"
              required>
          </div>


          <div>
            <label class="label-campo" for="codigo_postal">
              Código Postal *:
            </label>

            <input
              type="text"
              class="campo"
              id="codigo_postal"
              name="codigo_postal"
              maxlength="10"
              required>
          </div>

        </div>

      </fieldset>

      <fieldset>

        <legend>Información Laboral</legend>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">

          <div>
            <label class="label-campo" for="puesto">
              Puesto *:
            </label>

            <input
              type="text"
              class="campo"
              id="puesto"
              name="puesto"
              maxlength="35"
              required>
          </div>


          <div>
            <label class="label-campo" for="especialidad">
              Especialidad:
            </label>

            <input
              type="text"
              class="campo"
              id="especialidad"
              name="especialidad"
              maxlength="50">
          </div>


          <div>
            <label class="label-campo" for="num_cedula_profesional">
              Cédula Profesional:
            </label>

            <input
              type="text"
              class="campo"
              id="num_cedula_profesional"
              name="num_cedula_profesional"
              maxlength="20">
          </div>


          <div>
            <label class="label-campo" for="fecha_de_contratacion">
              Fecha de Contratación:
            </label>

            <input
              type="date"
              class="campo"
              id="fecha_de_contratacion"
              name="fecha_de_contratacion">
          </div>


          <div>
            <label class="label-campo" for="horario">
              Horario *:
            </label>

            <input
              type="text"
              class="campo"
              id="horario"
              name="horario"
              maxlength="20"
              required>
          </div>

        </div>

      </fieldset>

      <div style="margin-top: 20px;">
        <button type="submit" class="btn">Guardar</button>
        <a class="btn" href="empleados.php" style="background: #757575; margin-left: 8px;">Regresar</a>
      </div>

    </form>

  </main>

  <footer>
    <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
  </footer>

</body>
</html>