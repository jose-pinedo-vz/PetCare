<dialog class="inventario-dialog producto-dialog" id="modalBuscarProducto" aria-labelledby="tituloBuscarProducto">
    <div class="producto-dialog-encabezado">
        <h1 id="tituloBuscarProducto"> Buscar producto</h1>
        <form method="dialog">
            <button class="producto-dialog-cerrar" type="submit" aria-label="Cerrar">&times;</button>
        </form>
    </div>

    <div class="busqueda-productos">
        <label class="visualmente-oculto" for="buscarProducto">Nombre o código del producto</label>
        <input class="campo" id="buscarProducto" type="search" placeholder="Escribe el nombre o código del producto..." autocomplete="off" autofocus>

        <div class="tabla-envoltura busqueda-tabla">
            <table>
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody id="resultadosProductos">
                    <tr><td colspan="5" class="vacio">Escribe para buscar productos.</td></tr>
                </tbody>
            </table>
        </div>
        <p class="busqueda-mensaje" id="mensajeBusqueda" role="status" aria-live="polite"></p>
    </div>
</dialog>
