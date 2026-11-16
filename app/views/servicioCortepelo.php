<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Esta vista se carga desde controllers/FichaMedicaController.php,
// asi que puede que estas variables no existan si se entra directo.


// Devuelve el valor viejo guardado en sesion (si hubo error) y lo borra
function old(string $clave): string
{
    $valor = (string)($_SESSION[$clave] ?? '');
    unset($_SESSION[$clave]);
    return htmlspecialchars($valor);
}

//El controlador usa 'error' (texto) en un lado y 'errores' (arreglo) en otro
// ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Esta vista se muestra desde /controllers/, asi que las rutas relativas
       (css, imagenes, links del sidebar) se calculan desde /views/ -->
  <base href="../views/">
  <title>Veterinaria PetCare - Corte de pelo</title>
  <link rel="stylesheet" href="css/estilos_base_v2.css">
</head>
<body>

  <div class="layout">

  <?php $paginaActiva = 'citas';
    include __DIR__ . '/layout/sidebar.php'; ?>


  <main>
      <div class="migaja"><a href="citas.php" class="link-tenue">Gestión de citas</a> / ficha veterinaria</div>
      <div class="encabezado-pagina">
        <h1>Corte de pelo</h1>
      </div>

      <?php if (isset($_SESSION['error'])): ?>
        <div class="msg-error">
          <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
      <?php endif; ?>

      <div class="tarjeta">

      <?php
        $id_consulta = $_GET['id_consulta'] ?? '';
        $id_cita = (int)$id_consulta;
      ?>

    <form action="../controllers/servicioCortePelo.php" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="id_cita" value="<?= (int)$id_consulta ?>">

      <fieldset>
        <legend>Datos de la consulta</legend>
        <div class="grid-formulario">

        <div>
            <label class="label-campo" for="Tipo">Tipo de corte *:</label>
            <select class="campo" name="Tipo" id="Tipo" required>
              <option value="">Selecciona un tipo de corte</option>
              <option value="Corte león">Corte león</option>
              <option value="Corte de mantenimiento">Corte de mantenimiento</option>
              <option value="Rapado">Rapado</option>
              <option value="Corte sanitario / higiénico">Corte sanitario / higiénico</option>
              <option value="Sin corte solo mantenimiento">Sin corte solo mantenimiento</option>
            </select>
          </div>

          
          <div>
            <label class="label-campo" for="Longitud">Longitud (cm) *:</label>
            <input type="number" class="campo" id="Longitud" name="Longitud" step="0.01" min="0" placeholder="Ej. 5.50" value="<?= old('oldLongitud') ?>">
          </div>

          <div>
            <label class="label-campo" for="corte_higienico">Corte higienico *:</label>
            <select class="campo" name="corte_higienico" id="corte_higienico" required>
              <option value="">Selecciona una obcion</option>
              <option value="Si">Si</option>
              <option value="No">No</option>
            </select>
          </div>

          <div>
            <label class="label-campo" for="corteCara">Corte de la cara *:</label>
            <select class="campo" name="corteCara" id="corteCara" required>
              <option value="">Selecciona una obcion</option>
              <option value="No requerido">No requerido</option>
              <option value="Despeje de pelo alrededor de los ojos">Despeje de pelo alrededor de los ojos</option>
              <option value="Limpieza y recorte estético general">Limpieza y recorte estético general</option>
              <option value="Otro">otro</option>
              
            </select>
          </div>
          


          <div>
            <label class="label-campo" for="cortePatas">Corte de las patas *:</label>
            <select class="campo" name="cortePatas" id="cortePatas" required>
              <option value="">Selecciona una obcion</option>
              <option value="No requerido">No requerido</option>
              <option value="Despeje de pelo en cojinetes y entre los dedos">Despeje de pelo en cojinetes y entre los dedos</option>
              <option value="Recorte estético de contorno de patas">Recorte estético de contorno de patas</option>
            </select>
          </div>

          <div>
            <label class="label-campo" for="Cepillado">Cepillado *:</label>
            <select class="campo" name="Cepillado" id="Cepillado" required>
              <option value="">Selecciona cepillado</option>
              <option value="Si">Si</option>
              <option value="No">No</option>
            </select>
          </div>

          <div>
            <label class="label-campo" for="Deslanado">Deslanado *:</label>
            <select class="campo" name="Deslanado" id="Deslanado" required>
              <option value="">Selecciona una obcion</option>
              <option value="Si">Si</option>
              <option value="No">No</option>
            </select>
          </div>

      </fieldset>

      <fieldset>
          <div class="campo-completo">
            <label class="label-campo" for="accesorios">Accesorios:</label>
            <input type="text" class="campo" id="accesorios" name="accesorios" maxlength="100"
              placeholder="Ej. Moño, pañoleta o ninguno" value="<?= old('oldAccesorios') ?>">
          </div>

        <div class="campo-completo">
            <label class="label-campo" for="Observaciones">Observaciones:</label>
            <textarea class="campo" id="Observaciones" name="Observaciones" rows="3" maxlength="1000"
              placeholder="Observaciones"><?= old('oldCondiciones_entrega') ?></textarea>
          </div>

         <div class="acciones-formulario">
          <a class="btn-secundario" href="citas.php">Cancelar</a>
          <button type="submit" class="btn">Terminar consulta</button>
        </div>
      </fieldset>
    </form>

      </div>
    </main>

  </div>

  <footer>
    <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
  </footer>

</body>
</html>
