<?php
  session_start();
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
      <li><a href="citas.html" class="activo">Citas</a></li>


      <li><a href="#" class="deshabilitado">Clientes</a></li>
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

    </form>
  </main>

  <footer>
    <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
  </footer>

</body>
</html>
