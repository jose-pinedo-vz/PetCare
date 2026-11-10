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
      $estadocita = trim($d['estado_cita'] ?? '');
      if ($estadocita == "pendiente")
      {?>
        <tr data-nombre="<?php echo strtolower($d['nombre']); ?>" data-fecha="<?php echo strtolower($d['fecha']); ?>">
          <td><?php echo $d['nombre']; ?> </td>
          <td><?php echo $d['fecha']; ?> </td>
          <td><?php echo $d['motivo']; ?></td>
          <td><?php 
            $serv = consultarServicio($d['servicio']);
            echo htmlspecialchars($serv['nombre'] ?? 'Sin servicio'); 
          ?></td>

          <td>
            <a class="btn btn-chico" href="../controllers/FichaMedicaController.php?id_consulta=<?php echo urlencode((string)$d['id_consulta']); ?>">Consulta</a>
            <a class="btn btn-chico btn-peligro" onclick="cancelarCita(<?php echo $d['id_consulta']; ?>, event)">Cancelar</a>
            <a class="btn btn-chico btn-aviso btn-chico" href="reprogramar_cita.php?id_consulta=<?php echo urlencode((string)$d['id_consulta']); ?>">Reprogramar</a>
            <a class="btn btn-chico btn-peligro" onclick="cancelarCita(<?php echo $d['id_consulta']; ?>, event)">No asistio</a>

          </td>

        </tr>
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

    foreach ($datos as $d)
    {
      $estadocita = trim($d['estado_cita'] ?? '');
      if ($estadocita == "atendida")
      {?>
        <tr data-nombre="<?php echo strtolower($d['nombre']); ?>" data-fecha="<?php echo strtolower($d['fecha']); ?>">
          <td><?php echo $d['nombre']; ?> </td>
          <td><?php echo $d['fecha']; ?> </td>
          <td><?php echo $d['motivo']; ?></td>
          <td>
            
            
            <a class="btn">Pagar</a>
          </td>

        </tr>
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
      
      <div class="barra-acciones">
        <a class="btn" href="agendarCita.php">+ Agendar cita</a>
      </div>

      

      <div class="contenedor_citas">
        <!-- <div class="boton_control">
          <button type="button" onclick="mostrarSeccion('espera', event)" class="btn btn_active active">En espera</button>
          <button type="button" onclick="mostrarSeccion('atendidas', event)" class="btn btn_active">Atendidas</button>
        </div> -->
        <div></div>

        <style>
          .contenedor-busqueda {
            display: flex;
            align-items: center;
            gap: 15px; /* Espacio de separación entre cada elemento */
            width: 100%;
        }

        .contenedor-busqueda input.campo {
            flex: 1; /* Hace que el campo de texto crezca y ocupe el espacio del centro */
        }
        </style>


        <div class="contenedor-busqueda">
          <h3>Buscar</h3>
          <input type="text" class="campo" id="buscarNombre" name="motivoConsulta" placeholder="Nombre Ej. Juan Perez">
          <select class="select" id="fecha">
            <option value="todos">Todas</option>
            <option value="hoy">Hoy</option>
            <option value="semana">Esta semana</option>
            <option value="mes">Este mes</option>
          </select>
      </div>
        

        <div class="panel_contenido">
          <!-- Sección de espera  -->
          

          <div class="contenedor_citas">
            <div class="boton_control">
              <button type="button" onclick="mostrarSeccion('espera', event)" class="btn btn_active active btn-chico">En espera</button>
              <button type="button" onclick="mostrarSeccion('atendidas', event)" class="btn btn_active btn-chico">Atendidas</button>
            </div>

            <div id="seccion_espera" class="seccion-panel">
              <h3>Citas en espera</h3>
              <div class="tabla-envoltura"> 
                <table>
                  <thead>
                    <tr>
                      <th>Nombre del dueño:</th>
                      <th>Fecha:</th>
                      <th>Motivo de consulta:</th>
                      <th>Servicio:</th>
                      <th>Acciones:</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php mostrarProximasCitas(); ?>
                  </tbody>
                </table>
              </div>
              
            </div>
              
              <!-- Sección de atendidos-->
              <div id="seccion_atendidos" class="seccion-panel oculto">
                <h3>Citas atendidas (pendientes de pago)</h3>
                <div class="tabla-envoltura"> 
                <table>
                  <thead>
                    <tr>
                      <th>Nombre del dueño:</th>
                      <th>Fecha:</th>
                      <th>Motivo de consulta:</th>
                      <th>Servicio:</th>
                      <th>Acciones:</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php mostrarCitasAtendidas(); ?>
                  </tbody>
                </table>
              </div>
              </div>

            </div>
          </div>

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