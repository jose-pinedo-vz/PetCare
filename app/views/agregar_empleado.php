<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>

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

    <h1>Agregar empleado</h1>

    <?php if (isset($_SESSION['error'])): ?>
      <div style="background: #ffe6e6; color: #d32f2f; padding: 12px 15px; border-radius: 6px; border: 1px solid #ffcdd2; margin-bottom: 20px; font-weight: bold;">
        <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
      </div>
    <?php endif; ?>

    <form action="../controllers/guardar_empleado.php" method="POST">

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
              Calle:
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
              Número exterior:
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
              Número interior:
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
              Colonia:
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
              Ciudad:
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
              Estado:
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
              Código Postal:
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

        <legend>Información laboral</legend>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">

          <div>
            <label class="label-campo" for="puesto">
              Puesto *:
            </label>

            <select
              class="campo"
              id="puesto"
              name="puesto"
              required>
              <option value="">Selecciona un puesto</option>
              <option value="Gerente">Gerente</option>
              <option value="Veterinario/a">Veterinario</option>
              <option value="Vendedor">Vendedor</option>
              <option value="Estilista">Estilista</option>
            </select>
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
              Cédula profesional:
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
              Fecha de contratación:
            </label>

            <input
              type="date"
              class="campo"
              id="fecha_de_contratacion"
              name="fecha_de_contratacion">
          </div>


          <div>
            <label class="label-campo" for="horario">
              Horario:
            </label>

            <select
              class="campo"
              id="horario"
              name="horario"
              required>
              <option value="">Selecciona un horario</option>
              <option value="Matutino">Matutino</option>
              <option value="Vespertino">Vespertino</option>
            </select>
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
