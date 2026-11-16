<dialog class="inventario-dialog producto-dialog servicio-dialog" id="modalBuscarServicio" aria-labelledby="tituloBuscarServicio" style="width: 92vw; max-width: 1200px;">
    <div class="producto-dialog-encabezado">
        <h1 id="tituloBuscarServicio">Buscar servicio</h1>
        <form method="dialog">
            <button class="producto-dialog-cerrar" type="submit" aria-label="Cerrar">&times;</button>
        </form>
    </div>

    <div class="busqueda-productos">
        <label class="visualmente-oculto" for="buscarServicio">Buscar servicio</label>
        <input class="campo buscador-inteligente" id="buscarServicio" type="search" placeholder="Escribe el nombre o categoría del servicio..." autocomplete="off">

        <div class="tabla-envoltura busqueda-tabla servicio-tabla-envoltura" style="max-height: 65vh; overflow-y: auto;">
            <table class="servicio-tabla">
                <thead>
                    <tr>
                        <th>Servicio</th>
                        <th>Categoría</th>
                        <th>Descripción</th>
                        <th>Duración</th>
                        <th>Precio</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody id="resultadosServicios">
                    <tr><td colspan="6" class="vacio">Escribe para buscar servicios.</td></tr>
                </tbody>
            </table>
        </div>
        <p class="busqueda-mensaje" id="mensajeBusquedaServicio" role="status" aria-live="polite"></p>
    </div>
</dialog>
