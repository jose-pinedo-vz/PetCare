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
  <link rel="stylesheet" href="css/estilos_base_v2.css">
</head>
<body>

  <div class="layout">

  <?php $paginaActiva = 'empleados'; include __DIR__ . '/layout/sidebar.php'; ?>


  <main>
      <div class="migaja"><a href="empleados.php" class="link-tenue">Gestión de Empleados</a> / Eliminar</div>
      <div class="encabezado-pagina">
        <h1>Eliminar empleado</h1>
      </div>

      <div class="tarjeta">


    <form action="../controllers/eliminar_empleado.php" method="POST">

      <input type="hidden" id="id_empleado" name="id_empleado" value="<?= (int)$id_empleado ?>">

      <fieldset>

        <p>
          ¿Está seguro de que desea eliminar al empleado
          <strong><?= htmlspecialchars((string)$nombre_empleado) .' ' . htmlspecialchars((string)$apellido_empleado) . ' (ID: ' . htmlspecialchars((string)$id_empleado) . ')' ?></strong>?
          Esta acción no se puede deshacer.
        </p>

      </fieldset>

      <div class="acciones-formulario">
        <a class="btn btn-secundario btn-chico" href="empleados.php">Cancelar</a>
        <button type="submit" class="btn btn-chico btn-peligro">Eliminar empleado</button>
      </div>
    </form>

      </div>
    </main>

  </div>

  <footer>
    <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
  </footer>

</body>
</html>
