<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si no venimos de un error, borra restos de intentos anteriores
if (!isset($_SESSION['error'])) {
    $camposOld = [
        'oldnombre_empleado', 'oldapellido', 'oldtelefono', 'oldcorreo',
        'oldcalle', 'oldnumero_exterior', 'oldnumero_interior', 'oldcolonia',
        'oldciudad', 'oldestado', 'oldcodigo_postal', 'oldpuesto',
        'oldespecialidad', 'oldnum_cedula_profesional', 'oldFecha_de_contratacion', 'oldhorario'
    ];
    foreach ($camposOld as $campo) {
        unset($_SESSION[$campo]);
    }
}

// Devuelve el valor guardado en sesion tras un error (y lo borra)
function viejo(string $clave): string
{
    $valor = (string)($_SESSION[$clave] ?? '');
    unset($_SESSION[$clave]);
    return htmlspecialchars($valor);
}

$puestoViejo  = viejo('oldpuesto');
$horarioViejo = viejo('oldhorario');
$coloniaVieja = viejo('oldcolonia');

$mensajeError = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veterinaria PetCare - Agregar empleado</title>
  <link rel="stylesheet" href="css/estilos_base_v2.css">
  <script src="js/codigo_Postal.js"></script>
</head>
<body>

  <div class="layout">

  <?php $paginaActiva = 'empleados'; include __DIR__ . '/layout/sidebar.php'; ?>

  <main>
      <div class="migaja"><a href="empleados.php" class="link-tenue">Gestión de Empleados</a> / Agregar</div>
      <div class="encabezado-pagina">
        <h1>Agregar empleado</h1>
      </div>

      <div class="tarjeta">

    <?php if ($mensajeError): ?>
      <div class="msg-error"><?= htmlspecialchars($mensajeError) ?></div>
    <?php endif; ?>

    <form action="../controllers/guardar_empleado.php" method="POST">

      <fieldset>
        <legend>Datos personales</legend>
        <div class="grid-formulario">
          <div>
            <label class="label-campo" for="nombre">Nombre *:</label>
            <input type="text" class="campo" id="nombre" name="nombre" maxlength="20" required
              value="<?= viejo('oldnombre_empleado') ?>">
          </div>

          <div>
            <label class="label-campo" for="apellido">Apellido *:</label>
            <input type="text" class="campo" id="apellido" name="apellido" maxlength="40" required
              value="<?= viejo('oldapellido') ?>">
          </div>
        </div>
      </fieldset>

      <fieldset>
        <legend>Datos de contacto</legend>
        <div class="grid-formulario">
          <div>
            <label class="label-campo" for="telefono">Teléfono *:</label>
            <input type="tel" class="campo" id="telefono" name="telefono" maxlength="15" required
              value="<?= viejo('oldtelefono') ?>">
          </div>

          <div>
            <label class="label-campo" for="correo">Correo electrónico *:</label>
            <input type="email" class="campo" id="correo" name="correo" maxlength="100" required
              value="<?= viejo('oldcorreo') ?>">
          </div>
        </div>
      </fieldset>

      <fieldset>
        <legend>Domicilio</legend>
        <div class="grid-formulario">
          <div>
            <label class="label-campo" for="codigo_postal">Código postal *:</label>
            <input type="text" class="campo" id="codigo_postal" name="codigo_postal" maxlength="10" required
              value="<?= viejo('oldcodigo_postal') ?>">
          </div>

          <div>
            <label class="label-campo">&nbsp;</label>
            <button type="button" class="btn" id="validar_codigo_postal" onclick="codigoPostalEmpleados(event)">Validar código postal</button>
          </div>

          <div>
            <label class="label-campo" for="calle">Calle *:</label>
            <input type="text" class="campo" id="calle" name="calle" maxlength="100" required
              value="<?= viejo('oldcalle') ?>">
          </div>

          <div>
            <label class="label-campo" for="numero_exterior">Número exterior *:</label>
            <input type="text" class="campo" id="numero_exterior" name="numero_exterior" maxlength="15" required
              value="<?= viejo('oldnumero_exterior') ?>">
          </div>

          <div>
            <label class="label-campo" for="numero_interior">Número interior:</label>
            <input type="text" class="campo" id="numero_interior" name="numero_interior" maxlength="15"
              value="<?= viejo('oldnumero_interior') ?>">
          </div>

          <div>
            <label class="label-campo" for="colonia">Colonia *:</label>
            <select class="campo" id="colonia" name="colonia" required>
              <?php if ($coloniaVieja !== ''): ?>
                <option value="<?= $coloniaVieja ?>"><?= $coloniaVieja ?></option>
              <?php else: ?>
                <option value="">Valide su código postal...</option>
              <?php endif; ?>
            </select>
          </div>

          <div>
            <label class="label-campo" for="ciudad">Ciudad *:</label>
            <input type="text" class="campo" id="ciudad" name="ciudad" maxlength="60" required readonly
              value="<?= viejo('oldciudad') ?>">
          </div>

          <div>
            <label class="label-campo" for="estado">Estado *:</label>
            <input type="text" class="campo" id="estado" name="estado" maxlength="60" required readonly
              value="<?= viejo('oldestado') ?>">
          </div>
        </div>
      </fieldset>

      <fieldset>
        <legend>Información laboral</legend>
        <div class="grid-formulario">
          <div>
            <label class="label-campo" for="puesto">Puesto *:</label>
            <select class="campo" id="puesto" name="puesto" required>
              <option value="">Selecciona un puesto</option>
              <option value="Gerente"       <?= $puestoViejo === 'Gerente'       ? 'selected' : '' ?>>Gerente</option>
              <option value="Veterinario/a" <?= $puestoViejo === 'Veterinario/a' ? 'selected' : '' ?>>Veterinario</option>
              <option value="Vendedor"      <?= $puestoViejo === 'Vendedor'      ? 'selected' : '' ?>>Vendedor</option>
              <option value="Estilista"     <?= $puestoViejo === 'Estilista'     ? 'selected' : '' ?>>Estilista</option>
            </select>
          </div>

          <div>
            <label class="label-campo" for="especialidad">Especialidad:</label>
            <input type="text" class="campo" id="especialidad" name="especialidad" maxlength="50"
              value="<?= viejo('oldespecialidad') ?>">
          </div>

          <div>
            <label class="label-campo" for="num_cedula_profesional">Número de cédula profesional:</label>
            <input type="text" class="campo" id="num_cedula_profesional" name="num_cedula_profesional" maxlength="20"
              value="<?= viejo('oldnum_cedula_profesional') ?>">
          </div>

          <div>
            <label class="label-campo" for="Fecha_de_contratacion">Fecha de contratación:</label>
            <input type="date" class="campo" id="Fecha_de_contratacion" name="Fecha_de_contratacion"
              max="<?= date('Y-m-d') ?>" value="<?= viejo('oldFecha_de_contratacion') ?>">
          </div>

          <div>
            <label class="label-campo" for="horario">Horario *:</label>
            <select class="campo" id="horario" name="horario" required>
              <option value="">Selecciona un horario</option>
              <option value="Matutino"   <?= $horarioViejo === 'Matutino'   ? 'selected' : '' ?>>Matutino</option>
              <option value="Vespertino" <?= $horarioViejo === 'Vespertino' ? 'selected' : '' ?>>Vespertino</option>
            </select>
          </div>
        </div>
      </fieldset>

      <div class="acciones-formulario">
        <a class="btn-secundario" href="empleados.php">Regresar</a>
        <button type="submit" class="btn">Guardar</button>
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
