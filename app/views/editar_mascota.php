<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/Mascota.php';
require_once __DIR__ . '/../models/Cliente.php';


$idMascota = filter_input(INPUT_GET, 'id_mascota', FILTER_VALIDATE_INT);

if ($idMascota === false || $idMascota === null) {
    header('Location: mascotas.php');
    exit();
}

$mascota = obtenerMascotaPorId($idMascota);

if (!$mascota) {
    header('Location: mascotas.php');
    exit();
}

$errores = $_SESSION['errores'] ?? [];
$old     = $_SESSION['old'] ?? [];

unset($_SESSION['errores']);
unset($_SESSION['old']);

// actualizar_mascota.php tambien puede mandar los errores por ?error=a | b | c
if (!empty($_GET['error'])) {
    foreach (explode(' | ', (string)$_GET['error']) as $msg) {
        $errores[] = $msg;
    }
}

// Valor del formulario: lo que se escribio antes del error o, si no hay, lo de la BD
function dato(array $old, array $mascota, string $campo): string {
    $valor = array_key_exists($campo, $old) ? $old[$campo] : ($mascota[$campo] ?? '');
    return htmlspecialchars((string)($valor ?? ''));
}

$sexoActual = $old['sexo'] ?? $mascota['sexo'] ?? '';
$tamActual  = $old['tamanio'] ?? $mascota['tamanio'] ?? '';
$activo     = (int)($old['esta_activo'] ?? $mascota['esta_activo'] ?? 1) === 1;

$coloresLista = [
    'Blanco', 'Negro', 'Gris', 'Café', 'Amarillo', 'Verde', 'Rojo',
    'Blanco y negro', 'Café y blanco', 'Café y amarillo', 'Blanco y gris',
    'Verde y amarillo', 'Blanco, amarillo y negro'
];

$colorActual      = (string)($old['color'] ?? $mascota['color'] ?? '');
$colorEspecActual = (string)($old['color_espec'] ?? '');

if ($colorActual !== '' && $colorActual !== 'Otro' && !in_array($colorActual, $coloresLista, true)) {
    $colorEspecActual = $colorActual;
    $colorActual      = 'Otro';
}


$clientes = Cliente::obtenerTodos();
$veterinarios = Mascota::obtenerVeterinariosActivos();
$especies = Mascota::obtenerEspecies();
$razas = Mascota::obtenerRazas();

$catalogoVacunas = Mascota::catalogoVacunasMascota();
$vacunas_mascota = Mascota::obtenerVacunasMascota($idMascota);

$tieneVacunas = [];
foreach ($vacunas_mascota as $vm) {
    $idVacPues = (int)$vm['vacuna_puesta'];

    if (!isset($tieneVacunas[$idVacPues])) {
        $tieneVacunas[$idVacPues] = ['marcada' => false, 'fecha' => '', 'clinica' => false, 'fecha_clinica' => ''];
    }

    if ($vm['veterinario'] !== null) {

        $tieneVacunas[$idVacPues]['clinica'] = true;

        if ($tieneVacunas[$idVacPues]['fecha_clinica'] === '') {
            $tieneVacunas[$idVacPues]['fecha_clinica'] = (string)($vm['fecha_aplicacion'] ?? ''); 
        }
    } else {
        $tieneVacunas[$idVacPues]['marcada'] = true;
        $tieneVacunas[$idVacPues]['fecha'] = (string)($vm['fecha_aplicacion'] ?? '');
    }
}
$vacunasEnviadas = is_array($old['vacunas'] ?? null);

$escapar = static fn($valor) => htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');

$clienteIdAnterior = (string) ($old['id_cliente'] ?? $mascota['id_cliente'] ?? '');
$clienteSeleccionado = null;
foreach ($clientes as $registroCliente) {
  if ((string) $registroCliente['id_cliente'] === $clienteIdAnterior) {
    $clienteSeleccionado = $registroCliente;
    break;
  }
}

$veterinarioIdAnterior = (string) ($old['id_veterinario'] ?? $mascota['id_veterinario'] ?? '');
$veterinarioSeleccionado = null;
foreach ($veterinarios as $registroVeterinario) {
  if ((string) $registroVeterinario['id_empleado'] === $veterinarioIdAnterior) {
    $veterinarioSeleccionado = $registroVeterinario;
    break;
  }
}

