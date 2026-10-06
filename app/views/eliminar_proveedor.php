<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veterinaria PetCare - Eliminar proveedor</title>
  <link rel="stylesheet" href="css/estilos_base.css">
</head>

<body>

  <header>
    <div class="marca">
      <img src="img/logo3.svg" alt="Veterinaria PetCare">
    </div>

    <div class="sesion">
      <span>Empleado: Nombre del Empleado</span>
      <a href="#">Cerrar sesión</a>
    </div>
  </header>

  <nav>
    <ul>
      <li><a href="mascotas.php">Mascotas</a></li>
      <li><a href="citas.php">Citas</a></li>
      <li><a href="clientes.php">Clientes</a></li>
      <li><a href="empleados.php">Empleados</a></li>
      <li><a href="proveedores.php" class="activo">Proveedores</a></li>

      <li><a href="inventario.php">Inventario</a></li>
      <li><a href="#" class="deshabilitado">Ventas</a></li>
      <li><a href="#" class="deshabilitado">Servicios</a></li>
      <li><a href="#" class="deshabilitado">Adopción y venta</a></li>
      <li><a href="#" class="deshabilitado">Veterinaria</a></li>
      <li><a href="#" class="deshabilitado">Pagos</a></li>
      <li><a href="#" class="deshabilitado">Pagos con tarjeta</a></li>
    </ul>
  </nav>

  <main>

    <h1>Eliminar proveedor</h1>

    <form action="eliminar_proveedor.php" method="POST">

      <input type="hidden" id="id_proveedor" name="id_proveedor" value="1">

      <fieldset>

        <p>
          ¿Está seguro de que desea eliminar al proveedor
          <strong>Distribuidora Veterinaria del Norte S.A. de C.V.</strong>?
          Esta acción no se puede deshacer.
        </p>

        </div>

      </fieldset>

      <div style="margin-top: 20px;">

        <button type="submit" class="btn">
          Eliminar proveedor
        </button>

        <a
          class="btn"
          href="proveedores.php"
          style="background: #757575; margin-left: 8px;">
          Cancelar
        </a>

      </div>

    </form>

  </main>

  <footer>
    <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
  </footer>

</body>
</html>
