<?php
require_once __DIR__ . '/../models/Ventas_db.php';

$servicios = Ventas::obtenerServicios(conexionDB::obtenerConexion());
?>

<dialog class="inventario-dialog producto-dialog servicio-dialog" id="modalBuscarServicio" aria-labelledby="tituloBuscarServicio">
    <div class="producto-dialog-encabezado">
        <h1 id="tituloBuscarServicio">Buscar servicio</h1>
        <form method="dialog">
            <button class="producto-dialog-cerrar" type="submit" aria-label="Cerrar">&times;</button>
        </form>
    </div>

    <div class="busqueda-productos">
        <label class="visualmente-oculto" for="buscarServicio">Buscar servicio</label>
        <input class="campo buscador-inteligente" id="buscarServicio" type="search" placeholder="Escribe el nombre o categoría del servicio..." autocomplete="off">

        <div class="tabla-envoltura busqueda-tabla servicio-tabla-envoltura">
            <table class="servicio-tabla">
                <thead>
                    <tr>
                        <th>Servicio</th>
                        <th>Categoría</th>
                        <th>Descripción</th>
                        <th>Duración</th>
                        <th>Precio</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($servicios)): ?>
                        <tr>
                            <td colspan="7" class="vacio">Escribe para buscar servicios.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($servicios as $s): ?>
                            <tr>
                                <td><?php echo htmlspecialchars((string)$s['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($s['categoria'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($s['descripcion']); ?></td>
                                <td><?php echo htmlspecialchars($s['duracion']); ?></td>
                                <td>$<?php echo number_format($s['precio'], 2); ?></td>
                                <td><?php echo htmlspecialchars($s['estado']); ?></td>
                                <td>
                                    <a class="btn btn-chico btn-info" href="../controllers/busca_servicio.php?accion=agregar_servicio&nombre=<?php echo urlencode($s['nombre']); ?>&categoria=<?php echo urlencode($s['categoria']); ?>&descripcion=<?php echo urlencode($s['descripcion']); ?>&duracion=<?php echo urlencode($s['duracion']); ?>&precio=<?php echo urlencode($s['precio']); ?>&estado=<?php echo urlencode($s['estado']); ?>">Seleccionar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <p class="busqueda-mensaje" id="mensajeBusqueda" role="status" aria-live="polite"></p>
    </div>
</dialog>