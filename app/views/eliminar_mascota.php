<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veterinaria PetCare - Eliminar mascota</title>
  <link rel="stylesheet" href="css/estilos_base.css">
</head>
<body>

  <header>
    <div class="marca">
      <img src="logo.png" alt="Veterinaria PetCare">
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
    <h1>Eliminar mascota</h1>

    <form action="procesar_eliminar_mascota.php" method="POST">

      <input type="hidden" id="id_mascota" name="id_mascota" value="1">

      <fieldset>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
          <div>
            <label class="label-campo" for="nombre_empleado">Nombre de empleado *:</label>
            <input type="text" class="campo" id="nombre_empleado" name="nombre_empleado" required>
          </div>

          <div>
            <label class="label-campo" for="password_empleado">Contraseña *:</label>
            <input type="password" class="campo" id="password_empleado" name="password_empleado" required>
          </div>
        </div>
      </fieldset>

      <button type="submit">Eliminar mascota</button>
      <a class="btn" href="mascotas.php">Cancelar</a>
    </form>
  </main>

  <footer>
    <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
  </footer>

</body>
</html>