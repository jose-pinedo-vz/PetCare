<?php
<<<<<<< HEAD
  session_start();
?>

=======
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
>>>>>>> 69e62d25704b378eb798e55c6a7e7b7873ff7cb1
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
<<<<<<< HEAD
  <title>Veterinaria PetCare - Agregar mascota</title>
=======
  <title>Veterinaria PetCare - Agendar Cita</title>
>>>>>>> 69e62d25704b378eb798e55c6a7e7b7873ff7cb1
  <link rel="stylesheet" href="css/estilos_base.css">
</head>
<body>

  <header>
    <div class="marca">
<<<<<<< HEAD
      <img src="./img/logo.svg" alt="Veterinaria PetCare">
=======
      <img src="img/logo.svg" alt="Veterinaria PetCare">
>>>>>>> 69e62d25704b378eb798e55c6a7e7b7873ff7cb1
    </div>

    <div class="sesion">
      <span>Empleado: Nombre del Empleado</span>
      <a href="#">Cerrar sesión</a>
    </div>
  </header>

  <nav>
    <ul>
<<<<<<< HEAD
      <li><a href="mascotas.php" class="activo">Mascotas</a></li>
      <li><a href="citas.php" class="activo">Citas</a></li>


      <li><a href="#" class="deshabilitado">Clientes</a></li>
=======
      <li><a href="mascotas.php">Mascotas</a></li>
      <li><a href="clientes.php">Clientes</a></li>
      <li><a href="citas.html" class="activo">Citas</a></li>
>>>>>>> 69e62d25704b378eb798e55c6a7e7b7873ff7cb1
      <li><a href="#" class="deshabilitado">Empleados</a></li>
      <li><a href="#" class="deshabilitado">Proveedores</a></li>
      <li><a href="#" class="deshabilitado">Inventario</a></li>
      <li><a href="#" class="deshabilitado">Ventas</a></li>
      <li><a href="#" class="deshabilitado">Servicios</a></li>
      <li><a href="#" class="deshabilitado">Adopción y venta</a></li>
      <li><a href="#" class="deshabilitado">Veterinaria</a></li>
      <li><a href="#" class="deshabilitado">Pagos</a></li>
      <li><a href="#" class="deshabilitado">Pagos con tarjeta</a></li>
    </ul>
  </nav>

  <main>
<<<<<<< HEAD
    <h1>Registrar mascota</h1>


    <!-- sesion -->
    <!-- <?php //if (isset($_GET['error']) && $_GET['error'] === 'vacio'): ?>
        <div class="error-box" style="color: red;">Todos los campos deben estar llenos.</div>
    <?php // endif; ?> -->

    <?php if (isset($_SESSION['error'])): ?>
        <div class="error-box" style="background: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 15px;">
            <?php 
                echo $_SESSION['error']; 
                unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>

    <form id="formCita" action="../controllers/citas.php" method="POST" enctype="multipart/form-data">

        <fieldset>
            <legend style="font-weight: bold; color: #333; padding: 0 5px;"> Datos Básicos</legend>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
              <div>
                <label class="label-campo" class="label-campo" for="nombre" >Clave del cliente *:</label>
                <input type="text" class="campo" id="claveCliente" name="claveCliente" required
                  value="<?php echo htmlspecialchars($_SESSION['oldClave'] ?? ''); unset($_SESSION['oldClave']); ?>">
              </div>

              <div>
                <label class="label-campo" for="fecha" >Fecha *:</label>
                <input type="date" class="campo" id="fecha" name="fecha" placeholder="" required 
                  value="<?php echo htmlspecialchars($_SESSION['oldFecha'] ?? ''); unset($_SESSION['oldFecha']); ?>">
              </div>

              <div>
                <label class="label-campo" for="motivoConsulta" >Motivo de colsulta:</label>
                <input type="text" class="campo" id="motivoConsulta" name="motivoConsulta" placeholder="Ej. Tos" required
                  value="<?php echo htmlspecialchars($_SESSION['oldMotivo'] ?? ''); unset($_SESSION['oldMotivo']); ?>">
              </div>

              <!-- Caja de error oculta por defecto -->
              <div id="mensajeError" style="display: none; background-color: #f8d7da; color: #721c24; padding: 12px; margin-bottom: 15px; border-radius: 5px; border: 1px solid #f5c6cb; font-weight: bold;">
              </div>

              <!-- finta -->
              <div></div>

      <button type="submit">Guardar</button>
      <a class="btn" href="citas.html">Cancelar</a>

=======
    <h1>Agendar Cita</h1>

    <?php if (isset($_SESSION['exito'])): ?>
      <div style="background: #e8f5e9; color: #2e7d32; padding: 12px 15px; border-radius: 6px; border: 1px solid #c8e6c9; margin-bottom: 20px; font-weight: bold;">
        ✔ <?php echo htmlspecialchars($_SESSION['exito']); unset($_SESSION['exito']); ?>
      </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
      <div style="background: #ffe6e6; color: #d32f2f; padding: 12px 15px; border-radius: 6px; border: 1px solid #ffcdd2; margin-bottom: 20px; font-weight: bold;">
        ✖ <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
      </div>
    <?php endif; ?>

    <form id="formCita" action="../controllers/citas.php" method="POST">
      <fieldset>
        <legend style="font-weight: bold; color: #333; padding: 0 5px;">📅 Datos de la Cita</legend>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
          <div>
            <label class="label-campo" for="claveCliente">Clave del Cliente (ID) *:</label>
            <input type="number" class="campo" id="claveCliente" name="claveCliente" required placeholder="Ej. 12"
              value="<?php echo htmlspecialchars((string)($_SESSION['oldClave'] ?? '')); unset($_SESSION['oldClave']); ?>">
            <small style="color: #666;">El cliente debe estar registrado previamente en el sistema.</small>
          </div>

          <div>
            <label class="label-campo" for="fecha">Fecha *:</label>
            <input type="date" class="campo" id="fecha" name="fecha" required 
              value="<?php echo htmlspecialchars((string)($_SESSION['oldFecha'] ?? '')); unset($_SESSION['oldFecha']); ?>">
          </div>

          <div style="grid-column: 1 / -1;">
            <label class="label-campo" for="motivoConsulta">Motivo de consulta *:</label>
            <input type="text" class="campo" id="motivoConsulta" name="motivoConsulta" placeholder="Ej. Revisión general, vacunación, malestar estomacal" required
              value="<?php echo htmlspecialchars((string)($_SESSION['oldMotivo'] ?? '')); unset($_SESSION['oldMotivo']); ?>">
          </div>
        </div>
      </fieldset>

      <div style="margin-top: 20px;">
        <button type="submit" class="btn">Guardar Cita</button>
        <a class="btn" href="citas.html" style="background: #757575; margin-left: 8px;">Cancelar</a>
      </div>
>>>>>>> 69e62d25704b378eb798e55c6a7e7b7873ff7cb1
    </form>
  </main>

  <footer>
    <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
  </footer>

</body>
<<<<<<< HEAD
</html>
=======
</html>
>>>>>>> 69e62d25704b378eb798e55c6a7e7b7873ff7cb1
