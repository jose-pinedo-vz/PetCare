<?php
require_once __DIR__ . '/../controllers/veterinaria_vacunas.php';

$lista_vacunas=listado_Vacunas()['listaVacunas'];

$vacunas          = $vacunas ?? [];
$lista_vacunas = $lista_vacunas ?? [];
$especies = listado_Especies();

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

      <?php if (isset($_SESSION['exito'])): ?>
          <p><?= htmlspecialchars($_SESSION['exito']) ?></p>
          <?php unset($_SESSION['exito']); ?>
      <?php endif; ?>
      
      <?php if (isset($_SESSION['error'])): ?>
          <p><?= htmlspecialchars($_SESSION['error']) ?></p>
          <?php unset($_SESSION['error']); ?>
      <?php endif; ?>

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
                <?php foreach ($lista_vacunas as $v): ?>
                      <tr>
                        <td><?= htmlspecialchars($v['nombre']) ?></td>
                        <td><?= htmlspecialchars($v['especie']) ?></td>
                        <td> 
                          <button type="button" class="btn" onclick='abrirEditar(<?= (int)$v["id"] ?>,<?= json_encode($v["nombre"], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP) ?>,<?= (int)$v["especie"] ?>)'>Editar</button>
                          <button type="button" class="btn" onclick='abrirEliminar(<?= (int)$v["id"] ?>,<?= json_encode($v["nombre"], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP) ?>)'>Eliminar</button>
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

    <form id="formAgregar" method="POST" action="../controllers/veterinaria_vacunas.php">
      <input type="hidden" name="accion" value="agregarVacuna">

      <label class="label-campo">Nombre:</label>
      <input type="text" class="campo" id="nombreAgregar" name="nombre" maxlength="50" placeholder="Nombre de la vacuna" required>

      <label class="label-campo">Especie:</label>
      <select class="campo" id="especieAgregar" name="especie" required>
          <option value="" selected disabled>Elige una especie</option>

          <?php foreach ($especies as $e): ?>
            <option value="<?= (int)$e['id'] ?>">
                <?= htmlspecialchars($e['nombre']) ?>
            </option>
          <?php endforeach; ?>
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

    <form id="formEditar" method="POST" action="../controllers/veterinaria_vacunas.php">
      <input type="hidden" name="accion" value="editarVacuna">
      <input type="hidden" name="id" id="idEditar">

      <label class="label-campo">Nombre:</label>
      <input type="text" class="campo" id="nombreEditar" name="nombre" required>

      <label class="label-campo">Especie:</label>
      <select class="campo" id="especieEditar" name="especie" required>
          <option value="" selected disabled>Elige una especie</option>
          <?php foreach ($especies as $e): ?>
              <option value="<?= (int)$e['id'] ?>">
                  <?= htmlspecialchars($e['nombre']) ?>
              </option>
          <?php endforeach; ?>
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
  
  <form method="POST" action="../controllers/veterinaria_vacunas.php">
    <input type="hidden" name="accion" value="eliminarVacuna">
    <input type="hidden" name="id" id="idEliminar">
    <div class="inventario-dialog-acciones">
      <button type="button" class="btn-secundario" onclick="cerrarEliminar()">No</button>
      <button type="submit" class="btn btn-eliminar">Sí, eliminar</button>
    </div>
  </form>
</dialog>





<script>
//agregar
function abrirAgregar() {
    document.getElementById('formAgregar').reset();
    document.getElementById('modalAgregar').showModal();
}

function cerrarAgregar() {document.getElementById('modalAgregar').close();}


// editar
function abrirEditar(id, nombre, especie) {
    document.getElementById('idEditar').value = id;
    document.getElementById('nombreEditar').value = nombre;
    document.getElementById('especieEditar').value = especie;

    document.getElementById('modalEditar').showModal();
}

function cerrarEditar() {document.getElementById('modalEditar').close();}


//eliminar
function abrirEliminar(id, nombre) {
    document.getElementById('idEliminar').value = id;
    document.getElementById('vacunaEliminar').textContent = nombre;

    document.getElementById('modalEliminar').showModal();
}

function cerrarEliminar() {
    document.getElementById('modalEliminar').close();
}
</script>

</body>
</html>
