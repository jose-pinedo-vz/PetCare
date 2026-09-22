<?php require_once __DIR__ . '/../controllers/MascotaController.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veterinaria PetCare - Mascotas</title>
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
      <li><a href="clientes.php">Clientes</a></li>
      <li><a href="citas.html">Citas</a></li>
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
    <h1>Módulo de Mascotas</h1>
    <div style="margin-bottom: 20px;">
      <a class="btn" href="agregar_mascota.php">+ Agregar mascota</a>
    </div>
    <table border="1" style="width: 100%; border-collapse: collapse; text-align: left;">
      <thead>
        <tr>
          <th>Nombre</th>
          <th>Especie</th>
          <th>Raza</th>
          <th>Sexo</th>
          <th>Edad</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($mascotas)): ?>
          <?php foreach ($mascotas as $mascota): ?>
            <tr>
              <td><?= htmlspecialchars($mascota['nombre']) ?></td>
              <td><?= htmlspecialchars($mascota['especie']) ?></td>
              <td><?= htmlspecialchars($mascota['raza']) ?></td>
              <td><?= htmlspecialchars($mascota['sexo']) ?></td>
              <td><?= htmlspecialchars((string)$mascota['edad']) ?></td>
              <td style="padding: 15px;">
                <a class="btn" href="editar_mascota.php?id=<?= $mascota['id_mascota'] ?>">Editar</a>
                <a href="#">Eliminar</a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="6">No hay mascotas registradas.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </main>

  <footer>
    <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
  </footer>

</body>
</html>