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

      <div class="tarjeta">
        <div class="confirmacion">
          <p>¿Está seguro de que desea eliminar este producto?</p>

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
