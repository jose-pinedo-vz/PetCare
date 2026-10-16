<?php 
// Validar si realmente llegaron los datos por la URL (GET)
if (!isset($_GET['id']) || !isset($_GET['nombre']) || !isset($_GET['apellido'])) {
    // Si alguien entra por la fuerza sin ID, lo mandamos de regreso a la tabla
    header("Location: empleados.php");
    exit();
}

// Si sí llegaron, los guardamos con seguridad
$id_empleado = $_GET['id'];
$nombre_empleado = $_GET['nombre'];
$apellido_empleado = $_GET['apellido'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veterinaria PetCare - Eliminar empleado</title>
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
      <li><a href="empleados.php" class="activo">Empleados</a></li>
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
    <h1>Eliminar empleado</h1>

    <form action="../controllers/eliminar_empleado.php" method="POST">
  
      <input type="hidden" id="id_empleado" name="id_empleado" value="<?= (int)$id_empleado ?>">

      <fieldset>

        <p>
          ¿Está seguro de que desea eliminar al empleado
          <strong><?= htmlspecialchars((string)$nombre_empleado) .' ' . htmlspecialchars((string)$apellido_empleado) . ' (ID: ' . htmlspecialchars((string)$id_empleado) . ')' ?></strong>?
          Esta acción no se puede deshacer.
        </p>

      </fieldset>

      <div style="margin-top: 20px;">
        <button type="submit" class="btn">Eliminar empleado</button>
        <a class="btn" href="empleados.php" style="background: #757575; margin-left: 8px;">Cancelar</a>
      </div>
    </form>
  </main>

  <footer>
    <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
  </footer>

</body>
</html>