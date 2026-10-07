<?php  require_once __DIR__ . '/../controllers/citas.php'; ?>


<!-- se encrga de mostrar todas las citas pendientes -->
<?php
  // unicamente llama a funciones dentro de cotroller, de ahi saca los datos 
  // y los muestra dentro de otro div
  function mostrarProximasCitas()
  {
    $datos = estraerDatosConsultas();
    // echo $datos[0]['atendido'];
    foreach ($datos as $d) 
    {
      if ($d['atendido'] === 0)
      {?>
        <div class="Card_cita">
          <p><strong>Nombre del dueño:</strong> <?php echo $d['nombre']; ?> </p>
          <p><strong>Fecha:</strong> <?php echo $d['fecha']; ?> </p>
          <p><strong>Motivo de consulta:</strong><?php echo "<br>".$d['motivo']; ?></p>
          <button class="btn" type="button">Consulta</button>
        </div>
      <?php }
    } 
  } 
?>

<!-- se encrga de mostrar todas las citas ya concretadas pero pendientes de pago -->
<?php
  // unicamente llama a funciones dentro de cotroller, de ahi saca los datos 
  // y los muestra dentro de otro div
  function mostrarCitasAtendidas()
  {
    $datos = estraerDatosConsultas();

    foreach($datos as $d)
    {
      if ($d['atendido'] === 1)
      {?>
        <div class="Card_cita">
          <p><strong>Paciente: </strong> <?php echo $d['nombre']; ?> </p>
          <p><strong>Fecha: </strong> <?php echo $d['fecha']; ?> </p>
          <p><strong>Motivo de consulta: </strong> <?php echo "<br>".$d['motivo']; ?> </p>
          <button class="btn" type="button">Pagar</button>
        </div>
      <?php }
    } 
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

  <div class="layout">

  <?php $paginaActiva = 'citas'; include __DIR__ . '/layout/sidebar.php'; ?>


  <main>
      <div class="migaja">Gestión de Citas /</div>
      <div class="encabezado-pagina">
        <h1>Citas</h1>
      </div>

      <div class="tarjeta">
    
    <div class="barra-acciones">
      <a class="btn" href="agendarCita.php">+ Agendar cita</a>
    </div>
    
    <div class="contenedor_citas">
      <div class="boton_control">
        <button type="button" onclick="mostrarSeccion('espera', event)" class="btn btn_active active">En espera</button>
        <button type="button" onclick="mostrarSeccion('atendidas', event)" class="btn btn_active">Atendidas</button>
      </div>

      <div class="panel_contenido">
        <!-- Sección de espera  -->
        <div id="seccion_espera" class="seccion-panel">
          <h3>Citas en espera</h3> 
          <?php mostrarProximasCitas(); ?>
        </div>

        <!-- Sección de atendidos-->
        <div id="seccion_atendidos" class="seccion-panel oculto">
          <h3>Citas atendidas (pendientes de pago)</h3>
          <?php mostrarCitasAtendidas(); ?>
        </div>
         
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