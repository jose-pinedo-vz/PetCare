<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veterinaria PetCare - Nueva cita de estética</title>
  <link rel="stylesheet" href="css/estilos_base_v2.css">
  <style>
    /* Pestañas de servicio (no existen en el CSS base). Si se prefiere, mover a v2/modulos.css */
    .servicio-pestanas { display: flex; gap: 8px; flex-wrap: wrap; }
    .servicio-pestanas input { position: absolute; opacity: 0; pointer-events: none; }
    .servicio-pestanas label {
      padding: 10px 18px; border: 1px solid var(--border-light); border-radius: 6px;
      background: #fff; color: var(--primary-dark); font-weight: bold; font-size: 14px; cursor: pointer;
    }
    .servicio-pestanas input:checked + label { background: var(--primary-bg); color: #fff; border-color: var(--primary-bg); }
    .servicio-pestanas input:focus-visible + label { outline: 2px solid var(--primary-dark); }
    .cita-columnas { display: flex; flex-wrap: wrap; gap: 20px; align-items: flex-start; }
    .cita-principal { flex: 2 1 560px; min-width: 0; }
    /* Pestañas sin JavaScript: el radio marcado decide qué detalle se ve */
    #det-pelo, #det-unas { display: none; }
    form:has(#srv-pelo:checked) #det-bano, form:has(#srv-unas:checked) #det-bano { display: none; }
    form:has(#srv-pelo:checked) #det-pelo { display: block; }
    form:has(#srv-unas:checked) #det-unas { display: block; }
    .cita-resumen { flex: 1 1 280px; }
    .cita-resumen div.fila { display: flex; justify-content: space-between; gap: 12px; padding: 10px 0; border-bottom: 1px solid var(--border-light); font-size: 14px; }
    .cita-resumen div.fila:last-of-type { border-bottom: 0; font-size: 18px; font-weight: bold; }
  </style>
</head>

<body>
  <div class="layout">
    <?php $paginaActiva = 'servicios';
    include __DIR__ . '/layout/sidebar.php'; ?>

    <main>
      <div class="migaja"><a href="servicios.php" class="link-tenue">Gestión de servicios</a> / Nueva cita de estética</div>
      <div class="encabezado-pagina">
        <h1>Nueva cita de estética</h1>
      </div>

      <div class="cita-columnas">
        <div class="tarjeta cita-principal">
          
          <form method="POST" enctype="multipart/form-data">

            <fieldset>
              <legend>Datos de la cita</legend>
              <div class="grid-formulario">
                <div>
                  <label class="label-campo" for="id_cliente">Cliente *:</label>
                  <select class="campo" id="id_cliente" name="id_cliente" required>
                    <option value="">Seleccionar...</option>
                    <option value="1">María López</option>
                    <option value="2">Carlos Ramírez</option>
                  </select>
                </div>
                <div>
                  <label class="label-campo" for="id_mascota">Mascota *:</label>
                  <select class="campo" id="id_mascota" name="id_mascota" required>
                    <option value="">Seleccionar...</option>
                    <option value="1">Luna (perro)</option>
                    <option value="2">Pelusa (gato)</option>
                  </select>
                </div>

                <div class="campo-completo">
                  <span class="label-campo">Servicio *:</span>
                  <div class="servicio-pestanas" role="radiogroup" aria-label="Servicio">
                    <input type="radio" name="servicio" id="srv-bano" value="bano" checked><label for="srv-bano">Baño</label>
                    <input type="radio" name="servicio" id="srv-pelo" value="corte_pelo"><label for="srv-pelo">Corte de pelo</label>
                    <input type="radio" name="servicio" id="srv-unas" value="corte_unas"><label for="srv-unas">Corte de uñas</label>
                  </div>
                </div>

                <div>
                  <label class="label-campo" for="id_empleados">Estilista *:</label>
                  <select class="campo" id="id_empleados" name="id_empleados" required>
                    <option value="1">Ana Torres</option>
                    <option value="2">Luis Medina</option>
                  </select>
                </div>
                <div>
                  <label class="label-campo" for="duracion">Duración estimada:</label>
                  <input type="text" class="campo" id="duracion" value="" readonly>
                </div>
                <div>
                  <label class="label-campo" for="fecha">Fecha *:</label>
                  <input type="date" class="campo" id="fecha" name="fecha" required>
                </div>
                <div>
                  <label class="label-campo" for="hora">Hora *:</label>
                  <input type="time" class="campo" id="hora" name="hora" required>
                </div>
                <div class="campo-completo">
                  <label class="label-campo" for="observacion">Observación:</label>
                  <textarea class="campo" id="observacion" name="observacion" rows="2" placeholder="Ej. Piel sensible"></textarea>
                </div>
              </div>
              <!-- Back: quitar la clase "oculto" si el estilista ya tiene cita en esa fecha y hora -->
              <div class="msg-error oculto" role="status" style="margin-top:14px;padding:12px 14px;border-radius:6px;">
                El estilista ya tiene una cita en esa fecha y hora. Elige otra hora o estilista.
              </div>
            </fieldset>

            <fieldset id="det-bano">
              <legend>Detalle del baño</legend>
              <div class="grid-formulario">
                <div>
                  <label class="label-campo" for="tipo_bano">Tipo de baño *:</label>
                  <select class="campo" id="tipo_bano" name="tipo_bano">
                    <option>Normal</option>
                    <option>Medicado</option>
                    <option>Antipulgas</option>
                  </select>
                </div>
                <div>
                  <label class="label-campo" for="tipo_shampoo">Tipo de shampoo:</label>
                  <input type="text" class="campo" id="tipo_shampoo" name="tipo_shampoo">
                </div>
                <div>
                  <label class="label-campo" for="tratamiento_adicional">Tratamiento adicional:</label>
                  <input type="text" class="campo" id="tratamiento_adicional" name="tratamiento_adicional">
                </div>
                <div>
                  <label class="label-campo" for="cepillado_secado">Cepillado / secado:</label>
                  <input type="text" class="campo" id="cepillado_secado" name="cepillado_secado">
                </div>
                <div class="campo-completo">
                  <label class="label-campo" for="condiciones_recibida">Condiciones al recibir a la mascota:</label>
                  <textarea class="campo" id="condiciones_recibida" name="condiciones_recibida" rows="2" placeholder="Se captura al recibirla; también se llenan al entregar"></textarea>
                </div>
              </div>
            </fieldset>

            <fieldset id="det-pelo">
              <legend>Detalle del corte de pelo</legend>
              <div class="grid-formulario">
                <div>
                  <label class="label-campo" for="tipo_corte">Tipo de corte *:</label>
                  <input type="text" class="campo" id="tipo_corte" name="tipo_corte" placeholder="Ej. Tipo cachorro">
                </div>
                <div>
                  <label class="label-campo" for="longitud_cm">Longitud (cm):</label>
                  <input type="number" class="campo" id="longitud_cm" name="longitud_cm" step="0.1" min="0">
                </div>
                <div>
                  <label class="label-campo" for="corte_higienico">Corte higiénico:</label>
                  <select class="campo" id="corte_higienico" name="corte_higienico">
                    <option value="0">No</option>
                    <option value="1">Sí</option>
                  </select>
                </div>
                <div>
                  <label class="label-campo" for="corte_cara_patas">Corte de cara / patas:</label>
                  <input type="text" class="campo" id="corte_cara_patas" name="corte_cara_patas">
                </div>
                <div>
                  <label class="label-campo" for="deslanado">Deslanado:</label>
                  <input type="text" class="campo" id="deslanado" name="deslanado">
                </div>
                <div>
                  <label class="label-campo" for="accesorios">Accesorios (cantidad):</label>
                  <input type="number" class="campo" id="accesorios" name="accesorios" min="0" value="0">
                </div>
                <div class="campo-completo">
                  <label class="label-campo" for="foto_referencia">Fotografía de referencia:</label>
                  <input type="file" class="campo" id="foto_referencia" name="foto_referencia" accept="image/jpeg,image/png">
                </div>
              </div>
            </fieldset>

            <fieldset id="det-unas">
              <legend>Detalle del corte de uñas</legend>
              <div class="grid-formulario">
                <div>
                  <label class="label-campo" for="tipo_servicio_unas">Tipo de servicio *:</label>
                  <select class="campo" id="tipo_servicio_unas" name="tipo_servicio_unas">
                    <option>Corte</option>
                    <option>Corte y limado</option>
                  </select>
                </div>
                <div>
                  <label class="label-campo" for="comportamiento">Comportamiento de la mascota:</label>
                  <input type="text" class="campo" id="comportamiento" name="comportamiento" placeholder="Ej. Tranquilo, nervioso">
                </div>
              </div>
            </fieldset>

            <div class="acciones-formulario">
              <a class="btn-secundario" href="servicios.php">Cancelar</a>
              <button type="submit">Guardar cita</button>
            </div>
          </form>
        </div>

        <aside class="tarjeta cita-resumen" aria-label="Resumen de la cita">
          <h2>Resumen</h2>
          <div class="fila"><span>Servicio</span><strong>—</strong></div>
          <div class="fila"><span>Duración</span><strong>—</strong></div>
          <div class="fila"><span>Precio</span><strong>—</strong></div>
          <div class="fila"><span>Anticipo (10%)</span><span>—</span></div>
          <span class="nota-campo" style="margin:12px 0 14px;">El anticipo asegura la cita y se cobra por separado de la pensión del hotel.</span>
          <!-- enlazar con la pantalla de cobro/pago cuando exista -->
          <a class="btn" style="display:block;text-align:center;" href="pagos.php">Cobrar anticipo</a>
        </aside>
      </div>
    </main>
  </div>

  <footer>
    <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
  </footer>

</body>

</html>
