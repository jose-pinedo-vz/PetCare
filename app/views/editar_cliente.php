<?php
require_once __DIR__ . '/../models/Cliente.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$cliente = $id > 0 ? Cliente::obtenerPorId($id) : null;

if (!$cliente) {
    header("Location: clientes.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veterinaria PetCare - Editar cliente</title>
  <link rel="stylesheet" href="css/estilos_base_v2.css">
</head>
<body>

  <div class="layout">

  <?php $paginaActiva = 'clientes'; include __DIR__ . '/layout/sidebar.php'; ?>


  <main>
      <div class="migaja"><a href="clientes.php" class="link-tenue">Gestión de Clientes</a> / Editar</div>
      <div class="encabezado-pagina">
        <h1>Editar cliente (#<?php echo htmlspecialchars((string)$cliente['id_cliente']); ?>)</h1>
      </div>

      <div class="tarjeta">
    

    <form action="../controllers/guardar_cliente.php" method="POST">
      <input type="hidden" name="id_cliente" value="<?php echo htmlspecialchars((string)$cliente['id_cliente']); ?>">

      <fieldset>
        <legend> Datos personales</legend>
        <div class="grid-formulario">
          <div>
            <label class="label-campo" for="nombre">Nombre:</label>
            <input type="text" class="campo" id="nombre" name="nombre" maxlength="50" required 
              value="<?php echo htmlspecialchars($cliente['nombre']); ?>">
          </div>

          <div>
            <label class="label-campo" for="apellido">Apellido:</label>
            <input type="text" class="campo" id="apellido" name="apellido" maxlength="100" required 
              value="<?php echo htmlspecialchars($cliente['apellido']); ?>">
          </div>

          <div>
            <label class="label-campo" for="fecha_nacimiento">Fecha de nacimiento:</label>
            <input type="date" class="campo" id="fecha_nacimiento" name="fecha_nacimiento" 
              value="<?php echo htmlspecialchars($cliente['fecha_nacimiento'] ?? ''); ?>">
          </div>

          <div>
            <label class="label-campo" for="sexo">Sexo:</label>
            <select class="campo" id="sexo" name="sexo">
              <option value="">Selecciona una opción...</option>
              <option value="Femenino" <?php echo ($cliente['sexo'] === 'Femenino') ? 'selected' : ''; ?>>Femenino</option>
              <option value="Masculino" <?php echo ($cliente['sexo'] === 'Masculino') ? 'selected' : ''; ?>>Masculino</option>
              <option value="Otro" <?php echo ($cliente['sexo'] === 'Otro') ? 'selected' : ''; ?>>Otro</option>
            </select>
          </div>

          <div>
            <label class="label-campo" for="rfc">RFC:</label>
            <input type="text" class="campo" id="rfc" name="rfc" maxlength="13" 
              value="<?php echo htmlspecialchars($cliente['rfc'] ?? ''); ?>">
          </div>
        </div>
      </fieldset>

      <fieldset>
        <legend> Datos de contacto</legend>
        <div class="grid-formulario">
          <div>
            <label class="label-campo" for="telefono">Teléfono:</label>
            <input type="tel" class="campo" id="telefono" name="telefono" maxlength="15" required 
              value="<?php echo htmlspecialchars($cliente['telefono']); ?>">
          </div>

          <div>
            <label class="label-campo" for="telefono_alternativo">Teléfono alternativo:</label>
            <input type="tel" class="campo" id="telefono_alternativo" name="telefono_alternativo" maxlength="15" 
              value="<?php echo htmlspecialchars($cliente['telefono_alternativo'] ?? ''); ?>">
          </div>

          <div class="campo-completo">
            <label class="label-campo" for="correo">Correo electrónico:</label>
            <input type="email" class="campo" id="correo" name="correo" maxlength="100" required 
              value="<?php echo htmlspecialchars($cliente['correo']); ?>">
          </div>
        </div>
      </fieldset>

      <fieldset>
        <legend> Dirección / Domicilio</legend>
        <div class="grid-formulario">
          <div>
            <label class="label-campo" for="calle">Calle:</label>
            <input type="text" class="campo" id="calle" name="calle" maxlength="100" required 
              value="<?php echo htmlspecialchars($cliente['calle']); ?>">
          </div>

          <div>
            <label class="label-campo" for="numero_exterior">Número exterior:</label>
            <input type="text" class="campo" id="numero_exterior" name="numero_exterior" maxlength="15" required 
              value="<?php echo htmlspecialchars($cliente['numero_exterior']); ?>">
          </div>

          <div>
            <label class="label-campo" for="numero_interior">Número interior:</label>
            <input type="text" class="campo" id="numero_interior" name="numero_interior" maxlength="15" 
              value="<?php echo htmlspecialchars($cliente['numero_interior'] ?? ''); ?>">
          </div>

          <div>
            <label class="label-campo" for="colonia">Colonia:</label>
            <input type="text" class="campo" id="colonia" name="colonia" maxlength="60" required 
              value="<?php echo htmlspecialchars($cliente['colonia']); ?>">
          </div>

          <div>
            <label class="label-campo" for="codigo_postal">Código postal:</label>
            <input type="text" class="campo" id="codigo_postal" name="codigo_postal" maxlength="10" required 
              value="<?php echo htmlspecialchars($cliente['codigo_postal']); ?>">
          </div>

          <div>
            <label class="label-campo" for="ciudad">Ciudad:</label>
            <input type="text" class="campo" id="ciudad" name="ciudad" maxlength="60" required 
              value="<?php echo htmlspecialchars($cliente['ciudad']); ?>">
          </div>

          <div>
            <label class="label-campo" for="estado">Estado:</label>
            <input type="text" class="campo" id="estado" name="estado" maxlength="60" required 
              value="<?php echo htmlspecialchars($cliente['estado']); ?>">
          </div>
        </div>
      </fieldset>

      <fieldset>
        <legend> Contactos de Emergencia</legend>
        <div class="grid-formulario">
          <div>
            <label class="label-campo" for="contacto_emergencia">Nombre contacto de emergencia:</label>
            <input type="text" class="campo" id="contacto_emergencia" name="contacto_emergencia" maxlength="100" required 
              value="<?php echo htmlspecialchars($cliente['contacto_emergencia']); ?>">
          </div>

          <div>
            <label class="label-campo" for="telefono_emergencia">Teléfono de emergencia:</label>
            <input type="tel" class="campo" id="telefono_emergencia" name="telefono_emergencia" maxlength="15" required 
              value="<?php echo htmlspecialchars($cliente['telefono_emergencia']); ?>">
          </div>

          <div class="campo-completo">
            <label class="label-campo" for="observaciones">Observaciones:</label>
            <textarea class="campo" id="observaciones" name="observaciones" rows="2"><?php echo htmlspecialchars($cliente['observaciones'] ?? ''); ?></textarea>
          </div>
        </div>
      </fieldset>

      <div class="acciones-formulario">
        <a class="btn-secundario" href="clientes.php">Cancelar</a>
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