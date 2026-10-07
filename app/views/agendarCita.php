<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veterinaria PetCare - Agendar cita</title>
  <link rel="stylesheet" href="css/estilos_base_v2.css">
</head>
<body>

  <div class="layout">

  <?php $paginaActiva = 'citas'; include __DIR__ . '/layout/sidebar.php'; ?>


  <main>
      <div class="migaja"><a href="citas.php" class="link-tenue">Gestión de Citas</a> / Agendar</div>
      <div class="encabezado-pagina">
        <h1>Agendar cita</h1>
      </div>

      <div class="tarjeta">
    


    <!-- mensaje en caso de algun error o mostrar informaicon nesesaria -->
    <?php if (isset($_SESSION['exito'])): ?>
      <div class="msg-success">
        <?php echo htmlspecialchars($_SESSION['exito']); unset($_SESSION['exito']); ?>
      </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
      <div class="msg-error">
        <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
      </div>
    <?php endif; ?>


    <form id="formCita" action="../controllers/citas.php" method="POST">
      <fieldset>
        <legend>Datos de la Cita</legend>

        <div class="grid-formulario">
          <div>
            <label class="label-campo" for="claveCliente">Clave del cliente *:</label>
            <input type="number" class="campo" id="claveCliente" name="claveCliente" required placeholder="Ej. 12"

              value="<?php echo htmlspecialchars((string)($_SESSION['oldClave'] ?? '')); unset($_SESSION['oldClave']); ?>">
          </div>

          <div>
            <label class="label-campo" for="fecha">Fecha:</label>
            <input type="date" class="campo" id="fecha" name="fecha" required 

              value="<?php echo htmlspecialchars((string)($_SESSION['oldFecha'] ?? '')); unset($_SESSION['oldFecha']); ?>">

          </div>

          <div class="campo-completo">
            <label class="label-campo" for="motivoConsulta">Motivo de consulta:</label>
            <input type="text" class="campo" id="motivoConsulta" name="motivoConsulta" placeholder="Ej. Revisión general, vacunación, malestar estomacal" required

              value="<?php echo htmlspecialchars((string)($_SESSION['oldMotivo'] ?? '')); unset($_SESSION['oldMotivo']); ?>">

          </div>
        </div>
      </fieldset>

      <div class="acciones-formulario">
        <a class="btn-secundario" href="citas.php">Cancelar</a>
        <button type="submit" class="btn">Guardar cita</button>
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