<?php
$vacunas          = $vacunas ?? [];
$desparasitaciones = $desparasitaciones ?? [];
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

          <div class="tarjeta">
            <span>Vacunas</span>
            <span></br>-</span>
            <span></br>-</span>
            <span></br>-</span>
            <span></br>-</span>
          </div>
        </div>


      <!-- Columna Derecha -->
        <div class="columna-principal">
            <div class="tarjeta">
              <h2>Historial de vacunas</h2>
              <div class="tabla-envoltura">
                <table>
                  <thead>
                    <tr>
                      <th>Fecha</th>
                      <th>Vacuna</th>
                      <th>Lote</th>
                      <th>Veterinario</th>
                    </tr>
                  </thead>
                </table>
                <tbody id="tablaMascotas">
                  <?php foreach ($vacunas as $v): ?>
                    <tr>
                      <td><?= htmlspecialchars($v['nombre']) ?></td>
                      <td><?= htmlspecialchars($v['especie']) ?></td>
                      <td><?= htmlspecialchars($v['raza']) ?></td>
                      <td><?= htmlspecialchars($v['dueno']) ?></td>
                      <td> <a class="btn" href="datosMascota.php?id_mascota=<?= urlencode((string)$m['id_mascota']) ?>"> Ver datos</a></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </div>
            </div>

            <div class="tarjeta">   
                <h2>Historial de desparacitaciones</h2>
                <div class="tabla-envoltura">
                  <table>
                    <thead>
                      <tr>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Producto</th>
                        <th>Veterinario</th>
                      </tr>
                    </thead>
                  </table>
                  <tbody id="tablaDespara">
                    <?php foreach ($desparasitaciones as $d): ?>
                      <tr>
                        <td><?= htmlspecialchars($d['nombre']) ?></td>
                        <td><?= htmlspecialchars($d['especie']) ?></td>
                        <td><?= htmlspecialchars($d['raza']) ?></td>
                        <td><?= htmlspecialchars($d['dueno']) ?></td>
                        <td> <a class="btn" href="datosMascota.php?id_mascota=<?= urlencode((string)$m['id_mascota']) ?>"> Ver datos</a></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </div>
            </div>
        </div>
    </div>
    </main>
</div>

</body>
</html>
