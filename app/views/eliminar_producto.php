<?php
// Validar si realmente llegaron los datos por la URL (GET)
if (!isset($_GET['id']) || !isset($_GET['nombre']) || !isset($_GET['imagen'])) {
    // Si alguien entra por la fuerza sin ID, lo mandamos de regreso a la tabla
    header("Location: inventario.php");
    exit();
}

// Si sí llegaron, los guardamos con seguridad
$id_producto = (int) $_GET['id'];
$nombre = $_GET['nombre'];
$imagen = $_GET['imagen'];
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veterinaria PetCare - Eliminar producto</title>
  <link rel="stylesheet" href="css/estilos_base_v2.css">
</head>

<body>

  <div class="layout">

  <?php $paginaActiva = 'inventario'; include __DIR__ . '/layout/sidebar.php'; ?>

    <main>
      <div class="migaja"><a href="inventario.php" class="link-tenue">Gestión de Inventario</a> / Eliminar</div>
      <div class="encabezado-pagina">
        <h1>Eliminar producto</h1>
      </div>
      <form action="../controllers/eliminar_producto.php" method="POST">
  
      <input type="hidden" id="id_proveedor" name="id_proveedor" value="<?= (int)$id_producto ?>">

      <div class="tarjeta">
        <div class="confirmacion">
          <p>¿Está seguro de que desea eliminar este producto?
            <strong><?= htmlspecialchars((string)$nombre) . ' (ID: ' . htmlspecialchars((string)$id_producto) . ')' ?></strong>
            Esta acción no se puede deshacer.
          </p>

          <div class="acciones-formulario">
            <a class="btn btn-secundario btn-chico" href="inventario.php">Cancelar</a>
            <button type="button" class="btn btn-chico btn-peligro">Eliminar producto</button>
          </div>
        </div>
      </div>
    </main>

  </div>

  <footer>
    <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
  </footer>

</body>

</html>
