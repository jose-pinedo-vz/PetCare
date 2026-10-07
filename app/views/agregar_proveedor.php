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
    <meta name="viewport" content="width=device-widht, initial-scale=1.0">
    <title>Veterinaria PetCare - Agregar proveedor</title>
    <link rel="stylesheet" href="css/estilos_base.css">
</head>
<body>
    <header>
        <div class='marca'>
            <img src="img/logo3.svg" alt="Verterinaria PetCare">
        </div>

        <div class="marca">
            <span>Empleado: Nombre del empleado</span>
            <a href="#">Cerrar sesión</a>
        </div>
    </header>

    <nav>
        <ul>
            <li><a href="mascotas.php">Mascotas</a></li>
            <li><a href="citas.php">Citas</a></li>
            <li><a href="clientes.php">Clientes</a></li>
            <li><a href="empleados.php">Empleados</a></li>
            <li><a href="proveedores.php" class="activo">Proveedores</a></li>


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

        <h1>Agregar proveedor</h1>

        <?php if (!empty($errores)): ?>
            <div class="alerta-errores">
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

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">

                    <div>
                        <label class="label-campo" for="nombre">
                            Razon social *:
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
 

            </fieldset>

             <fieldset>

                <legend>Datos de contacto</legend>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">

                    <div>
                        <label class="label-campo" for="telefono">
                            Teléfono *:
                        </label>

                        <input
                          type="tel"
                          class="campo"
                          id="telefono"
                          name="telefono"
                          maxlenght="15"
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
                    
            </fieldset>

            <fieldset>

                <legend>Datos de ubicación</legend>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">

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

                    <div>
                        <label class="label-campo" for="codigo_postal">
                             Código Postal:
                        </label>

                        <input
                          type="text"
                          class="campo"
                          id="codigo_postal"
                          name="codigo_postal"
                          maxlength="10"
                          value="<?= htmlspecialchars($old['codigo_postal'] ?? '') ?>"
                          required>
                    </div>
                </fieldset>    

                <fieldset>

                <legend>Información comercial</legend>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">

                    <div>
                        <label class="label-campo" for="condicion_pago">
                            Condición de pago:
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
                            Tiempo de entrega *:
                        </label>

                        <input
                          type="text"
                          class="campo"
                          id="tiempo_entrega"
                          name="tiempo_entrega"
                          maxlength="60"
                          value="<?= htmlspecialchars($old['tiempo_entrega'] ?? '') ?>">
                    </div>

                    <div tyle="grid-column: 1 / -1;">
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
        <button type="submit">Guardar</button>
        <a class="btn" href="proveedores.php">Cancelar</a>

        </form>

    </main>

  <footer>
    <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
  </footer>

</body>
</html>