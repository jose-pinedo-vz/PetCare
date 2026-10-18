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
        <?php $paginaActiva = 'proveedores';
        include __DIR__ . '/layout/sidebar.php'; ?>

        <div class="tabla-envoltura">


        <main>

          <div class="migaja">Gestión de Proveedores /</div>
          <div class="encabezado-pagina">
            <h1>Proveedores</h1>
          </div>

            <div style="margin-bottom: 20px;">
                <a class="btn" href="agregar_proveedor.php">+ Agregar proveedor</a>
            </div>
            <div style="overflow-x: auto; border: 1px solid var(--border-light); border-radius: 6px;">
            <table border="1" style="width: 100%; min-width: 1400px; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Razón social</th>
                        <th>Nombre comercial</th>
                        <th>RFC</th>
                        <th>Teléfono</th>
                        <th>Correo</th>
                        <th>Domicilio</th>
                        <th>Ciudad</th>
                        <th>Estado</th>
                        <th>Código postal</th>
                        <th>Condición de pago</th>
                        <th>Tiempo de entrega</th>
                        <th>Observaciones</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                            <tbody>
                    <?php if (!empty($proveedores)): ?>
                        <?php foreach ($proveedores as $proveedor): ?>
                            <tr>
                                <td><?= htmlspecialchars((string)$proveedor['id_proveedor']) ?></td>
                                <td><?= htmlspecialchars((string)$proveedor['razon_social']) ?></td>
                                <td><?= htmlspecialchars((string)$proveedor['nombre_comercial']) ?></td>
                                <td><?= htmlspecialchars((string)$proveedor['rfc']) ?></td>
                                <td><?= htmlspecialchars((string)$proveedor['telefono']) ?></td>
                                <td><?= htmlspecialchars((string)($proveedor['correo'] ?? '')) ?></td>
                                <td><?= htmlspecialchars((string)($proveedor['domicilio'] ?? '')) ?></td>
                                <td><?= htmlspecialchars((string)($proveedor['ciudad'] ?? '')) ?></td>
                                <td><?= htmlspecialchars((string)($proveedor['estado'] ?? '')) ?></td>
                                <td><?= htmlspecialchars((string)($proveedor['codigo_postal'] ?? '')) ?></td>
                                <td><?= htmlspecialchars((string)($proveedor['condicion_pago'] ?? '')) ?></td>
                                <td><?= htmlspecialchars((string)$proveedor['tiempo_entrega']) ?> días hábiles</td>
                                <td><?= htmlspecialchars((string)($proveedor['observaciones'] ?? '')) ?></td>
                                <td style="padding: 15px; white-space: nowrap;">
                                    <a class="btn" href="editar_proveedor.php?id=<?= (int)$proveedor['id_proveedor'] ?>">Editar</a>
                                    <a class="btn btn-peligro" href="eliminar_proveedor.php?id=<?= (int)$proveedor['id_proveedor'] ?>&razon_social=<?= urlencode($proveedor['razon_social']) ?>" style="margin-left: 6px;">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="14">No hay proveedores registrados.</td></tr>
                    <?php endif; ?>
            </tbody>
            </table>
            </div>
        </main>

        </div>

    </div>
    </nav>

    

    <footer>
        <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
