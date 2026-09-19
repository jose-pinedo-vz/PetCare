<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veterinaria PetCare - Agregar cliente</title>
  <link rel="stylesheet" href="estilos_base.css">
</head>
<body>

  <header>
    <div class="marca">
      <img src="logo.svg" alt="Veterinaria PetCare">
    </div>

    <div class="sesion">
      <span>Empleado: Nombre del Empleado</span>
      <a href="#">Cerrar sesión</a>
    </div>
  </header>

  <nav>
    <ul>
      <li><a href="mascotas.php" class="activo">Mascotas</a></li>

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
    <h1>Registrar cliente</h1>

    <form action="guardar_mascota.php" method="POST" enctype="multipart/form-data">

<fieldset>
  <legend>👤 Datos personales</legend>

  <!-- ==========================================
       SECCIÓN: DATOS PERSONALES 
  =========================================== -->
  <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">

    <div>
      <label class="label-campo" for="nombre">Nombre:</label>
      <input type="text" class="campo" id="nombre" name="nombre" maxlength="50" required>
    </div>

    <div>
      <label class="label-campo" for="apellido">Apellido:</label>
      <input type="text" class="campo" id="apellido" name="apellido" maxlength="100" required>
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
      <input type="text" class="campo" id="rfc" name="rfc" maxlength="13">
    </div>

  </div>
</fieldset>

<fieldset>
  <legend>📞 Datos de contacto</legend>

  <!-- ==========================================
       SECCIÓN: Datos de contacto
  =========================================== -->
  <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">

    <div>
      <label class="label-campo" for="nombre">Telefono:</label>
      <input type="text" class="campo" id="telefono" name="telefono" maxlength="50" required>
    </div>

    <div>
      <label class="label-campo" for="apellido">Telefono alternativo:</label>
      <input type="text" class="campo" id="telefono_alternativo" name="telefono_alternativo" maxlength="100" required>
    </div>

    <div>
      <label class="label-campo" for="apellido">Correo electronico:</label>
      <input type="text" class="campo" id="correo" name="correo" maxlength="100" required>
    </div>

    

  </div>
</fieldset>

<fieldset>
  <legend>🏠 Dirección / Domicilio</legend>

  <!-- ==========================================
       SECCIÓN: Datos de direccion
  =========================================== -->
  <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">

    <div>
      <label class="label-campo" for="nombre">Calle:</label>
      <input type="text" class="campo" id="calle" name="calle" maxlength="50" required>
    </div>

    <div>
      <label class="label-campo" for="apellido">Numero exterior:</label>
      <input type="text" class="campo" id="numero_exterior" name="numero_exterior" maxlength="100" required>
    </div>

    <div>
      <label class="label-campo" for="apellido">Numero interior:</label>
      <input type="text" class="campo" id="numero_interior" name="numero_interior" maxlength="100" required>
    </div>

    <div>
      <label class="label-campo" for="apellido">Colonia:</label>
      <input type="text" class="campo" id="colonia" name="colonia" maxlength="100" required>
    </div>

    <div>
      <label class="label-campo" for="apellido">Codigo postal:</label>
      <input type="text" class="campo" id="codigo_postal" name="codigo_postal" maxlength="100" required>
    </div>

    <div>
      <label class="label-campo" for="apellido">Ciudad:</label>
      <input type="text" class="campo" id="ciudad" name="ciudad" maxlength="100" required>
    </div>

    <div>
      <label class="label-campo" for="apellido">Estado:</label>
      <input type="text" class="campo" id="estado" name="estado" maxlength="100" required>
    </div>
  </div>
</fieldset>

<fieldset>
  <legend>🚨 Contactos de Emergencia</legend>

  <!-- ==========================================
       SECCIÓN: Datos de contacto
  =========================================== -->
  <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">

    <div>
      <label class="label-campo" for="nombre">Contacto de emergencia:</label>
      <input type="text" class="campo" id="contacto_emergencia" name="contacto_emergencia" maxlength="50" required>
    </div>

    <div>
      <label class="label-campo" for="apellido">Telefono de emergencia:</label>
      <input type="text" class="campo" id="telefono_emergencia" name="telefono_emergencia" maxlength="100" required>
    </div>
  </div>
</fieldset>

      <button type="submit">Guardar</button>
      <a class="btn" href="clientes.php">Cancelar</a>
    </form>
  </main>

  <footer>
    <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
  </footer>

</body>
</html>
