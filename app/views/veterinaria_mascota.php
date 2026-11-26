<?php
require_once __DIR__ . '/../controllers/veterinaria.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$datos = extraerDatosMascota();

$idMascota = $datos['idMascota'];
$listaMascotas = $datos['listaMascotas'];
$detMascotas = $datos['detMascotas'];
$detProp = $datos['detProp'];
$edad = $datos['edad'];
$vacunas_mascota = $datos['vacunas'] ?? [];

$urlNuevaConsulta = !empty($detProp['id_cliente']) ? 'agendarCita.php?claveCliente=' . urlencode((string)$detProp['id_cliente']) : '#';
$urlEditarMascota = $idMascota > 0 ? 'editar_mascota.php?id_mascota=' . urlencode((string)$idMascota) : '#';

/* $idConsulta    = 0; */
/* $idVeterinario = 1; */
/* $vacunas = $datos['vacunas']; */

// Devuelve el valor viejo guardado en sesion (si hubo error) y lo borra
function old(string $clave): string
{
    $valor = (string)($_SESSION[$clave] ?? '');
    unset($_SESSION[$clave]);
    return htmlspecialchars($valor);
}

function muestra(array $datos, string $campo): string {
    return htmlspecialchars((string)($datos[$campo] ?? ''));
}

$mensajeError = $_SESSION['error'] ?? null;
unset($_SESSION['error']);


?>


