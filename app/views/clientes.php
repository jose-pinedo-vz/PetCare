<?php
require_once __DIR__ . '/../models/Cliente.php';
$listaClientes = Cliente::obtenerTodos();
// Mostrar nombre del usuario
session_start();
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
  <title>Veterinaria PetCare - Clientes</title>
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
      <li><a href="clientes.php" class="activo">Clientes</a></li>
      <li><a href="empleados.php">Empleados</a></li>
      <li><a href="proveedores.php">Proveedores</a></li>

      
      <li><a href="#" class="deshabilitado">Inventario</a></li>
      <li><a href="#" class="deshabilitado">Ventas</a></li>
      <li><a href="#" class="deshabilitado">Servicios</a></li>
      <li><a href="#" class="deshabilitado">Adopción y venta</a></li>
      <li><a href="#" class="deshabilitado">Veterinaria</a></li>
      <li><a href="#" class="deshabilitado">Pagos</a></li>
      <li><a href="#" class="deshabilitado">Pagos con tarjeta</a></li>
      <!-- Usuarios para administradores -->
      <?php if ($_SESSION['rol'] === 'admin'): ?>
        <li><a href="usuarios.php">Usuarios</a></li>
      <?php endif; ?>
      <!-- Usuarios para administradores -->

    </ul>
  </nav>

  <main>
    <h1>Módulo de clientes</h1>
    <div style="margin-bottom: 20px;">
      <a class="btn" href="agregar_cliente.php">+ Agregar cliente</a>
    </div>
    
    <?php 
    $listaClientes = Cliente::obtenerTodos();
    if (empty($listaClientes)): ?>
      <div style="background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); text-align: center;">
        <p style="font-size: 16px; color: #666; margin-bottom: 15px;">No hay clientes registrados en la base de datos.</p>
        <a class="btn" href="agregar_cliente.php">+ Registrar el primer cliente</a>
      </div>
    <?php else: ?>
      
      <!-- INICIA CONTENEDOR RESPONSIVE -->
      <div class="table-responsive">
        <table border="1" style="width: 100%; border-collapse: collapse; text-align: left; background: #fff;">
          <thead>
            <tr style="background: #f5f5f5;">
              <th style="padding: 10px;">ID</th>
              <th style="padding: 10px;">Nombre</th>
              <th style="padding: 10px;">Teléfono</th>
              <th style="padding: 10px;">Correo</th>
              <th style="padding: 10px;">Ciudad / Estado</th>
              <th style="padding: 10px; text-align: center;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($listaClientes as $c): ?>
              <tr>
                <td style="padding: 10px;"><strong>#<?php echo htmlspecialchars((string)$c['id_cliente']); ?></strong></td>
                <td style="padding: 10px;"><?php echo htmlspecialchars($c['nombre'] . ' ' . $c['apellido']); ?></td>
                <td style="padding: 10px;"><?php echo htmlspecialchars($c['telefono']); ?></td>
                <td style="padding: 10px;"><?php echo htmlspecialchars($c['correo']); ?></td>
                <td style="padding: 10px;"><?php echo htmlspecialchars($c['ciudad'] . ', ' . $c['estado']); ?></td>
                
                <!-- Celda de acciones con white-space: nowrap; -->
                <td style="padding: 10px; text-align: center; white-space: nowrap;">
                  <a class="btn" href="editar_cliente.php?id=<?php echo urlencode((string)$c['id_cliente']); ?>" style="padding: 5px 10px; font-size: 13px;">Editar</a>
                  <a class="btn" href="agregar_mascota.php?id_cliente=<?php echo urlencode((string)$c['id_cliente']); ?>" style="padding: 5px 10px; font-size: 13px; background: #2196F3;">+ Mascota</a>
                  <a class="btn" href="agendarCita.php?claveCliente=<?php echo urlencode((string)$c['id_cliente']); ?>" style="padding: 5px 10px; font-size: 13px; background: #ff9800;">+ Cita</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div> 
      <!-- TERMINA CONTENEDOR RESPONSIVE -->

    <?php endif; ?>
  </main>

  <footer>
    <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
  </footer>

</body>
</html>