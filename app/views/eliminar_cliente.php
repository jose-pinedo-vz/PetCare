<?php
$id_cliente = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$nombre_cliente = isset($_GET['nombre']) ? trim((string)$_GET['nombre']) : '';
$apellido_cliente = isset($_GET['apellido']) ? trim((string)$_GET['apellido']) : '';

if (!$id_cliente || $id_cliente <= 0 || $nombre_cliente === '' || $apellido_cliente === '') {
    header("Location: clientes.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veterinaria PetCare - Eliminar cliente</title>
  <link rel="stylesheet" href="css/estilos_base_v2.css">
</head>

<body>

  <div class="layout">

  <?php $paginaActiva = 'clientes'; include __DIR__ . '/layout/sidebar.php'; ?>

  <main>

      <div class="migaja"><a href="clientes.php" class="link-tenue">Gestión de Clientes</a> / Eliminar</div>

    <h1>Eliminar cliente</h1>

    <?php if (isset($_SESSION['error'])): ?>
      <div class="msg-error">
        <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
      </div>
    <?php endif; ?>

    <form action="../controllers/eliminar_cliente.php" method="POST">

      <input type="hidden" id="id_cliente" name="id_cliente" value="<?= htmlspecialchars((string)$id_cliente) ?>">

      <div class="tarjeta">

        <fieldset>
          <p>
            ¿Está seguro de que desea eliminar al cliente
            <strong><?= htmlspecialchars((string)$nombre_cliente) . ' ' . htmlspecialchars((string)$apellido_cliente) . ' (ID: ' . htmlspecialchars((string)$id_cliente) . ')' ?></strong>?
            Esta acción no se puede deshacer.
          </p>
        </fieldset>

          <div class="acciones-formulario">
            <a class="btn btn-secundario btn-chico" href="clientes.php">Cancelar</a>
            <button type="submit" class="btn btn-chico">Eliminar</button>
          </div>

        </div>

      </div>

    </form>
  </main>

  </div>

  <footer>
    <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
  </footer>

</body>

</html>
