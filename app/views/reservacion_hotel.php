<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veterinaria PetCare - Nueva reservación de hotel</title>
  <link rel="stylesheet" href="css/estilos_base_v2.css">
</head>

<body>
  <div class="layout">
    <?php $paginaActiva = 'hotel';
    include __DIR__ . '/layout/sidebar.php'; ?>

    <main>
      <div class="migaja"><a href="hotel.php" class="link-tenue">Gestión de hotel</a> / Nueva reservación</div>
      <div class="encabezado-pagina">
        <h1>Nueva reservación de hotel</h1>
      </div>

      <div class="tarjeta">
        
        <form action="guardar_hotel.php" method="POST" enctype="multipart/form-data">

          <fieldset>
            <legend>Datos de la reservación</legend>
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
              <div>
                <label class="label-campo" for="id_jaula">Jaula *:</label>
                <select class="campo" id="id_jaula" name="id_jaula" required>
                  <option value="">Seleccionar...</option>
                  <option value="2">J-02 · Chica · Disponible</option>
                  <option value="1">J-01 · Grande · Disponible</option>
                </select>
              </div>
              <div>
                <label class="label-campo" for="fecha_entrada">Fecha de entrada *:</label>
                <input type="date" class="campo" id="fecha_entrada" name="fecha_entrada" required>
              </div>
              <div>
                <label class="label-campo" for="fecha_salida">Fecha de salida *:</label>
                <input type="date" class="campo" id="fecha_salida" name="fecha_salida" required>
              </div>
              <div>
                <label class="label-campo" for="noches">Noches:</label>
                <input type="text" class="campo" id="noches" value="" readonly>
              </div>
              <div>
                <label class="label-campo" for="precio_noche">Precio por noche *:</label>
                <input type="number" class="campo" id="precio_noche" name="precio_noche" step="0.01" min="0" value="180.00" required>
              </div>
              <div>
                <label class="label-campo" for="anticipo">Anticipo:</label>
                <input type="number" class="campo" id="anticipo" name="anticipo" step="0.01" min="0" value="0">
              </div>
              <div>
                <label class="label-campo" for="saldo">Saldo:</label>
                <input type="text" class="campo" id="saldo" value="" readonly>
              </div>
              <div class="campo-completo">
                <label class="label-campo" for="servicios_adicionales">Servicios adicionales:</label>
                <textarea class="campo" id="servicios_adicionales" name="servicios_adicionales" rows="2" placeholder="Ej. Paseo diario, juguete propio"></textarea>
    
              </div>
            </div>
          </fieldset>

          <fieldset>
            <legend>Validación de vacunas</legend>
            <!-- backend: llenar con las vacunas de la mascota (HU05) y comparar contra fecha_salida -->
            <div class="tabla-envoltura">
              <table>
                <thead>
                  <tr>
                    <th>Vacuna requerida</th>
                    <th>Vigente hasta</th>
                    <th>Resultado</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>Rabia</td>
                    <td>2027-03-10</td>
                    <td><span class="historial-estado pagada">Vigente</span></td>
                  </tr>
                  <tr>
                    <td>Parvovirus</td>
                    <td>2027-01-15</td>
                    <td><span class="historial-estado pagada">Vigente</span></td>
                  </tr>
                  <tr>
                    <td>Moquillo</td>
                    <td>2026-10-30</td>
                    <td><span class="historial-estado cancelada">Vence antes de la salida</span></td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Back: mostrar este aviso solo si alguna vacuna no está vigente -->
            <div class="msg-error" role="alert" style="margin-top:14px;padding:12px 14px;border-radius:6px;">
              No se puede confirmar la reservación: falta renovar la vacuna de Moquillo antes de la fecha de salida.
            </div>

            

            <div class="acciones-formulario" style="justify-content:flex-start;">
              <button type="button">Validar vacunas</button>
            </div>
          </fieldset>

          <fieldset>
            <legend>Datos de la estancia</legend>
            <div class="grid-formulario">
              <div>
                <label class="label-campo" for="alimentacion">Alimentación habitual:</label>
                <input type="text" class="campo" id="alimentacion" name="alimentacion" maxlength="100" placeholder="Ej. Croqueta adulto">
              </div>
              <div>
                <label class="label-campo" for="cantidad_alimento">Cantidad de alimento:</label>
                <input type="text" class="campo" id="cantidad_alimento" name="cantidad_alimento" maxlength="50" placeholder="Ej. 150 g">
              </div>
              <div>
                <label class="label-campo" for="horario_alimentacion">Horario de alimentación:</label>
                <input type="text" class="campo" id="horario_alimentacion" name="horario_alimentacion" maxlength="50" placeholder="Ej. 8:00 y 18:00">
              </div>
              <div>
                <label class="label-campo" for="medicamentos">Medicamentos:</label>
                <input type="text" class="campo" id="medicamentos" name="medicamentos" maxlength="100" placeholder="Ninguno">
              </div>
              <div>
                <label class="label-campo" for="horario_medicamentos">Horario de medicamentos:</label>
                <input type="text" class="campo" id="horario_medicamentos" name="horario_medicamentos" maxlength="50" placeholder="Obligatorio si hay medicamentos">
              </div>
              <div>
                <label class="label-campo" for="persona_recoge">Persona que recoge *:</label>
                <input type="text" class="campo" id="persona_recoge" name="persona_recoge" maxlength="60" required>
              </div>
              <div>
                <label class="label-campo" for="telefono_emergencia">Teléfono de emergencia *:</label>
                <input type="tel" class="campo" id="telefono_emergencia" name="telefono_emergencia" maxlength="15" required>
              </div>
              <div class="campo-completo">
                <label class="label-campo" for="necesidades_especiales">Necesidades especiales:</label>
                <textarea class="campo" id="necesidades_especiales" name="necesidades_especiales" rows="2"></textarea>
              </div>
              <div class="campo-completo">
                <label class="label-campo" for="observaciones">Observaciones:</label>
                <textarea class="campo" id="observaciones" name="observaciones" rows="2"></textarea>
              </div>
            </div>
          </fieldset>

          <div class="acciones-formulario">
            <a class="btn-secundario" href="hotel.php">Cancelar</a>
            <button type="submit">Guardar reservación</button>
          </div>
          <span class="nota-campo" style="text-align:right;">Solo se puede guardar cuando las vacunas están vigentes hasta la fecha de salida.</span>
        </form>
      </div>
    </main>
  </div>

  <footer>
    <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
  </footer>

</body>

</html>
