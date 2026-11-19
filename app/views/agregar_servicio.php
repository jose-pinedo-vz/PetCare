<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['usuario'])) {
    header("Location: ../views/login.php");
    exit();
}
$rolSesion = strtolower(trim((string)($_SESSION['rol'] ?? '')));
if (!in_array($rolSesion, ['admin', 'gerente'], true)) {
    header("Location: servicios.php");
    exit();
}
require_once __DIR__ . '/../models/Empleados.php';
$empleadosActivos = Empleados::Listar_empleados_activos();
// Lee y limpia el error y los datos guardados en sesion
$error = $_SESSION['error'] ?? '';
$old = [
    'nombre'       => $_SESSION['oldNombre_servicios'] ?? '',
    'categoria'    => $_SESSION['oldCategoria_servicios'] ?? '',
    'duracion'     => $_SESSION['oldDuracion_servicios'] ?? '30',
    'precio'       => $_SESSION['oldPrecio_servicios'] ?? '',
    'id_empleados' => $_SESSION['oldEmpleado_servicios'] ?? '',
    'estado'       => $_SESSION['oldEstado_servicios'] ?? '1',
    'descripcion'  => $_SESSION['oldDescripcion_servicios'] ?? ''
];
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
  <title>Veterinaria PetCare - Agregar servicio</title>
  <link rel="stylesheet" href="css/estilos_base_v2.css">
</head>

<body>
  <div class="layout">
    <?php $paginaActiva = 'servicios';
    include __DIR__ . '/layout/sidebar.php'; ?>

    <main>
      <div class="migaja"><a href="servicios.php" class="link-tenue">Gestión de servicios</a> / Agregar</div>
      <div class="encabezado-pagina">
        <h1>Registrar servicio</h1>
      </div>

      <div class="tarjeta">
        <form action="../controllers/guardar_servicio.php" method="POST">

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
                <input type="text" class="campo" id="nombre" name="nombre" maxlength="50" required placeholder="Ej. Baño medicado"
                  value="<?php echo htmlspecialchars((string)$old['nombre']); ?>">
              </div>

              <div>
                <label class="label-campo" for="categoria">Categoría *:</label>
                <select class="campo" id="categoria" name="categoria" required>
                  <option value="">Seleccionar...</option>
                  <option value="Estética" <?php echo $old['categoria'] === 'Estética' ? 'selected' : ''; ?>>Estética</option>
                  <option value="Salud" <?php echo $old['categoria'] === 'Salud' ? 'selected' : ''; ?>>Salud</option>
                </select>
              </div>

              <div>
                <label class="label-campo" for="duracion">Duración (minutos) *:</label>
                <input type="number" class="campo" id="duracion" name="duracion" min="1" required
                  value="<?php echo htmlspecialchars((string)$old['duracion']); ?>">
              </div>

              <div>
                <label class="label-campo" for="precio">Precio *:</label>
                <input type="number" class="campo" id="precio" name="precio" step="0.01" min="0" required
                  value="<?php echo htmlspecialchars((string)$old['precio']); ?>">
              </div>

              <div>
                <label class="label-campo" for="id_empleados">Empleado responsable:</label>
                <select class="campo" id="id_empleados" name="id_empleados">
                  <option value="">Seleccionar...</option>
                  <?php foreach ($empleadosActivos as $emp): ?>
                    <option value="<?php echo (int)$emp['id_empleado']; ?>" <?php echo (string)$old['id_empleados'] === (string)$emp['id_empleado'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($emp['nombre'] . ' ' . $emp['apellido']); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div>
                <label class="label-campo" for="estado">Estado:</label>
                <select class="campo" id="estado" name="estado">
                  <option value="1" <?php echo $old['estado'] === '1' ? 'selected' : ''; ?>>Activo</option>
                  <option value="0" <?php echo $old['estado'] === '0' ? 'selected' : ''; ?>>Inactivo</option>
                </select>
              </div>

              <div class="campo-completo">
                <label class="label-campo" for="descripcion">Descripción:</label>
                <textarea class="campo" id="descripcion" name="descripcion" rows="3" placeholder="¿Qué incluye el servicio?"><?php echo htmlspecialchars((string)$old['descripcion']); ?></textarea>
              </div>
            </div>
          </fieldset>

          <div class="acciones-formulario">
            <a class="btn-secundario" href="servicios.php">Cancelar</a>
            <button type="submit">Guardar</button>
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