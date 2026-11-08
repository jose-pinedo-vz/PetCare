<?php  require_once __DIR__ . '/../controllers/citas.php';
if (session_status() === PHP_SESSION_NONE)
{
  session_start();
}

?>


<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veterinaria PetCare - Citas</title>
  <link rel="stylesheet" href="css/estilos_base_v2.css">
  <script src="js/citas.js"></script>
</head>

<body>

  <header>
    </div>
  </header>
  <div class="layout">

    <?php $paginaActiva = 'citas';
    include __DIR__ . '/layout/sidebar.php'; ?>

    
  <main>
    
    <div id="contenedor-modal"></div>
    <div id="mainAOcultar">

        <div class="migaja">Gestión de citas /</div>
        <div class="encabezado-pagina">
          <h1>Citas</h1>
        </div>

        <div class="tarjeta">
      
        <div class="acciones-formulario">
            <button type="button" class="btn-secundario" onclick="window.location.reload()">Regresar</button>
            <button type="button" class="btn" onclick="ejecutarFetchCancelacion(${idCita})">Contirunar</button>
        </div>

      <div class="contenedor_citas">
      </div>
    
        </div>
      </div>
    </main>

  </div>

  <footer>
    <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
  </footer>

</body>

</html>