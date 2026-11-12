<?php
require_once __DIR__ . '/../Models/Productos.php';
require_once __DIR__ . '/../models/conexion_db.php';

$productos = Productos::obtenerDatosProductosActivos();
$productosEnRiesgo = array_values(array_filter(
  $productos,
  static fn ($producto): bool => (int) $producto['existencia'] <= (int) $producto['existencia_minima']
));
$movimientosHoy = null;

try {
  $conexion = ConexionDB::obtenerConexion();
  $consultaMovimientos = $conexion->query(
    "SELECT COUNT(*) FROM movimientos_inventario WHERE DATE(fecha) = CURDATE()"
  );
  $movimientosHoy = (int) $consultaMovimientos->fetchColumn();
} catch (Throwable $e) {
  error_log('Error al consultar movimientos de inventario del día: ' . $e->getMessage());
}

$escaparInventario = static fn ($valor): string => htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veterinaria PetCare - Inventario</title>
  <link rel="stylesheet" href="css/estilos_base_v2.css">
</head>

<body>
  <div class="layout">
    <?php $paginaActiva = 'inventario'; include __DIR__ . '/layout/sidebar.php'; ?>

    <main class="">
      <div class="migaja">Gestión de inventario</div>
      <div class="encabezado-pagina">
        <h1>Control de inventario</h1>
      </div>

      <nav class="inventario-pestanas" aria-label="Secciones de inventario">
        <a href="#catalogo" class="activa" aria-current="page">1. Catálogo de productos</a>
        <a href="lotes.php">2. Gestión de lotes</a>
        <a href="pedidos.php#pedidos">3. Pedidos pendientes</a>
      </nav>

      <section id="catalogo" aria-label="Catálogo de productos">
        <div class="inventario-resumen">
          <div class="inventario-tarjeta">
            <h3>Riesgo de desabasto</h3>
            <p><?= count($productosEnRiesgo) ?> productos</p>
            <button class="btn inventario-btn-contactar btn-chico" type="button" data-abrir-agotados>Detalles</button>
          </div>

          <div class="inventario-tarjeta">
            <h3>Total catálogo</h3>
            <p><?= count($productos) ?> productos</p>
          </div>

          <div class="inventario-tarjeta">
            <h3>Movimientos hoy</h3>
            <p><?= $movimientosHoy === null ? 'No disponible' : $movimientosHoy . ' registros' ?></p>
            <a class="btn inventario-btn-contactar btn-chico" href="movimientos_inventario.php">Detalles</a>
          </div>
        </div>

        <h2 class="subtitulo">Productos</h2>

        <div class="inventario-toolbar agregar-buscar">
          <label class="visualmente-oculto" for="buscar-producto">Buscar producto</label>
          <input id="buscar-producto" type="search" class="campo inventario-buscador buscador-inteligente" placeholder="Buscar por nombre o código de barras...">
          <a class="btn" href="agregar_producto.php">+ Agregar producto</a>
        </div>

        <div class="tabla-envoltura">
          <table>
            <thead>
              <tr>
                <th>Imagen</th>
                <th>Codigo</th>
                <th>Nombre</th>
                <th>Existencia</th>
                <th>Precio venta</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($productos)): ?>
                  <?php foreach ($productos as $producto): ?>
                      <?php
                        $imagenProducto = '../../' . ltrim((string) $producto['imagen'], '/');
                        $nombreProducto = (string) $producto['nombre'];
                      ?>
                      <?php if ($producto['existencia'] <= $producto['existencia_minima']): ?>
                        <tr>
                          <td>
                            <div class="foto-miniatura-celda">
                              <img class="producto-imagen foto-miniatura"
                                   src="<?= $escaparInventario($imagenProducto) ?>"
                                   alt="<?= $escaparInventario($nombreProducto) ?>"
                                   title="Click para ampliar imagen"
                                   data-full="<?= $escaparInventario($imagenProducto) ?>"
                                   data-title="<?= $escaparInventario($nombreProducto) ?>"
                                   onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                              <span class="sin-foto" style="display:none;">Imagen no disponible</span>
                            </div>
                          </td>
                          <td><?= $producto['codigo'] ?></td>
                          <td><?= $producto['nombre'] ?></td>
                          <td><span class="estado-chip estado-espera"><?= $producto['existencia'] ?> (bajo)</span></td>
                          <td>$<?= $producto['precio_venta'] ?></td>
                          <td class="acciones">
                            <a class="btn btn-chico btn-info" href="orden_compra.php?id=<?= $producto['id_producto'] ?>">Nueva orden de compra</a>
                            <a class="btn btn-chico" href="editar_producto.php?id=<?= $producto['id_producto'] ?>">Editar</a>
                            <a class="btn btn-chico btn-peligro" href="eliminar_producto.php?id=<?= $producto['id_producto'] ?>">Eliminar</a>
                          </td>
                        </tr>
                      <?php else: ?>
                        <tr>
                          <td>
                            <div class="foto-miniatura-celda">
                              <img class="producto-imagen foto-miniatura"
                                   src="<?= $escaparInventario($imagenProducto) ?>"
                                   alt="<?= $escaparInventario($nombreProducto) ?>"
                                   title="Click para ampliar imagen"
                                   data-full="<?= $escaparInventario($imagenProducto) ?>"
                                   data-title="<?= $escaparInventario($nombreProducto) ?>"
                                   onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                              <span class="sin-foto" style="display:none;">Imagen no disponible</span>
                            </div>
                          </td>
                          <td><?= $producto['codigo'] ?></td>
                          <td><?= $producto['nombre'] ?></td>
                          <td><span class="estado-chip estado-atendida"><?= $producto['existencia'] ?> (normal)</span></td>
                          <td>$<?= $producto['precio_venta'] ?></td>
                          <td class="acciones">
                            <a class="btn btn-chico btn-info" href="orden_compra.php?id=<?= (int)$producto['id_producto'] ?>">Nueva orden de compra</a>
                            <a class="btn btn-chico" href="editar_producto.php?id=<?= (int)$producto['id_producto'] ?>">Editar</a>
                            <a class="btn btn-chico btn-peligro" href="eliminar_producto.php?id=<?= (int)$producto['id_producto'] ?>&nombre=<?= urlencode($producto['nombre']) ?>&imagen=<?= urlencode($producto['imagen']) ?>">Eliminar</a>
                          </td>
                        </tr>
                      <?php endif; ?>
                  <?php endforeach; ?>
              <?php else: ?>
                  <tr><td colspan="6">No hay productos registrados.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>
    </main>
  </div>

  <footer>
    <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
  </footer>

  <div id="modal-visor-imagen" class="modal-visor" role="dialog" aria-modal="true" aria-label="Visor de imagen">
    <span class="btn-cerrar-visor" title="Cerrar (Esc)">&times;</span>
    <div class="visor-contenido">
      <img id="imagen-ampliada" src="" alt="Imagen ampliada" title="Haz clic para cerrar">
      <div id="pie-imagen-ampliada" class="pie-visor"></div>
    </div>
  </div>

  <dialog id="ventana-agotados" class="inventario-dialog" aria-labelledby="titulo-agotados">
    <h2 id="titulo-agotados">Productos agotados</h2>
    <div class="tabla-envoltura">
      <table>
        <thead>
          <tr>
            <th>Producto</th>
            <th>Existencia</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($productosEnRiesgo !== []): ?>
            <?php foreach ($productosEnRiesgo as $productoRiesgo): ?>
              <tr>
                <td><?= $escaparInventario($productoRiesgo['nombre']) ?></td>
                <td><?= (int) $productoRiesgo['existencia'] ?> (bajo)</td>
                <td class="acciones">
                  <a class="btn btn-chico btn-info" href="orden_compra.php?id=<?= (int) $productoRiesgo['id_producto'] ?>">Nueva orden</a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="3">No hay productos con riesgo de desabasto.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
    <div class="inventario-dialog-acciones">
      <button id="cerrar-agotados" class="btn btn-chico" type="button">Cerrar</button>
    </div>
  </dialog>

    <script>
        const ventanaAgotados = document.getElementById('ventana-agotados');
        document.querySelectorAll('[data-abrir-agotados]').forEach((boton) => {
            boton.addEventListener('click', () => ventanaAgotados.showModal());
        });
        document.getElementById('cerrar-agotados').addEventListener('click', () => ventanaAgotados.close());
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && ventanaAgotados.open) ventanaAgotados.close();
        });
    </script>

    <script>
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

            document.addEventListener('click', function (event) {
                var target = event.target;
                if (target && target.classList.contains('foto-miniatura')) {
                    var src = target.getAttribute('data-full') || target.src;
                    var titulo = target.getAttribute('data-title') || '';
                    abrirModal(src, titulo);
                }
            });

            btnCerrar.addEventListener('click', function (event) {
                event.stopPropagation();
                cerrarModal();
            });

            modal.addEventListener('click', function (event) {
                if (event.target === modal || event.target === imgAmpliada || event.target.classList.contains('visor-contenido')) {
                    cerrarModal();
                }
            });

            document.addEventListener('keydown', function (event) {
                if ((event.key === 'Escape' || event.key === 'Esc') && modal.classList.contains('activo')) {
                    cerrarModal();
                }
            });
        });
    </script>

    <script src="js/buscador_inteligente.js"></script>
</body>

</html>
