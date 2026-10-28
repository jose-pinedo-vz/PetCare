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

    <?php $paginaActiva = 'empleados';
    include __DIR__ . '/layout/sidebar.php'; ?>


    <main>
      <div class="migaja">Gestión de empleados /</div>
      <div class="encabezado-pagina">
        <h1>Empleados</h1>
      </div>

      <div class="tarjeta">

        <div class="barra-acciones-busqueda">
          <form class="busqueda-barra" method="get" action="empleados.php" role="search">
            <label class="visualmente-oculto" for="buscarEmpleado">Buscar empleado</label>
            <input class="campo" id="buscarEmpleado" name="buscar" type="search" placeholder="Buscar..." value="<?= htmlspecialchars($_GET['buscar'] ?? '') ?>" autocomplete="off">
            <?php if (!empty($_GET['buscar'])): ?>
              <a class="btn btn-secundario btn-chico" href="empleados.php">Limpiar</a>
            <?php endif; ?>
          </form>

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
                  <td><?= htmlspecialchars((string)$e['id_empleado']) ?></td>
                  <td><?= htmlspecialchars($e['nombre'] ?? '') ?></td>
                  <td><?= htmlspecialchars($e['apellido'] ?? '') ?></td>
                  <td><?= htmlspecialchars($e['telefono'] ?? '') ?></td>
                  <td><?= htmlspecialchars($e['correo'] ?? '') ?></td>
                  <td><?= htmlspecialchars($e['puesto'] ?? '') ?></td>
                  <td><?= htmlspecialchars($e['especialidad'] ?? '') ?></td>
                  <td><?= htmlspecialchars((string)($e['num_cedula_profecional'] ?? $e['num_cedula_profesional'] ?? '')) ?></td>
                  <td><?= htmlspecialchars((string)($e['Fecha_de_contratacion'] ?? '')) ?></td>
                  <td><?= htmlspecialchars($e['horario'] ?? '') ?></td>
                  <td class="acciones">
                    <a class="btn btn-chico" href="editar_empleado.php?id=<?= $e['id_empleado'] ?>">Editar</a>
                    <a class="btn btn-peligro btn-chico" href="../controllers/eliminar_empleado.php?id=<?= $e['id_empleado'] ?>" onclick="return confirm('¿Seguro que quieres eliminar este empleado?');">Eliminar</a>
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