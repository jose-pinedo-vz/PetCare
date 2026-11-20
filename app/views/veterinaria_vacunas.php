<?php
$vacunas          = $vacunas ?? [];
$lista_vacunas = $lista_vacunas ?? [];
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
    <div class="migaja"><a href="veterinaria_Inicio.php" class="link-tenue">Veterinaria</a> / Vacunas</div>
    
    <div class="panel-inicio">
    <!-- Columna Izquierda -->
        <div class="columna-lateral">
          <div class="tarjeta"> <!-- Al darle clic en agregar te va a abrir un text ahi pones el nombre de la nueva vacuna y ya, despues veo como se hace eso-->
            <h2>Lista de Vacunas</h2>
            
            <table id="listaVacuna">
              <thead>
                <tr>
                  <th>Vacunas</th>
                  <th>Especie</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                <td>Pargovirus</td>
                <td>Aves</td>
                <td><button type="button" class="btn" id="editarVacuna">Editar</button> <button type="button" class="btn" id="eliminarVacuna">Eliminar</button></td>
                </tr>
                <?php foreach ($lista_vacunas as $v): ?>
                      <tr>
                        <td><?= htmlspecialchars($v['Vacuna']) ?></td>
                        <td><?= htmlspecialchars($v['Especie']) ?></td>
                        <td> <a class="btn" href="datosMascota.php?id_mascota=<?= urlencode((string)$m['id_mascota']) ?>"> Ver datos</a></td>
                      </tr>
                <?php endforeach; ?>
              </tbody>
            </table>                
          </div>
          <div class="tarjeta">
            <h2>Agregar vacuna</h2>
            <table id="Nueva Vacuna">
              <thead>
                <tr>
                  <th>Nombre</th>
                  <th>Especie</th>
                </tr>
              </thead>

              <tbody>
                <tr>
                  <td><input type="text" id="NuevaVacunaNombre" placeholder="Nombre de la vacuna" requiered></input></td>
                  <td><input type="text" id="NuevaVacunaEspecie" placeholder="Especie" requiered></input></td>
                </tr>
              </tbody>
            </table>
              <button type="button" class="btn">Agregar</button>
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
                      <th>Mascota</th>
                      <th>Fecha</th>
                      <th>Vacuna</th>
                      <th>Lote</th>
                      <th>Proxima aplicacion</th>
                    </tr>
                  </thead>
                  <tbody id="tablaMascotas">
                    <td>Free of lies</td>
                    <td>02/07/2026</td>
                    <td>Pargovirus</td>
                    <td>024024</td>
                    <td>11/11/2026</td>
                    <?php foreach ($vacunas as $v): ?>
                      <tr>
                        <td><?= htmlspecialchars($v['mascota']) ?></td>
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
                        <th>Mascota</th>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Producto</th>
                        <th>Dosis</th>
                      </tr>
                    </thead>
                    <tbody id="tablaDespara">
                    <tr> <!-- EJEMPLO -->
                    <td>Free of lies</td>
                    <td>02/07/2026</td>
                    <td>Lombrices</td>
                    <td>Desparasitanol Iboproxido</td>
                    <td>1/2 pastilla</td>
                    </tr>
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

<script>
document.getElementById('agregarVacuna').addEventListener('click', function() {
    document.getElementById('formularioVacuna').style.display = 'block';
});
</script>
