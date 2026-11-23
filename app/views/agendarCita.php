<?php
require_once __DIR__ . '/../controllers/citas.php';
require_once __DIR__ . '/../models/PagosAdmin.php';
include __DIR__ . '/pagar_vents.php'; 

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
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veterinaria PetCare - Agendar cita</title>
  <link rel="stylesheet" href="css/estilos_base_v2.css">
  <script src="js/agendarCitas.js"></script>
</head>


<!-- <dialog id="modalAnticipo">
  <h2>Cobrar anticipo</h2>
  <p>Cita #<span id="antIdCita"></span> — Total: $<span id="antTotal"></span></p>
  <label>Método</label>
  <select id="antMetodo">
    <option value="efectivo">Efectivo</option>
    <option value="tarjeta">Tarjeta</option>
    <option value="transferencia">Transferencia</option>
  </select>
  <label>Monto</label>
  <input type="number" id="antMonto" step="0.01">
  <button type="button" id="btnConfirmarAnticipo">Confirmar</button>
  <button type="button" id="btnCerrarAnticipo">Cancelar</button>
</dialog> -->



<body>

  <div class="layout">
  <?php $paginaActiva = 'citas'; include __DIR__ . '/layout/sidebar.php'; ?>
  <main>
      <div class="migaja"><a href="citas.php" class="link-tenue">Gestión de citas</a> / Agendar</div>
      <div class="encabezado-pagina">
        <h1>Agendar cita</h1>
      </div>

      <div class="tarjeta">

    <!-- mensaje en caso de algun error o mostrar informaicon nesesaria -->
    <?php if (isset($_SESSION['error'])): ?>
      <div class="msg-error">
        <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
      </div>
    <?php endif; ?>


    <!-- <form id="formCita" action="../controllers/citas.php" method="POST"> -->
    <form action="../controllers/citas.php?accion=agendar_cita" method="POST">
      <fieldset>
        <div id="datosSesion" data-id-empleado="<?php echo htmlspecialchars((string)($_SESSION['id_empleado'] ?? $_SESSION['id_usuario'] ?? '')); ?>"></div>
        <legend>Datos de la cita</legend>

        <div class="grid-formulario">
          <div>
            <label class="label-campo" for="claveCliente">Clave del cliente *:</label>
            <select class="campo" name="cliente" id="cliente" required>
              <?php
              if (!empty($_SESSION['oldClave'])) {
                  $idOld = $_SESSION['oldClave'];
                  unset($_SESSION['oldClave']);

                  $res = MostrarnombreCliente($idOld);
                  $fila = $res[0] ?? [];
                  $textoOpcion = $idOld . " - " . ($fila['nombre'] ?? '') . " " . ($fila['apellido'] ?? '');
                  ?>
                  <option value="<?= htmlspecialchars((string)$idOld) ?>"><?= htmlspecialchars($textoOpcion) ?></option>
              <?php } else { ?>
                  <option value="">Seleccione un Cliente</option>
              <?php } ?>
          </select>

              
          </div>

          <div>
            <label class="label-campo" for="fecha">Mascota *:</label>
            <select class="campo" name="mascota" id="mascota" required>
              <?php 
                $valorOldMascota = $_SESSION['oldMascota'] ?? '';
                unset($_SESSION['oldMascota']);
                $mascota = consultarMascota((int)$valorOldMascota);
                $mascota = $mascota['nombre'];

                if (!empty($valorOldMascota))
                { ?> 
                  <option value="<?php echo htmlspecialchars((int)$valorOldMascota); ?>" selected>
                    <?php echo htmlspecialchars((string)$mascota); ?>
                  </option>
                <?php } 
                else 
                  { ?>
                    <option value="">Seleccione un cliente primero</option>
                  <?php } 
                ?>
            </select>
          </div>

          <div>
            <label class="label-campo" for="fecha">Fecha *:</label>
            <input type="date" class="campo" id="fecha" name="fecha" data-fecha="fecha" required
              min="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d', strtotime('+1 year')); ?>"
              value="<?php echo htmlspecialchars((string)($_SESSION['oldFecha'] ?? '')); unset($_SESSION['oldFecha']); ?>">
          </div>

          <div>
            <label class="label-campo" for="duracion">Duración estimada (minutos) *:</label>
            <input type="number" class="campo" id="duracion" name="duracion" required min="1" max="480" placeholder="Ej. 60" readonly
              value="<?php echo htmlspecialchars((string)($_SESSION['oldDuracion'] ?? '')); unset($_SESSION['oldDuracion']); ?>">
          </div>

          <div>
            <label class="label-campo" for="fecha">Servicio *:</label>
            <select class="campo" name="servicio" id="servicio" required>
              <?php 
                $valorOldServicio = $_SESSION['oldServicio'] ?? '';
                unset($_SESSION['oldServicio']); 
                // lo unico que tengo que hacer es sacar que servicio es el que me traigo
                $servicio = consultarServicio((int)$valorOldServicio);
                if (!empty($servicio) && !empty($valorOldServicio))
                { ?> 
                  <option value="<?php echo htmlspecialchars((string)$valorOldServicio); ?>" selected>
                    <?php echo htmlspecialchars($servicio['nombre']); ?>
                  </option>
                <?php } 
                else 
                  { ?>
                    <option value="">Seleccione un servicio</option>
                  <?php } 
                ?>
            </select>
          </div>

          <div class="campo-completo">
            <label class="label-campo" for="motivoConsulta">Observación *:</label>
            <input type="text" class="campo" id="motivoConsulta" name="motivoConsulta" placeholder="Ej. Revisión general, vacunación, malestar estomacal" required

              value="<?php echo htmlspecialchars((string)($_SESSION['oldMotivo'] ?? '')); unset($_SESSION['oldMotivo']); ?>">

          </div>

          <div>
            <label class="label-campo" for="responsable">Responsable *:</label>
            <select class="campo" name="responsable" id="responsable" required>
              <?php 
                $valorOldResponsable = $_SESSION['oldResponsable'] ?? '';
                unset($_SESSION['oldResponsable']); 
                $responsable = consultarResponsable((int)$valorOldResponsable);
                $responsable = $responsable['nombre'] . " " . $responsable['apellido'];

                if (!empty($valorOldResponsable))
                { ?> 
                  <option value="<?php echo htmlspecialchars((string)$valorOldResponsable); ?>" selected>
                    <?php echo htmlspecialchars($responsable); ?>
                  </option>
                <?php } 
                else 
                  { ?>
                    <option value="">Seleccione un responsable</option>
                  <?php } 
                ?>
            </select>
          </div>

          <div>
            <label class="label-campo" for="horarioConsulta">Horario de consulta *:</label>

            <select class="campo" name="horarioConsulta" id="horarioConsulta" required>
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
                    <option value="">Seleccione una fecha y responsable primero</option>
                  <?php } 
                ?>
            </select>
          </div>



        </div>
       <button type="button" class="btn btn-chico" id="btnAnticipo">Anticipo</button>
      </fieldset>

      <div class="acciones-formulario">
        <a class="btn btn-secundario btn-chico" href="citas.php">Cancelar</a>
        <button type="submit" class="btn btn-chico">Guardar cita</button>
        
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
