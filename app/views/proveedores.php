<?php
// Mostrar nombre del usuario
session_start();
if (!isset($_SESSION['usuario']))
{
    header("Location: ../views/login.php");
    exit();
}
$usuario=$_SESSION['usuario'];
// Mostrar nombre del usuario
require_once __DIR__ . '/../controllers/proveedor.php';
$proveedores = listar_proveedores();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veterinaria PetCare - Proveedores</title>
    <link rel="stylesheet" href="css/estilos_base_v2.css">
</head>

<body>
    <div class="layout">
         <?php $paginaActiva = 'proveedores'; include __DIR__ . '/layout/sidebar.php'; ?>
        <main>
            <div class="migaja">Gestión de proveedores /</div>
            <div class="encabezado-pagina">
            <h1>Proveedores</h1>
            </div>
            <div class="tarjeta">
            <div class="barra-acciones-busqueda">
                <div class="busqueda-barra" role="search">
                    <label class="visualmente-oculto" for="buscarProveedor">Buscar proveedor</label>
                    <input class="campo buscador-inteligente" id="buscarProveedor" type="search" placeholder="Buscar por datos del proveedor..." autocomplete="off">
                </div>
                <div class="barra-acciones">
                    <a class="btn" href="agregar_proveedor.php">+ Agregar proveedor</a>
                </div>
            </div>
            <div class="tabla-envoltura">
            <table class="tabla-ancha">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Razón social</th>
                    <th>Teléfono</th>
                    <th>Correo</th>
                    <th>Ciudad</th>
                    <th>Estado</th>
                    <th>Tiempo de entrega</th>
                    <th>Acciones</th>
                </tr>
            </thead>
                <tbody>
                            <?php if (!empty($proveedores)): ?>
                                <?php foreach ($proveedores as $proveedor): ?>
                                    <tr data-proveedor>
                                        <td><?= htmlspecialchars((string)$proveedor['id_proveedor']) ?></td>
                                        <td><?= htmlspecialchars((string)$proveedor['razon_social']) ?></td>
                                        <td><?= htmlspecialchars((string)$proveedor['telefono']) ?></td>
                                        <td><?= htmlspecialchars((string)($proveedor['correo'] ?? '')) ?></td>
                                        <td><?= htmlspecialchars((string)($proveedor['ciudad'] ?? '')) ?></td>
                                        <td><?= htmlspecialchars((string)($proveedor['estado'] ?? '')) ?></td>
                                        <td><?= htmlspecialchars((string)$proveedor['tiempo_entrega']) ?> días hábiles</td>
                                        <td class="acciones">
                                            <a class="btn btn-chico" href="editar_proveedor.php?id=<?= (int)$proveedor['id_proveedor'] ?>">Editar</a>
                                            <a class="btn btn-chico btn-peligro" href="eliminar_proveedor.php?id=<?= (int)$proveedor['id_proveedor'] ?>&razon_social=<?= urlencode($proveedor['razon_social']) ?>">Eliminar</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="8">No hay proveedores registrados.</td></tr>
                            <?php endif; ?>
                            <tr id="sinResultadosProveedor" hidden>
                                <td colspan="8">No hay coincidencias para la búsqueda.</td>
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
  <script>
    const buscarProveedor = document.getElementById('buscarProveedor');
    const cuerpoTablaProveedores = document.querySelector('.tabla-envoltura tbody');

    if (buscarProveedor && cuerpoTablaProveedores) {
        const filasProveedor = cuerpoTablaProveedores.querySelectorAll('tr[data-proveedor]');
        const sinResultadosProveedor = document.getElementById('sinResultadosProveedor');

        buscarProveedor.addEventListener('input', () => {
            const termino = buscarProveedor.value.trim().toLocaleLowerCase();
            let resultados = 0;

            filasProveedor.forEach((fila) => {
                const texto = Array.from(fila.cells)
                    .slice(0, -1)
                    .map((celda) => celda.textContent)
                    .join(' ')
                    .toLocaleLowerCase();
                const coincide = texto.includes(termino);
                fila.hidden = !coincide;
                resultados += coincide ? 1 : 0;
            });

            sinResultadosProveedor.hidden = filasProveedor.length === 0 || resultados > 0;
        });
    }
  </script>
</body>
</html>
