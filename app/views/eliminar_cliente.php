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
      <div class="migaja"><a href="clientes.php" class="link-tenue">Gestión de clientes</a> / Eliminar</div>
      <div class="encabezado-pagina">
        <h1>Eliminar cliente</h1>
      </div>

      <div class="tarjeta">
        <div class="confirmacion">
          <p>¿Está seguro de que desea eliminar este cliente?</p>

          <div class="acciones-formulario">
            <a class="btn-secundario" href="clientes.php">Cancelar</a>
            <button type="submit" class="btn">Eliminar cliente</button>
            
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
