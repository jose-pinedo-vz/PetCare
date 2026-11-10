<?php
$mascotas = $mascotas ?? [];
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
        <a href="veterinaria_mascota.php" class="resumen-item">
          <span class="resumen-titulo">Detalles Mascotas</span>
          <span class="resumen-cifra" id="detalleMascota"></span>
        </a>
        <a href="citas.php" class="resumen-item">
          <span class="resumen-titulo">Citas</span>
          <span class="resumen-cifra" id="citas"></span>
        </a>
        <a href="veterinaria_vacunas.php" class="resumen-item">
          <span class="resumen-titulo">Vacunas</span>
          <span class="resumen-cifra" id="vacunas"></span>
        </a>
      </div>

      <div class="panel-inicio">
        <div class="tarjeta columna-principal">
          <h2>Citas de hoy</h2>
          <div class="tabla-envoltura">
            <table>
              <thead>
                <tr>
                  <th>Fecha</th>
                  <th>Dueño</th>
                  <th>Motivo</th>
                  <th>Estado</th>
                </tr>
              </thead>
            </table>
            <tbody id="tablaMascotas">
              <?php foreach ($mascotas as $m): ?>
                <tr>
                  <td><?= htmlspecialchars($m['nombre']) ?></td>
                  <td><?= htmlspecialchars($m['especie']) ?></td>
                  <td><?= htmlspecialchars($m['raza']) ?></td>
                  <td><?= htmlspecialchars($m['dueno']) ?></td>
                  <td> <a class="btn" href="datosMascota.php?id_mascota=<?= urlencode((string)$m['id_mascota']) ?>"> Ver datos</a></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </div>
        </div>

        <div class="columna-lateral">
          <div class="tarjeta">
            <h2>Accesos rápidos</h2>
            <div class="lista-accesos">
              <a class="btn" href="vacuna.php">Nueva vacuna</a>
              <a class="btn" href="nose.php">Nueva desparasitación</a>
              <a class="btn" href="citas.php">Nueva consulta</a>
              <a class="btn" href="nose.php">Compartir acceso temporal</a>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>

</body>
</html>