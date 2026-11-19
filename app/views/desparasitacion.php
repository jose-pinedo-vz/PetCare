<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/conexion_db.php';

$listaProductos = [];
try {
    $dbProd = ConexionDB::obtenerConexion();
    $stmtProd = $dbProd->query("SELECT id_producto, nombre FROM productos WHERE estado = 1 OR estado = 'activo' ORDER BY nombre ASC");
    $listaProductos = $stmtProd ? $stmtProd->fetchAll(PDO::FETCH_ASSOC) : [];
} catch (Exception $e) {
    error_log("Error al cargar productos en desparasitacion: " . $e->getMessage());
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
  <title>Veterinaria PetCare - Desparasitación</title>
  <link rel="stylesheet" href="css/estilos_base_v2.css">
</head>
<body>

  <div class="layout">

  <?php $paginaActiva = 'citas';
    include __DIR__ . '/layout/sidebar.php'; ?>


  <main>
      <div class="migaja"><a href="citas.php" class="link-tenue">Gestión de citas</a> / ficha veterinaria</div>
      <div class="encabezado-pagina">
        <h1>Desparasitación</h1>
      </div>

      <?php if (isset($_SESSION['error'])): ?>
        <div class="msg-error">
          <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
      <?php endif; ?>

      <div class="tarjeta">

      <?php
        $id_consulta = $_GET['id_consulta'] ?? $_SESSION['id_consulta_error'] ?? '';
        unset($_SESSION['id_consulta_error']);
        $id_cita = (int)$id_consulta;
        $oldTipo = old('oldTipo');
        $oldProducto = old('oldProducto');
        $oldDosis = old('oldDosis') ?: old('oldDicis');
      ?>

    <form action="../controllers/servicioDesparacitacion.php" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="id_cita" value="<?= (int)$id_consulta ?>">

      <fieldset>
        <legend>Datos de la consulta</legend>
        <div class="grid-formulario">

        <div>
            <label class="label-campo" for="Tipo">Tipo de desparasitación *:</label>
            <select class="campo" name="Tipo" id="Tipo" required>
              <option value="">Selecciona un tipo</option>
              <option value="Interno" <?= $oldTipo === 'Interno' ? 'selected' : '' ?>>Interno</option>
              <option value="Externo" <?= $oldTipo === 'Externo' ? 'selected' : '' ?>>Externo</option>
            </select>
          </div>

          
          <div>
            <label class="label-campo" for="ProductoUtilizado">Producto utilizado *:</label>
            <select class="campo" name="ProductoUtilizado" id="ProductoUtilizado" required>
              <option value="">Selecciona un producto</option>
              <?php foreach ($listaProductos as $prod): ?>
                <?php $nombreProd = $prod['nombre']; ?>
                <option value="<?= htmlspecialchars($nombreProd) ?>" <?= ($oldProducto === $nombreProd) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($nombreProd) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
      </fieldset>

      <fieldset>
        <div class="grid-formulario">
          <div>
            <label class="label-campo" for="dosis">Dosis (mg por kg) *:</label>
            <input type="number" class="campo" id="dosis" name="dosis" min="0" max="1000" step="0.1"
              placeholder="Ej. 10.5" required value="<?= $oldDosis ?>">
          </div>

           <div>
            <label class="label-campo" for="fecha">Próxima cita:</label>
            <input type="date" class="campo" id="fecha" name="fecha"
              min="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d', strtotime('+2 years')) ?>" value="<?= old('oldFecha') ?>">
          </div>
        </div>
      </fieldset>

      <fieldset>
          <div class="campo-completo">
            <label class="label-campo" for="observaciones">Observaciones:</label>
            <textarea class="campo" id="observaciones" name="observaciones" rows="3" maxlength="1000"
              placeholder="Notas u observaciones adicionales"><?= old('oldObservaciones') ?></textarea>
          </div>
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
