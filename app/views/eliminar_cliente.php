<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veterinaria PetCare - Eliminar cliente</title>
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
      <li><a href="clientes.php"  class="activo">Clientes</a></li>
      <li><a href="empleados.php">Empleados</a></li>
      <li><a href="proveedores.php">Proveedores</a></li>
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
        <h1>Eliminar cliente</h1>

        <div class="confirmacion">
            <p>¿Está seguro de que desea eliminar este cliente?</p>

            <button type="submit" class="btn">
                Eliminar proveedor
            </button>
            <a class="btn" href="clientes.php">Cancelar</a>
        </div>
    </main>

    <footer>
      <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>
</body>
</html>