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

        <main>
            <div class="migaja">Gestión de proveedores /</div>
            <div class="encabezado-pagina">
                <h1>Proveedores</h1>
            </div>
            <div class="tarjeta">
                <div class="barra-acciones-busqueda">
                    <a class="btn btn-chico" href="agregar_proveedor.php">+Agregar proveedor</a>

                    <form class="busqueda-barra" method="get" action="proveedores.php" role="search">
                        <label class="visualmente-oculto" for="buscarProveedor">Buscar proveedor</label>
                        <input class="campo" id="buscarProveedor" name="buscar" type="search" placeholder="Buscar por nombre" value="<?= htmlspecialchars($_GET['buscar'] ?? '') ?>" autocomplete="off">
                        <button class="btn btn-info btn-chico" type="submit">Buscar</button>
                        <?php if (!empty($_GET['buscar'])): ?>
                            <a class="btn btn-secundario btn-chico" href="proveedores.php">Limpiar</a>
                        <?php endif; ?>
                    </form>
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

    </div>

    <footer>
        <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>
</body>

</html>