<?php
    require_once __DIR__ . '/../models/Empleados.php';
    $empleados = Empleados::Listar_empleados_activos();
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
  <title>Veterinaria PetCare - Empleados</title>
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
      <li><a href="empleados.php" class="activo">Empleados</a></li>
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
    <h1>Módulo de empleados</h1>
    <div style="margin-bottom: 20px;">
      <a class="btn" href="agregar_empleado.php">+ Agregar empleado</a>
    </div>
    <div style="overflow-x: auto; border: 1px solid var(--border-light); border-radius: 6px;">
    <table border="1" style="width: 100%; min-width: 1400px; border-collapse: collapse; text-align: left;">
      <thead>
              <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Teléfono</th>
                <th>Correo</th>
                <th>Calle</th>
                <th>Número exterior</th>
                <th>Número interior</th>
                <th>Colonia</th>
                <th>Ciudad</th>
                <th>Estado</th>
                <th>Código postal</th>
                <th>Puesto</th>
                <th>Especialidad</th>
                <th>Cédula profesional</th>
                <th>Fecha de contratación</th>
                <th>Horario</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
<?php foreach ($empleados as $e): ?>
              <tr>
                <td><?= $e['id_empleado'] ?></td>
                <td><?= htmlspecialchars($e['nombre']) ?></td>
                <td><?= htmlspecialchars($e['apellido']) ?></td>
                <td><?= htmlspecialchars($e['telefono']) ?></td>
                <td><?= htmlspecialchars($e['correo']) ?></td>
                <td><?= htmlspecialchars($e['calle']) ?></td>
                <td><?= htmlspecialchars($e['numero_exterior']) ?></td>
                <td><?= htmlspecialchars($e['numero_interior'] ?? '') ?></td>
                <td><?= htmlspecialchars($e['colonia']) ?></td>
                <td><?= htmlspecialchars($e['ciudad']) ?></td>
                <td><?= htmlspecialchars($e['estado']) ?></td>
                <td><?= htmlspecialchars($e['codigo_postal']) ?></td>
                <td><?= htmlspecialchars($e['puesto']) ?></td>
                <td><?= htmlspecialchars($e['especialidad'] ?? '') ?></td>
                <td><?= htmlspecialchars((string)($e['num_cedula_profesional'] ?? '')) ?></td>
                <td><?= htmlspecialchars((string)($e['Fecha_de_contratacion'] ?? '')) ?></td>
                <td><?= htmlspecialchars($e['horario']) ?></td>
                <td style="padding: 15px; white-space: nowrap;">
                  <a class="btn" href="editar_empleado.php?id=<?= $e['id_empleado'] ?>">Editar</a>
                  <a class="btn" href="../controllers/eliminar_empleado.php?id=<?= $e['id_empleado'] ?>" style="margin-left: 6px;" onclick="return confirm('¿Seguro que quieres eliminar este empleado?');">Eliminar</a>
                </td>
              </tr>
<?php endforeach; ?>
            </tbody>
    </table>
    </div>
  </main>

  <footer>
    <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
  </footer>

</body>
</html>
