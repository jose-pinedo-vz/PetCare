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
                    <tr>
                        <td><strong>Consulta veterinaria</strong></td>
                        <td>Consulta</td>
                        <td>Revisión general de la mascota</td>
                        <td>30 min</td>
                        <td>$320.00</td>
                        <td><span class="historial-estado pagada">Activo</span></td>
                        <td><button class="btn btn-info btn-chico" type="button">Agregar</button></td>
                    </tr>
                    <tr>
                        <td><strong>Baño para perro</strong></td>
                        <td>Estética</td>
                        <td>Baño y secado</td>
                        <td>60 min</td>
                        <td>$250.00</td>
                        <td><span class="historial-estado pagada">Activo</span></td>
                        <td><button class="btn btn-info btn-chico" type="button">Agregar</button></td>
                    </tr>
                    <tr>
                        <td><strong>Corte de uñas</strong></td>
                        <td>Estética</td>
                        <td>Corte y limado de uñas</td>
                        <td>15 min</td>
                        <td>$90.00</td>
                        <td><span class="historial-estado cancelada">Inactivo</span></td>
                        <td><button class="btn btn-info btn-chico" type="button" disabled>Agregar</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</dialog>