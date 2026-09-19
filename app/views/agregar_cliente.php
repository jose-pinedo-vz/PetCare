<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veterinaria PetCare - Registrar Cliente</title>
  <link rel="stylesheet" href="css/estilos_base.css">
</head>
<body>

  <header>
    <div class="marca">
      <img src="img/logo.svg" alt="Veterinaria PetCare">
    </div>

    <div class="sesion">
      <span>Empleado: Nombre del Empleado</span>
      <a href="#">Cerrar sesión</a>
    </div>
  </header>

  <nav>
    <ul>
      <li><a href="mascotas.php">Mascotas</a></li>
      <li><a href="clientes.php">Clientes</a></li>
      <li><a href="citas.php" class="activo">Citas</a></li>

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
    <h1>Registrar Cliente</h1>

    <form action="../controllers/guardar_cliente.php" method="POST">

      <fieldset>
        <legend>👤 Datos personales</legend>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
          <div>
            <label class="label-campo" for="nombre">Nombre *:</label>
            <input type="text" class="campo" id="nombre" name="nombre" maxlength="50" required placeholder="Ej. Juan Carlos">
          </div>

          <div>
            <label class="label-campo" for="apellido">Apellido *:</label>
            <input type="text" class="campo" id="apellido" name="apellido" maxlength="100" required placeholder="Ej. Pérez García">
          </div>

          <div>
            <label class="label-campo" for="fecha_nacimiento">Fecha de nacimiento:</label>
            <input type="date" class="campo" id="fecha_nacimiento" name="fecha_nacimiento">
          </div>

          <div>
            <label class="label-campo" for="sexo">Sexo:</label>
            <select class="campo" id="sexo" name="sexo">
              <option value="">Selecciona una opción...</option>
              <option value="Femenino">Femenino</option>
              <option value="Masculino">Masculino</option>
              <option value="Otro">Otro</option>
            </select>
          </div>

          <div>
            <label class="label-campo" for="rfc">RFC:</label>
            <input type="text" class="campo" id="rfc" name="rfc" maxlength="13" placeholder="Ej. PEGJ900101XXX">
          </div>
        </div>
      </fieldset>

      <fieldset>
        <legend>📞 Datos de contacto</legend>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
          <div>
            <label class="label-campo" for="telefono">Teléfono *:</label>
            <input type="tel" class="campo" id="telefono" name="telefono" maxlength="15" required placeholder="Ej. 4921234567">
          </div>

          <div>
            <label class="label-campo" for="telefono_alternativo">Teléfono alternativo:</label>
            <input type="tel" class="campo" id="telefono_alternativo" name="telefono_alternativo" maxlength="15" placeholder="Opcional">
          </div>

          <div style="grid-column: 1 / -1;">
            <label class="label-campo" for="correo">Correo electrónico *:</label>
            <input type="email" class="campo" id="correo" name="correo" maxlength="100" required placeholder="correo@ejemplo.com">
          </div>
        </div>
      </fieldset>

      <fieldset>
        <legend>🏠 Dirección / Domicilio</legend>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
          <div>
            <label class="label-campo" for="calle">Calle *:</label>
            <input type="text" class="campo" id="calle" name="calle" maxlength="100" required>
          </div>

          <div>
            <label class="label-campo" for="numero_exterior">Número exterior *:</label>
            <input type="text" class="campo" id="numero_exterior" name="numero_exterior" maxlength="15" required>
          </div>

          <div>
            <label class="label-campo" for="numero_interior">Número interior:</label>
            <input type="text" class="campo" id="numero_interior" name="numero_interior" maxlength="15" placeholder="Opcional">
          </div>

          <div>
            <label class="label-campo" for="colonia">Colonia *:</label>
            <input type="text" class="campo" id="colonia" name="colonia" maxlength="60" required>
          </div>

          <div>
            <label class="label-campo" for="codigo_postal">Código postal *:</label>
            <input type="text" class="campo" id="codigo_postal" name="codigo_postal" maxlength="10" required>
          </div>

          <div>
            <label class="label-campo" for="ciudad">Ciudad *:</label>
            <input type="text" class="campo" id="ciudad" name="ciudad" maxlength="60" required>
          </div>

          <div>
            <label class="label-campo" for="estado">Estado *:</label>
            <input type="text" class="campo" id="estado" name="estado" maxlength="60" required>
          </div>
        </div>
      </fieldset>

      <fieldset>
        <legend>🚨 Contactos de Emergencia</legend>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
          <div>
            <label class="label-campo" for="contacto_emergencia">Nombre contacto de emergencia *:</label>
            <input type="text" class="campo" id="contacto_emergencia" name="contacto_emergencia" maxlength="100" required>
          </div>

          <div>
            <label class="label-campo" for="telefono_emergencia">Teléfono de emergencia *:</label>
            <input type="tel" class="campo" id="telefono_emergencia" name="telefono_emergencia" maxlength="15" required>
          </div>

          <div style="grid-column: 1 / -1;">
            <label class="label-campo" for="observaciones">Observaciones:</label>
            <textarea class="campo" id="observaciones" name="observaciones" rows="2" placeholder="Observaciones o notas adicionales del cliente"></textarea>
          </div>
        </div>
      </fieldset>

      <div style="margin-top: 20px;">
        <button type="submit" class="btn">Guardar Cliente</button>
        <a class="btn" href="clientes.php" style="background: #757575; margin-left: 8px;">Cancelar</a>
      </div>
    </form>
  </main>

  <footer>
    <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
  </footer>

</body>
</html>