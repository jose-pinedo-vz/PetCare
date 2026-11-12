<?php
$vacunas          = $vacunas ?? [];
$desparasitaciones = $desparasitaciones ?? [];
$catalogoVacunas = $catalogoVacunas ?? [];
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veterinaria PetCare - Vacunas</title>
  <link rel="stylesheet" href="css/estilos_base_v2.css">
</head>

<body>
<div class="layout">

  <?php $paginaActiva = 'Veterinaria'; include __DIR__ . '/layout/sidebar.php'; ?>

    <main>
    <div class="migaja"><a href="veterinaria_inicio.php" class="link-tenue">Veterinaria</a> / Datos de mascota</div>
    
    <div class="panel-inicio">
    <!-- Columna Izquierda -->
        <div class="columna-lateral">
          <div class=" tarjeta">
            <a href="#" class="resumen-item">
              <span class="resumen-titulo">Nueva vacuna</span>
              <span class="resumen-cifra" id="Nueva vacuna"></span>
            </a>
            <a href="#" class="resumen-item">
              <span class="resumen-titulo">Nueva desparasitación</span>
              <span class="resumen-cifra" id="Nueva desparacitacion"></span>
            </a>
          </div>

          <div class="tarjeta"> <!-- Al darle clic en agregar te va a abrir un text ahi pones el nombre de la nueva vacuna y ya, despues veo como se hace eso-->
            <h2>Vacunas</h2>
            <ul class="lista-vacunas" id="listaVacunas">
                <?php if (!empty($catalogoVacunas)): ?>
                    <?php foreach ($catalogoVacunas as $vacuna): ?>
                        <li>
                            <?= htmlspecialchars($vacuna['nombre']) ?>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li>No hay vacunas registradas</li>
                <?php endif; ?>
            </ul>
                
            <button type="button" class="btn" id="agregarVacuna">Agregar vacuna</button>
          </div>
        </div>


      <!-- Columna Derecha -->
        <div class="columna-principal">
            <div class="tarjeta">
              <h2>Historial de vacunas</h2> 
              <div class="tabla-envoltura">
                <table> <!-- Tabla de historial de vacunas -->
                  <thead>
                    <tr>
                      <th>Fecha</th>
                      <th>Vacuna</th>
                      <th>Lote</th>
                      <th>Proxima aplicacion</th>
                    </tr>
                  </thead>
                  <tbody id="tablaMascotas">
                    <?php foreach ($vacunas as $v): ?>
                      <tr>
                        <td><?= htmlspecialchars($v['fecha_aplicacion']) ?></td>
                        <td><?= htmlspecialchars($v['tipo_vacunas']) ?></td>
                        <td><?= htmlspecialchars($v['lote']) ?></td>
                        <td><?= htmlspecialchars($v['fecha_proxima']) ?></td>
                        <td> <a class="btn" href="datosMascota.php?id_mascota=<?= urlencode((string)$m['id_mascota']) ?>"> Ver datos</a></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="tarjeta">   
                <h2>Historial de desparacitaciones</h2>
                <div class="tabla-envoltura"> <!-- Tabla de desparacitaciones -->
                  <table>
                    <thead>
                      <tr>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Producto</th>
                        <th>Dosis</th>
                      </tr>
                    </thead>
                    <tbody id="tablaDespara">
                      <?php foreach ($desparasitaciones as $d): ?>
                        <tr>
                          <td><?= htmlspecialchars($d['fecha']) ?></td> <!--La fecha de aqui saldra de la tabla citas-->
                          <td><?= htmlspecialchars($d['tipo']) ?></td>
                          <td><?= htmlspecialchars($d['producto_utilizado']) ?></td>
                          <td><?= htmlspecialchars($d['dosis']) ?></td>
                          <td> <a class="btn" href="datosMascota.php?id_mascota=<?= urlencode((string)$m['id_mascota']) ?>"> Ver datos</a></td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
            </div>
        </div>
    </div>
    </main>
</div>

</body>
</html>
