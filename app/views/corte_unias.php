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
  <title>Veterinaria PetCare - Corte de uñas</title>
  <link rel="stylesheet" href="css/estilos_base_v2.css">
</head>
<body>

  <div class="layout">

  <?php $paginaActiva = 'citas';
    include __DIR__ . '/layout/sidebar.php'; ?>


  <main>
      <div class="migaja"><a href="citas.php" class="link-tenue">Gestión de citas</a> / ficha veterinaria</div>
      <div class="encabezado-pagina">
        <h1>Corte de uñas</h1>
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

    <form action="../controllers/servicioCorteUnias.php" method="POST">
      <input type="hidden" name="id_cita" value="<?= $id_cita ?>">

      <fieldset>
        <legend>Datos de la consulta</legend>
        <div class="grid-formulario">

        
          <div class="campo-completo">
            <label class="label-campo" for="comportamiento">Comportamiento *:</label>
            <textarea class="campo" id="comportamiento" name="comportamiento"
            rows="3" placeholder="Comportamiento del animal durante el servicio"><?= old('oldComportamiento'); unset($_SESSION['oldComportamiento']); ?></textarea>
            </div>

            <div class="campo-completo">
            <label class="label-campo" for="observaciones">Observaciones *:</label>
            <textarea class="campo" id="observaciones" name="observaciones"
            rows="3" placeholder="Notas u observaciones adicionales"><?= old('oldObserbaciones'); unset($_SESSION['oldObserbaciones']); ?></textarea>
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
