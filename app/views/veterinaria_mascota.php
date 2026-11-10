
<?php
//Se toman desde back igual que en ficha medica
$mascotas      = $mascotas ?? [];
$idConsulta    = $idConsulta ?? 0;
$idVeterinario = $idVeterinario ?? 1;


// Devuelve el valor viejo guardado en sesion (si hubo error) y lo borra
function old(string $clave): string
{
    $valor = (string)($_SESSION[$clave] ?? '');
    unset($_SESSION[$clave]);
    return htmlspecialchars($valor);
}

$mensajeError = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
$listaErrores = $_SESSION['errores'] ?? [];
unset($_SESSION['errores']);
$mascotaVieja = old('oldMascota');
?>


<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veterinaria PetCare - Datos Mascota</title>
  <link rel="stylesheet" href="css/estilos_base_v2.css">
</head>

<body>

  <div class="layout">

    <?php $paginaActiva = 'Veterinaria'; include __DIR__ . '/layout/sidebar.php';?>

    <main>
      <div class="migaja"><a href="veterinaria_inicio.php" class="link-tenue">Veterinaria</a> / Datos de mascota</div>

      <div class="encabezado-pagina">
        <h1>Datos de la mascota</h1>
      </div>

      <div class="panel-inicio">

        <!-- COLUMNA IZQUIERDA -->
        <div class="columna-principal">

          <div class="tarjeta">

            <h2>Seleccionar mascota</h2>
            <select class="campo" name="mascota" id="mascota">
                <option value="">Selecciona una mascota</option>
                <?php foreach ($mascotas as $m): ?>
                    <option value="<?= (int)$m['id'] ?>" <?= ((string)$m['id'] === (string)$mascotaVieja) ? 'selected' : '' ?>><?= htmlspecialchars($m['nombre']) ?></option>
                <?php endforeach; ?>
            </select>

          </div>

          <div class="tarjeta">

            <h2>Información de la mascota</h2>
                
                <div class="Card_cita">
                    <div>
                      <p><strong>Especie: </strong><?php //echo $d['nombre']; ?></p>
                      <p><strong>Raza: </strong></p>
                      <p><strong>Edad: </strong></p>
                      <p><strong>Peso: </strong></p>
                      <p><strong>Color: </strong></p>
                    </div>
                </div>

            <h2>Datos del propietario</h2>

            <div class="Card_cita">
                <p><strong>Nombre:</strong></p>
                <p><strong>Telefono:</strong></p>
                <p><strong>Correo:</strong></p>
            </div>

          </div>

        </div>

        <!-- COLUMNA DERECHA -->
        <div class="columna-lateral">

          <div class="tarjeta">

            <h2>Acciones</h2>

            <div class="lista-accesos">
              <a href="#" class="btn btn-chico">Nueva consulta</a>
              <a href="#" class="btn btn-chico btn-secundario">Editar datos</a>
            </div>

          </div>

          <div class="tarjeta">

            <h2>Última desparasitación</h2>

            <p><strong>Fecha:</strong></p>
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
                    <th>Aplicada</th>
                    <th>Fecha</th>
                  </tr>
                </thead>

                <tbody>
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