<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veterinaria PetCare - Datos Mascota</title>
  <link rel="stylesheet" href="css/estilos_base_v2.css">
  <style> 
    .columna-lateral .barra-acciones {
        margin-top: 16px;
        flex-shrink: 0;
    }

    .columna-principal {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .tarjeta .tabla-envoltura {
        overflow-y: auto;
    }

    .tarjeta .tabla-envoltura thead th {
        position: sticky;
        top: 0;
        z-index: 1;
    }

    @media (min-width: 769px) {
        main {
            display: flex;
            flex-direction: column;
            height: 100vh;
            box-sizing: border-box;
            overflow-y: auto;
        }

        main .panel-inicio {
            /* flex: 1; */
            flex: 1 0 auto;
            min-height: 0;
            align-items: stretch;
        }

        .columna-principal,
        .columna-lateral {
            min-height: 0;
        }

        .columna-principal .tarjeta {
            flex: 0 0 auto;
        }

        .columna-lateral {
            display: flex;
            flex-direction: column;
            gap: 15px;
            height: 100%;
        }

        .columna-lateral .tarjeta {
            flex: 0 0 auto;
        }

        .columna-lateral .tarjeta:last-child {
            /* flex: 0 1 auto; */
            flex: 1 1 0;
            min-height: 0;
            display: flex;
            flex-direction: column;
        }

        .columna-lateral .tarjeta:last-child .tabla-envoltura {
            /* flex: 0 1 auto; */
            flex: 1 1 0;
            min-height: 0;
            max-height: none;
        }
    }

    @media (max-width: 768px) {
        .tarjeta .tabla-envoltura {
            max-height: 360px;
        }
    }

  </style>
</head>

<body>

  <div class="layout">

    <?php $paginaActiva = 'veterinaria'; include __DIR__ . '/layout/sidebar.php';?>

    <main>
      <div class="migaja"><a href="veterinaria_Inicio.php" class="link-tenue">Veterinaria</a> / Datos de mascota</div>

      <div class="encabezado-pagina">
        <h1>Datos de la mascota</h1>
      </div>

      <?php if ($mensajeError): ?>
      <div class="msg-error"> <?php echo htmlspecialchars($mensajeError)?> </div>
      <?php endif; ?>

      <div class="panel-inicio">

        <!-- COLUMNA DE LA IZQUIERDA -->
        <div class="columna-principal">
          <div class="tarjeta">
            <h2>Seleccionar mascota</h2>
            <form method="POST" action="../controllers/veterinaria.php">
                <select class="campo" name="id_mascota" id="mascota" onchange="this.form.submit()">
                    <option value="" selected disabled>Selecciona una mascota</option>
                    <?php foreach ($listaMascotas as $listM): ?>
                        <option value="<?php echo (int)$listM['id_mascota']?>" <?php echo ((int)$listM['id_mascota'] === $idMascota) ? 'selected' : '' ?> > <?php echo htmlspecialchars($listM['nombre']) ?> </option>
                    <?php endforeach; ?>
                </select>
            </form>
          </div>
          <div class="tarjeta">

            <h2>Información de la mascota</h2>  
                <div class="Card_cita">
                    <div>
                        <p><strong>Especie: </strong><span id="especie"> <?php echo muestra($detMascotas, 'especie')?> </span></p>
                        <p><strong>Raza: </strong><span id="raza"> <?php echo muestra($detMascotas, 'raza')?> </span></p>
                        <p><strong>Edad: </strong><span id="edad"> <?php echo htmlspecialchars($edad) ?> </span></p>
                        <p><strong>Peso: </strong><span id="peso"> <?php echo muestra($detMascotas, 'peso')?> </span></p>
                        <p><strong>Color: </strong><span id="color"> <?php echo muestra($detMascotas, 'color')?> </span></p>
                    </div>
                </div>
            <h2>Datos del propietario</h2>

            <div class="Card_cita">
                <p><strong>Nombre:</strong> <?php echo muestra($detProp, 'nombre') ?></p>
                <p><strong>Telefono:</strong> <?php echo muestra($detProp, 'telefono') ?></p>
                <p><strong>Correo:</strong> <?php echo muestra($detProp, 'correo') ?></p>
            </div>
          </div>
        </div>

        <!-- cOLUMNA DE LA DERECHA -->
        <div class="columna-lateral">
          <div class="tarjeta">
            <h2>Acciones</h2>
            <div class="lista-accesos">
                <a href="<?php echo $urlNuevaConsulta ?>" class="btn btn-chico">Nueva consulta</a>
                <a href="<?php echo $urlEditarMascota ?>" class="btn btn-chico btn-secundario">Editar datos</a>
            </div>
          </div>

          <div class="tarjeta">
            <h2>Última desparasitación</h2>
            <p><strong>Fecha: <?php echo htmlspecialchars(formatearFechaEsp((string)($detMascotas['ultima_desparasitacion'] ?? ''))) ?></strong></p>
            <p><strong>Producto:</strong></p>
            <p><strong>Próxima:</strong></p>
          </div>

          <div class="tarjeta">
            <h2>Vacunas de la mascota</h2>
            <div class="tabla-envoltura">
              <table>
                <thead>
                    <tr>
                        <th>Vacuna</th>
                        <th>Fecha</th>
                        <th>Veterinario</th>
                    </tr>
                </thead>
                <tbody id="tablaVacunas"><!--Estas recuerda que se sacan de la tabla vacunas siempre que tengan el id de la mascota seleccionada-->
                    <?php if (empty($vacunas_mascota)): ?>
                        <tr>
                            <td colspan="3">
                                <?php echo $idMascota > 0 ? 'Esta mascota no tiene vacunas registradas' : 'Primero seleccione una mascota' ?>
                            </td>
                        </tr>
                        <?php endif; ?>
                    <?php foreach ($vacunas_mascota as $vm): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($vm['vacuna']) ?></td>
                            <td><?php echo !empty($vm['fecha_aplicacion']) ? htmlspecialchars(formatearFechaEsp((string)$vm['fecha_aplicacion'])) : 'Sin fecha' ?></td>
                            <td><?php echo htmlspecialchars($vm['veterinario'] ?? 'Externo') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
              </table>
            </div>

            <div class="barra-acciones">
              <a href="veterinaria_vacunas.php" class="btn">
                Gestionar vacunas
              </a>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</body>
</html>
