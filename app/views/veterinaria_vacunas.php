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
                  <td>Parvovirus</td>
                  <td>Perros</td>
                  <td> 
                    <button type="button" class="btn" onclick="abrirEditar('Parvovirus','Perros')">Editar</button> 
                    <button type="button" class="btn" onclick="abrirEliminar('Parvovirus')"> Eliminar</button>
                  </td>
                </tr>
                <?php foreach ($lista_vacunas as $v): ?>
                      <tr>
                        <td><?= htmlspecialchars($v['Vacuna']) ?></td>
                        <td><?= htmlspecialchars($v['Especie']) ?></td>
                        <td> 
                          <button type="button" class="btn" onclick='abrirEditar(<?= json_encode($v["Vacuna"]) ?>,<?= json_encode($v["Especie"]) ?>)'>Editar</button> 
                          <button type="button" class="btn" onclick='abrirEliminar(     <?= json_encode($v["Vacuna"]) ?> )'> Eliminar</button>
                        </td>
                      </tr>
                <?php endforeach; ?>
              </tbody>
            </table>  
            <button type="button" class="btn" onclick="abrirAgregar()">Agregar vacuna</button>              
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
                    <tr>
                      <td>Free of lies</td>
                      <td>02/07/2026</td>
                      <td>Pargovirus</td>
                      <td>024024</td>
                      <td>11/11/2026</td>
                    </tr>
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


<!-- Modal para agregar -->
<dialog id="modalAgregar" class="inventario-dialog">

    <h3>Nueva vacuna</h3>

    <form id="formAgregar">
      <label class="label-campo">Nombre:</label>
      <input type="text" class="campo" id="nombreAgregar" placeholder="Nombre de la vacuna" required>

      <label class="label-campo">Especie:</label>
      <select class="campo" id="especieAgregar" required>
          <option value="" selected disabled>Elige una especie</option>
      </select>

      <div class="inventario-dialog-acciones">
        <button type="button" class="btn-secundario" onclick="cerrarAgregar()">Cancelar</button>
        <button type="submit" class="btn">Guardar vacuna</button>
      </div>
    </form>
</dialog>


<!-- Modal para editar -->
<dialog id="modalEditar" class="inventario-dialog">

    <h2>Editar vacuna</h2>

    <form id="formEditar">
      <label class="label-campo">Nombre:</label>
      <input type="text" class="campo" id="nombreEditar" required>

      <label class="label-campo">Especie:</label>
      <select class="campo" id="especieEditar" required>
          <option value="" selected disabled>Elige una especie</option>
      </select>

      <div class="inventario-dialog-acciones">
        <button type="button" class="btn-secundario" onclick="cerrarEditar()"> Cancelar </button>
        <button type="submit" class="btn">Guardar cambios</button>
      </div>
    </form>
</dialog>


<!-- Modal para eliminar -->
<dialog id="modalEliminar" class="inventario-dialog">
  <h2>Eliminar vacuna</h2>
  <div class="confirmacion">
    <p>¿Seguro que deseas eliminar esta vacuna?</p>
    <strong id="vacunaEliminar"></strong>
  </div>
  
  <div class="inventario-dialog-acciones">
      <button type="button" class="btn-secundario" onclick="cerrarEliminar()">No</button>
      <button type="button" class="btn btn-eliminar" id="confirmarEliminar">Sí, eliminar</button>
  </div>
</dialog>

</body>
</html>

<script>
//agregar
function abrirAgregar() {
    document.getElementById('formAgregar').reset();
    document.getElementById('modalAgregar').showModal();
}

function cerrarAgregar() {document.getElementById('modalAgregar').close();}


// editar
function abrirEditar(nombre, especie) {
    document.getElementById('nombreEditar').value = nombre;
    document.getElementById('especieEditar').value = especie;

    document.getElementById('modalEditar').showModal();
}

function cerrarEditar() {document.getElementById('modalEditar').close();}


//eliminar
function abrirEliminar(nombre) {
    document.getElementById('vacunaEliminar').textContent = nombre;

    document.getElementById('modalEliminar').showModal();
}

function cerrarEliminar() {
    document.getElementById('modalEliminar').close();
}


// EVITAR ENVÍOS HASTA CONECTAR PHP
document.getElementById('formAgregar')
    .addEventListener('submit', function(e) {
        e.preventDefault();
        alert('Pendiente conectar con la base de datos');
    });

document.getElementById('formEditar')
    .addEventListener('submit', function(e) {
        e.preventDefault();
        alert('Pendiente conectar con la base de datos');
    });

document.getElementById('confirmarEliminar')
    .addEventListener('click', function() {
        alert('Pendiente conectar con la base de datos');
    });
</script>