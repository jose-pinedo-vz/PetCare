<?php
require_once __DIR__ . '/../controllers/adopcion_venta.php';
$animales = listar_adopciones_ventas();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veterinaria PetCare - Adopción y venta</title>
    <link rel="stylesheet" href="css/estilos_base_v2.css">
</head>

<body>
    <div class="layout">
        <?php $paginaActiva = 'adopcion';
        include __DIR__ . '/layout/sidebar.php'; ?>

        <main>
            <div class="migaja">Gestión de adopción y venta /</div>
            <div class="encabezado-pagina">
                <h1>Adopción y venta</h1>
            </div>
            <div class="tarjeta">
                <div class="barra-acciones">
                    <a class="btn" href="agregar_adopcion_venta.php">+ Agregar adopción y venta</a>
                </div>
                <div class="tabla-envoltura">
                    <table border="1">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Especie</th>
                                <th>Raza</th>
                                <th>Sexo</th>
                                <th>Fecha de nacimiento</th>
                                <th>Color</th>
                                <th>Peso</th>
                                <th>Características</th>
                                <th>Fotografía</th>
                                <th>Procedencia</th>
                                <th>Fecha de ingreso</th>
                                <th>Costo de adquisición</th>
                                <th>Precio de venta</th>
                                <th>Estado</th>
                                <th>Vacunas</th>
                                <th>Desparasitación</th>
                                <th>Observaciones</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($animales)): ?>
                                <?php foreach ($animales as $animal): ?>
                                    <tr>
                                        <td><?= htmlspecialchars((string)$animal['id_animal']) ?></td>
                                        <td><?= htmlspecialchars((string)($animal['especie'] ?? '-')) ?></td>
                                        <td><?= htmlspecialchars((string)($animal['raza'] ?? '-')) ?></td>
                                        <td><?= htmlspecialchars((string)($animal['sexo'] ?? '-')) ?></td>
                                        <td><?= htmlspecialchars(substr((string)$animal['fecha_de_nacimiento'], 0, 10)) ?></td>
                                        <td><?= htmlspecialchars((string)$animal['color']) ?></td>
                                        <td><?= htmlspecialchars((string)$animal['peso']) ?> kg</td>
                                        <td><?= htmlspecialchars((string)$animal['caracteristicas']) ?></td>
                                        <td><?= htmlspecialchars((string)($animal['fotografia'] ?? 'Sin foto')) ?></td>
                                        <td><?= htmlspecialchars((string)$animal['procedencia']) ?></td>
                                        <td><?= htmlspecialchars((string)$animal['fecha_de_ingreso']) ?></td>
                                        <td><?= ($animal['costo_de_adquisicion'] !== null && $animal['costo_de_adquisicion'] !== '') ? '$' . number_format((float)$animal['costo_de_adquisicion'], 2) : '-' ?></td>
                                        <td><?= ($animal['precio_de_venta'] > 0) ? '$' . number_format((float)$animal['precio_de_venta'], 2) : 'No aplica (adopción)' ?></td>
                                        <td><?= htmlspecialchars((string)($animal['estado'] ?? 'Disponible')) ?></td>
                                        <td><?= htmlspecialchars((string)$animal['vacunas']) ?></td>
                                        <td><?= htmlspecialchars(substr((string)$animal['desparasitacion'], 0, 10)) ?></td>
                                        <td><?= htmlspecialchars((string)$animal['observaciones']) ?></td>
                                        <td>
                                            <a class="btn" href="editar_adopcion_venta.php?id=<?= $animal['id_animal'] ?>">Editar</a>
                                            <a class="btn btn-peligro" href="eliminar_adopcion_venta.php?id=<?= $animal['id_animal'] ?>">Eliminar</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="18" style="text-align: center;">No hay registros de adopción y venta.</td>
                                </tr>
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
