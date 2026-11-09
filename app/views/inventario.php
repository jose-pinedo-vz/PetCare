<?php
require_once __DIR__ . '/../models/Cliente.php';
$listaClientes = Cliente::obtenerTodos();
// Mostrar nombre del usuario
session_start();
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

        <div class="inventario-toolbar agregar-buscar">
          <label class="visualmente-oculto" for="buscar-producto">Buscar producto</label>
          <input id="buscar-producto" type="search" class="campo inventario-buscador buscador-inteligente" placeholder="Buscar por nombre o código de barras...">
          <a class="btn" href="agregar_producto.php">+ Agregar producto</a>
        </div>

        <div class="tabla-envoltura">
          <table class="inventario-tabla">
            <thead>
              <tr>
                <th>Imagen</th>
                <th>Código de barra</th>
                <th>Nombre</th>
                <th>Existencia</th>
                <th>Precio venta</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><img class="producto-imagen" src="img/logo.svg" alt="Producto 1"></td>
                <td>1234567890</td>
                <td>Pelota</td>
                <td><span class="estado-chip estado-espera">2 (bajo)</span></td>
                <td>$30.50</td>
                <td class="acciones">
                  <a class="btn btn-chico btn-info" href="orden_compra.php?id=1">Nueva orden</a>
                  <a class="btn btn-chico" href="editar_producto.php?id=1">Editar</a>
                  <a class="btn btn-chico btn-peligro" href="eliminar_producto.php?id=1">Eliminar</a>
                </td>
              </tr>

              <tr>
                <td><img class="producto-imagen" src="img/logo3.svg" alt="Producto 2"></td>
                <td>2345678901</td>
                <td>Collar</td>
                <td><span class="estado-chip estado-atendida">18 (normal)</span></td>
                <td>$120.00</td>
                <td class="acciones">
                  <a class="btn btn-chico btn-info" href="orden_compra.php?id=2">Nueva orden</a>
                  <a class="btn btn-chico" href="editar_producto.php?id=2">Editar</a>
                  <a class="btn btn-chico btn-peligro" href="eliminar_producto.php?id=2">Eliminar</a>
                </td>
              </tr>
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
    const buscadorProductos = document.getElementById('buscar-producto');
    const filasProductos = [...document.querySelectorAll('tbody tr')];
    const ventanaAgotados = document.getElementById('ventana-agotados');

    buscadorProductos?.addEventListener('input', () => {
      const consulta = buscadorProductos.value.trim().toLocaleLowerCase('es');

      filasProductos.forEach((fila) => {
        const coincidencia = fila.textContent.toLocaleLowerCase('es').includes(consulta);
        fila.hidden = !coincidencia;
      });
    });

    document.querySelectorAll('[data-abrir-agotados]').forEach((boton) => {
      boton.addEventListener('click', () => ventanaAgotados.showModal());
    });

    document.getElementById('cerrar-agotados').addEventListener('click', () => {
      ventanaAgotados.close();
    });

    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape' && ventanaAgotados.open) {
        ventanaAgotados.close();
      }
    });
  </script>
</body>

</html>
