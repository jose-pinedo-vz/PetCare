<?php
require_once __DIR__ . '/../models/Empleados.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$idEmpleado = filter_input(INPUT_GET, 'id_empleado', FILTER_VALIDATE_INT);

if ($idEmpleado === false || $idEmpleado === null) {
    header('Location: empleados.php');
    exit();
}

$empleado = Empleados::obtenerporId($idEmpleado);

if (!$empleado) {
    header('Location: empleados.php');
    exit();
}

// Si no venimos de un error, borra restos de intentos anteriores (por ejemplo de "agregar")
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

// Valor del formulario: lo escrito antes del error o, si no hay, el de la BD
function dato(array $empleado, string $campoBD, string $claveSesion): string
{
    if (array_key_exists($claveSesion, $_SESSION)) {
        $valor = $_SESSION[$claveSesion];
        unset($_SESSION[$claveSesion]);
    } else {
        $valor = $empleado[$campoBD] ?? '';
    }
    return (string)($valor ?? '');
}

function e($texto): string
{
    return htmlspecialchars((string)$texto);
}

$nombre      = dato($empleado, 'nombre', 'oldnombre_empleado');
$apellido    = dato($empleado, 'apellido', 'oldapellido');
$telefono    = dato($empleado, 'telefono', 'oldtelefono');
$correo      = dato($empleado, 'correo', 'oldcorreo');
$calle       = dato($empleado, 'calle', 'oldcalle');
$numExterior = dato($empleado, 'numero_exterior', 'oldnumero_exterior');
$numInterior = dato($empleado, 'numero_interior', 'oldnumero_interior');
$colonia     = dato($empleado, 'colonia', 'oldcolonia');
$ciudad      = dato($empleado, 'ciudad', 'oldciudad');
$estado      = dato($empleado, 'estado', 'oldestado');
$codigoPost  = dato($empleado, 'codigo_postal', 'oldcodigo_postal');
$especialidad = dato($empleado, 'especialidad', 'oldespecialidad');
$cedula      = dato($empleado, 'num_cedula_profesional', 'oldnum_cedula_profesional');
$fecha       = substr(dato($empleado, 'Fecha_de_contratacion', 'oldFecha_de_contratacion'), 0, 10);
$horario     = dato($empleado, 'horario', 'oldhorario');

// Al agregar se guarda "Veterinario/a"; registros viejos pueden tener "Veterinario".
// Se normaliza para que el <select> lo preseleccione.
$puesto = dato($empleado, 'puesto', 'oldpuesto');
if ($puesto === 'Veterinario') {
    $puesto = 'Veterinario/a';
}

$mensajeError = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veterinaria PetCare - Editar empleado</title>
  <link rel="stylesheet" href="css/estilos_base_v2.css">
  <script src="js/codigo_Postal.js"></script>
