<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veterinaria PetCare - Eliminar mascota</title>
  <link rel="stylesheet" href="css/estilos_base_v2.css">
</head>
<body>

  <div class="layout">

  <?php $paginaActiva = 'mascotas'; include __DIR__ . '/layout/sidebar.php'; ?>


  <main>
      <div class="migaja"><a href="mascotas.php" class="link-tenue">Gestión de Mascotas</a> / Eliminar</div>
      <div class="encabezado-pagina">
        <h1>Eliminar mascota</h1>
      </div>

      <div class="tarjeta">
    

    <form action="procesar_eliminar_mascota.php" method="POST">

      <input type="hidden" id="id_mascota" name="id_mascota" value="<?= htmlspecialchars($_GET['id'] ?? '') ?>">

      <fieldset>
        <div class="grid-formulario">
          <div>
            <label class="label-campo" for="nombre_empleado">Nombre de empleado:</label>
            <input type="text" class="campo" id="nombre_empleado" name="nombre_empleado" required>
          </div>

          <div>
            <label class="label-campo" for="password_empleado">Contraseña:</label>
            <input type="password" class="campo" id="password_empleado" name="password_empleado" required>
          </div>
        </div>
      </fieldset>

      <div class="acciones-formulario">
        <a class="btn-secundario" href="mascotas.php">Cancelar</a>
        <button type="submit">Eliminar mascota</button>
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