<?php require_once __DIR__ . '/../controllers/MascotaController.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veterinaria PetCare - Mascotas</title>
  <link rel="stylesheet" href="css/estilos_base_v2.css">

</head>
<body>

  <div class="layout">

  <?php $paginaActiva = 'mascotas'; include __DIR__ . '/layout/sidebar.php'; ?>


  <main>
      <div class="migaja">Gestión de Mascotas /</div>
      <div class="encabezado-pagina">
        <h1>Mascotas</h1>
      </div>

      <div class="tarjeta">
    
    <div class="barra-acciones">
      <a class="btn" href="agregar_mascota.php">+ Agregar mascota</a>
    </div>
    <!-- AQUÍ INICIA EL CONTENEDOR RESPONSIVE -->
    <div class="tabla-envoltura">

    <table>
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
              <td class="acciones">
                <a class="btn" href="editar_mascota.php?id_mascota=<?= $mascota['id_mascota'] ?>">Editar</a>
                <a class="btn btn-peligro" href="eliminar_mascota.php?id_mascota=<?= $mascota['id_mascota'] ?>">Eliminar</a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="6">No hay mascotas registradas.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>

    </div> <!-- AQUÍ TERMINA EL CONTENEDOR RESPONSIVE -->
  
      </div>
    </main>

  </div>

  <footer>
    <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
  </footer>

</body>
</html>