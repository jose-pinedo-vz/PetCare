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

    <main>
      <div class="migaja">Gestión de Inventario /</div>
      <div class="encabezado-pagina">
        <h1>Control de inventario</h1>
      </div>

      <div class="tarjeta">
        <div class="inventario-resumen">
          <div class="inventario-tarjeta">
            <h3>Riesgo de desabasto</h3>
            <p>-----------</p>
            <button class="btn inventario-btn-contactar" type="button" data-abrir-agotados>detalles</button>
          </div>

          <div class="inventario-tarjeta">
            <h3>Total catálogo</h3>
            <p>x Productos</p>
          </div>

          <div class="inventario-tarjeta">
            <h3>Movimientos hoy</h3>
            <p>------------</p>
          </div>
        </div>

        <h2 class="subtitulo">Productos</h2>

        <div class="inventario-toolbar">
          <input type="text" class="campo inventario-buscador" placeholder="Buscar por código o nombre...">
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
                          <td class="existencia-baja"><?= $producto['existencia'] ?> (bajo)</td>
                          <td>$<?= $producto['precio_venta'] ?></td>
                          <td class="acciones">
                            <a class="btn btn-chico btn-info" href="enespera.php?id=<?= $producto['id_producto'] ?>">Pedir</a>
                            <a class="btn btn-chico" href="editar_producto.php?id=<?= $producto['id_producto'] ?>">Editar</a>
                            <a class="btn btn-chico btn-peligro" href="eliminar_producto.php?id=<?= $producto['id_producto'] ?>">Eliminar</a>
                          </td>
                        </tr>
                      <?php else: ?>
                        <tr>
                          <td><img class="producto-imagen" src="../../<?= $producto['imagen'] ?>" alt="<?= $producto['nombre'] ?>"></td>
                          <td><?= $producto['codigo'] ?></td>
                          <td><?= $producto['nombre'] ?></td>
                          <td><?= $producto['existencia'] ?></td>
                          <td>$<?= $producto['precio_venta'] ?></td>
                          <td class="acciones">
                            <a class="btn btn-chico btn-info" href="enespera.php?id=<?= (int)$producto['id_producto'] ?>">Pedir</a>
                            <a class="btn btn-chico" href="editar_producto.php?id=<?= (int)$producto['id_producto'] ?>">Editar</a>
                            <a class="btn btn-chico btn-peligro" href="eliminar_producto.php?id=<?= (int)$producto['id_producto'] ?>">Eliminar</a>
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
      </div>
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
                            <a class="btn btn-chico btn-info" href="enespera.php?id=1">Pedir</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="inventario-dialog-acciones">
            <button id="cerrar-agotados" class="btn" type="button">Cerrar</button>
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
</body>

</html>
