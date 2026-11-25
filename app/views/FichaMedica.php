<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario'])) {
    header("Location: ../views/login.php");
    exit();
}
// Esta vista se carga desde controllers/FichaMedicaController.php,
// asi que puede que estas variables no existan si se entra directo.
$mascotas             = $mascotas ?? [];
$veterinarios         = $veterinarios ?? [];
$idConsulta           = $idConsulta ?? 0;
$idVeterinario        = $idVeterinario ?? 1;
$radiografiasPrevias  = $radiografiasPrevias ?? [];
$catalogoVacunas      = $catalogoVacunas ?? [];
$Datosconsulta        = $Datosconsulta ?? [];

// Devuelve el valor viejo guardado en sesion (si hubo error) y lo borra
function old(string $clave): string
{
    $valor = (string)($_SESSION[$clave] ?? '');
    unset($_SESSION[$clave]);
    return htmlspecialchars($valor);
}

// El controlador usa 'error' (texto) en un lado y 'errores' (arreglo) en otro
$mensajeError = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
$listaErrores = $_SESSION['errores'] ?? [];
unset($_SESSION['errores']);
$mascotaVieja = old('oldMascota');
if ($mascotaVieja === '' && !empty($Datosconsulta['mascota'])) {
    $mascotaVieja = (string)$Datosconsulta['mascota'];
}
$veterinarioViejo = old('oldid_veterinario');
if ($veterinarioViejo === '' && $idVeterinario > 0) {
    $veterinarioViejo = (string)$idVeterinario;
}
$vacunasViejas = $_SESSION['oldVacunas'] ?? [];
$vacunasProximaViejas = $_SESSION['oldVacunasProxima'] ?? [];
$vacunasLoteViejas = $_SESSION['oldVacunasLote'] ?? [];
$vacunasFabricanteViejas = $_SESSION['oldVacunasFabricante'] ?? [];
unset(
    $_SESSION['oldVacunas'],
    $_SESSION['oldVacunasProxima'],
    $_SESSION['oldVacunasLote'],
    $_SESSION['oldVacunasFabricante']
);
$vacunasViejasStr = is_array($vacunasViejas) ? array_map('strval', $vacunasViejas) : [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Esta vista se muestra desde /controllers/, asi que las rutas relativas
       (css, imagenes, links del sidebar) se calculan desde /views/ -->
  <base href="../views/">
  <title>Veterinaria PetCare - Ficha veterinaria</title>
  <link rel="stylesheet" href="css/estilos_base_v2.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
  <style>
    .rejilla-vacunas {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
      gap: 12px 16px;
      margin-top: 8px;
    }
    .tarjeta-vacuna {
      display: flex;
      flex-direction: column;
      gap: 8px;
      padding: 10px 12px;
      border: 1px solid var(--border-light);
      border-radius: 6px;
      background-color: var(--white);
    }
    .tarjeta-vacuna .titulo-vacuna {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 14px;
      font-weight: bold;
      cursor: pointer;
    }
    .tarjeta-vacuna .detalle-vacuna {
      display: flex;
      flex-direction: column;
      gap: 8px;
      margin-top: 4px;
      padding-top: 8px;
      border-top: 1px solid var(--border-light);
    }
    .tarjeta-vacuna .detalle-vacuna .campo {
      padding: 6px 10px;
    }
  </style>
</head>
<body>

  <div class="layout">

  <?php $paginaActiva = 'citas'; include __DIR__ . '/layout/sidebar.php'; ?>

  <main>
      <div class="migaja"><a href="citas.php" class="link-tenue">Gestión de citas</a> / ficha veterinaria</div>
      <div class="encabezado-pagina">
        <h1>Ficha veterinaria</h1>
      </div>

      <div class="tarjeta">

    <?php if ($mensajeError && empty($listaErrores)): ?>
      <div class="msg-error"><?= htmlspecialchars($mensajeError) ?></div>
    <?php endif; ?>

    <?php if (!empty($listaErrores)): ?>
      <div class="msg-error">
        <?php foreach ($listaErrores as $e): ?>
          <div>• <?= htmlspecialchars($e) ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form action="../controllers/FichaMedicaController.php" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="id_consulta" value="<?= (int)$idConsulta ?>">

      <fieldset>
        <legend>Datos de la consulta</legend>
        <div class="grid-formulario">
          <div>
            <label class="label-campo" for="mascota">Mascota *:</label>
            <select class="campo" name="mascota" id="mascota" required>
              <option value="">Selecciona una mascota</option>
              <?php foreach ($mascotas as $m): ?>
                <option value="<?= (int)$m['id'] ?>" data-especie-id="<?= (int)($m['id_especie'] ?? 0) ?>" <?= ((string)$m['id'] === (string)$mascotaVieja) ? 'selected' : '' ?>><?= htmlspecialchars($m['nombre']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label class="label-campo" for="id_veterinario">Veterinario *:</label>
            <select class="campo" name="id_veterinario" id="id_veterinario" required>
              <option value="">Selecciona un veterinario</option>
              <?php foreach ($veterinarios as $v): ?>
                <option value="<?= (int)$v['id'] ?>" <?= ((string)$v['id'] === (string)$veterinarioViejo) ? 'selected' : '' ?>><?= htmlspecialchars($v['nombre']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label class="label-campo" for="precio">Costo ($) *:</label>
            <input type="number" step="0.01" min="1" max="100000" class="campo" id="precio" name="precio"
              placeholder="Ingresa el costo (ej. 350.00)" required value="<?= old('oldPrecio') ?>">
          </div>
        </div>
      </fieldset>

      <fieldset>
        <legend>Datos del paciente</legend>
        <div class="grid-formulario">
          <div>
            <label class="label-campo" for="temperatura">Temperatura (°C) *:</label>
            <input type="number" class="campo" id="temperatura" name="temperatura" min="30" max="45" step="0.1"
              placeholder="Ej. 38.5" required value="<?= old('oldTemperatura') ?>">
          </div>

          <div>
            <label class="label-campo" for="peso">Peso (kg) *:</label>
            <input type="number" class="campo" id="peso" name="peso" min="0.1" max="150" step="0.1"
              placeholder="Ej. 10.5" required value="<?= old('oldPeso') ?>">
          </div>

          <div>
            <label class="label-campo" for="frecuenciaC">Frecuencia cardíaca (lpm) *:</label>
            <input type="number" class="campo" id="frecuenciaC" name="frecuenciaC" min="30" max="350"
              placeholder="Ej. 90" required value="<?= old('oldFrecuenciaC') ?>">
          </div>

          <div>
            <label class="label-campo" for="frecuenciaR">Frecuencia respiratoria (rpm) *:</label>
            <input type="number" class="campo" id="frecuenciaR" name="frecuenciaR" min="10" max="150"
              placeholder="Ej. 24" required value="<?= old('oldFrecuenciaR') ?>">
          </div>

          <div class="campo-completo">
            <label class="label-campo" for="sintomas">Síntomas presentados *:</label>
            <textarea class="campo" id="sintomas" name="sintomas" rows="4" minlength="5" maxlength="1000"
              placeholder="Describe los síntomas observados" required><?= old('oldSintomas') ?></textarea>
          </div>
        </div>
      </fieldset>

      <fieldset>
        <legend>Diagnóstico y tratamiento</legend>
        <div class="grid-formulario">
          <div class="campo-completo">
            <label class="label-campo" for="diagnostico">Diagnóstico clínico *:</label>
            <textarea class="campo" id="diagnostico" name="diagnostico" rows="3" minlength="5" maxlength="1000"
              placeholder="Diagnóstico del paciente" required><?= old('oldDiagnostico') ?></textarea>
          </div>

          <div>
            <label class="label-campo" for="tratamiento">Tratamiento *:</label>
            <textarea class="campo" id="tratamiento" name="tratamiento" rows="3" minlength="5" maxlength="1000"
              placeholder="Tratamiento prescrito" required><?= old('oldTratamiento') ?></textarea>
          </div>

          <div>
            <label class="label-campo" for="Medicamento">Medicamentos *:</label>
            <textarea class="campo" id="Medicamento" name="Medicamento" rows="3" minlength="3" maxlength="500"
              placeholder="Medicamentos recetados" required><?= old('oldMedicamento') ?></textarea>
          </div>

          <div>
            <label class="label-campo" for="Dosis">Dosis:</label>
            <textarea class="campo" id="Dosis" name="Dosis" rows="3" maxlength="500"
              placeholder="Ej. 1 tableta cada 12 horas"><?= old('oldDosis') ?></textarea>
          </div>

          <div>
            <label class="label-campo" for="indicaciones">Indicaciones para el dueño *:</label>
            <textarea class="campo" id="indicaciones" name="indicaciones" rows="3" minlength="5" maxlength="1000"
              placeholder="Cuidados y recomendaciones" required><?= old('oldIndicacion') ?></textarea>
          </div>

          <div>
            <label class="label-campo" for="estudiosA">Estudios asociados:</label>
            <textarea class="campo" id="estudiosA" name="estudiosA" rows="3" maxlength="500"
              placeholder="Rayos X, análisis de sangre, etc."><?= old('oldEstudiosaA') ?></textarea>
          </div>

          <div>
            <label class="label-campo" for="fecha">Próxima cita:</label>
            <input type="date" class="campo" id="fecha" name="fecha"
              min="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d', strtotime('+2 years')) ?>" value="<?= old('oldFecha') ?>">
          </div>

          <div class="campo-completo campo-completo-sep">
            <label class="label-campo" for="radiografias">Radiografías / Estudios de imagen:</label>
            <input type="file" class="campo" id="radiografias" name="radiografias[]" accept="image/*" multiple>
            <p style="font-size: 0.95rem; color: #90a4ae; margin-top: 6px;">
              Puedes seleccionar una o varias imágenes. Se abrirá el recortador para ajustar cada una antes de guardar.
            </p>
            <div id="contenedor-previews-radiografias" style="display: none; flex-wrap: wrap; gap: 12px; margin-top: 12px;"></div>
          </div>

          <?php if (!empty($radiografiasPrevias)): ?>
            <div class="campo-completo" style="margin-top: 10px;">
              <label class="label-campo">Radiografías registradas previamente en esta consulta:</label>
              <div style="display: flex; flex-wrap: wrap; gap: 12px; margin-top: 8px;">
                <?php foreach ($radiografiasPrevias as $rx): ?>
                  <div style="background: rgba(0,0,0,0.25); border: 1px solid rgba(255,255,255,0.15); border-radius: 8px; padding: 6px; text-align: center;">
                    <a href="../../<?= htmlspecialchars($rx['ruta']) ?>" target="_blank" title="Ver imagen completa">
                      <img src="../../<?= htmlspecialchars($rx['ruta']) ?>" alt="Radiografía" style="width: 100px; height: 100px; object-fit: cover; border-radius: 6px; display: block;">
                    </a>
                    <span style="font-size: 0.72rem; color: #90a4ae; display: block; margin-top: 4px;"><?= htmlspecialchars(substr($rx['fecha_subida'] ?? '', 0, 10)) ?></span>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>

          <div class="campo-completo">
            <label class="label-campo" for="observaciones">Observaciones:</label>
            <textarea class="campo" id="observaciones" name="observaciones" rows="3" maxlength="1000"
              placeholder="Notas u observaciones adicionales"><?= old('oldObservaciones') ?></textarea>
          </div>
        </div>
      </fieldset>

      <fieldset>
        <legend>Vacunas aplicadas</legend>
        <div class="campo-completo" id="contenedorVacunas">
          <small class="nota-campo" id="notaVacunas">Selecciona primero una mascota...</small>
          <small class="nota-campo">Marca las vacunas aplicadas durante esta consulta (se registrarán con fecha de hoy).</small>

          <div class="rejilla-vacunas">
            <?php foreach ($catalogoVacunas as $vac):
              $idVac = (int)$vac['id'];
              $marcada = in_array((string)$idVac, $vacunasViejasStr, true);
              $fechaProx = is_array($vacunasProximaViejas) ? (string)($vacunasProximaViejas[$idVac] ?? '') : '';
              $loteVac = is_array($vacunasLoteViejas) ? (string)($vacunasLoteViejas[$idVac] ?? '') : '';
              $fabVac = is_array($vacunasFabricanteViejas) ? (string)($vacunasFabricanteViejas[$idVac] ?? '') : '';
            ?>
              <div class="oculto tarjeta-vacuna" data-especie-id="<?= (int)$vac['especie'] ?>">
                <label class="titulo-vacuna">
                  <input type="checkbox" name="vacunas[]" value="<?= $idVac ?>" disabled <?= $marcada ? 'checked' : '' ?>>
                  <span><?= htmlspecialchars($vac['nombre']) ?></span>
                </label>
                <div class="detalle-vacuna oculto">
                  <div>
                    <label class="label-campo" for="vacuna_proxima_<?= $idVac ?>">Próxima aplicación:</label>
                    <input type="date" class="campo" id="vacuna_proxima_<?= $idVac ?>" name="vacunas_proxima[<?= $idVac ?>]"
                      min="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d', strtotime('+5 years')) ?>"
                      value="<?= htmlspecialchars($fechaProx) ?>" disabled>
                  </div>
                  <div>
                    <label class="label-campo" for="vacuna_lote_<?= $idVac ?>">Lote:</label>
                    <input type="text" class="campo" id="vacuna_lote_<?= $idVac ?>" name="vacunas_lote[<?= $idVac ?>]"
                      maxlength="20" placeholder="Opcional" value="<?= htmlspecialchars($loteVac) ?>" disabled>
                  </div>
                  <div>
                    <label class="label-campo" for="vacuna_fabricante_<?= $idVac ?>">Fabricante:</label>
                    <input type="text" class="campo" id="vacuna_fabricante_<?= $idVac ?>" name="vacunas_fabricante[<?= $idVac ?>]"
                      maxlength="20" placeholder="Opcional" value="<?= htmlspecialchars($fabVac) ?>" disabled>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </fieldset>

      <div id="modal-crop">
        <div class="caja">
          <h3 id="crop-titulo" style="color: #1e293b; margin-top: 0;">Recortar radiografía</h3>
          <img id="imagen-a-recortar" alt="Imagen a recortar">
          <div class="crop-acciones">
            <button type="button" id="btn-cancelar-crop" class="btn btn-secundario btn-chico">Omitir / Cancelar</button>
            <button type="button" id="btn-recortar" class="btn btn-chico">Recortar y usar</button>
          </div>
        </div>
      </div>

      <div class="acciones-formulario">
        <a class="btn-secundario" href="citas.php">Cancelar</a>
        <button type="submit" class="btn">Terminar consulta</button>
      </div>
    </form>

      </div>
    </main>

  </div>

  <footer>
    <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
  </footer>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
  <script src="js/crop-radiografias.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const selectMascota = document.getElementById('mascota');
      const contenedorVacunas = document.getElementById('contenedorVacunas');
      const notaVacunas = document.getElementById('notaVacunas');
      if (!selectMascota || !contenedorVacunas) return;

      const tarjetas = Array.from(contenedorVacunas.querySelectorAll('.tarjeta-vacuna[data-especie-id]'));

      function actualizarDetalleVacuna(tarjeta) {
        const casilla = tarjeta.querySelector('input[type="checkbox"]');
        const detalle = tarjeta.querySelector('.detalle-vacuna');
        if (!casilla || !detalle) return;

        const mostrarDetalle = !casilla.disabled && casilla.checked;
        detalle.classList.toggle('oculto', !mostrarDetalle);
        detalle.querySelectorAll('input').forEach((campo) => {
          campo.disabled = !mostrarDetalle;
          if (!mostrarDetalle) campo.value = '';
        });
      }

      function filtrarVacunas(reiniciarSeleccion) {
        const opcion = selectMascota.options[selectMascota.selectedIndex];
        const especieId = opcion && opcion.value !== '' ? (opcion.dataset.especieId || '') : '';
        let hayVacunas = false;

        tarjetas.forEach((tarjeta) => {
          const casilla = tarjeta.querySelector('input[type="checkbox"]');
          const pertenece = especieId !== '' && especieId !== '0' && tarjeta.dataset.especieId === especieId;

          tarjeta.classList.toggle('oculto', !pertenece);
          casilla.disabled = !pertenece;
          if (!pertenece || reiniciarSeleccion) {
            casilla.checked = false;
          }
          if (pertenece) {
            hayVacunas = true;
          }
          actualizarDetalleVacuna(tarjeta);
        });

        if (notaVacunas) {
          if (selectMascota.value === '') {
            notaVacunas.textContent = 'Selecciona primero una mascota...';
          } else {
            notaVacunas.textContent = hayVacunas ? '' : 'No hay vacunas registradas para esta especie.';
          }
        }
      }

      tarjetas.forEach((tarjeta) => {
        const casilla = tarjeta.querySelector('input[type="checkbox"]');
        if (casilla) {
          casilla.addEventListener('change', () => actualizarDetalleVacuna(tarjeta));
        }
      });

      selectMascota.addEventListener('change', () => filtrarVacunas(true));
      filtrarVacunas(false);
    });
  </script>

</body>
</html>
