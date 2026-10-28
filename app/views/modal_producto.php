<?php
require_once __DIR__ . '/../models/Ventas_db.php';
?>
<dialog id="modalBuscarProducto" class="modal">
    <div class="modal-contenido">
        <h2>Buscar Producto</h2>
        
        <!-- Formulario opcional para filtrar por texto -->
        <form method="GET" class="input-group" style="margin-bottom: 15px;">
            <input class="campo" type="text" name="buscar_prod" placeholder="Nombre o código del producto...">
            <button class="btn btn-info" type="submit">Filtrar</button>
        </form>

        <div class="tabla-envoltura" style="max-height: 300px; overflow-y: auto;">
            <table class="venta-tabla">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $busqueda = $_GET['buscar_prod'] ?? '';
                    $resultado = Ventas::cargar(conexionDB::obtenerConexion(), $busqueda);

                    if (empty($resultado)) {
                        echo '<tr><td colspan="5" style="text-align: center;">No se encontraron productos</td></tr>';
                    } else {
                        foreach ($resultado as $p) {
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($p['codigo']); ?></td>
                                <td><?php echo htmlspecialchars($p['nombre']); ?></td>
                                <td>$<?php echo number_format($p['precio_venta'], 2); ?></td>
                                <td><?php echo $p['existencia']; ?></td>
                                <td>
                                    <a class="btn" href="Venta.php?accion=agregar&codigo=<?php echo urlencode($p['codigo']); ?>&nombre=<?php echo urlencode($p['nombre']); ?>&precio_venta=<?php echo urlencode($p['precio_venta']); ?>&existencia=<?php echo urlencode($p['existencia']); ?>">Seleccionar</a>
                                </td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div style="margin-top: 15px; text-align: right;">
            <button class="btn btn-peligro" type="button" onclick="document.getElementById('modalBuscarProducto').close()">Cerrar</button>
        </div>
    </div>
</dialog>