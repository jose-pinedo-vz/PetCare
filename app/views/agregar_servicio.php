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
                <input type="text" class="campo" id="nombre" name="nombre" maxlength="50" minlength="2" required pattern="[A-Za-zÁÉÍÓÚÜÑáéíóúüñ]+( [A-Za-zÁÉÍÓÚÜÑáéíóúüñ]+)*" autocomplete="off" placeholder="Ej. Baño medicado" title="Solo letras y espacios (mínimo 2 letras)"
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
                <input type="number" class="campo" id="duracion" name="duracion" min="1" max="1440" step="1" inputmode="numeric" required placeholder="Ej. 45" title="La duración debe estar entre 1 minuto y 24 horas."
                  value="<?php echo htmlspecialchars((string)$old['duracion']); ?>">
              </div>

              <div>
                <label class="label-campo" for="precio">Precio ($) *:</label>
                <input type="number" class="campo" id="precio" name="precio" step="0.01" min="1" max="999" inputmode="decimal" required placeholder="Ej. 250" title="El precio debe estar entre $1 y $999."
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
                <textarea class="campo" id="descripcion" name="descripcion" rows="3" maxlength="250" placeholder="¿Qué incluye el servicio?"><?php echo htmlspecialchars((string)$old['descripcion']); ?></textarea>
              </div>
            </div>
          </fieldset>

          <div class="acciones-formulario">
            <a class="btn btn-secundario btn-chico" href="servicios.php">Cancelar</a>
            <button type="submit" class="btn btn-chico">Guardar</button>
          </div>
        </form>
      </div>
    </main>
  </div>

  <footer>
    <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
  </footer>
  <script>
    // Validacion en pantalla: nombre solo letras y descripcion con texto real
    (function () {
      var n = document.getElementById('nombre');
      var d = document.getElementById('descripcion');
      function validarNombre() {
        var ok = /^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ]+( [A-Za-zÁÉÍÓÚÜÑáéíóúüñ]+)*$/.test(n.value.trim());
        n.setCustomValidity(ok ? '' : 'El nombre solo puede llevar letras y espacios.');
      }
      function validarDescripcion() {
        var v = d.value.trim();
        var letras = (v.match(/\p{L}/gu) || []).length;
        var ok = v === '' || (/^[\p{L}\p{N}\s.,;:()¿?¡!\-\/]+$/u.test(v) && letras >= 3);
        d.setCustomValidity(ok ? '' : 'Escribe texto (mínimo 3 letras); no se permiten solo números ni símbolos especiales.');
      }
      // No deja escribir ni pegar caracteres que no se permiten
      function limpiarNombre() {
        n.value = n.value.replace(/[^A-Za-zÁÉÍÓÚÜÑáéíóúüñ ]/g, '').replace(/^ +/, '').replace(/ {2,}/g, ' ');
      }
      function limpiarDescripcion() {
        d.value = d.value.replace(/[^\p{L}\p{N}\s.,;:()¿?¡!\-\/]/gu, '');
      }
      function soloEnteros(e) { if (['e', 'E', '+', '-', '.', ','].indexOf(e.key) !== -1) e.preventDefault(); }
      function soloDecimales(e) { if (['e', 'E', '+', '-'].indexOf(e.key) !== -1) e.preventDefault(); }
      var dur = document.getElementById('duracion');
      var pre = document.getElementById('precio');
      dur.addEventListener('keydown', soloEnteros);
      pre.addEventListener('keydown', soloDecimales);
      // Se deja escribir y se avisa con el error; el precio admite hasta 2 decimales
      dur.addEventListener('input', function () {
        if (dur.value.length > 6) dur.value = dur.value.slice(0, 6);
        dur.setCustomValidity(dur.value !== '' && (+dur.value < 1 || +dur.value > 1440) ? 'La duración debe estar entre 1 minuto y 24 horas.' : '');
      });
      pre.addEventListener('input', function () {
        var p = pre.value.split('.');
        if (p[0].length > 7 || (p[1] && p[1].length > 2)) pre.value = p[0].slice(0, 7) + (p.length > 1 ? '.' + p[1].slice(0, 2) : '');
        pre.setCustomValidity(pre.value !== '' && (+pre.value < 1 || +pre.value > 999) ? 'El precio debe estar entre $1 y $999.' : '');
      });
      n.addEventListener('input', function () { limpiarNombre(); validarNombre(); });
      d.addEventListener('input', function () { limpiarDescripcion(); validarDescripcion(); });
      validarNombre();
      validarDescripcion();
    })();
  </script>
</body>

</html>
