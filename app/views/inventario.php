<?php require_once __DIR__ . '/../Models/Productos.php';
 $productos = Productos::obtenerDatosProductosActivos();
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
            <p>3 productos</p>
            <button class="btn inventario-btn-contactar btn-chico" type="button" data-abrir-agotados>Detalles</button>
          </div>

          <div class="inventario-tarjeta">
            <h3>Total catálogo</h3>
            <p>128 productos</p>
          </div>

          <div class="inventario-tarjeta">
            <h3>Movimientos hoy</h3>
            <p>14 registros</p>
          </div>
        </div>

        <h2 class="subtitulo">Productos</h2>

        <div class="agregar-buscar">
          <input type="text" class="campo buscador-inteligente" placeholder="Buscar...">
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
                      <?php if ($producto['existencia'] <= $producto['existencia_minima']): ?>
                        <tr>
                          <td><img class="producto-imagen" src="../../<?= $producto['imagen'] ?>" alt="<?= $producto['nombre'] ?>"></td>
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
                          <td><img class="producto-imagen" src="../../<?= $producto['imagen'] ?>" alt="<?= $producto['nombre'] ?>"></td>
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
          <tr>
            <td>Pelota</td>
            <td>2 (bajo)</td>
            <td class="acciones">
              <a class="btn btn-chico btn-info" href="orden_compra.php?id=1">Nueva orden</a>
            </td>
          </tr>
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

    <script src="js/buscador_inteligente.js"></script>
</body>

</html>
