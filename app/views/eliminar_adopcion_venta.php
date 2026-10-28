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
      <div class="migaja"><a href="empleados.php" class="link-tenue">Gestión de adopción y venta</a> / Eliminar</div>
      <div class="encabezado-pagina">
        <h1>Eliminar adopcion y venta</h1>
      </div>

      <div class="tarjeta">
    

    <form action="procesar_eliminar_adopcion_venta.php" method="POST">

      <input type="hidden" id="id_adopcion_venta" name="id_adopcion_venta" value="1">

      <fieldset>
        <p>¿Está seguro de que desea eliminarla? Esta acción no se puede deshacer.</p>
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