<?php
    require_once __DIR__ . '/../models/Empleados.php';
    $empleados = Empleados::Listar_empleados_activos();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veterinaria PetCare - Empleados</title>
  <link rel="stylesheet" href="css/estilos_base_v2.css">
</head>
<body>

  <div class="layout">

  <?php $paginaActiva = 'empleados'; include __DIR__ . '/layout/sidebar.php'; ?>


  <main>
      <div class="migaja">Gestión de Empleados /</div>
      <div class="encabezado-pagina">
        <h1>Empleados</h1>
      </div>

      <div class="tarjeta">
    
    <div class="barra-acciones">
      <a class="btn" href="agregar_empleado.php">+ Agregar empleado</a>
    </div>
    <div class="tabla-envoltura">
    <table class="tabla-ancha">
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
                <td class="acciones">
                  <a class="btn" href="editar_empleado.php?id=<?= $e['id_empleado'] ?>">Editar</a>
                  <a class="btn btn-peligro" href="../controllers/eliminar_empleado.php?id=<?= $e['id_empleado'] ?>" onclick="return confirm('¿Seguro que quieres eliminar este empleado?');">Eliminar</a>
                </td>
              </tr>
<?php endforeach; ?>
            </tbody>
    </table>
    </div>
  
      </div>
    </main>

  </div>

  <footer>
    <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
  </footer>

</body>
</html>