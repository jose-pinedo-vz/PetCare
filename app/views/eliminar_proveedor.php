<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veterinaria PetCare - Eliminar proveedor</title>
  <link rel="stylesheet" href="css/estilos_base_v2.css">
</head>

<body>

  <div class="layout">

  <?php $paginaActiva = 'proveedores'; include __DIR__ . '/layout/sidebar.php'; ?>

    <main>
      <div class="migaja"><a href="proveedores.php" class="link-tenue">Gestión de Proveedores</a> / Eliminar</div>
      <div class="encabezado-pagina">
        <h1>Eliminar proveedor</h1>
      </div>

      <div class="tarjeta">
    <form action="eliminar_proveedor.php" method="POST">

      <input type="hidden" id="id_proveedor" name="id_proveedor" value="1">

      <fieldset>

        <p>
          ¿Está seguro de que desea eliminar al proveedor
          <strong>Distribuidora Veterinaria del Norte S.A. de C.V.</strong>?
          Esta acción no se puede deshacer.
        </p>

      </fieldset>

      <div class="acciones-formulario">
        <a class="btn-secundario" href="proveedores.php">Cancelar</a>
        <button type="submit">Eliminar proveedor</button>
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
