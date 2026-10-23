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

function e($texto): string
{
    return htmlspecialchars((string)$texto);
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
  <script src="js/codigo_Postal.js?v=2"></script>
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
      <div class="msg-error">
        <?php foreach (explode("\n\n", $mensajeError) as $error): ?>
          <div>• <?= e($error) ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form action="../controllers/guardar_empleado.php" method="POST">

      <fieldset>
        <legend>Datos personales</legend>
        <div class="grid-formulario">
          <div>
            <label class="label-campo" for="nombre">Nombre *:</label>
            <input type="text" class="campo" id="nombre" name="nombre" minlength="2" maxlength="20" required
              pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñ]{2,}( [A-Za-zÁÉÍÓÚáéíóúÑñ]{2,})*$"
              title="Solo letras y un espacio simple entre palabras (mínimo 2 letras por palabra)"
              value="<?= viejo('oldnombre_empleado') ?>">
          </div>

          <div>
            <label class="label-campo" for="apellido">Apellido *:</label>
            <input type="text" class="campo" id="apellido" name="apellido" minlength="2" maxlength="40" required
              pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñ]{2,}( [A-Za-zÁÉÍÓÚáéíóúÑñ]{2,})*$"
              title="Solo letras y un espacio simple entre palabras (mínimo 2 letras por palabra)"
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
              pattern="^[0-9]{10}$|^[0-9]{3}[-\s]?[0-9]{3}[-\s]?[0-9]{4}$"
              title="10 dígitos numéricos (ej. 3312345678 o 345-103-2345)"
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
            <input type="text" class="campo" id="codigo_postal" name="codigo_postal" maxlength="5" required
              pattern="^[0-9]{5}$" title="Código postal de exactamente 5 dígitos numéricos"
              value="<?= viejo('oldcodigo_postal') ?>">
          </div>

          <div>
            <label class="label-campo">&nbsp;</label>
            <button type="button" class="btn" id="validar_codigo_postal" onclick="codigoPostalEmpleados(event)">Validar código postal</button>
          </div>

          <div>
            <label class="label-campo" for="calle">Calle *:</label>
            <input type="text" class="campo" id="calle" name="calle" minlength="3" maxlength="100" required
              title="Debe incluir el nombre de la calle (mínimo 3 caracteres)"
              value="<?= viejo('oldcalle') ?>">
          </div>

          <div>
            <label class="label-campo" for="numero_exterior">Número exterior *:</label>
            <input type="text" class="campo" id="numero_exterior" name="numero_exterior" maxlength="15" required
              pattern="^[a-zA-Z0-9\-\/]{1,15}$" title="Solo números, letras y guiones (sin espacios)"
              value="<?= viejo('oldnumero_exterior') ?>">
          </div>

          <div>
            <label class="label-campo" for="numero_interior">Número interior:</label>
            <input type="text" class="campo" id="numero_interior" name="numero_interior" maxlength="15"
              pattern="^[a-zA-Z0-9\-\/]{1,15}$" title="Solo números, letras y guiones (sin espacios)"
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
            <input type="text" class="campo" id="especialidad" name="especialidad" minlength="3" maxlength="50"
              pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñ]{2,}([\s\-][A-Za-zÁÉÍÓÚáéíóúÑñ]{2,})*$"
              title="Solo letras y espacios (sin números ni símbolos especiales)"
              value="<?= viejo('oldespecialidad') ?>">
          </div>

          <div>
            <label class="label-campo" for="num_cedula_profesional">Número de cédula profesional:</label>
            <input type="text" class="campo" id="num_cedula_profesional" name="num_cedula_profesional" minlength="6" maxlength="10"
              pattern="^[0-9]{6,10}$" title="Entre 6 y 10 dígitos numéricos"
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
