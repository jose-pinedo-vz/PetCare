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
    header("Location: empleados.php");
    exit();
}

// function v($valor) {
//     return htmlspecialchars((string)($valor ?? ''));
// }
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veterinaria PetCare - Editar empleado</title>
  <link rel="stylesheet" href="css/estilos_base_v2.css">
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
    

    <?php if (isset($_SESSION['error'])): ?>
      <div style="background: #ffe6e6; color: #d32f2f; padding: 12px 15px; border-radius: 6px; border: 1px solid #ffcdd2; margin-bottom: 20px; font-weight: bold;">
        <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
      </div>
    <?php endif; ?>

    <form action="../controllers/guardar_empleado.php" method="POST">


      <input
        type="hidden"
        id="id_empleado"
        name="id_empleado"
        value="<?php echo htmlspecialchars((string)$empleado['id_empleado']); ?>"
      >

      <fieldset>

        <legend>Datos personales</legend>

        <div class="grid-formulario">

          <div>
            <label class="label-campo" for="nombre">
              Nombre*:
            </label>

            <input
              type="text"
              class="campo"
              id="nombre"
              name="nombre"
              value="<?php echo htmlspecialchars($empleado['nombre'] ?? ''); ?>"
              maxlength="20"
              required
            >
          </div>


          <div>
            <label class="label-campo" for="apellido">
              Apellido*:
            </label>

            <input
              type="text"
              class="campo"
              id="apellido"
              name="apellido"
              value="<?php echo htmlspecialchars($empleado['apellido'] ?? ''); ?>"
              maxlength="40"
              required
            >
          </div>

        </div>

      </fieldset>

      <fieldset>

        <legend>Datos de contacto</legend>

        <div class="grid-formulario">

          <div>
            <label class="label-campo" for="telefono">
              Teléfono*:
            </label>

            <input
              type="tel"
              class="campo"
              id="telefono"
              name="telefono"
              value="<?php echo htmlspecialchars($empleado['telefono'] ?? ''); ?>"
              maxlength="15"
              required
            >
          </div>


          <div>
            <label class="label-campo" for="correo">
              Correo electrónico*:
            </label>

            <input
              type="email"
              class="campo"
              id="correo"
              name="correo"
              value="<?php echo htmlspecialchars($empleado['correo'] ?? ''); ?>"
              maxlength="100"
              required
            >
          </div>

        </div>

      </fieldset>


      <!-- DOMICILIO -->

      <fieldset>

        <legend>Domicilio</legend>

        <div class="grid-formulario">

          <div>
            <label class="label-campo" for="calle">
              Calle*:
            </label>

            <input
              type="text"
              class="campo"
              id="calle"
              name="calle"
              value="<?php echo htmlspecialchars($empleado['calle'] ?? ''); ?>"
              maxlength="100"
              required
            >
          </div>


          <div>
            <label class="label-campo" for="numero_exterior">
              Número exterior*:
            </label>

            <input
              type="text"
              class="campo"
              id="numero_exterior"
              name="numero_exterior"
              value="<?php echo htmlspecialchars($empleado['numero_exterior'] ?? ''); ?>"
              maxlength="15"
              required
            >
          </div>


          <div>
            <label class="label-campo" for="numero_interior">
              Número interior:
            </label>

            <input
              type="text"
              class="campo"
              id="numero_interior"
              name="numero_interior"
              value="<?php echo htmlspecialchars($empleado['numero_interior'] ?? ''); ?>"
              maxlength="15"
            >
          </div>


          <div>
            <label class="label-campo" for="colonia">
              Colonia*:
            </label>

            <input
              type="text"
              class="campo"
              id="colonia"
              name="colonia"
              value="<?php echo htmlspecialchars($empleado['colonia'] ?? ''); ?>"
              maxlength="60"
              required
            >
          </div>


          <div>
            <label class="label-campo" for="ciudad">
              Ciudad*:
            </label>

            <input
              type="text"
              class="campo"
              id="ciudad"
              name="ciudad"
              value="<?php echo htmlspecialchars($empleado['ciudad'] ?? ''); ?>"
              maxlength="60"
              required
            >
          </div>


          <div>
            <label class="label-campo" for="estado">
              Estado*:
            </label>

            <input
              type="text"
              class="campo"
              id="estado"
              name="estado"
              value="<?php echo htmlspecialchars($empleado['estado'] ?? ''); ?>"
              maxlength="60"
              required
            >
          </div>


          <div>
            <label class="label-campo" for="codigo_postal">
              Código Postal*:
            </label>

            <input
              type="text"
              class="campo"
              id="codigo_postal"
              name="codigo_postal"
              value="<?php echo htmlspecialchars($empleado['codigo_postal'] ?? ''); ?>"
              maxlength="10"
              required
            >
          </div>

        </div>

      </fieldset>

      <fieldset>

        <legend>Información laboral</legend>

        <div class="grid-formulario">

          <div>
            <label class="label-campo" for="puesto">
              Puesto*:
            </label>

            <select class="campo" id="puesto" name="puesto" required>
                <option value="">Selecciona un puesto</option>
                <option value="Gerente" <?php echo (($empleado['puesto'] ?? '') === 'Gerente') ? 'selected' : ''; ?>>Gerente</option>
                <option value="Veterinario" <?php echo (($empleado['puesto'] ?? '') === 'Veterinario') ? 'selected' : ''; ?>>Veterinario</option>
                <option value="Vendedor" <?php echo (($empleado['puesto'] ?? '') === 'Vendedor') ? 'selected' : ''; ?>>Vendedor</option>
                <option value="Estilista" <?php echo (($empleado['puesto'] ?? '') === 'Estilista') ? 'selected' : ''; ?>>Estilista</option>
            </select>
          </div>


          <div>
            <label class="label-campo" for="especialidad">
              Especialidad*:
            </label>

            <input
              type="text"
              class="campo"
              id="especialidad"
              name="especialidad"
              value="<?php echo htmlspecialchars($empleado['especialidad'] ?? ''); ?>"
              maxlength="50"
            >
          </div>


          <div>
            <label class="label-campo" for="num_cedula_profesional">
               Número de cédula profesional*:
            </label>

            <input
              type="text"
              class="campo"
              id="num_cedula_profesional"
              name="num_cedula_profesional"
              value="<?php echo htmlspecialchars($empleado['num_cedula_profesional'] ?? ''); ?>"
              maxlength="20"
            >
          </div>


          <div>
            <label class="label-campo" for="Fecha_de_contratacion">
              Fecha de contratación:
            </label>

            <input
              type="date"
              class="campo"
              id="Fecha_de_contratacion"
              name="Fecha_de_contratacion"
              value="<?php echo htmlspecialchars(substr((string)($empleado['Fecha_de_contratacion'] ?? ''), 0, 10)); ?>"
            >
          </div>


          <div>
            <label class="label-campo" for="horario">
              Horario*:
            </label>
            <select class="campo" id="horario" name="horario" required>
                <option value="">Selecciona un horario</option>
                <option value="Matutino" <?php echo (($empleado['horario'] ?? '') === 'Matutino') ? 'selected' : ''; ?>>Matutino</option>
                <option value="Vespertino" <?php echo (($empleado['horario'] ?? '') === 'Vespertino') ? 'selected' : ''; ?>>Vespertino</option>
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
