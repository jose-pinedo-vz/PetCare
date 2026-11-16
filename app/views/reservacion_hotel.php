<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

require_once __DIR__ . '/../models/Cliente.php';
$cliente = Cliente::obtenerTodos();

require_once __DIR__ . '/../models/Mascota.php';
$mascota = Mascota::obtenerTodos();

require_once __DIR__ . '/../models/Hotel.php';
$jaulas = Hotel::obtenerJaulas();

// Obtener errores y datos enviados previamente (si existen)
$errores = is_array($_SESSION['errores'] ?? null) ? $_SESSION['errores'] : [];
$old = is_array($_SESSION['old'] ?? null) ? $_SESSION['old'] : [];

// Limpiar la sesión para que las alertas no reaparezcan al recargar
unset($_SESSION['errores']);
unset($_SESSION['old']);

if (isset($jaulas[0]) && $jaulas[0] === 'Error BD') {
  $errores[] = 'No se pudieron cargar las jaulas: ' . ($jaulas[1] ?? 'error desconocido');
  $jaulas = [];
}

$clientesPorId = [];
foreach ($cliente as $registroCliente) {
  $clientesPorId[(string)$registroCliente['id_cliente']] = $registroCliente;
}

$mascotasPorId = [];
foreach ($mascota as $registroMascota) {
  $mascotasPorId[(string)$registroMascota['id_mascota']] = $registroMascota;
}

$jaulasPorId = [];
foreach ($jaulas as $registroJaula) {
  if (isset($registroJaula['id'])) {
    $jaulasPorId[(string)$registroJaula['id']] = $registroJaula;
  }
}

$clienteAnterior = (string)($old['id_cliente'] ?? '');
$mascotaAnterior = (string)($old['id_mascota'] ?? '');
$jaulaAnterior = (string)($old['id_jaula'] ?? '');
$clienteSeleccionado = $clientesPorId[$clienteAnterior] ?? null;
$mascotaSeleccionada = $mascotasPorId[$mascotaAnterior] ?? null;
$jaulaSeleccionada = $jaulasPorId[$jaulaAnterior] ?? null;

// Solo se restaura una mascota si pertenece al cliente recuperado de la session
if (
  !$clienteSeleccionado ||
  !$mascotaSeleccionada ||
  (string)$mascotaSeleccionada['id_cliente'] !== $clienteAnterior
) {
  $mascotaSeleccionada = null;
}

