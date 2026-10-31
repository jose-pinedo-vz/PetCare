<?php
require_once __DIR__ . '/../models/adopcion_venta.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id = (int)($_GET['id'] ?? $_GET['id_adopcion_venta'] ?? 0);
$animal = $id > 0 ? obtenerPorId($id) : null;

if ($animal === null) {
    header("Location: adopcion_venta.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veterinaria PetCare - Eliminar adopción y venta</title>
  <link rel="stylesheet" href="css/estilos_base_v2.css">
</head>
<body>

  <div class="layout">

  <?php $paginaActiva = 'adopcion'; include __DIR__ . '/layout/sidebar.php'; ?>


  <main>
      <div class="migaja"><a href="adopcion_venta.php" class="link-tenue">Gestión de adopción y venta</a> / Eliminar</div>
      <div class="encabezado-pagina">
        <h1>Eliminar adopcion y venta</h1>
      </div>

      <div class="tarjeta">
    

    <form action="../controllers/eliminar_adopcion_venta.php" method="POST">

      <input type="hidden" id="id_adopcion_venta" name="id_adopcion_venta" value="<?= (int)$id ?>">

      <fieldset>
        <p>
          ¿Está seguro de que desea eliminar el registro de adopción y venta de
          <strong><?= htmlspecialchars(($animal['especie'] ?? 'Animal') . (!empty($animal['raza']) ? ' - ' . $animal['raza'] : '') . ' (ID: ' . $id . ')') ?></strong>?
          Esta acción no se puede deshacer.
        </p>
      </fieldset>

      <div class="acciones-formulario">
        <a class="btn btn-secundario btn-chico" href="adopcion_venta.php">Cancelar</a>
        <button type="submit" class="btn btn-chico btn-peligro">Eliminar </button>
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