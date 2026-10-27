<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veterinaria PetCare - Agregar proveedor</title>
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
                          required>
                    </div>
                </div>
            </fieldset>

            <fieldset>

                <legend>Datos de ubicación</legend>

                <div class="grid-formulario">

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
                          required>
                    </div>
                </div>
            </fieldset>    

                <fieldset>

                <legend>Información comercial</legend>

                <div class="grid-formulario">

                    <div>
                        <label class="label-campo" for="condicion_pago">
                            Condición de pago:
                        </label>

                        <input
                          type="text"
                          class="campo"
                          id="condicion_pago"
                          name="condicion_pago"
                          maxlength="60">
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
                          maxlength="60">
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
                          maxlength="255"></textarea>
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
