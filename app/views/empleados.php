<?php
// Mostrar nombre del usuario
session_start();

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
          <div class="busqueda-barra" role="search">
            <label class="visualmente-oculto" for="buscarEmpleado">Buscar empleado</label>
            <input class="campo buscador-inteligente" id="buscarEmpleado" type="search" placeholder="Buscar..." autocomplete="off">
          </div>
          <div class="barra-acciones">
            <a class="btn" href="agregar_empleado.php">+ Agregar empleado</a>
          </div>
        </div>

    <!-- Esta tabla omite intencionalmente la dirección; sincroniza sus columnas con Listar_empleados_activos(). -->
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
          <td><?= (int)$e['id_empleado'] ?></td>
          <td><?= htmlspecialchars($e['nombre']) ?></td>
          <td><?= htmlspecialchars($e['apellido']) ?></td>
          <td><?= htmlspecialchars($e['telefono']) ?></td>
          <td><?= htmlspecialchars($e['correo']) ?></td>
          <td><?= htmlspecialchars($e['puesto']) ?></td>
          <td><?= htmlspecialchars($e['especialidad'] ?? '') ?></td>
          <td><?= htmlspecialchars((string)($e['num_cedula_profesional'] ?? '')) ?></td>
          <td><?= htmlspecialchars(substr((string)($e['Fecha_de_contratacion'] ?? ''), 0, 10)) ?></td>
          <td><?= htmlspecialchars($e['horario']) ?></td>
          <td class="acciones">
            <a class="btn btn-chico" href="editar_empleado.php?id_empleado=<?= (int)$e['id_empleado'] ?>">Editar</a>
            <a class="btn btn-chico btn-peligro" href="eliminar_empleado.php?id=<?= (int)$e['id_empleado'] ?>&nombre=<?= urlencode($e['nombre']) ?>&apellido=<?= urlencode($e['apellido']) ?>">Eliminar</a>
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

  <script>
    const buscarEmpleado = document.getElementById('buscarEmpleado');
    const tablaEmpleados = document.querySelector('.tabla-envoltura tbody');

    if (buscarEmpleado && tablaEmpleados) {
      buscarEmpleado.addEventListener('input', () => {
        const termino = buscarEmpleado.value.trim().toLocaleLowerCase();
        // Filtra las columnas visibles, excluyendo Acciones; la dirección no forma parte del listado.
        tablaEmpleados.querySelectorAll('tr').forEach((fila) => {
          const texto = Array.from(fila.cells)
            .slice(0, -1)
            .map((celda) => celda.textContent)
            .join(' ')
            .toLocaleLowerCase();
          fila.hidden = !texto.includes(termino);
        });
      });
    }
  </script>
</body>
</html>
