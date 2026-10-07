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
  <link rel="stylesheet" href="css/estilos_base_v2.css">
</head>
<body>

  <div class="layout">

  <?php $paginaActiva = 'clientes'; include __DIR__ . '/layout/sidebar.php'; ?>


  <main>
      <div class="migaja"><a href="clientes.php" class="link-tenue">Gestión de Clientes</a> / Agregar</div>
      <div class="encabezado-pagina">
        <h1>Registrar cliente</h1>
      </div>

      <div class="tarjeta">
    

    <form action="../controllers/guardar_cliente.php" method="POST">

      <!-- mensaje de error  -->
      <?php if (isset($_SESSION['error'])): ?>
        <div class="msg-error">
          <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
      <?php endif; ?>
      <!-- hata aqui -->

      <fieldset>
        <legend> Datos personales</legend>
        <div class="grid-formulario">
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
        <div class="grid-formulario">
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

          <div class="campo-completo">
            <label class="label-campo" for="correo">Correo electrónico:</label>
            <input type="email" class="campo" id="correo" name="correo" maxlength="100" required placeholder="correo@ejemplo.com"

              value="<?php echo htmlspecialchars((string)($_SESSION['oldCorreo_clientes'] ?? '')); unset($_SESSION['oldCorreo_clientes']); ?>">
          </div>
        </div>
      </fieldset>

      <fieldset>
        <legend> Dirección / Domicilio</legend>
        <div class="grid-formulario">
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
        <div class="grid-formulario">
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

          <div class="campo-completo">
            <label class="label-campo" for="observaciones">Observaciones:</label>
            <textarea class="campo" id="observaciones" name="observaciones" rows="2" placeholder="Observaciones o notas adicionales del cliente"><?php
              if (isset($_SESSION['oldObservaciones_clientes'])) {
                  echo htmlspecialchars((string)$_SESSION['oldObservaciones_clientes']);
                  unset($_SESSION['oldObservaciones_clientes']);
              }?></textarea>
          </div>
        </div>
      </fieldset>

      <div class="acciones-formulario">
        <a class="btn-secundario" href="clientes.php">Cancelar</a>
        <button type="submit" class="btn">Guardar cliente</button>
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