<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}



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
  <title>Veterinaria PetCare - Baño</title>
  <link rel="stylesheet" href="css/estilos_base_v2.css">
</head>
<body>

  <div class="layout">

  <?php $paginaActiva = 'citas';
    include __DIR__ . '/layout/sidebar.php'; ?>


  <main>
      <div class="migaja"><a href="citas.php" class="link-tenue">Gestión de citas</a> / ficha veterinaria</div>
      <div class="encabezado-pagina">
        <h1>Baño</h1>
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

    <form action="../controllers/servicioBanio.php" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="id_cita" value="<?= (int)$id_consulta ?>">

      <fieldset>
        <legend>Datos de la consulta</legend>
        <div class="grid-formulario">

        <div>
            <label class="label-campo" for="Tipo">Tipo de baño *:</label>
            <select class="campo" name="Tipo" id="Tipo" required>
              <option value="">Selecciona un tipo</option>
              <option value="Interno">Baño normal</option>
              <option value="Baño antipulga">Baño antipulgas</option>
            </select>
          </div>

          
          <div>
            <label class="label-campo" for="Shampoo">Tipo de shampoo *:</label>
            <select class="campo" name="Shampoo" id="Shampoo" required>
              <option value="">Selecciona un producto</option>
              <option value="Shampoo hipoalergénico">Shampoo hipoalergénico</option>
              <option value="Shampoo para pelo corto">Shampoo para pelo corto</option>
              <option value="Shampoo para pelo largo">Shampoo para pelo largo</option>
              <option value="Shampoo antipulgas">Shampoo antipulgas</option>
            </select>
          </div>

          <div>
            <label class="label-campo" for="TratamientoAdicional">Tratamiento adicional *:</label>
            <select class="campo" name="TratamientoAdicional" id="TratamientoAdicional" required>
              <option value="">Selecciona un tratamiento adicional</option>
              <option value="Ninguno">Ninguno</option>
              <option value="Hidratación de cojinetes y nariz">Hidratación de cojinetes y nariz</option>
              <option value="Shampoo para pelo largo">Limpieza profunda de oídos</option>
            </select>
          </div>

          <div>
            <label class="label-campo" for="Cepillado">Cepillado *:</label>
            <select class="campo" name="Cepillado" id="Cepillado" required>
              <option value="">Selecciona un cepillado</option>
              <option value="Cepillado básico">Cepillado básico</option>
              <option value="Desenredado">Desenredado</option>
              <option value="Cepillado para pelo corto">Cepillado para pelo corto</option>
            </select>
          </div>

          <div>
            <label class="label-campo" for="Cepillado">Secado *:</label>
            <select class="campo" name="Secado" id="Secado" required>
              <option value="">Selecciona un tipo de secado</option>
              <option value="Secado a toalla y aire tibio">Secado a toalla y aire tibio</option>
              <option value="Secado rápido">Secado rápido</option>
              <option value="No aplica">No aplica</option>
            </select>
          </div>
      </fieldset>

      <fieldset>
          <div class="campo-completo">
            <label class="label-campo" for="observaciones">Observaciones:</label>
            <textarea class="campo" id="observaciones" name="observaciones" rows="3" maxlength="1000"
              placeholder="Notas u observaciones adicionales"><?= old('oldObservaciones') ?></textarea>
          </div>
            

        <div class="campo-completo">
            <label class="label-campo" for="Condiciones_recibidas">Condiciones recibidas:</label>
            <textarea class="campo" id="Condiciones_recibidas" name="Condiciones_recibidas" rows="3" maxlength="1000"
              placeholder="Condiciones en las que se recibió"><?= old('oldCondiciones_recibidas') ?></textarea>
          </div>


        <div class="campo-completo">
            <label class="label-campo" for="Condiciones_entrega">Condiciones de entrega:</label>
            <textarea class="campo" id="Condiciones_entrega" name="Condiciones_entrega" rows="3" maxlength="1000"
              placeholder="Condiciones en las que se entregó"><?= old('oldCondiciones_entrega') ?></textarea>
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
