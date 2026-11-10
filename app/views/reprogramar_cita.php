<?php  require_once __DIR__ . '/../controllers/citas.php';
  if (session_status() === PHP_SESSION_NONE)
  {
    session_start();
  }

    function responsable()
    {
        $idConsulta = $_GET['id_consulta'] ?? null;
        $persona = obtenerNombreResponsable($idConsulta);

        if ($persona) {
            echo '<option value="' . htmlspecialchars($persona) . '" selected>' . htmlspecialchars($persona) . '</option>';
        } else {
            echo '<option value="">Sin responsable asignado</option>';
        }
    }

  if ($_SERVER["REQUEST_METHOD"] === "POST" && (($_GET['accion'] ?? '') === 'guardar_reprogramacion')) {
  
    $idConsulta = $_POST['id_consulta'] ?? null;
    $nuevaFecha = $_POST['reprogramar_fecha'] ?? null;
    $responsable = $_POST['responsable_reprogramar'] ?? null;
    $nuevoHorario = $_POST['reprogramarHorario'] ?? null;
    actualizarConsulta($idConsulta, $nuevaFecha, $responsable, $nuevoHorario);
  }
?>


<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veterinaria PetCare - Citas</title>
  <link rel="stylesheet" href="css/estilos_base_v2.css">
  <script src="js/agendarCitas.js"></script>
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

        <?php if (isset($_SESSION['error'])): ?>
          <div class="msg-error">
            <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
          </div>
        <?php endif; ?>

      <form action="reprogramar_cita.php?id_consulta=<?php echo htmlspecialchars($_GET['id_consulta'] ?? ''); ?>&accion=guardar_reprogramacion" method="POST">
          <input type="hidden" name="id_consulta" value="<?php echo htmlspecialchars($_GET['id_consulta'] ?? ''); ?>">
        <div>
            <label class="label-campo" for="fecha">Fecha *:</label>
            <input type="date" class="campo" id="reprogramar_fecha" name="reprogramar_fecha" data-fecha="reprogramar_fecha" required
              value="<?php echo htmlspecialchars((string)($_SESSION['oldFecha'] ?? '')); unset($_SESSION['oldFecha']); ?>">
        </div>

        <div>
            <label class="label-campo" for="responsable">Responsable *:</label>
            <select class="campo" name="responsable_reprogramar" id="responsable_reprogramar" required>
              

              <?php 
                $valorOldResponsable = $_SESSION['oldResponsable'] ?? '';
                

                if (!empty($valorOldResponsable))
                { ?> 
                  <option value="<?php echo htmlspecialchars((int)$valorOldResponsable); ?>" selected>
                    <?php echo htmlspecialchars((string)$valorOldResponsable); ?>
                  </option>
                <?php } 
                else 
                  { responsable(); } 
                ?>
            </select>
          </div>


        <div>
            <label class="label-campo" for="horarioConsulta">Horario de consulta *:</label>

            <select class="campo" name="reprogramarHorario" id="reprogramarHorario" required>
              <?php 
                $valorOldHorario = $_SESSION['oldHorario'] ?? '';
                unset($_SESSION['oldHorario']); 
                if (!empty($valorOldHorario))
                { ?> 
                  <option value="<?php echo htmlspecialchars((string)$valorOldHorario); ?>" selected>
                    <?php echo htmlspecialchars($valorOldHorario); ?>
                  </option>
                <?php } 
                else 
                  { ?>
                    <option value="">Seleccione una fecha primero</option>
                  <?php } 
                ?>
            </select>
          </div>

        <div class="acciones-formulario">
            <button type="button" class="btn-secundario">Regresar</button>
            <button type="submit" class="btn">Contirunar</button>
        </div>

      <div class="contenedor_citas">
      </div>
    

      </form>
        </div>
      </div>
    </main>

  </div>

  <footer>
    <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
  </footer>

</body>

</html>