$especieActual = (string)($old['especie'] ?? $mascota['especie'] ?? '');
$razaAnterior = (string) ($old['raza'] ?? $mascota['raza'] ?? '');
$especieIdAnterior = '';
foreach ($especies as $registroEspecie) {
  if ((string) $registroEspecie['especie'] === $especieActual) {
    $especieIdAnterior = (string) $registroEspecie['id'];
    break;
  }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veterinaria PetCare - Editar mascota</title>
  <link rel="stylesheet" href="css/estilos_base_v2.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
  <style>
    /* Switch Activo / Inactivo (si otra vista lo necesita, moverlo a css/v2/base.css) */
    .switch-fila { display: flex; align-items: center; gap: 12px; margin-top: 4px; }
    .switch { position: relative; display: inline-block; width: 46px; height: 26px; flex-shrink: 0; }
    .switch input { opacity: 0; width: 0; height: 0; }
    .slider {
      position: absolute; inset: 0; cursor: pointer;
      background-color: var(--gray-light);
      border: 1px solid var(--border-light);
      border-radius: 26px;
      transition: background-color 0.2s;
    }
    .slider::before {
      content: ""; position: absolute; left: 2px; top: 2px;
      width: 20px; height: 20px; border-radius: 50%;
      background-color: var(--white);
      box-shadow: 0 1px 2px rgba(0, 0, 0, 0.25);
      transition: transform 0.2s;
    }
    .switch input:checked + .slider { background-color: var(--accent-teal); }
    .switch input:checked + .slider::before { transform: translateX(20px); }
    .switch input:focus-visible + .slider { outline: 2px solid var(--primary-dark); outline-offset: 2px; }

    /* vacunas */
    .rejilla-vacunas { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 12px 16px; margin-top: 8px; }
    .tarjeta-vacuna { display: flex; flex-direction: column; gap: 8px; padding: 10px 12px; border: 1px solid var(--border-light); border-radius: 6px; }
    .tarjeta-vacuna .titulo-vacuna { display: flex; align-items: center; gap: 8px; font-size: 14px; }
    .tarjeta-vacuna input[type="date"] { padding: 6px 10px; }
  </style>
</head>
<body>

  <div class="layout">

  <?php $paginaActiva = 'mascotas'; include __DIR__ . '/layout/sidebar.php'; ?>

  <main>
      <div class="migaja"><a href="mascotas.php" class="link-tenue">Gestión de mascotas</a> / Editar</div>
      <div class="encabezado-pagina">
        <h1>Editar mascota</h1>
      </div>

      <div class="tarjeta">

    <?php if (!empty($errores)): ?>
      <div class="msg-error">
        <div>Por favor corrige los siguientes errores:</div>
        <?php foreach ($errores as $error): ?>
          <div>• <?= htmlspecialchars($error) ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form action="../controllers/actualizar_mascota.php" method="POST" enctype="multipart/form-data">

      <input type="hidden" id="id_mascota" name="id_mascota" value="<?= (int)$mascota['id_mascota'] ?>">

      <fieldset>
        <legend>Datos básicos</legend>

        <div class="grid-formulario">
          <div>
            <label class="label-campo" for="nombre">Nombre *:</label>
            <input type="text" class="campo" id="nombre" name="nombre" value="<?= dato($old, $mascota, 'nombre') ?>" required>
          </div>

          <div>
            <label class="label-campo" for="especie">Especie *:</label>
            <select class="campo" id="especie" name="especie" required>
              <option value="">Seleccionar...</option>
              <?php foreach ($especies as $registroEspecie): ?>
                <option 
                value="<?= $escapar($registroEspecie['especie']) ?>" 
                data-especie-id="<?= (int) $registroEspecie['id'] ?>"
                <?= $especieActual === (string) $registroEspecie['especie'] ? 'selected' : '' ?>
                ><?= $escapar($registroEspecie['especie']) ?></option>
              <?php endforeach; ?>
            <?php if (!$especies): ?>
                <small>No hay especies registradas en el catálogo.</small>
            <?php endif; ?>
            </select>
          </div>

          <div>
            <label class="label-campo" for="raza">Raza *:</label>
            <select class="campo" id="raza" name="raza" required <?= $especieIdAnterior === '' ? 'disabled' : ''?>>
                <option value="">Selecciona primero una especie...</option>
                <?php foreach ($razas as $registroRaza): 
                $razaCoincideConEspecie = $especieIdAnterior !== '' 
                    && (string) $registroRaza['especie'] === $especieIdAnterior;
                $razaSeleccionada = $razaCoincideConEspecie 
                    && $razaAnterior === (string) $registroRaza['raza'];
                ?>
                    <option 
                        value="<?= $escapar($registroRaza['raza']) ?>"
                        data-especie-id="<?= (int) $registroRaza['especie'] ?>" 
                        <?= $razaSeleccionada ? 'selected' : '' ?>
                        <?= $razaCoincideConEspecie ? '' : 'hidden disabled' ?>
                    ><?= $escapar($registroRaza['raza']) ?></option>
                <?php endforeach; ?>
            </select>
            <?php if ($especies && !$razas): ?>
                <small>No hay razas registradas en el catálogo.</small>
            <?php endif; ?>
          </div>

          <div>
            <label class="label-campo" for="sexo">Sexo *:</label>
            <select class="campo" id="sexo" name="sexo" required>
              <option value="Macho"  <?= $sexoActual === 'Macho'  ? 'selected' : '' ?>>Macho</option>
              <option value="Hembra" <?= $sexoActual === 'Hembra' ? 'selected' : '' ?>>Hembra</option>
            </select>
          </div>

          <div>
            <label class="label-campo" for="edad">Edad / Fecha nacimiento *:</label>
            <input type="date" class="campo" id="edad" name="edad" value="<?= dato($old, $mascota, 'edad') ?>" required>
          </div>

          <div>
            <label class="label-campo" for="color">Color *:</label>
            <select class="campo" id="color" name="color" required>
              <option value="">Seleccionar...</option>
              <?php foreach ($coloresLista as $opcionColor): ?>
                <option value="<?= htmlspecialchars($opcionColor) ?>" <?= $colorActual === $opcionColor ? 'selected' : '' ?>><?= htmlspecialchars($opcionColor) ?></option>
              <?php endforeach; ?>
              <option value="Otro" <?= $colorActual === 'Otro' ? 'selected' : '' ?>>Otro (especificar)</option>
            </select>
          </div>
          
          <div id="contenedor-color-espec" style="<?= $colorActual === 'Otro' ? '' : 'display: none;' ?>">
            <label class="label-campo" for="color_espec">Especifique el color *:</label>
            <input type="text" class="campo" id="color_espec" name="color_espec" style="border: 1px solid #3b82f6; background-color: #f9f9f9;"
                   value="<?= htmlspecialchars($colorEspecActual) ?>"
                   placeholder="Ej. Marrón rojizo" <?= $colorActual === 'Otro' ? 'required' : '' ?>>
          </div>

          <div>
            <label class="label-campo" for="peso">Peso (kg) *:</label>
            <input type="number" step="0.01" class="campo" id="peso" name="peso" value="<?= dato($old, $mascota, 'peso') ?>">
          </div>

          <div>
            <label class="label-campo" for="tamanio">Tamaño *:</label>
            <select class="campo" id="tamanio" name="tamanio">
              <option value="">Seleccionar...</option>
              <option value="Pequeño" <?= $tamActual === 'Pequeño' ? 'selected' : '' ?>>Pequeño</option>
              <option value="Mediano" <?= $tamActual === 'Mediano' ? 'selected' : '' ?>>Mediano</option>
              <option value="Grande"  <?= $tamActual === 'Grande'  ? 'selected' : '' ?>>Grande</option>
              <option value="Gigante" <?= $tamActual === 'Gigante' ? 'selected' : '' ?>>Gigante</option>
            </select>
          </div>

          <div class="contenedor-buscador">
            <label class="label-campo" for="cliente_busqueda">Dueño (cliente) *:</label>
            <input 
            type="text" 
            class="campo" 
            id="cliente_busqueda" 
            autocomplete="off"
            placeholder="Seleccione cliente..."
            value="<?= $clienteSeleccionado ? $escapar('Id: ' . $clienteSeleccionado['id_cliente'] . ' - ' . $clienteSeleccionado['nombre'] . ' ' . $clienteSeleccionado['apellido']) : '' ?>" 
            required 
            >
            <ul id="lista_clientes_mascota" class="lista-resultados">
            <?php foreach ($clientes as $registroCliente): ?>
                <li data-id="<?= (int) $registroCliente['id_cliente'] ?>">
                    <?= $escapar('Id: ' . $registroCliente['id_cliente'] . ' - ' . $registroCliente['nombre'] . ' ' . $registroCliente['apellido']) ?>
                </li>
            <?php endforeach; ?>
            </ul>
            <input
                type="hidden"
                id="id_cliente"
                name="id_cliente"
                value="<?= $clienteSeleccionado ? (int) $clienteSeleccionado['id_cliente'] : '' ?>"
            >
          </div>

          <div class="contenedor-buscador">
            <label class="label-campo" for="veterinario_busqueda">Veterinario asignado:</label>
            <input
                type="text"
                class="campo"
                id="veterinario_busqueda"
                autocomplete="off"
                placeholder="Seleccionar veterinario..."
                value="<?= $veterinarioSeleccionado ? $escapar('Id: ' . $veterinarioSeleccionado['id_empleado'] . ' - ' . $veterinarioSeleccionado['nombre'] . ' ' . $veterinarioSeleccionado['apellido']) : '' ?>"
            >
            <ul id="lista_veterinarios_mascota" class="lista-resultados">
                <?php foreach ($veterinarios as $registroVeterinario): ?>
                <li data-id="<?= (int) $registroVeterinario['id_empleado'] ?>">
                        <?= $escapar('Id: ' . $registroVeterinario['id_empleado'] . ' - ' . $registroVeterinario['nombre'] . ' ' . $registroVeterinario['apellido']) ?>
                </li>
            <?php endforeach; ?>
            </ul>
            <input
                type="hidden"
                id="id_veterinario"
                name="id_veterinario"
                value="<?= $veterinarioSeleccionado ? (int) $veterinarioSeleccionado['id_empleado'] : '' ?>"
            >
          </div>
        </div>

  
        <div class="campo-completo-sep">
          <label class="label-campo" for="fotografia">Fotografía:</label>
          <input type="file" class="campo" id="fotografia" name="fotografia" accept="image/*">

          <div id="contenedor-imagen-nueva" style="margin-top: 12px; display: none;">
              <p style="margin: 0 0 4px 0; font-size: 0.75rem; color: #b0bec5; letter-spacing: 0.5px; font-weight: 600;">Imagen nueva</p>
              <img id="preview-crop" alt="Vista previa recortada (cuadrado)">
          </div>

          <?php if (!empty($mascota['fotografia'])): ?>
              <div style="margin-top: 12px; display: flex; align-items: center; gap: 12px;">
                <div>
                  <p style="margin: 0 0 4px 0; font-size: 0.75rem; color: #b0bec5; letter-spacing: 0.5px; font-weight: 600;"> <br>Imagen actual</p>
                  <img src="../../<?= htmlspecialchars($mascota['fotografia']) ?>" alt="Foto de la mascota" style="max-width: 200px; max-height: 200px; border-radius: 8px; object-fit: cover; border: 1px solid rgba(255,255,255,0.2);">
                </div>
              </div>
              
            <p style="margin: 3px 0 0 0; font-size: 0.85rem; color: #90a4ae;">Sube un archivo nuevo solo si deseas reemplazarla.</p>
          <?php endif; ?>


          <?php if (empty($mascota['fotografia'])): ?>
            <p style="font-size: 0.95rem; color: #90a4ae;"><br>Al elegir una foto se abrirá el cuadrado de recorte.</p>
          <?php endif; ?>
        </div>

        <div id="modal-crop">
          <div class="caja">
            <h3>Recortar foto (cuadrado)</h3>
            <img id="imagen-a-recortar" alt="Imagen a recortar">
            <div class="crop-acciones">
              <button type="button" id="btn-cancelar-crop" class="btn btn-secundario btn-chico">Cancelar</button>
              <button type="button" id="btn-recortar" class="btn btn-chico">Recortar y usar</button>
            </div>
          </div>
        </div>
      </fieldset>

      <fieldset>
        <legend>Datos clínicos</legend>

        <div class="grid-formulario">
          <div>
            <label class="label-campo" for="alergias">Alergias *:</label>
            <textarea class="campo" id="alergias" name="alergias" rows="2"><?= dato($old, $mascota, 'alergias') ?></textarea>
          </div>

          <div>
            <label class="label-campo" for="enfermedades">Enfermedades *:</label>
            <textarea class="campo" id="enfermedades" name="enfermedades" rows="2"><?= dato($old, $mascota, 'enfermedades') ?></textarea>
          </div>

          <div>
            <label class="label-campo" for="medicamentos">Medicamentos *:</label>
            <textarea class="campo" id="medicamentos" name="medicamentos" rows="2"><?= dato($old, $mascota, 'medicamentos') ?></textarea>
          </div>

          <div>
            <label class="label-campo" for="condiciones_especiales">Condiciones especiales *:</label>
            <textarea class="campo" id="condiciones_especiales" name="condiciones_especiales" rows="2"><?= dato($old, $mascota, 'condiciones_especiales') ?></textarea>
          </div>

          <div class="campo-completo" id="contenedorVacunas">
            <label class="label-campo">Vacunas que ya tiene:</label>
            <small class="nota-campo" id="notaVacunas">Seleccione primero una especie</small>

            <div class="rejilla-vacunas">

                <?php foreach ($catalogoVacunas as $catVac): 
                    $info = $tieneVacunas[(int)$catVac['id']] ?? null;
                    if (!($info && $info['clinica'])) {
                        continue;
                    }
                ?>
                <div class="tarjeta-vacuna">
                    <span class="titulo-vacuna"> ✔ <?php echo $escapar($catVac['nombre']) ?> </span>
                    <small class="nota-campo">Aplicada en la clinica <?php echo $info['fecha_clinica'] !== '' ? ' el ' . $escapar($info['fecha_clinica']) : '' ?></small>
                </div>
                <?php endforeach; ?>
        
                <?php foreach ($catalogoVacunas as $catVac): 
                    $idVac = (int)$catVac['id'];
                    $info = $tieneVacunas[$idVac] ?? null;

                    if ($info && $info['clinica']) {
                        continue;
                    }
                 
                    $marcada = $vacunasEnviadas ? in_array((string)$idVac, array_map('strval', $old['vacunas']), true) : (bool)($info['marcada'] ?? false);

                    $fechaVacuna = $vacunasEnviadas ? ($old['fechas_vacunas'][$idVac] ?? '') : ($info['fecha'] ?? '');
                ?>
                  <label class="oculto tarjeta-vacuna" data-especie-id="<?php echo (int)$catVac['especie'] ?>">
                    <span class="titulo-vacuna">
                      <input type="checkbox" name="vacunas[]" value="<?php echo $idVac ?>" disabled <?php echo $marcada ? 'checked' : '' ?>>
                      <?php echo $escapar($catVac['nombre']) ?>
                    </span>
                    <input type="date" class="campo" name="fechas_vacunas[<?php echo $idVac ?>]" max="<?php echo date('Y-m-d') ?>"
                      value="<?php echo $escapar($fechaVacuna) ?>" disabled>
                  </label>
                <?php endforeach; ?>
            </div>
          </div>

          <div>
            <label class="label-campo" for="ultima_desparasitacion">Última desparasitación *:</label>
            <input type="date" class="campo" id="ultima_desparasitacion" name="ultima_desparasitacion" value="<?= dato($old, $mascota, 'ultima_desparasitacion') ?>">
          </div>

          <div>
            <label class="label-campo" for="temperamento">Temperamento:</label>
            <textarea class="campo" id="temperamento" name="temperamento" rows="2"><?= dato($old, $mascota, 'temperamento') ?></textarea>
          </div>

          <div>
            <label class="label-campo" for="restricciones_para_manejo">Restricciones para manejo:</label>
            <textarea class="campo" id="restricciones_para_manejo" name="restricciones_para_manejo" rows="2"><?= dato($old, $mascota, 'restricciones_para_manejo') ?></textarea>
          </div>

          <div>
            <label class="label-campo" for="observaciones">Observaciones:</label>
            <textarea class="campo" id="observaciones" name="observaciones" rows="2"><?= dato($old, $mascota, 'observaciones') ?></textarea>
          </div>

          <div>
            <label class="label-campo" for="activo">Estado:</label>
            <select class="campo" id="activo" name="activo">
              <option value="1" <?= $activo ? 'selected' : '' ?>>Activo</option>
              <option value="0" <?= $activo ? '' : 'selected' ?>>Inactivo</option>
            </select>
          </div>
        </div>
      </fieldset>

      <div class="acciones-formulario">
        <a class="btn btn-secundario btn-chico" href="mascotas.php">Cancelar</a>
        <button type="submit" class="btn btn-chico">Guardar cambios</button>
      </div>
    </form>

      </div>
    </main>

  </div>

  <footer>
    <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
  </footer>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
  <script src="js/crop-mascota.js"></script>
  <script src="js/mascota.js"></script>

  <script>
    const previewImg = document.getElementById('preview-crop');
    const contenedorNuevo = document.getElementById('contenedor-imagen-nueva');

    if (previewImg && contenedorNuevo) {
        const observador = new MutationObserver(() => {
            if (previewImg.getAttribute('src')) {
                contenedorNuevo.style.display = 'block';
            }
        });
        observador.observe(previewImg, { attributes: true, attributeFilter: ['src'] });
    }
  </script>

  
  <script>
    const selectColor = document.getElementById('color');
    const contenedorColorEspec = document.getElementById('contenedor-color-espec');
    const inputColorEspec = document.getElementById('color_espec');
 
    function alternarColorEspec() {
        const esOtro = selectColor.value === 'Otro';
        contenedorColorEspec.style.display = esOtro ? '' : 'none';
        inputColorEspec.required = esOtro;
    }
 
    if (selectColor && contenedorColorEspec && inputColorEspec) {
        selectColor.addEventListener('change', alternarColorEspec);
        alternarColorEspec();
    }
  </script>

</body>
</html>
