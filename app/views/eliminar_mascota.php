<?php
require_once __DIR__ . '/../models/Mascota.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id_mascota = (int)($_GET['id_mascota'] ?? $_GET['id'] ?? 0);
$mascota = $id_mascota > 0 ? Mascota::obtenerPorId($id_mascota) : null;

if ($mascota === null || (int)($mascota['esta_activo'] ?? 1) !== 1) {
    header('Location: mascotas.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veterinaria PetCare - Eliminar mascota</title>
  <link rel="stylesheet" href="css/estilos_base_v2.css">
</head>
<body>

  <div class="layout">

  <?php $paginaActiva = 'mascotas'; include __DIR__ . '/layout/sidebar.php'; ?>


  <main>
    <h1>Eliminar mascota</h1>
        <?php if (isset($_SESSION['error'])): ?>
      <div style="background: #ffe6e6; color: #d32f2f; padding: 12px 15px; border-radius: 6px; border: 1px solid #ffcdd2; margin-bottom: 20px; font-weight: bold;">
        <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
      </div>
    <?php endif; ?>
        <form action="../controllers/procesar_eliminar_mascotas.php" method="POST">

      <input type="hidden" id="id_mascota" name="id_mascota" value="<?= (int)$id_mascota ?>">

      <div class="tarjeta">

        <fieldset>
          <p>
            ¿Está seguro de que desea eliminar a la mascota
            <strong><?= htmlspecialchars((string)$mascota['nombre']) . ' (ID: ' . $id_mascota . ')' ?></strong>?
            Esta acción no se puede deshacer.
          </p>
        </fieldset>

        <div style="margin-top: 20px;">
          <button type="submit" class="btn">Eliminar mascota</button>
          <a class="btn" href="mascotas.php" style="background: #757575; margin-left: 8px;">Cancelar</a>
        </div>

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
