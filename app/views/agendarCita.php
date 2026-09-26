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
  <title>Veterinaria PetCare - Agregar mascota</title>
  <link rel="stylesheet" href="css/estilos_base.css">
</head>
<body>

  <header>
    <div class="marca">
      <img src="./img/logo.svg" alt="Veterinaria PetCare">
    </div>

    <div class="sesion">
      <span>Empleado: Nombre del Empleado</span>
      <a href="#">Cerrar sesión</a>
    </div>
  </header>

  <nav>
    <ul>
      <li><a href="mascotas.php" class="activo">Mascotas</a></li>
      <li><a href="citas.php" class="activo">citas</a></li>
      <li><a href="clientes.php" class="activo">Clientes</a></li>

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
    <h1>Agendar Cita</h1>


    <!-- mensaje en caso de algun error o mostrar informaicon nesesaria -->
    <?php if (isset($_SESSION['exito'])): ?>
      <div style="background: #e8f5e9; color: #2e7d32; padding: 12px 15px; border-radius: 6px; border: 1px solid #c8e6c9; margin-bottom: 20px; font-weight: bold;">
        <?php echo htmlspecialchars($_SESSION['exito']); unset($_SESSION['exito']); ?>
      </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
      <div style="background: #ffe6e6; color: #d32f2f; padding: 12px 15px; border-radius: 6px; border: 1px solid #ffcdd2; margin-bottom: 20px; font-weight: bold;">
        <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
      </div>
    <?php endif; ?>



    <form id="formCita" action="../controllers/citas.php" method="POST">
      <fieldset>
        <legend style="font-weight: bold; color: #333; padding: 0 5px;">Datos de la Cita</legend>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
          <div>
            <label class="label-campo" for="claveCliente">Clave del cliente *:</label>
            <input type="number" class="campo" id="claveCliente" name="claveCliente" required placeholder="Ej. 12"

              value="<?php echo htmlspecialchars((string)($_SESSION['oldClave'] ?? '')); unset($_SESSION['oldClave']); ?>">

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
        <a class="btn" href="citas.php" style="background: #757575; margin-left: 8px;">Cancelar</a>
      </div>
    </form>
  </main>

  <footer>
    <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
  </footer>

</body>
</html>
