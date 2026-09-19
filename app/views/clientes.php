<?php
require_once __DIR__ . '/../models/Cliente.php';
$listaClientes = Cliente::obtenerTodos();
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
      <img src="img/logo.svg" alt="Veterinaria PetCare">
    </div>

    <div class="sesion">
      <span>Empleado: Nombre del Empleado</span>
      <a href="#">Cerrar sesión</a>
    </div>
  </header>

  <nav>
    <ul>
      <li><a href="mascotas.php" class="activo">Mascotas</a></li>
      <li><a href="citas.php" class="activo">citas</a></li>
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

    <?php if (empty($listaClientes)): ?>
      <div style="background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); text-align: center;">
        <p style="font-size: 16px; color: #666; margin-bottom: 15px;">No hay clientes registrados en la base de datos.</p>
        <a class="btn" href="agregar_cliente.php">+ Registrar el primer cliente</a>
      </div>
    <?php else: ?>
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
              <td style="padding: 10px; text-align: center;">
                <a class="btn" href="editar_cliente.php?id=<?php echo urlencode((string)$c['id_cliente']); ?>" style="padding: 5px 10px; font-size: 13px;">Editar</a>
                <a class="btn" href="agregar_mascota.php?id_cliente=<?php echo urlencode((string)$c['id_cliente']); ?>" style="padding: 5px 10px; font-size: 13px; background: #2196F3; margin-left: 5px;">+ Mascota</a>
                <a class="btn" href="agendarCita.php?claveCliente=<?php echo urlencode((string)$c['id_cliente']); ?>" style="padding: 5px 10px; font-size: 13px; background: #ff9800; margin-left: 5px;">+ Cita</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </main>

  <footer>
    <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
  </footer>

</body>
</html>