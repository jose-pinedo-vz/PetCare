<?php  require_once __DIR__ . '/../controllers/citas.php'; 
if (session_status() === PHP_SESSION_NONE)
{
  session_start();
} 

?>


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
          <button class="btn" id="btn_consultas">Consulta</button>
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
          <button class="btn" id="btn_consultas">Pagar</button>
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
  <title>Veterinaria PetCare - Clientes</title>
  <link rel="stylesheet" href="css/estilos_base.css">
  <script src="js/citas.js"></script>

</head>
<body>

  <header>
    <div class="marca">
      <img src="./img/logo3.svg" alt="Veterinaria PetCare">
    </div>
    <div class="sesion">

    </div>
  </header>

  <nav>
    <ul>
      <li><a href="mascotas.php">Mascotas</a></li>
      <li><a href="citas.php" class="activo">Citas</a></li>
      <li><a href="clientes.php">Clientes</a></li>
      <li><a href="empleados.php">Empleados</a></li>
      <li><a href="proveedores.php">Proveedores</a></li>

      <li><a href="#" class="deshabilitado">Inventario</a></li>
      <li><a href="#" class="deshabilitado">Ventas</a></li>
      <li><a href="#" class="deshabilitado">Servicios</a></li>
      <li><a href="#" class="deshabilitado">Adopción y venta</a></li>
      <li><a href="#" class="deshabilitado">Veterinaria</a></li>
      <li><a href="#" class="deshabilitado">Pagos</a></li>
      <li><a href="#" class="deshabilitado">Pagos con tarjeta</a></li>
    </ul>
  </nav>

  <main>
    <h1>Citas</h1>
    <div style="margin-bottom: 20px;">
      <a class="btn" href="agendarCita.php">+ Agendar cita</a>
    </div>
    
    <div class="contenedor_citas">
      <div class="boton_control">
        <button id="btn_active active" onclick="mostrarSeccion('espera', event)" class="btn">En espera</button>
        <button id="btn_active" onclick="mostrarSeccion('atendidas', event)" class="btn" style="background: #757575; margin-left: 8px;">Atendidas</button>
      </div>

      <div class="panel_contenido">
        <!-- Sección de espera  -->
        <div id="seccion_espera" class="seccion-panel">
          <h3>Citas en espera</h3> 
          <?php mostrarProximasCitas(); ?>
        </div>

        <!-- Sección de atendidos-->
        <div id="seccion_atendidos" class="seccion-panel oculto">
          <h3>Nombre del dueño</h3>
          <?php mostrarCitasAtendidas(); ?>
        </div>
         
      </div>
    </div>
  </main>

  <footer>
    <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
  </footer>

</body>
</html>
