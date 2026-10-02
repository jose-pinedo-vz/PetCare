<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veterinaria PetCare - Editar empleado</title>
  <link rel="stylesheet" href="css/estilos_base.css">
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
      <li><a href="mascotas.php">Mascotas</a></li>
      <li><a href="citas.php">Citas</a></li>
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

    <h1>Editar empleado</h1>


    <form action="guardar_empleado.php" method="POST">


      <input
        type="hidden"
        id="id_empleado"
        name="id_empleado"
        value="1">

      <fieldset>

        <legend>Datos personales</legend>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">

          <div>
            <label class="label-campo" for="nombre">
              Nombre:
            </label>

            <input
              type="text"
              class="campo"
              id="nombre"
              name="nombre"
              value="Ana"
              maxlength="20"
              required>
          </div>


          <div>
            <label class="label-campo" for="apellido">
              Apellido:
            </label>

            <input
              type="text"
              class="campo"
              id="apellido"
              name="apellido"
              value="López García"
              maxlength="40"
              required>
          </div>

        </div>

      </fieldset>

      <fieldset>

        <legend>Datos de contacto</legend>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">

          <div>
            <label class="label-campo" for="telefono">
              Teléfono:
            </label>

            <input
              type="tel"
              class="campo"
              id="telefono"
              name="telefono"
              value="4921234567"
              maxlength="15"
              required>
          </div>


          <div>
            <label class="label-campo" for="correo">
              Correo electrónico:
            </label>

            <input
              type="email"
              class="campo"
              id="correo"
              name="correo"
              value="ana.lopez@gmail.com"
              maxlength="100"
              required>
          </div>

        </div>

      </fieldset>


      <!-- DOMICILIO -->

      <fieldset>

        <legend>Domicilio</legend>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">

          <div>
            <label class="label-campo" for="calle">
              Calle:
            </label>

            <input
              type="text"
              class="campo"
              id="calle"
              name="calle"
              value="Hidalgo"
              maxlength="100"
              required>
          </div>


          <div>
            <label class="label-campo" for="numero_exterior">
              Número exterior:
            </label>

            <input
              type="text"
              class="campo"
              id="numero_exterior"
              name="numero_exterior"
              value="125"
              maxlength="15"
              required>
          </div>


          <div>
            <label class="label-campo" for="numero_interior">
              Número interior:
            </label>

            <input
              type="text"
              class="campo"
              id="numero_interior"
              name="numero_interior"
              value="2"
              maxlength="15">
          </div>


          <div>
            <label class="label-campo" for="colonia">
              Colonia:
            </label>

            <input
              type="text"
              class="campo"
              id="colonia"
              name="colonia"
              value="Centro"
              maxlength="60"
              required>
          </div>


          <div>
            <label class="label-campo" for="ciudad">
              Ciudad:
            </label>

            <input
              type="text"
              class="campo"
              id="ciudad"
              name="ciudad"
              value="Zacatecas"
              maxlength="60"
              required>
          </div>


          <div>
            <label class="label-campo" for="estado">
              Estado:
            </label>

            <input
              type="text"
              class="campo"
              id="estado"
              name="estado"
              value="Zacatecas"
              maxlength="60"
              required>
          </div>


          <div>
            <label class="label-campo" for="codigo_postal">
              Código Postal:
            </label>

            <input
              type="text"
              class="campo"
              id="codigo_postal"
              name="codigo_postal"
              value="98000"
              maxlength="10"
              required>
          </div>

        </div>

      </fieldset>

      <fieldset>

        <legend>Información laboral</legend>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">

          <div>
            <label class="label-campo" for="puesto">
              Puesto:
            </label>

            <input
              type="text"
              class="campo"
              id="puesto"
              name="puesto"
              value="Veterinaria"
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
              value="Medicina veterinaria"
              maxlength="50">
          </div>


          <div>
            <label class="label-campo" for="num_cedula_profesional">
              Cédula profesional:
            </label>

            <input
              type="text"
              class="campo"
              id="num_cedula_profesional"
              name="num_cedula_profesional"
              value="VET123456"
              maxlength="20">
          </div>


          <div>
            <label class="label-campo" for="fecha_de_contratacion">
              Fecha de contratación:
            </label>

            <input
              type="date"
              class="campo"
              id="fecha_de_contratacion"
              name="fecha_de_contratacion"
              value="2024-02-15">
          </div>


          <div>
            <label class="label-campo" for="horario">
              Horario:
            </label>

            <input
              type="text"
              class="campo"
              id="horario"
              name="horario"
              value="08:00 - 16:00"
              maxlength="20"
              required>
          </div>

        </div>

      </fieldset>

      <div style="margin-top: 20px;">
        <button type="submit" class="btn">Guardar cambios</button>
        <a class="btn" href="empleados.php" style="background: #757575; margin-left: 8px;">Cancelar</a>
      </div>

    </form>

  </main>


  <footer>
    <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
  </footer>

</body>
</html>