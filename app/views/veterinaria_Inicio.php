<?php
$citas = $citas ?? [];
?>



<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veterinaria PetCare - Inicio</title>
  <link rel="stylesheet" href="css/estilos_base_v2.css">
</head>

<body>

  <div class="layout">

  <?php $paginaActiva = 'Veterinaria'; include __DIR__ . '/layout/sidebar.php'; ?>

    <main>
      <div class="migaja">Veterinaria /</div>
      <div class="resumen">
        <div class="resumen-item">
        <a class="btn" href="veterinaria_mascota.php">
          Detalles Mascotas
        </a>
        </div>
        <div class="resumen-item">
        <a class="btn" href="citas.php">
          citas
        </a>
        </div>
        <div class="resumen-item">
        <a class="btn" href="veterinaria_vacunas.php" >
          vacunas
        </a>
      </div>
      </div>

      <div class="panel-inicio">
        <div class="tarjeta columna-principal">
          <h2>Citas de hoy</h2>
          <div class="tabla-envoltura">
            <table> <!-- En esta tabla se muestran las citas del veterinario, no se si eso tambien me tocaba a mi desde front, si fuera asi avisame porfas -->
              <thead>
                <tr>
                  <th>Fecha</th>
                  <th>Dueño</th>
                  <th>Motivo</th>
                  <th>Estado</th>
                  <th>Boton</th>
                </tr>
              </thead>
              <tbody id="tablaCitas">
                <tr>
                    <td>12:04 11/11/2011</td>
                    <td>Ramon</td> <!-- Vas a tener que buscar el nombre del cliente por id-->
                    <td>Tenia frio</td>
                    <td>Atendida</td> <!-- Es tinyint en bd, o sea, 1 es atendido y 0 es no-->
                    <td> <a class="btn" href="datosMascota.php?id_mascota=<?= urlencode((string)$c['id_mascota']) ?>"> Ver datos</a></td>
                  </tr>
                <?php foreach ($citas as $c): ?>
                  <tr>
                    <td><?= htmlspecialchars($c['fecha']) ?></td>
                    <td><?= htmlspecialchars($c['cliente']) ?></td>
                    <td><?= htmlspecialchars($c['motivo_consulta']) ?></td>
                    <td><?= (int)$c['atendido']===1 ? 'Atendida': 'Pendiente'?></td>
                    <td> <a class="btn" href="datosMascota.php?id_mascota=<?= urlencode((string)$c['id_mascota']) ?>"> Ver datos</a></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

        <div class="columna-lateral">
          <div class="tarjeta">
            <h2>Accesos rápidos</h2>
            <div class="lista-accesos">
              <a class="btn" href="citas.php">Nueva consulta</a>
              <a class="btn" href="citas.php">Acceder como otro veterinario</a> <!--Aqui mandara al historial de vacunas o al historial de citas, pero ese modulo debe de pedir un veterinario responsable-->
              <button type="button" class="btn" id="generarCodigo">Compartir acceso temporal</button> <!-- Al darle clic en este boton debe de aparecer en el text de abajo el codigo, si crees que es mejor por gmail me avisas para quitar el text y poner una mini interfaz para que le diga a que correo -->
              <input type="text" id="codigo" readonly>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>

</body>
</html>