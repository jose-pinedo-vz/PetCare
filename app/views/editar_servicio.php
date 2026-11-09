<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../models/Servicios.php';

// Busca el servicio por el id de la URL
$id = (int)($_GET['id_servicio'] ?? 0);
$servicio = $id > 0 ? Servicios::obtenerPorId($id) : null;

// Si no existe, regresa a la lista
if ($servicio === null) {
    header("Location: servicios.php");
    exit();
}

// Si hubo error usa lo escrito, si no usa los datos de la BD
$error = $_SESSION['error'] ?? '';
$f = [
    'nombre'       => $_SESSION['oldNombre_servicios'] ?? $servicio['nombre'],
    'categoria'    => $_SESSION['oldCategoria_servicios'] ?? $servicio['categoria'],
    'duracion'     => $_SESSION['oldDuracion_servicios'] ?? $servicio['duracion'],
    'precio'       => $_SESSION['oldPrecio_servicios'] ?? $servicio['precio'],
    'id_empleados' => $_SESSION['oldEmpleado_servicios'] ?? ($servicio['id_empleados'] ?? ''),
    'estado'       => $_SESSION['oldEstado_servicios'] ?? $servicio['estado'],
    'descripcion'  => $_SESSION['oldDescripcion_servicios'] ?? ($servicio['descripcion'] ?? '')
];
// Todo a texto para comparar bien
$f = array_map(fn($v) => (string)$v, $f);

unset(
    $_SESSION['error'],
    $_SESSION['oldNombre_servicios'],
    $_SESSION['oldCategoria_servicios'],
    $_SESSION['oldDuracion_servicios'],
    $_SESSION['oldPrecio_servicios'],
    $_SESSION['oldEmpleado_servicios'],
    $_SESSION['oldEstado_servicios'],
    $_SESSION['oldDescripcion_servicios']
);
?>
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
        <form method="POST" action="../controllers/guardar_servicio.php">
          <input type="hidden" name="id_servicio" value="<?php echo (int)$servicio['id_servicio']; ?>">

          <?php if ($error !== ''): ?>
            <div class="msg-error">
              <?php echo htmlspecialchars($error); ?>
            </div>
          <?php endif; ?>

          <fieldset>
            <legend>Datos del servicio</legend>

            <div class="grid-formulario">
              <div>
                <label class="label-campo" for="nombre">Nombre *:</label>
                <input type="text" class="campo" id="nombre" name="nombre" maxlength="50" required
                  value="<?php echo htmlspecialchars($f['nombre']); ?>">
              </div>

              <div>
                <label class="label-campo" for="categoria">Categoría *:</label>
                <select class="campo" id="categoria" name="categoria" required>
                  <option value="">Seleccionar...</option>
                  <option value="Estética" <?php echo $f['categoria'] === 'Estética' ? 'selected' : ''; ?>>Estética</option>
                  <option value="Salud" <?php echo $f['categoria'] === 'Salud' ? 'selected' : ''; ?>>Salud</option>
                </select>
              </div>

              <div>
                <label class="label-campo" for="duracion">Duración (minutos) *:</label>
                <input type="number" class="campo" id="duracion" name="duracion" min="1" required
                  value="<?php echo htmlspecialchars($f['duracion']); ?>">
              </div>

              <div>
                <label class="label-campo" for="precio">Precio *:</label>
                <input type="number" class="campo" id="precio" name="precio" step="0.01" min="0" required
                  value="<?php echo htmlspecialchars($f['precio']); ?>">
              </div>

              <div>
                <label class="label-campo" for="id_empleados">Empleado responsable:</label>
                <select class="campo" id="id_empleados" name="id_empleados">
                  <option value="">Seleccionar...</option>
                  <option value="1" <?php echo $f['id_empleados'] === '1' ? 'selected' : ''; ?>>Ana Torres</option>
                  <option value="2" <?php echo $f['id_empleados'] === '2' ? 'selected' : ''; ?>>Luis Medina</option>
                  <option value="3" <?php echo $f['id_empleados'] === '3' ? 'selected' : ''; ?>>Dra. Pérez</option>
                </select>
              </div>

              <div>
                <label class="label-campo" for="estado">Estado:</label>
                <select class="campo" id="estado" name="estado">
                  <option value="1" <?php echo $f['estado'] === '1' ? 'selected' : ''; ?>>Activo</option>
                  <option value="0" <?php echo $f['estado'] === '0' ? 'selected' : ''; ?>>Inactivo</option>
                </select>
              </div>

              <div class="campo-completo">
                <label class="label-campo" for="descripcion">Descripción:</label>
                <textarea class="campo" id="descripcion" name="descripcion" rows="3" placeholder="¿Qué incluye el servicio?"><?php echo htmlspecialchars($f['descripcion']); ?></textarea>
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