<?php require_once __DIR__ . '/../controllers/MascotaController.php'; ?>
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

    <?php $paginaActiva = 'mascotas';
    include __DIR__ . '/layout/sidebar.php'; ?>


    <main>
      <div class="migaja">Gestión de mascotas /</div>
      <div class="encabezado-pagina">
        <h1>Mascotas</h1>
      </div>

      <div class="tarjeta">

        <div class="barra-acciones-busqueda">
          <form class="busqueda-barra" method="get" action="mascotas.php" role="search">
            <label class="visualmente-oculto" for="buscarMascota">Buscar mascota</label>
            <input class="campo" id="buscarMascota" name="buscar" type="search" placeholder="Buscar por nombre, especie o raza" value="<?= htmlspecialchars($_GET['buscar'] ?? '') ?>" autocomplete="off">
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
                    <td><?= htmlspecialchars((string)$mascota['edad']) ?></td>
                    <td class="acciones">
                      <a class="btn btn-chico" href="editar_mascota.php?id_mascota=<?= $mascota['id_mascota'] ?>">Editar</a>
                      <a class="btn btn-peligro btn-chico" href="eliminar_mascota.php?id_mascota=<?= $mascota['id_mascota'] ?>">Eliminar</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="6">No hay mascotas registradas.</td>
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
  <dialog class="producto-dialog" id="modalDetalle">
    <div class="producto-dialog-encabezado">
      <h1>Detalle de la mascota</h1>
      <form method="dialog"><button class="producto-dialog-cerrar" aria-label="cerrar">&times;</button></form>
    </div>

    <div class="detalle-cuerpo grid-formulario">
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
      <div class="campo-completo"><span class="label-campo">Observaciones</span><span data-campo="alergias"></span></div>
    </div>
  </dialog>


  <script>
    document.querySelector('tbody').addEventListener('click', (e) => {
      if(e.target.closest('a, button')) return;
      const fila = e.target.closest('tr[data-registro]');
      if(!fila) return;

      const datos = JSON.parse(fila.dataset.registro);
      document.querySelectorAll('#modalDetalle [data-campo]').forEach((el) => {
        el.textContent = datos[el.dataset.campo] || '-';
      });
      document.getElementById('modalDetalle').showModal();
    })

  </script>

</body>

</html>