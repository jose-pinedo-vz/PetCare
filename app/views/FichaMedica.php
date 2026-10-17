<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Esta vista se carga desde controllers/FichaMedicaController.php,
// asi que puede que estas variables no existan si se entra directo.
$mascotas      = $mascotas ?? [];
$idConsulta    = $idConsulta ?? 0;
$idVeterinario = $idVeterinario ?? 1;

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
</head>
<body>

  <div class="layout">

  <?php $paginaActiva = 'citas'; include __DIR__ . '/layout/sidebar.php'; ?>

  <main>
      <div class="migaja"><a href="citas.php" class="link-tenue">Gestión de Citas</a> / Ficha veterinaria</div>
      <div class="encabezado-pagina">
        <h1>Ficha veterinaria</h1>
      </div>

      <div class="tarjeta">

    <?php if ($mensajeError): ?>
      <div class="msg-error"><?= htmlspecialchars($mensajeError) ?></div>
    <?php endif; ?>

    <?php if (!empty($listaErrores)): ?>
      <div class="msg-error">
        <?php foreach ($listaErrores as $e): ?>
          <div><?= htmlspecialchars($e) ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form action="../controllers/FichaMedicaController.php" method="POST">
      <input type="hidden" name="id_consulta" value="<?= (int)$idConsulta ?>">
      <input type="hidden" name="id_veterinario" value="<?= (int)$idVeterinario ?>">

      <fieldset>
        <legend>Datos de la consulta</legend>
        <div class="grid-formulario">
          <div>
            <label class="label-campo" for="mascota">Mascota *:</label>
            <select class="campo" name="mascota" id="mascota" required>
              <option value="">Selecciona una mascota</option>
              <?php foreach ($mascotas as $m): ?>
                <option value="<?= (int)$m['id'] ?>"><?= htmlspecialchars($m['nombre']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label class="label-campo" for="precio">Costo ($) *:</label>
            <input type="number" step="0.01" min="1" class="campo" id="precio" name="precio"
              placeholder="Ingresa el costo" required value="<?= old('oldPrecio') ?>">
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
            <input type="number" class="campo" id="peso" name="peso" min="0.5" max="100" step="0.1"
              placeholder="Ej. 10.5" required value="<?= old('oldPeso') ?>">
          </div>

          <div>
            <label class="label-campo" for="frecuenciaC">Frecuencia cardíaca (lpm) *:</label>
            <input type="number" class="campo" id="frecuenciaC" name="frecuenciaC"
              placeholder="Ej. 90" required value="<?= old('oldFrecuenciaC') ?>">
          </div>

          <div>
            <label class="label-campo" for="frecuenciaR">Frecuencia respiratoria (rpm) *:</label>
            <input type="number" class="campo" id="frecuenciaR" name="frecuenciaR"
              placeholder="Ej. 24" required value="<?= old('oldFrecuenciaR') ?>">
          </div>

          <div class="campo-completo">
            <label class="label-campo" for="sintomas">Síntomas presentados *:</label>
            <textarea class="campo" id="sintomas" name="sintomas" rows="4"
              placeholder="Describe los síntomas observados" required><?= old('oldSintomas') ?></textarea>
          </div>
        </div>
      </fieldset>

      <fieldset>
        <legend>Diagnóstico y tratamiento</legend>
        <div class="grid-formulario">
          <div class="campo-completo">
            <label class="label-campo" for="diagnostico">Diagnóstico clínico *:</label>
            <textarea class="campo" id="diagnostico" name="diagnostico" rows="3"
              placeholder="Diagnóstico del paciente" required><?= old('oldDiagnostico') ?></textarea>
          </div>

          <div>
            <label class="label-campo" for="tratamiento">Tratamiento *:</label>
            <textarea class="campo" id="tratamiento" name="tratamiento" rows="3"
              placeholder="Tratamiento prescrito" required><?= old('oldTratamiento') ?></textarea>
          </div>

          <div>
            <label class="label-campo" for="Medicamento">Medicamentos *:</label>
            <textarea class="campo" id="Medicamento" name="Medicamento" rows="3"
              placeholder="Medicamentos recetados" required><?= old('oldMedicamento') ?></textarea>
          </div>

          <div>
            <label class="label-campo" for="Dosis">Dosis:</label>
            <textarea class="campo" id="Dosis" name="Dosis" rows="3"
              placeholder="Ej. 1 tableta cada 12 horas"><?= old('oldDosis') ?></textarea>
          </div>

          <div>
            <label class="label-campo" for="indicaciones">Indicaciones para el dueño *:</label>
            <textarea class="campo" id="indicaciones" name="indicaciones" rows="3"
              placeholder="Cuidados y recomendaciones" required><?= old('oldIndicacion') ?></textarea>
          </div>

          <div>
            <label class="label-campo" for="estudiosA">Estudios asociados:</label>
            <textarea class="campo" id="estudiosA" name="estudiosA" rows="3"
              placeholder="Rayos X, análisis de sangre, etc."><?= old('oldEstudiosaA') ?></textarea>
          </div>

          <div>
            <label class="label-campo" for="fecha">Próxima cita:</label>
            <input type="date" class="campo" id="fecha" name="fecha"
              min="<?= date('Y-m-d') ?>" value="<?= old('oldFecha') ?>">
          </div>

          <div class="campo-completo">
            <label class="label-campo" for="observaciones">Observaciones:</label>
            <textarea class="campo" id="observaciones" name="observaciones" rows="3"
              placeholder="Notas u observaciones adicionales"><?= old('oldObservaciones') ?></textarea>
          </div>
        </div>
      </fieldset>

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

</body>
</html>
