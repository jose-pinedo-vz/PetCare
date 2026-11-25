<?php
require_once __DIR__ . '/../controllers/veterinaria_vacunas.php';

$lista_vacunas=listado_Vacunas()['listaVacunas'];

/* $vacunas = $vacunas ?? []; */
$vacunas = listado_HistorialVacunas();
$lista_vacunas = $lista_vacunas ?? [];
$especies = listado_Especies();

$desparasitaciones = historialDesparasitaciones();
$catalogoVacunas = $catalogoVacunas ?? [];


$modalError = $_SESSION['modalError'] ?? '';
$mensajeError = $_SESSION['error'] ?? '';
$oldNombre = $_SESSION['oldNombre_vacunas'] ?? '';
$oldEspecie = $_SESSION['oldEspecie_vacunas'] ?? '';
$oldId = $_SESSION['oldId_vacunas'] ?? '';

unset(
    $_SESSION['modalError'],
    $_SESSION['error'],
    $_SESSION['oldNombre_vacunas'],
    $_SESSION['oldEspecie_vacunas'],
    $_SESSION['oldId_vacunas']
);

$nombreEliminar = '';
if ($modalError === 'eliminar') {
    foreach ($lista_vacunas as $v) {
        if ((string)$v['id'] === (string)$oldId) {
            $nombreEliminar = $v['nombre'];
            break;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veterinaria PetCare - Vacunas</title>
  <link rel="stylesheet" href="css/estilos_base_v2.css">

  <style>
    #modalAgregar input.campo,
    #modalEditar input.campo {
      margin-bottom: 14px;
    }

    .columna-lateral .tarjeta > .btn {
      margin-top: 12px;
      align-self: flex-start;
      flex-shrink: 0;
    }
    /* Espacio entre las dos tarjetas de historial */
  .columna-principal {
    display: flex;
    flex-direction: column;
    gap: 16px;
  }

  /* Cada tabla tiene su propio scroll y el encabezado se queda fijo */
  .tarjeta .tabla-envoltura {
    overflow-y: auto;
  }

  .tarjeta .tabla-envoltura thead th {
    position: sticky;
    top: 0;
    z-index: 1;
  }

  /* Escritorio: la pantalla ocupa exactamente el alto de la ventana */
  @media (min-width: 769px) {
    main {
      display: flex;
      flex-direction: column;
      height: 100vh;
      box-sizing: border-box;
      overflow-y: auto;
    }

    main .panel-inicio {
      flex: 1;
      min-height: 0;
      align-items: stretch;
    }

    .columna-lateral,
    .columna-principal {
      min-height: 0;
    }

    /* Todas las tarjetas se reparten el alto disponible */
    .columna-lateral .tarjeta,
    .columna-principal .tarjeta {
      flex: 1 1 0;
      min-height: 0;
      display: flex;
      flex-direction: column;
    }

    .tarjeta .tabla-envoltura {
      flex: 1;
      min-height: 0;
    }
  }

  /* Celular: sin alto fijo, pero con tope para que las tablas no se alarguen */
  @media (max-width: 768px) {
    .tarjeta .tabla-envoltura {
      max-height: 360px;
    }
  }
  </style>
</head>

<body>
<div class="layout">

  <?php $paginaActiva = 'veterinaria'; include __DIR__ . '/layout/sidebar.php'; ?>

    <main>
    <div class="migaja"><a href="veterinaria_Inicio.php" class="link-tenue">Veterinaria</a> / Vacunas</div>
    
    <div class="panel-inicio">
    <!-- Columna Izquierda -->
        <div class="columna-lateral">
          <div class="tarjeta"> <!-- Al darle clic en agregar te va a abrir un text ahi pones el nombre de la nueva vacuna y ya, despues veo como se hace eso-->
            <h2>Lista de Vacunas</h2>
            
            <div class="tabla-envoltura">
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
                            <td><?= htmlspecialchars($v['nombre_especie']) ?></td>
                            <td> 
                              <button type="button" class="btn" onclick='abrirEditar(<?= (int)$v["id"] ?>,<?= json_encode($v["nombre"], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP) ?>,<?= (int)$v["especie"] ?>)'>Editar</button>
                              <button type="button" class="btn" onclick='abrirEliminar(<?= (int)$v["id"] ?>,<?= json_encode($v["nombre"] . " (" . ($v["nombre_especie"] ?? "") . ")", JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP) ?>)'>Eliminar</button>
                            </td>
                          </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>  
            </div>
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
                      <th>Proxima aplicacion</th>
                    </tr>
                  </thead>
                  <tbody id="tablaMascotas">
                    <?php if (count($vacunas) > 0) : ?>
                        <?php foreach ($vacunas as $v): ?>
                            <tr>
                                <td><?= htmlspecialchars($v['mascota']) ?></td>
                                <td><?= !empty($v['fecha_aplicacion']) ? htmlspecialchars((string)$v['fecha_aplicacion']) : 'Sin fecha' ?></td>
                                <td><?= htmlspecialchars($v['tipo_vacunas']) ?></td>
                                <td><?= !empty($v['fecha_proxima']) ? htmlspecialchars((string)$v['fecha_proxima']) : 'Sin fecha' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center;">
                                No hay vacunas registradas
                            </td>
                        </tr>
                    <?php endif; ?>
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
                        <th>Proxima aplicacion</th>
                      </tr>
                    </thead>
                    <tbody id="tablaDespara">
                      <?php if (count($desparasitaciones) > 0): ?>
                        <?php foreach ($desparasitaciones as $d): ?>
                            <tr>
                              <td><?= htmlspecialchars($d['mascota']) ?></td>                        
                              <td><?= date('d/m/Y', strtotime($d['fecha'])) ?></td>                        
                              <td><?= htmlspecialchars($d['tipo']) ?></td>                        
                              <td><?= htmlspecialchars($d['producto_utilizado']) ?></td>                        
                              <td><?= htmlspecialchars((string)$d['dosis']) ?></td>                        
                              <td><?= date('d/m/Y', strtotime($d['proxima_aplicacion'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                      <?php else: ?>
                        <tr>
                          <td colspan="7" style="text-align: center;">
                            No hay desparasitaciones registradas
                          </td>
                        </tr>
                      <?php endif; ?>
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

    <h2>Nueva vacuna</h2>

    <form id="formAgregar" method="POST" action="../controllers/veterinaria_vacunas.php">
      <input type="hidden" name="accion" value="agregarVacuna">

    <?php if ($modalError === 'agregar' && $mensajeError !== ''): ?>
        <div class="msg-error"> <?php echo htmlspecialchars($mensajeError) ?></div>
    <?php endif; ?>

      <label class="label-campo">Nombre:</label>
          <input type="text" class="campo" id="nombreAgregar" name="nombre" maxlength="50" placeholder="Nombre de la vacuna" required value="<?php echo $modalError === 'agregar' ? htmlspecialchars($oldNombre) : '' ?>">

      <label class="label-campo">Especie:</label>
      <select class="campo" id="especieAgregar" name="especie" required>
          <option value="" selected disabled>Elige una especie</option>

          <?php foreach ($especies as $e): ?>
          <option value="<?= (int)$e['id'] ?>" <?php echo ($modalError === 'agregar' && (string)$oldEspecie === (string)$e['id']) ? 'selected' : '' ?>> 
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
      <input type="hidden" name="id" id="idEditar" value="<?php echo $modalError === 'editar' ? htmlspecialchars($oldId) : '' ?>">

      <?php if ($modalError === 'editar' && $mensajeError !== ''): ?>
        <div class="msg-error"> <?php echo htmlspecialchars($mensajeError) ?> </div>
      <?php endif; ?>

      <label class="label-campo">Nombre:</label>
          <input type="text" class="campo" id="nombreEditar" name="nombre" maxlength="50" value="<?php echo $modalError === 'editar' ? htmlspecialchars($oldNombre) : '' ?>" required>

      <label class="label-campo">Especie:</label>
      <select class="campo" id="especieEditar" name="especie" required>
          <option value="" selected disabled>Elige una especie</option>
          <?php foreach ($especies as $e): ?>
              <option value="<?= (int)$e['id'] ?>" <?php echo ($modalError === 'editar' && (string)$oldEspecie === (string)$e['id']) ? 'selected' : '' ?>>
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

<?php if ($modalError === 'eliminar' && $mensajeError !== ''):  ?>
    <div class="msg-error"> <?php echo htmlspecialchars($mensajeError) ?> </div>
<?php endif; ?>

  <div class="confirmacion">
    <p>¿Seguro que deseas eliminar esta vacuna?</p>
    <strong id="vacunaEliminar"> <?php echo htmlspecialchars($nombreEliminar)?> </strong>

  </div>
  
  <form method="POST" action="../controllers/veterinaria_vacunas.php">
    <input type="hidden" name="accion" value="eliminarVacuna">
    <input type="hidden" name="id" id="idEliminar" value="<?php echo $modalError === 'eliminar' ? htmlspecialchars($oldId) : '' ?>">
    <div class="inventario-dialog-acciones">
      <button type="button" class="btn-secundario" onclick="cerrarEliminar()">No</button>
      <button type="submit" class="btn btn-eliminar">Sí, eliminar</button>
    </div>
  </form>
</dialog>





<script>

function limpiarError(idModal) {
    const msj_error = document.querySelector('#' + idModal + ' .msg-error');
    if (msj_error) {
        msj_error.remove();
    }
}

//agregar
function abrirAgregar() {
    limpiarError('modalAgregar');
    document.getElementById('nombreAgregar').value = '';
    document.getElementById('especieAgregar').value = '';
    document.getElementById('modalAgregar').showModal();
}

function cerrarAgregar() {document.getElementById('modalAgregar').close();}


// editar
function abrirEditar(id, nombre, especie) {
    limpiarError('modalEditar');
    document.getElementById('idEditar').value = id;
    document.getElementById('nombreEditar').value = nombre;
    document.getElementById('especieEditar').value = especie;

    document.getElementById('modalEditar').showModal();
}

function cerrarEditar() {document.getElementById('modalEditar').close();}


//eliminar
function abrirEliminar(id, nombre) {
    limpiarError('modalEliminar');
    document.getElementById('idEliminar').value = id;
    document.getElementById('vacunaEliminar').textContent = nombre;

    document.getElementById('modalEliminar').showModal();
}

function cerrarEliminar() {
    document.getElementById('modalEliminar').close();
}

<?php if ($modalError === 'agregar'): ?>
    document.getElementById('modalAgregar').showModal();
<?php elseif ($modalError === 'editar'): ?>
    document.getElementById('modalEditar').showModal();
<?php elseif ($modalError === 'eliminar'): ?>
    document.getElementById('modalEliminar').showModal();
<?php endif; ?>

</script>

</body>
</html>
