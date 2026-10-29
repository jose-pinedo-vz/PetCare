<?php
// Validar si realmente llegaron los datos por la URL (GET)
if (!isset($_GET['id']) || !isset($_GET['razon_social'])) {
    // Si alguien entra por la fuerza sin ID, lo mandamos de regreso a la tabla
    header("Location: proveedores.php");
    exit();
}
// Mostrar nombre del usuario
if (session_status() === PHP_SESSION_NONE)
{
    session_start();
}
if (!isset($_SESSION['usuario']))
{
  header("Location: ../views/login.php");
  exit();
}
$usuario=$_SESSION['usuario'];
// Mostrar nombre del usuario
// Si sí llegaron, los guardamos con seguridad
$id_proveedor = (int) $_GET['id'];
$razon_social = $_GET['razon_social'];
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veterinaria PetCare - Eliminar proveedor</title>
  <link rel="stylesheet" href="css/estilos_base_v2.css">
</head>

<body>

  <div class="layout">

  <?php $paginaActiva = 'proveedores'; include __DIR__ . '/layout/sidebar.php'; ?>

    <main>
      <div class="migaja"><a href="proveedores.php" class="link-tenue">Gestión de proveedores</a> / Eliminar</div>
      <div class="encabezado-pagina">
        <h1>Eliminar proveedor</h1>
      </div>

    <form action="../controllers/eliminar_proveedor.php" method="POST">
  
      <input type="hidden" id="id_proveedor" name="id_proveedor" value="<?= (int)$id_proveedor ?>">

      <div class="tarjeta"> 

        <fieldset>
          <p>
            ¿Está seguro de que desea eliminar al proveedor
            <strong><?= htmlspecialchars((string)$razon_social) . ' (ID: ' . htmlspecialchars((string)$id_proveedor) . ')' ?></strong>?
            Esta acción no se puede deshacer.
          </p>

        </fieldset>

        <div class="acciones-formulario">
          <a class="btn-secundario" href="proveedores.php">Cancelar</a>
          <button type="submit">Eliminar</button>
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
