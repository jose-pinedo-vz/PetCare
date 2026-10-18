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
          <input type="text" class="campo inventario-buscador" placeholder="Buscar por código de barra...">
          <a class="btn" href="agregar_producto.php">+ Agregar producto</a>
        </div>

        <div class="tabla-envoltura">
          <table>
            <thead>
              <tr>
                <th>Imagen</th>
                <th>Codigo de barra</th>
                <th>Nombre</th>
                <th>Existencia</th>
                <th>Precio venta</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><img class="producto-imagen" src="ruta/a/imagen1.jpg" alt="Producto 1"></td>
                <td>1234567890</td>
                <td>Pelota</td>
                <td>2 (bajo)</td>
                <td>30.50</td>
                <td class="acciones">
                  <a class="btn btn-chico btn-info" href="enespera.php?id=1">Pedir</a>
                  <a class="btn btn-chico" href="editar_producto.php?id=1">Editar</a>
                  <a class="btn btn-chico btn-peligro" href="eliminar_producto.php?id=1">Eliminar</a>
                </td>
              </tr>
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
