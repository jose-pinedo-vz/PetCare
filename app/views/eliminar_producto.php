<?php
// Mostrar nombre del usuario
if (session_status() === PHP_SESSION_NONE)
{
    session_start();
}
if (!isset($_SESSION['usuario']))
{
  header("Location: ../views/login.php");
  exit();
}
$usuario=$_SESSION['usuario'];
// Mostrar nombre del usuario
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veterinaria PetCare - Eliminar producto</title>
    <link rel="stylesheet" href="css/estilos_base.css">
</head>

<body>

    <header>
        <div class="marca">
            <img src="img/logo3.svg" alt="Veterinaria PetCare">
        </div>

        <div class="sesion">
            <!-- Mostrar nombre del usuario -->
            <span>Empleado: <?php echo htmlspecialchars($usuario); ?></span>
            <a href="../controllers/Login.php?action=logout" class="btn-logout">Cerrar Sesión</a>
            <!-- Mostrar nombre del usuario -->
        </div>
    </header>

    <nav>
      <ul>
      <li><a href="mascotas.php">Mascotas</a></li>
      <li><a href="citas.php">Citas</a></li>
      <li><a href="clientes.php">Clientes</a></li>
      <li><a href="empleados.php">Empleados</a></li>
      <li><a href="proveedores.php">Proveedores</a></li>
      <li><a href="inventario.php" class="activo">Inventario</a></li>
      <li><a href="#" class="deshabilitado">Ventas</a></li>
      <li><a href="#" class="deshabilitado">Servicios</a></li>
      <li><a href="#" class="deshabilitado">Adopción y venta</a></li>
      <li><a href="#" class="deshabilitado">Veterinaria</a></li>
      <li><a href="#" class="deshabilitado">Pagos</a></li>
      <li><a href="#" class="deshabilitado">Pagos con tarjeta</a></li>
      </ul>
    </nav>

    <main>
        <h1>Eliminar producto</h1>

        <div class="confirmacion">
            <p>¿Está seguro de que desea eliminar este producto?</p>

            <button type="button">Eliminar producto</button>
            <a class="btn" href="inventario.php">Cancelar</a>
        </div>
    </main>

    <footer>
      <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>
</body>
</html>