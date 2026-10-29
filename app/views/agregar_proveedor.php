<?php
    // Iniciar la sesión al principio
    session_start();

    // Obtener errores y datos enviados previamente (si existen)
    $errores = $_SESSION['errores'] ?? [];
    $old     = $_SESSION['old'] ?? [];

    // Limpiar la sesión para que las alertas no reaparezcan al recargar
    unset($_SESSION['errores']);
    unset($_SESSION['old']);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veterinaria PetCare - Agregar proveedor</title>
    <script src="js/codigo_Postal.js?v=2"></script>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <link rel="stylesheet" href="css/estilos_base_v2.css">
</head>

<body>

  <div class="layout">

  <?php $paginaActiva = 'proveedores'; include __DIR__ . '/layout/sidebar.php'; ?>

    <main>
      <div class="migaja"><a href="proveedores.php" class="link-tenue">Gestión de proveedores</a> / Agregar</div>
      <div class="encabezado-pagina">
        <h1>Agregar proveedor</h1>
      </div>

      <div class="tarjeta">

        <?php if (!empty($errores)): ?>
            <div class="msg-error">
              <h4>Por favor corrige los siguientes errores:</h4>
              <ul>
                <?php foreach ($errores as $error): ?>
                  <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
        <?php endif; ?>

        <form action="guardar_proveedor.php" method="POST">

            <fieldset>

                <legend>Datos generales</legend>

                <div class="grid-formulario">

                    <div>
                        <label class="label-campo" for="razon_social">
                            Razón social *:
                        </label>

                        <input
                          type="text"
                          class="campo"
                          id="razon_social"
                          name="razon_social"
                          maxlength="100"
                          value="<?= htmlspecialchars($old['razon_social'] ?? '') ?>"
                          required>
                    </div>

                    <div>
                        <label class="label-campo" for="nombre_comercial">
                            Nombre Comercial *:
                        </label>

                        <input
                          type="text"
                          class="campo"
                          id="nombre_comercial"
                          name="nombre_comercial"
                          maxlength="100"
                          value="<?= htmlspecialchars($old['nombre_comercial'] ?? '') ?>"
                          required>

                    </div>

                    <div>
                        <label class="label-campo" for="rfc">
                            RFC *:
                        </label>

                        <input
                          type="text"
                          class="campo"
                          id="rfc"
                          name="rfc"
                          maxlength="13"
                            value="<?= htmlspecialchars($old['rfc'] ?? '') ?>"
                          required>

                    </div>
                </div>
            </fieldset>

             <fieldset>

                <legend>Datos de contacto</legend>

                <div class="grid-formulario">

                    <div>
                        <label class="label-campo" for="telefono">
                            Teléfono *:
                        </label>

                        <input
                          type="tel"
                          class="campo"
                          id="telefono"
                          name="telefono"
                          maxlength="15"
                          value="<?= htmlspecialchars($old['telefono'] ?? '') ?>"
                          required>
                    </div>

                    
 
                    <div>
                        <label class="label-campo" for="correo">
                            Correo Electrónico:
                        </label>

                        <input
                          type="email"
                          class="campo"
                          id="correo"
                          name="correo"
                          maxlength="100"
                          value="<?= htmlspecialchars($old['correo'] ?? '') ?>"
                          required>
                    </div>
                </div>
            </fieldset>

            <fieldset>

                <legend>Datos de ubicación</legend>

                <div class="grid-formulario">

                    <div>
                        <label class="label-campo" for="codigo_postal">Código postal *:</label>
                        <input type="text" class="campo" id="codigo_postal" name="codigo_postal" maxlength="10" required
            
                        value="<?php echo htmlspecialchars((string)($old['codigo_postal'] ?? '')); unset($old['CodigoPostal_clientes']); ?>">
                    </div>

                    <div>
                        <label class="oculto" for="codigo_postal">Validar codigo postal</label><br>
                        <button type="button" onclick="codigoPostalProveedores(event)" class="btn" id="validar_codigo_postal">Validar codigo postal</button>
                    </div>

                    <div>
                        <label class="label-campo" for="domicilio">
                            Domicilio:
                        </label>

                        <input
                          type="text"
                          class="campo"
                          id="domicilio"
                          name="domicilio"
                          maxlength="100"
                          value="<?= htmlspecialchars($old['domicilio'] ?? '') ?>"
                          required>
                    </div>

                    <div>
                        <label class="label-campo" for="ciudad">
                            Ciudad:
                        </label>

                        <input
                          type="text"
                          class="campo"
                          id="ciudad"
                          name="ciudad"
                          maxlength="60"
                          value="<?= htmlspecialchars($old['ciudad'] ?? '') ?>"
                          required>

                    </div>

                    <div>
                        <label class="label-campo" for="estado">
                            Estado:
                        </label>

                        <input
                          type="text"
                          class="campo"
                          id="estado"
                          name="estado"
                          maxlength="60"
                          value="<?= htmlspecialchars($old['estado'] ?? '') ?>"
                          required>

                    </div>
                </div>
            </fieldset>    

                <fieldset>

                <legend>Información comercial</legend>

                <div class="grid-formulario">

                    <div>
                        <label class="label-campo" for="condicion_pago">
                            Condición de pago *:
                        </label>

                        <input
                          type="text"
                          class="campo"
                          id="condicion_pago"
                          name="condicion_pago"
                          maxlength="60"
                          value="<?= htmlspecialchars($old['condicion_pago'] ?? '') ?>">
                    </div>

                    <div>
                        <label class="label-campo" for="tiempo_entrega">
                            Tiempo de entrega (Días) *:
                        </label>

                        <input
                          type="text"
                          class="campo"
                          id="tiempo_entrega"
                          name="tiempo_entrega"
                          maxlength="60"
                          value="<?= htmlspecialchars($old['tiempo_entrega'] ?? '') ?>">
                    </div>

                    <div class="campo-completo">
                        <label class="label-campo" for="observaciones">
                            Observaciones:
                        </label>

                        <textarea
                          class="campo"
                          id="observaciones"
                          name="observaciones"
                          rows="3"
                          maxlength="255" ><?= htmlspecialchars($old['observaciones'] ?? '') ?></textarea>
                    </div>

                </div>

            </fieldset>
        <div class="acciones-formulario">
          <a class="btn btn-secundario btn-chico" href="proveedores.php">Cancelar</a>
          <button type="submit" class="btn btn-chico">Guardar</button>
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