$valorCampo = static function (string $campo, string $porDefecto = '') use ($old): string {
  return htmlspecialchars((string)($old[$campo] ?? $porDefecto), ENT_QUOTES, 'UTF-8');
};
?>
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
          <?php if (!empty($errores)): ?>
            <div class="msg-error" role="alert">
              <h4>Por favor corrige los siguientes errores:</h4>
              <ul>
                <?php foreach ($errores as $error): ?>
                  <li><?= htmlspecialchars((string)$error, ENT_QUOTES, 'UTF-8') ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>

          <fieldset>
            <legend>Datos de la reservación</legend>
            <div class="grid-formulario">
              <div class="contenedor-buscador">
                <label class="label-campo" for="id_cliente">Cliente *:</label>
                <input
                  type="text"
                  class="campo"
                  id="id_cliente"
                  placeholder="Seleccionar cliente..."
                  autocomplete="off"
                  value="<?= $clienteSeleccionado ? htmlspecialchars('Id: ' . $clienteSeleccionado['id_cliente'] . ' - ' . $clienteSeleccionado['nombre'] . ' ' . $clienteSeleccionado['apellido'], ENT_QUOTES, 'UTF-8') : '' ?>"
                  required>
                <ul id="lista_clientes" class="lista-resultados">
                  <?php foreach ($cliente as $registroCliente): ?>
                    <li data-id="<?= (int)$registroCliente['id_cliente'] ?>"><?= htmlspecialchars('Id: ' . $registroCliente['id_cliente'] . ' - ' . $registroCliente['nombre'] . ' ' . $registroCliente['apellido'], ENT_QUOTES, 'UTF-8') ?></li>
                  <?php endforeach; ?>
                </ul>
                <input type="hidden" id="cliente_id_oculto" name="id_cliente" value="<?= $clienteSeleccionado ? (int)$clienteSeleccionado['id_cliente'] : '' ?>">
              </div>
              <div class="contenedor-buscador">
                <label class="label-campo" for="id_mascota">Mascota *:</label>
                <input
                  type="text"
                  class="campo"
                  id="id_mascota"
                  placeholder="<?= $clienteSeleccionado ? 'Seleccionar mascota...' : 'Primero selecciona un cliente' ?>"
                  autocomplete="off"
                  value="<?= $mascotaSeleccionada ? htmlspecialchars('Id: ' . $mascotaSeleccionada['id_mascota'] . ' - ' . $mascotaSeleccionada['nombre'] . ' (' . $mascotaSeleccionada['especie'] . ')', ENT_QUOTES, 'UTF-8') : '' ?>"
                  <?= $clienteSeleccionado ? '' : 'disabled' ?>
                  required>
                <ul id="lista_mascotas" class="lista-resultados">
                  <?php foreach ($mascota as $registroMascota): ?>
                    <li
                      data-id="<?= (int)$registroMascota['id_mascota'] ?>"
                      data-cliente="<?= (int)$registroMascota['id_cliente'] ?>"><?= htmlspecialchars('Id: ' . $registroMascota['id_mascota'] . ' - ' . $registroMascota['nombre'] . ' (' . $registroMascota['especie'] . ')', ENT_QUOTES, 'UTF-8') ?></li>
                  <?php endforeach; ?>
                </ul>
                <input type="hidden" id="mascota_id_oculto" name="id_mascota" value="<?= $mascotaSeleccionada ? (int)$mascotaSeleccionada['id_mascota'] : '' ?>">
              </div>
              <div class="contenedor-buscador">
                <label class="label-campo" for="id_jaula">Jaula *:</label>
                <input
                  type="text"
                  class="campo"
                  id="id_jaula"
                  placeholder="Seleccionar jaula..."
                  autocomplete="off"
                  value="<?= $jaulaSeleccionada ? htmlspecialchars('Jaula ID: ' . $jaulaSeleccionada['id'] . ' - Tamaño: ' . ucfirst((string)$jaulaSeleccionada['tamano']) . ' - Disponible', ENT_QUOTES, 'UTF-8') : '' ?>"
                  required>
                <ul id="lista_jaulas" class="lista-resultados">
                  <?php foreach ($jaulas as $registroJaula): ?>
                    <li
                      data-id="<?= (int)$registroJaula['id'] ?>"
                      data-tamano="<?= htmlspecialchars((string)$registroJaula['tamano'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars('Jaula ID: ' . $registroJaula['id'] . ' - Tamaño: ' . ucfirst((string)$registroJaula['tamano']) . ' - ' . ucfirst((string)$registroJaula['estado']), ENT_QUOTES, 'UTF-8') ?></li>
                  <?php endforeach; ?>
                </ul>
                <input type="hidden" id="jaula_id_oculto" name="id_jaula" value="<?= $jaulaSeleccionada ? (int)$jaulaAnterior : '' ?>">
              </div>
              <div>
                <label class="label-campo" for="fecha_entrada">Fecha de entrada *:</label>
                <input type="date" class="campo" id="fecha_entrada" name="fecha_entrada" value="<?= $valorCampo('fecha_entrada') ?>" required>
              </div>
              <div>
                <label class="label-campo" for="fecha_salida">Fecha de salida *:</label>
                <input type="date" class="campo" id="fecha_salida" name="fecha_salida" value="<?= $valorCampo('fecha_salida') ?>" required>
              </div>
              <div>
                <label class="label-campo" for="noches">Noches:</label>
                <input type="text" class="campo" id="noches" name="noches" value="<?= $valorCampo('num_noches') ?>" readonly>
              </div>
              <div>
                <label class="label-campo" for="precio_noche">Precio por noche *:</label>
                <input type="number" class="campo" id="precio_noche" name="precio_noche" step="0.01" min="0" value="<?= $valorCampo('precio_noche', '180.00') ?>" required>
              </div>

              <div>
                <label class="label-campo" for="saldo">Total a pagar $:</label>
                <input type="text" class="campo" id="saldo" name="saldo" value="<?= $valorCampo('saldo') ?>" readonly>
              </div>
              <div class="campo-completo">
                <label class="label-campo" for="servicios_adicionales">Servicios adicionales:</label>
                <textarea class="campo" id="servicios_adicionales" name="servicios_adicionales" rows="2" placeholder="Ej. Paseo diario, juguete propio"><?= $valorCampo('servicios_adicionales') ?></textarea>
    
              </div>
            </div>
          </fieldset>

          <fieldset>
            <legend>Validación de vacunas</legend>
            <div id="estado_validacion_vacunas" class="nota-campo" role="status" aria-live="polite">
              Selecciona una mascota y la fecha de salida para revisar las vacunas.
            </div>
            <div class="tabla-envoltura">
              <table>
                <thead>
                  <tr>
                    <th>Vacuna</th>
                    <th>Fecha de aplicación</th>
                    <th>Fecha próxima</th>
                    <th>Veterinario (ID)</th>
                    <th>Lote</th>
                    <th>Fabricante</th>
                    <th>Observaciones</th>
                    <th>Requerida</th>
                    <th>Estado para la salida</th>
                  </tr>
                </thead>
                <tbody id="tabla_vacunas_body">
                  <tr>
                    <td colspan="9">Selecciona una mascota y la fecha de salida para consultar sus vacunas.</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </fieldset>

          <fieldset>
            <legend>Datos de la estancia</legend>
            <div class="grid-formulario">
              <div>
                <label class="label-campo" for="alimentacion">Alimentación habitual:</label>
                <input type="text" class="campo" id="alimentacion" name="alimentacion" maxlength="100" value="<?= $valorCampo('alimentacion_habitual') ?>" placeholder="Ej. Croqueta adulto">
              </div>
              <div>
                <label class="label-campo" for="cantidad_alimento">Cantidad de alimento g:</label>
                <input type="text" class="campo" id="cantidad_alimento" name="cantidad_alimento" maxlength="50" value="<?= $valorCampo('cantidad_alimento') ?>" placeholder="Ej. 150 g">
              </div>
              <div>
                <label class="label-campo" for="horario_alimentacion">Horario de alimentación:</label>
                <input type="text" class="campo" id="horario_alimentacion" name="horario_alimentacion" maxlength="50" value="<?= $valorCampo('horario_alimentacion') ?>" placeholder="Ej. 8:00 y 18:00">
              </div>
              <div>
                <label class="label-campo" for="medicamentos">Medicamentos:</label>
                <input type="text" class="campo" id="medicamentos" name="medicamentos" maxlength="100" value="<?= $valorCampo('medicamentos') ?>" placeholder="Ninguno">
              </div>
              <div>
                <label class="label-campo" for="horario_medicamentos">Horario de medicamentos:</label>
                <input type="text" class="campo" id="horario_medicamentos" name="horario_medicamentos" maxlength="50" value="<?= $valorCampo('horario_medicamentos') ?>" placeholder="Obligatorio si hay medicamentos">
              </div>
              <div>
                <label class="label-campo" for="persona_recoge">Persona que recoge *:</label>
                <input type="text" class="campo" id="persona_recoge" name="persona_recoge" maxlength="60" value="<?= $valorCampo('persona_recoge') ?>" required>
              </div>
              <div>
                <label class="label-campo" for="telefono_emergencia">Teléfono de emergencia *:</label>
                <input type="tel" class="campo" id="telefono_emergencia" name="telefono_emergencia" maxlength="15" value="<?= $valorCampo('telefono_emergencia') ?>" required>
              </div>
              <div class="campo-completo">
                <label class="label-campo" for="necesidades_especiales">Necesidades especiales:</label>
                <textarea class="campo" id="necesidades_especiales" name="necesidades_especiales" rows="2"><?= $valorCampo('necesidades_especiales') ?></textarea>
              </div>
              <div class="campo-completo">
                <label class="label-campo" for="observaciones">Observaciones:</label>
                <textarea class="campo" id="observaciones" name="observaciones" rows="2"><?= $valorCampo('observaciones') ?></textarea>
              </div>
            </div>
          </fieldset>

          <div class="acciones-formulario">
            <a class="btn-secundario" href="hotel.php">Cancelar</a>
            <button type="submit" id="guardar_reservacion" disabled>Guardar reservación</button>
          </div>
          <span class="nota-campo" style="text-align:right;">Solo se puede guardar cuando las vacunas están vigentes hasta la fecha de salida.</span>
        </form>
      </div>
    </main>
  </div>

  <footer>
    <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
  </footer>

  <script src="js/hotel.js"></script>
</body>

</html>