</head>
<body>

  <div class="layout">

  <?php $paginaActiva = 'empleados'; include __DIR__ . '/layout/sidebar.php'; ?>

  <main>
      <div class="migaja"><a href="empleados.php" class="link-tenue">Gestión de Empleados</a> / Editar</div>
      <div class="encabezado-pagina">
        <h1>Editar empleado</h1>
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

      <input type="hidden" id="id_empleado" name="id_empleado" value="<?= (int)$empleado['id_empleado'] ?>">

      <fieldset>
        <legend>Datos personales</legend>
        <div class="grid-formulario">
          <div>
            <label class="label-campo" for="nombre">Nombre *:</label>
            <input type="text" class="campo" id="nombre" name="nombre" minlength="2" maxlength="20" required
              pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñ]{2,}( [A-Za-zÁÉÍÓÚáéíóúÑñ]{2,})*$"
              title="Solo letras y un espacio simple entre palabras (mínimo 2 letras por palabra)"
              value="<?= e($nombre) ?>">
          </div>

          <div>
            <label class="label-campo" for="apellido">Apellido *:</label>
            <input type="text" class="campo" id="apellido" name="apellido" minlength="2" maxlength="40" required
              pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñ]{2,}( [A-Za-zÁÉÍÓÚáéíóúÑñ]{2,})*$"
              title="Solo letras y un espacio simple entre palabras (mínimo 2 letras por palabra)"
              value="<?= e($apellido) ?>">
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
              value="<?= e($telefono) ?>">
          </div>

          <div>
            <label class="label-campo" for="correo">Correo electrónico *:</label>
            <input type="email" class="campo" id="correo" name="correo" maxlength="100" required
              value="<?= e($correo) ?>">
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
              value="<?= e($codigoPost) ?>">
          </div>

          <div>
            <label class="label-campo">&nbsp;</label>
            <button type="button" class="btn" id="validar_codigo_postal" onclick="codigoPostalEmpleados(event)">Validar código postal</button>
          </div>

          <div>
            <label class="label-campo" for="calle">Calle *:</label>
            <input type="text" class="campo" id="calle" name="calle" minlength="3" maxlength="100" required
              title="Debe incluir el nombre de la calle (mínimo 3 caracteres)"
              value="<?= e($calle) ?>">
          </div>

          <div>
            <label class="label-campo" for="numero_exterior">Número exterior *:</label>
            <input type="text" class="campo" id="numero_exterior" name="numero_exterior" maxlength="15" required
              pattern="^[a-zA-Z0-9\-\/]{1,15}$" title="Solo números, letras y guiones (sin espacios)"
              value="<?= e($numExterior) ?>">
          </div>

          <div>
            <label class="label-campo" for="numero_interior">Número interior:</label>
            <input type="text" class="campo" id="numero_interior" name="numero_interior" maxlength="15"
              pattern="^[a-zA-Z0-9\s.\-\/]{1,15}$" title="Número interior válido"
              value="<?= e($numInterior) ?>">
          </div>

          <div>
            <label class="label-campo" for="colonia">Colonia *:</label>
            <select class="campo" id="colonia" name="colonia" required>
              <?php if (!empty($colonia)): ?>
                <option value="<?= e($colonia) ?>" selected><?= e($colonia) ?></option>
              <?php else: ?>
                <option value="">Valide su código postal...</option>
              <?php endif; ?>
            </select>
          </div>

          <div>
            <label class="label-campo" for="ciudad">Ciudad *:</label>
            <input type="text" class="campo" id="ciudad" name="ciudad" maxlength="60" required readonly
              value="<?= e($ciudad) ?>">
          </div>

          <div>
            <label class="label-campo" for="estado">Estado *:</label>
            <input type="text" class="campo" id="estado" name="estado" maxlength="60" required readonly
              value="<?= e($estado) ?>">
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
              <option value="Gerente"       <?= $puesto === 'Gerente'       ? 'selected' : '' ?>>Gerente</option>
              <option value="Veterinario/a" <?= $puesto === 'Veterinario/a' ? 'selected' : '' ?>>Veterinario</option>
              <option value="Vendedor"      <?= $puesto === 'Vendedor'      ? 'selected' : '' ?>>Vendedor</option>
              <option value="Estilista"     <?= $puesto === 'Estilista'     ? 'selected' : '' ?>>Estilista</option>
            </select>
          </div>

          <div>
            <label class="label-campo" for="especialidad">Especialidad:</label>
            <input type="text" class="campo" id="especialidad" name="especialidad" minlength="3" maxlength="50"
              pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñ]{2,}([\s\-][A-Za-zÁÉÍÓÚáéíóúÑñ]{2,})*$"
              title="Solo letras y espacios (sin números ni símbolos especiales)"
              value="<?= e($especialidad) ?>">
          </div>

          <div>
            <label class="label-campo" for="num_cedula_profesional">Número de cédula profesional:</label>
            <input type="text" class="campo" id="num_cedula_profesional" name="num_cedula_profesional" minlength="6" maxlength="8"
              pattern="^[0-9]{6,8}$" title="Entre 6 y 8 dígitos numéricos"
              value="<?= e($cedula) ?>">
          </div>

          <div>
            <label class="label-campo" for="Fecha_de_contratacion">Fecha de contratación:</label>
            <input type="date" class="campo" id="Fecha_de_contratacion" name="Fecha_de_contratacion"
              max="<?= date('Y-m-d') ?>" value="<?= e($fecha) ?>">
          </div>

          <div>
            <label class="label-campo" for="horario">Horario *:</label>
            <select class="campo" id="horario" name="horario" required>
              <option value="">Selecciona un horario</option>
              <option value="Matutino"   <?= $horario === 'Matutino'   ? 'selected' : '' ?>>Matutino</option>
              <option value="Vespertino" <?= $horario === 'Vespertino' ? 'selected' : '' ?>>Vespertino</option>
            </select>
          </div>
        </div>
      </fieldset>

      <div class="acciones-formulario">
        <a class="btn-secundario" href="empleados.php">Cancelar</a>
        <button type="submit" class="btn">Guardar cambios</button>
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
