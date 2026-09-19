<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veterinaria PetCare - Mascotas</title>
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

      <li><a href="clientes.php" class="activo">Clientes</a></li>
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
    <h1>Módulo de Clientes</h1>
    <div style="margin-bottom: 20px;">
      <a class="btn" href="agregar_cliente.php">+ Agregar cliente</a>
    </div>
    <table border="1" style="width: 100%; border-collapse: collapse; text-align: left;">
      <thead>
        <tr>
          <th>Nombre</th>
          <th>Sexo</th>
          <th>Telefono</th>
          <th>Codigo postal</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Jose</td>
          <td>Hombre</td>
          <td>4371084048</td>
          <td>99700</td>
          <td>Activo</td>
          <td style="padding: 15px;">
            <a class="btn" href="editar_cliente.php?id=1">Editar</a>
            <a href="#">Eliminar</a>
          </td>
        </tr>
        
      </tbody>
    </table>
  </main>

  <footer>
    <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
  </footer>

</body>
</html>