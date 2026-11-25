<?php require_once __DIR__ . '/../controllers/MascotaController.php';
// Mostrar nombre del usuario
if (session_status() === PHP_SESSION_NONE)
{
    session_start();
}
if (!isset($_SESSION['usuario']))
{
  header("Location: ../views/login.php");
  exit();
}
$usuario=$_SESSION['usuario'];
// Mostrar nombre del usuario
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veterinaria PetCare - mascotas</title>
  <link rel="stylesheet" href="css/estilos_base_v2.css">

</head>

<body>

  <div class="layout">
  <?php $paginaActiva = 'mascotas'; include __DIR__ . '/layout/sidebar.php'; ?>

  <main>
      <div class="migaja">Gestión de mascotas /</div>
      <div class="encabezado-pagina">
        <h1>Mascotas</h1>
      </div>

      <div class="tarjeta">

        <div class="barra-acciones-busqueda">
          <form class="busqueda-barra" method="get" action="mascotas.php" role="search">
            <label class="visualmente-oculto" for="buscarMascota">Buscar mascota</label>
            <input class="campo buscador-inteligente" id="buscarMascota" name="buscar" type="search" placeholder="Buscar por nombre, especie o raza" value="<?= htmlspecialchars($_GET['buscar'] ?? '') ?>" autocomplete="off">
            <?php if (!empty($_GET['buscar'])): ?>
              <a class="btn btn-secundario btn-chico" href="mascotas.php">Limpiar</a>
            <?php endif; ?>
          </form>

          <a class="btn" href="agregar_mascota.php">+ Agregar mascota</a>
        </div>
        <!-- AQUÍ INICIA EL CONTENEDOR RESPONSIVE -->
        <div class="tabla-envoltura">

          <table>
            <thead>
              <tr>
                <th>Nombre</th>
                <th>Especie</th>
                <th>Raza</th>
                <th>Sexo</th>
                <th>Edad</th>
                <th>Fotografía</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($mascotas)): ?>
                <?php foreach ($mascotas as $mascota): ?>
                  <tr class="fila-clic" data-registro="<?= htmlspecialchars(json_encode($mascota), ENT_QUOTES) ?>">
                    <td><?= htmlspecialchars($mascota['nombre']) ?></td>
                    <td><?= htmlspecialchars($mascota['especie']) ?></td>
                    <td><?= htmlspecialchars($mascota['raza']) ?></td>
                    <td><?= htmlspecialchars($mascota['sexo']) ?></td>
                    <td><?= htmlspecialchars(calcularEdad((string)($mascota['edad'] ?? ''), false)) ?></td>
                    <td>
                      <?php
                      $foto = trim((string)($mascota['fotografia'] ?? ''));
                      if ($foto !== '' && $foto !== 'Sin foto'):
                        if (str_starts_with($foto, 'http://') || str_starts_with($foto, 'https://') || str_starts_with($foto, '/')) {
                          $rutaFoto = $foto;
                        } elseif (str_starts_with($foto, '../../')) {
                          $rutaFoto = $foto;
                        } elseif (str_starts_with($foto, '../')) {
                          $rutaFoto = '../' . $foto;
                        } elseif (str_starts_with($foto, 'uploads/')) {
                          $rutaFoto = '../../' . $foto;
                        } else {
                          $rutaFoto = '../../uploads/mascotas/' . ltrim($foto, '/');
                        }
                        $nombreMascota = trim((string)($mascota['nombre'] ?? ''));
                        if ($nombreMascota === '') {
                          $nombreMascota = 'Mascota #' . ($mascota['id_mascota'] ?? '');
                        }
                      ?>
                        <div class="foto-miniatura-celda">
                          <img src="<?= htmlspecialchars($rutaFoto) ?>"
                               alt="<?= htmlspecialchars($nombreMascota) ?>"
                               class="foto-miniatura"
                               title="Click para ampliar imagen"
                               data-full="<?= htmlspecialchars($rutaFoto) ?>"
                               data-title="<?= htmlspecialchars($nombreMascota) ?>"
                               onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                          <span class="sin-foto" style="display:none;">Imagen no disponible</span>
                        </div>
                      <?php else: ?>
                        <span class="sin-foto">Sin foto</span>
                      <?php endif; ?>
                    </td>
                    <td class="acciones">
                      <a class="btn btn-chico" href="editar_mascota.php?id_mascota=<?= $mascota['id_mascota'] ?>">Editar</a>
                      <a class="btn btn-peligro btn-chico" href="eliminar_mascota.php?id_mascota=<?= $mascota['id_mascota'] ?>">Eliminar</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="7">No hay mascotas registradas.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>

        </div> <!-- AQUÍ TERMINA EL CONTENEDOR RESPONSIVE -->

      </div>
    </main>

  </div>

  <footer>
    <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
  </footer>

  <!--ventana modal-->
  <dialog class="producto-dialog" id="modalDetalle" style="max-width: 700px; width: 90%; max-height: 85vh; overflow-y: auto; padding: 2px;">
    <div class="producto-dialog-encabezado">
      <h1>Detalle de la mascota</h1>
      <form method="dialog"><button class="producto-dialog-cerrar" aria-label="cerrar">&times;</button></form>
    </div>

    <div class="detalle-cuerpo grid-formulario" style="text-align: center;">
      <div><span class="label-campo">Nombre</span><span data-campo="nombre"></span></div>
      <div><span class="label-campo">Especie</span><span data-campo="especie"></span></div>
      <div><span class="label-campo">Raza</span><span data-campo="raza"></span></div>
      <div><span class="label-campo">Sexo</span><span data-campo="sexo"></span></div>
      <div><span class="label-campo">Color</span><span data-campo="color"></span></div>
      <div><span class="label-campo">Peso (kg)</span><span data-campo="peso"></span></div>
      <div><span class="label-campo">Tamaño</span><span data-campo="tamanio"></span></div>
      <div><span class="label-campo">Última desparasitación</span><span data-campo="ultima_desparasitacion"></span></div>
      <div class="campo-completo"><span class="label-campo">Alergias</span><span data-campo="alergias"></span></div>
      <div class="campo-completo"><span class="label-campo">Enfermedades</span><span data-campo="enfermedades"></span></div>
      <div class="campo-completo"><span class="label-campo">Vacunas</span><span data-campo="vacunas"></span></div>
      <div class="campo-completo"><span class="label-campo">Observaciones</span><span data-campo="observaciones"></span></div>
    </div>
  </dialog>

  <div id="modal-visor-imagen" class="modal-visor" role="dialog" aria-modal="true" aria-label="Visor de imagen">
    <span class="btn-cerrar-visor" title="Cerrar (Esc)">&times;</span>
    <div class="visor-contenido">
      <img id="imagen-ampliada" src="" alt="Imagen ampliada" title="Haz clic para cerrar">
      <div id="pie-imagen-ampliada" class="pie-visor"></div>
    </div>
  </div>

  <script src="js/buscador_inteligente.js"></script>
  <script>
    document.querySelector('tbody').addEventListener('click', (e) => {
      if (e.target.closest('a, button, .foto-miniatura')) return;
      const fila = e.target.closest('tr[data-registro]');
      if (!fila) return;

      const datos = JSON.parse(fila.dataset.registro);
      document.querySelectorAll('#modalDetalle [data-campo]').forEach((el) => {
        el.textContent = datos[el.dataset.campo] || '-';
      });
      document.getElementById('modalDetalle').showModal();
    })

    document.addEventListener('DOMContentLoaded', function () {
      var modal = document.getElementById('modal-visor-imagen');
      var imgAmpliada = document.getElementById('imagen-ampliada');
      var pie = document.getElementById('pie-imagen-ampliada');
      var btnCerrar = modal.querySelector('.btn-cerrar-visor');

      function abrirModal(src, titulo) {
        imgAmpliada.src = src;
        pie.textContent = titulo || '';
        modal.classList.add('activo');
        document.body.style.overflow = 'hidden';
      }

      function cerrarModal() {
        modal.classList.remove('activo');
        document.body.style.overflow = '';
        imgAmpliada.src = '';
      }

      document.addEventListener('click', function (e) {
        var target = e.target;
        if (target && target.classList.contains('foto-miniatura')) {
          var src = target.getAttribute('data-full') || target.src;
          var titulo = target.getAttribute('data-title') || '';
          abrirModal(src, titulo);
        }
      });

      btnCerrar.addEventListener('click', function (e) {
        e.stopPropagation();
        cerrarModal();
      });

      modal.addEventListener('click', function (e) {
        if (e.target === modal || e.target === imgAmpliada || e.target.classList.contains('visor-contenido')) {
          cerrarModal();
        }
      });

      document.addEventListener('keydown', function (e) {
        if ((e.key === 'Escape' || e.key === 'Esc') && modal.classList.contains('activo')) {
          cerrarModal();
        }
      });
    });
  </script>

</body>

</html>
