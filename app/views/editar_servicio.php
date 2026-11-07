<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veterinaria PetCare - Editar servicio</title>
  <link rel="stylesheet" href="css/estilos_base_v2.css">
</head>

<body>
  <div class="layout">
    <?php $paginaActiva = 'servicios';
    include __DIR__ . '/layout/sidebar.php'; ?>

    <main>
      <div class="migaja"><a href="servicios.php" class="link-tenue">Gestión de servicios</a> / Editar</div>
      <div class="encabezado-pagina">
        <h1>Editar servicio</h1>
      </div>

      <div class="tarjeta">
        <form method="POST" action="editar_servicio.php?id_servicio=1">
          <input type="hidden" name="id_servicio" value="1">

          <fieldset>
            <legend>Datos del servicio</legend>

            <div class="grid-formulario">
              <div>
                <label class="label-campo" for="nombre">Nombre *:</label>
                <input type="text" class="campo" id="nombre" name="nombre" maxlength="50" required value="Baño">
              </div>

              <div>
                <label class="label-campo" for="categoria">Categoría *:</label>
                <select class="campo" id="categoria" name="categoria" required>
                  <option value="">Seleccionar...</option>
                  <option value="Estética" selected>Estética</option>
                  <option value="Salud">Salud</option>
                </select>
              </div>

              <div>
                <label class="label-campo" for="duracion">Duración (minutos) *:</label>
                <input type="number" class="campo" id="duracion" name="duracion" min="1" required value="60">
              </div>

              <div>
                <label class="label-campo" for="precio">Precio *:</label>
                <input type="number" class="campo" id="precio" name="precio" step="0.01" min="0" required value="150.00">
              </div>

              <div>
                <label class="label-campo" for="id_empleados">Empleado responsable:</label>
                <select class="campo" id="id_empleados" name="id_empleados">
                  <option value="">Seleccionar...</option>
                  <option value="1" selected>Ana Torres</option>
                  <option value="2">Luis Medina</option>
                  <option value="3">Dra. Pérez</option>
                </select>
              </div>

              <div>
                <label class="label-campo" for="estado">Estado:</label>
                <select class="campo" id="estado" name="estado">
                  <option value="1" selected>Activo</option>
                  <option value="0">Inactivo</option>
                </select>
              </div>

              <div class="campo-completo">
                <label class="label-campo" for="descripcion">Descripción:</label>
                <textarea class="campo" id="descripcion" name="descripcion" rows="3" placeholder="¿Qué incluye el servicio?"></textarea>
              </div>
            </div>
          </fieldset>

          <div class="acciones-formulario">
            <a class="btn-secundario" href="servicios.php">Cancelar</a>
            <button type="submit">Guardar cambios</button>
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