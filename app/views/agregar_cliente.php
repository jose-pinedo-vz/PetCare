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
  <title>Veterinaria PetCare - Registrar cliente</title>
  <link rel="stylesheet" href="css/estilos_base.css">
</head>
<body>

  <header>
    <div class="marca">
      <img src="img/logo3.svg" alt="Veterinaria PetCare">
    </div>

    <div class="sesion">
      <span>Empleado: Nombre del empleado</span>
      <a href="#">Cerrar sesión</a>
    </div>
  </header>

  <nav>
    <ul>
      <li><a href="mascotas.php">Mascotas</a></li>
      <li><a href="citas.php"o">Citas</a></li>
      <li><a href="clientes.php" class="activo">Clientes</a></li>
      <li><a href="empleados.php">Empleados</a></li>

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
    <h1>Registrar cliente</h1>

    <form action="../controllers/guardar_cliente.php" method="POST">

      <!-- mensaje de error  -->
      <?php if (isset($_SESSION['error'])): ?>
        <div style="background: #ffe6e6; color: #d32f2f; padding: 12px 15px; border-radius: 6px; border: 1px solid #ffcdd2; margin-bottom: 20px; font-weight: bold;">
          <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
      <?php endif; ?>
      <!-- hata aqui -->

      <fieldset>
        <legend> Datos personales</legend>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
          <div>
            <label class="label-campo" for="nombre">Nombre:</label>
            <input type="text" class="campo" id="nombre" name="nombre" maxlength="50" required placeholder="Ej. Juan Carlos"
              value="<?php echo htmlspecialchars((string)($_SESSION['oldnombre_clientes'] ?? '')); unset($_SESSION['oldnombre_clientes']); ?>">
            
          </div>

          <div>
            <label class="label-campo" for="apellido">Apellido:</label>
            <input type="text" class="campo" id="apellido" name="apellido" maxlength="100" required placeholder="Ej. Pérez García"
             value="<?php echo htmlspecialchars((string)($_SESSION['oldApellido_clientes'] ?? '')); unset($_SESSION['oldApellido_clientes']); ?>">
          </div>

          <div>
            <label class="label-campo" for="fecha_nacimiento">Fecha de nacimiento:</label>
            <input type="date" class="campo" id="fecha_nacimiento" name="fecha_nacimiento"

              value="<?php echo htmlspecialchars((string)($_SESSION['oldFechaNacimiento_clientes'] ?? '')); unset($_SESSION['oldFechaNacimiento_clientes']); ?>">
          </div>

          <div>
            <label class="label-campo" for="sexo">Sexo:</label>
            <select class="campo" id="sexo" name="sexo">
                <option value="">Selecciona una opción...</option>
                <option value="Femenino" <?php echo (isset($_SESSION['oldSexo_clientes']) && $_SESSION['oldSexo_clientes'] === 'Femenino') ? 'selected' : ''; ?>>Femenino</option>
                <option value="Masculino" <?php echo (isset($_SESSION['oldSexo_clientes']) && $_SESSION['oldSexo_clientes'] === 'Masculino') ? 'selected' : ''; ?>>Masculino</option>
                <option value="Otro" <?php echo (isset($_SESSION['oldSexo_clientes']) && $_SESSION['oldSexo_clientes'] === 'Otro') ? 'selected' : ''; ?>>Otro</option>
              ?>
            </select>

            <!-- Para serrar la secion -->
            <?php 
            if (isset($_SESSION['oldSexo_clientes'])) 
            {
              unset($_SESSION['oldSexo_clientes']);
            }
            ?>

          </div>

          <div>
            <label class="label-campo" for="rfc">RFC:</label>
            <input type="text" class="campo" id="rfc" name="rfc" maxlength="13" placeholder="Ej. PEGJ900101XXX"

              value="<?php echo htmlspecialchars((string)($_SESSION['oldRfc_clientes'] ?? '')); unset($_SESSION['oldRfc_clientes']); ?>">
          </div>
        </div>
      </fieldset>

      <fieldset>
        <legend> Datos de contacto</legend>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
          <div>
            <label class="label-campo" for="telefono">Teléfono:</label>
            <input type="tel" class="campo" id="telefono" name="telefono" maxlength="15" required placeholder="Ej. 4921234567"

              value="<?php echo htmlspecialchars((string)($_SESSION['oldTelefono_clientes'] ?? '')); unset($_SESSION['oldTelefono_clientes']); ?>">
          </div>

          <div>
            <label class="label-campo" for="telefono_alternativo">Teléfono alternativo:</label>
            <input type="tel" class="campo" id="telefono_alternativo" name="telefono_alternativo" maxlength="15" placeholder="Opcional"

              value="<?php echo htmlspecialchars((string)($_SESSION['oldTelefonoAlternativo_clientes'] ?? '')); unset($_SESSION['oldTelefonoAlternativo_clientes']); ?>">
          </div>

          <div style="grid-column: 1 / -1;">
            <label class="label-campo" for="correo">Correo electrónico:</label>
            <input type="email" class="campo" id="correo" name="correo" maxlength="100" required placeholder="correo@ejemplo.com"

              value="<?php echo htmlspecialchars((string)($_SESSION['oldCorreo_clientes'] ?? '')); unset($_SESSION['oldCorreo_clientes']); ?>">
          </div>
        </div>
      </fieldset>

      <fieldset>
        <legend> Dirección / Domicilio</legend>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
          <div>
            <label class="label-campo" for="calle">Calle:</label>
            <input type="text" class="campo" id="calle" name="calle" maxlength="100" required

              value="<?php echo htmlspecialchars((string)($_SESSION['oldCalle_clientes'] ?? '')); unset($_SESSION['oldCalle_clientes']); ?>">
          </div>

          <div>
            <label class="label-campo" for="numero_exterior">Número exterior:</label>
            <input type="text" class="campo" id="numero_exterior" name="numero_exterior" maxlength="15" required

              value="<?php echo htmlspecialchars((string)($_SESSION['oldNumeroExterior_clientes'] ?? '')); unset($_SESSION['oldNumeroExterior_clientes']); ?>">
          </div>

          <div>
            <label class="label-campo" for="numero_interior">Número interior:</label>
            <input type="text" class="campo" id="numero_interior" name="numero_interior" maxlength="15" placeholder="Opcional"

              value="<?php echo htmlspecialchars((string)($_SESSION['oldNumeroInterior_clientes'] ?? '')); unset($_SESSION['oldNumeroInterior_clientes']); ?>">
          </div>

          <div>
            <label class="label-campo" for="colonia">Colonia:</label>
            <input type="text" class="campo" id="colonia" name="colonia" maxlength="60" required

              value="<?php echo htmlspecialchars((string)($_SESSION['oldColonia_clientes'] ?? '')); unset($_SESSION['oldColonia_clientes']); ?>">
          </div>

          <div>
            <label class="label-campo" for="codigo_postal">Código postal:</label>
            <input type="text" class="campo" id="codigo_postal" name="codigo_postal" maxlength="10" required

              value="<?php echo htmlspecialchars((string)($_SESSION['oldCodigoPostal_clientes'] ?? '')); unset($_SESSION['oldCodigoPostal_clientes']); ?>">
          </div>

          <div>
            <label class="label-campo" for="ciudad">Ciudad:</label>
            <input type="text" class="campo" id="ciudad" name="ciudad" maxlength="60" required

              value="<?php echo htmlspecialchars((string)($_SESSION['oldCiudad_clientes'] ?? '')); unset($_SESSION['oldCiudad_clientes']); ?>">
          </div>

          <div>
            <label class="label-campo" for="estado">Estado:</label>
            <input type="text" class="campo" id="estado" name="estado" maxlength="60" required

              value="<?php echo htmlspecialchars((string)($_SESSION['oldEstado_clientes'] ?? '')); unset($_SESSION['oldEstado_clientes']); ?>">
          </div>
        </div>
      </fieldset>

      <fieldset>
        <legend> Contactos de emergencia</legend>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
          <div>
            <label class="label-campo" for="contacto_emergencia">Nombre contacto de emergencia:</label>
            <input type="text" class="campo" id="contacto_emergencia" name="contacto_emergencia" maxlength="100" required

              value="<?php echo htmlspecialchars((string)($_SESSION['oldContactoEmergencia_clientes'] ?? '')); unset($_SESSION['oldContactoEmergencia_clientes']); ?>">
          </div>

          <div>
            <label class="label-campo" for="telefono_emergencia">Teléfono de emergencia:</label>
            <input type="tel" class="campo" id="telefono_emergencia" name="telefono_emergencia" maxlength="15" required

              value="<?php echo htmlspecialchars((string)($_SESSION['oldTelefonoEmergencia_clientes'] ?? '')); unset($_SESSION['oldTelefonoEmergencia_clientes']); ?>">
          </div>

          <div style="grid-column: 1 / -1;">
            <label class="label-campo" for="observaciones">Observaciones:</label>
            <textarea class="campo" id="observaciones" name="observaciones" rows="2" placeholder="Observaciones o notas adicionales del cliente"><?php
              if (isset($_SESSION['oldObservaciones_clientes'])) {
                  echo htmlspecialchars((string)$_SESSION['oldObservaciones_clientes']);
                  unset($_SESSION['oldObservaciones_clientes']);
              }?></textarea>
          </div>
        </div>
      </fieldset>

      <div style="margin-top: 20px;">
        <button type="submit" class="btn">Guardar cliente</button>
        <a class="btn" href="clientes.php" style="background: #757575; margin-left: 8px;">Cancelar</a>
      </div>
    </form>
  </main>

  <footer>
    <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
  </footer>

</body>
</html>