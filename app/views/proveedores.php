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

        <main>
            <div class="migaja">Gestión de proveedores /</div>
            <div class="encabezado-pagina">
                <h1>Proveedores</h1>
            </div>
            <div class="tarjeta">
                <div class="barra-acciones-busqueda">
                    <form class="busqueda-barra" method="get" action="proveedores.php" role="search">
                        <label class="visualmente-oculto" for="buscarProveedor">Buscar proveedor</label>
                        <input class="campo" id="buscarProveedor" name="buscar" type="search" placeholder="Buscar por nombre" value="<?= htmlspecialchars($_GET['buscar'] ?? '') ?>" autocomplete="off">
                        <?php if (!empty($_GET['buscar'])): ?>
                            <a class="btn btn-secundario btn-chico" href="proveedores.php">Limpiar</a>
                        <?php endif; ?>
                    </form>

                    <a class="btn" href="agregar_proveedor.php">+ Agregar proveedor</a>
                </div>
                <div class="tabla-envoltura">
                    <table class="tabla-proveedores" border="1">
                        <thead>
                            <tr>
                                <th>ID</th><!-- se queda -->
                                <th>Razón social</th><!-- se queda -->
                                
                                <th>Teléfono</th><!-- se queda -->
                                <th>Correo</th><!-- se queda -->
                                <th>Ciudad</th><!-- se queda -->
                                <th>Estado</th><!-- se queda -->
                                <th>Tiempo de entrega</th><!-- se queda -->
                                <th>Acciones</th><!-- se queda -->
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Proveedor A</td>
                                <td>555-1234</td>
                                <td>proveedorA@example.com</td>
                                <td> Ciudad A</td>
                                <td>Activo</td>
                                <td>5 días</td>
                                <td class="acciones">
                                    <a class="btn btn-chico" href="editar_proveedor.php?id=1">Editar</a>
                                    <a class="btn btn-chico btn-peligro" href="eliminar_proveedor.php?id=1" onclick="return confirm('¿Estás seguro de que deseas eliminar este proveedor?');">Eliminar</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>

  <?php $paginaActiva = 'proveedores'; include __DIR__ . '/layout/sidebar.php'; ?>

  <main>
    <div class="migaja">Gestión de proveedores /</div>
    <div class="encabezado-pagina">
      <h1>Proveedores</h1>
    </div>

    <div class="tarjeta">
      <div class="barra-acciones">
        <a class="btn" href="agregar_proveedor.php">+ Agregar proveedor</a>
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
                            <tr>
                                <td><?= htmlspecialchars((string)$proveedor['id_proveedor']) ?></td>
                                <td><?= htmlspecialchars((string)$proveedor['razon_social']) ?></td>
                                <td><?= htmlspecialchars((string)$proveedor['telefono']) ?></td>
                                <td><?= htmlspecialchars((string)($proveedor['correo'] ?? '')) ?></td>
                                <td><?= htmlspecialchars((string)($proveedor['ciudad'] ?? '')) ?></td>
                                <td><?= htmlspecialchars((string)($proveedor['estado'] ?? '')) ?></td>
                                <td><?= htmlspecialchars((string)$proveedor['tiempo_entrega']) ?> días hábiles</td>
                                <td class="acciones">
                                    <a class="btn" href="editar_proveedor.php?id=<?= (int)$proveedor['id_proveedor'] ?>">Editar</a>
                                    <a class="btn btn-peligro" href="eliminar_proveedor.php?id=<?= (int)$proveedor['id_proveedor'] ?>&razon_social=<?= urlencode($proveedor['razon_social']) ?>">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="14">No hay proveedores registrados.</td></tr>
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
</body>
</html>
