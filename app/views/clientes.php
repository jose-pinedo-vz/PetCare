<?php
  require_once __DIR__ . '/../models/Cliente.php';
  $listaClientes = Cliente::obtenerTodos();
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
  <title>Veterinaria PetCare - Clientes</title>
  <link rel="stylesheet" href="css/estilos_base_v2.css">
</head>

<body>

  <div class="layout">

    <?php
      $paginaActiva = 'clientes';
      include __DIR__ . '/layout/sidebar.php';
    ?>


  <main>
      <div class="migaja">Gestión de clientes /</div>
      <div class="encabezado-pagina">
        <h1>Clientes</h1>
      </div>

      <div class="tarjeta">

        <div class="barra-acciones-busqueda">
          <div class="busqueda-barra" role="search">
            <label class="visualmente-oculto" for="buscarCliente">Buscar cliente</label>
            <input class="campo buscador-inteligente" id="buscarCliente" type="search" placeholder="Buscar por datos del cliente..." autocomplete="off">
          </div>
          <div class="barra-acciones">
            <a class="btn" href="agregar_cliente.php">+ Agregar cliente</a>
          </div>
        </div>

        <?php
        $listaClientes = Cliente::obtenerTodos();
        if (empty($listaClientes)): ?>
          <div class="vacio">
            <p>No hay clientes registrados en la base de datos.</p>
            <a class="btn" href="agregar_cliente.php">+ Registrar el primer cliente</a>
          </div>
        <?php else: ?>

          <!-- INICIA CONTENEDOR RESPONSIVE -->
          <div class="tabla-envoltura">
            <table>
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Nombre</th>
                  <th>Teléfono</th>
                  <th>Correo</th>
                  <th>Ciudad / Estado</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($listaClientes as $c): ?>
                  <tr class="fila-clic" data-registro="<?= htmlspecialchars(json_encode($c), ENT_QUOTES) ?>">
                    <td><strong>#<?php echo htmlspecialchars((string)$c['id_cliente']); ?></strong></td>
                    <td><?php echo htmlspecialchars($c['nombre'] . ' ' . $c['apellido']); ?></td>
                    <td><?php echo htmlspecialchars($c['telefono']); ?></td>
                    <td><?php echo htmlspecialchars($c['correo']); ?></td>
                    <td><?php echo htmlspecialchars($c['ciudad'] . ', ' . $c['estado']); ?></td>

                    <!-- Celda de acciones con white-space: nowrap; -->
                    <td class="acciones">
                      <a class="btn btn-chico" href="editar_cliente.php?id=<?php echo urlencode((string)$c['id_cliente']); ?>">Editar</a>
                      <a class="btn btn-chico btn-info" href="agregar_mascota.php?id_cliente=<?php echo urlencode((string)$c['id_cliente']); ?>">+ Mascota</a>
                      <a class="btn btn-chico btn-aviso" href="agendarCita.php?claveCliente=<?php echo urlencode((string)$c['id_cliente']); ?>">+ Cita</a>
                      <a class="btn btn-chico btn-eliminar btn-peligro" href="eliminar_cliente.php?id=<?php echo urlencode((string)$c['id_cliente']); ?>">Eliminar</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <!-- TERMINA CONTENEDOR RESPONSIVE -->

        <?php endif; ?>

      </div>
    </main>

  </div>

  <footer>
    <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
  </footer>

  <dialog class="producto-dialog" id="modalDetalle">
  <div class="producto-dialog-encabezado">
    <h1>Detalle del cliente</h1>
    <form method="dialog"><button class="producto-dialog-cerrar" aria-label="Cerrar">&times;</button></form>
  </div>

  <div class="detalle-cuerpo grid-formulario">
    <div><span class="label-campo">Nombre</span><span data-campo="nombre"></span></div>
    <div><span class="label-campo">Apellido</span><span data-campo="apellido"></span></div>
    <div><span class="label-campo">Fecha de nacimiento</span><span data-campo="fecha_nacimiento"></span></div>
    <div><span class="label-campo">Sexo</span><span data-campo="sexo"></span></div>
    <div><span class="label-campo">RFC</span><span data-campo="rfc"></span></div>
    <div><span class="label-campo">Teléfono</span><span data-campo="telefono"></span></div>
    <div><span class="label-campo">Teléfono alternativo</span><span data-campo="telefono_alternativo"></span></div>
    <div><span class="label-campo">Correo</span><span data-campo="correo"></span></div>
    <div class="campo-completo"><span class="label-campo">Calle</span><span data-campo="calle"></span></div>
    <div><span class="label-campo">Número exterior</span><span data-campo="numero_exterior"></span></div>
    <div><span class="label-campo">Número interior</span><span data-campo="numero_interior"></span></div>
    <div><span class="label-campo">Colonia</span><span data-campo="colonia"></span></div>
    <div><span class="label-campo">Código postal</span><span data-campo="codigo_postal"></span></div>
    <div><span class="label-campo">Ciudad</span><span data-campo="ciudad"></span></div>
    <div><span class="label-campo">Estado</span><span data-campo="estado"></span></div>
    <div><span class="label-campo">Contacto de emergencia</span><span data-campo="contacto_emergencia"></span></div>
    <div><span class="label-campo">Teléfono de emergencia</span><span data-campo="telefono_emergencia"></span></div>
    <div class="campo-completo"><span class="label-campo">Observaciones</span><span data-campo="observaciones"></span></div>
  </div>
</dialog>

<script>
  const cuerpoTablaClientes = document.querySelector('.tabla-envoltura tbody');
  const buscarCliente = document.getElementById('buscarCliente');

  if (cuerpoTablaClientes && buscarCliente) {
    buscarCliente.addEventListener('input', () => {
      const termino = buscarCliente.value.trim().toLocaleLowerCase();
      cuerpoTablaClientes.querySelectorAll('tr[data-registro]').forEach((fila) => {
        const texto = Array.from(fila.cells)
          .slice(0, -1)
          .map((celda) => celda.textContent)
          .join(' ')
          .toLocaleLowerCase();
        fila.hidden = !texto.includes(termino);
      });
    });

    cuerpoTablaClientes.addEventListener('click', (e) => {
      if (e.target.closest('a, button')) return;
      const fila = e.target.closest('tr[data-registro]');
      if (!fila) return;

      const datos = JSON.parse(fila.dataset.registro);
      document.querySelectorAll('#modalDetalle [data-campo]').forEach((el) => {
        el.textContent = datos[el.dataset.campo] || '—';
      });
      document.getElementById('modalDetalle').showModal();
    });
  }
</script>

</body>

</html>