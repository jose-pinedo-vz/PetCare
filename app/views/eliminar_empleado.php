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
    

    <form action="procesar_eliminar_empleado.php" method="POST">

      <input type="hidden" id="id_empleado" name="id_empleado" value="1">

      <fieldset>
        <p>¿Está seguro de que desea eliminar a <strong>Ana López García</strong>? Esta acción no se puede deshacer.</p>

        <div class="grid-formulario">
          <div>
            <label class="label-campo" for="nombre_empleado">Nombre de empleado *:</label>
            <input type="text" class="campo" id="nombre_empleado" name="nombre_empleado" required>
          </div>

          <div>
            <label class="label-campo" for="password_empleado">Contraseña *:</label>
            <input type="password" class="campo" id="password_empleado" name="password_empleado" required>
          </div>
        </div>
      </fieldset>

      <div class="acciones-formulario">
        <a class="btn-secundario" href="empleados.php">Cancelar</a>
        <button type="submit" class="btn">Eliminar empleado</button>
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