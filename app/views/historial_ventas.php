<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de ventas - PetCare</title>
    <link rel="stylesheet" href="css/estilos_base_v2.css">
</head>
<body>
    <div class="layout">
        <?php $paginaActiva = 'ventas'; include __DIR__ . '/layout/sidebar.php'; ?>

        <main>
            <div class="migaja">Punto de venta </div>
            <div class="encabezado-pagina">
                <h1>Historial de ventas</h1>
                <a class="btn-secundario" href="Ventas.php">&larr; Regresar a ventas</a>
            </div>

            <section class="historial-filtros" aria-label="Filtros del historial de ventas">
                <div class="historial-filtro historial-filtro-busqueda">
                    <label for="buscarVenta">Buscar folio, cliente o empleado</label>
                    <div class="historial-control-icono">
                        <span aria-hidden="true">⌕</span>
                        <input class="campo buscador-inteligente" id="buscarVenta" type="search" placeholder="Ej. V0000012, Juan Pérez, Ana López...">
                    </div>
                </div>
                <div class="historial-filtro">
                    <label for="fechaDesde">Desde</label>
                    <input class="campo" id="fechaDesde" type="date">
                </div>
                <div class="historial-filtro">
                    <label for="fechaHasta">Hasta</label>
                    <input class="campo" id="fechaHasta" type="date">
                </div>
                <div class="historial-filtro">
                    <label for="estadoVenta">Estado</label>
                    <select class="campo" id="estadoVenta">
                        <option value="">Todos los estados</option>
                        <option value="Pagada">Pagada</option>
                        <option value="Pendiente">Pendiente</option>
                        <option value="Cancelada">Cancelada</option>
                    </select>
                </div>
                <div class="historial-filtro-acciones">
                    <button class="btn historial-buscar btn-chico" id="btnBuscarHistorial" type="button"><span aria-hidden="true"></span> Buscar</button>
                    <button class="btn btn-secundario historial-limpiar btn-chico" id="btnLimpiarHistorial" type="button"><span aria-hidden="true"></span> Limpiar filtros</button>
                </div>
            </section>

            <div class="tabla-envoltura historial-tabla-envoltura">
                <table class="historial-tabla">
                    <thead>
                        <tr>
                            <th>Folio</th>
                            <th>Fecha <span aria-hidden="true">↕</span></th>
                            <th>Cliente</th>
                            <th>Empleado</th>
                            <th>Artículos</th>
                            <th>Total</th>
                            <th>Forma de pago</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaHistorial">
                        <tr><td colspan="9" class="vacio">Cargando ventas...</td></tr>
                    </tbody>
                </table>
                <div class="historial-pie-tabla">
                    <span id="textoConteo">Mostrando 0 - 0 de 0 ventas</span>
                    <nav class="historial-paginacion" id="paginacionHistorial" aria-label="Paginación del historial"></nav>
                </div>
            </div>

            <dialog class="inventario-dialog producto-dialog" id="modalDetalleVenta" aria-labelledby="tituloDetalleVenta" style="width: 92vw; max-width: 900px;">
                <div class="producto-dialog-encabezado">
                    <h1 id="tituloDetalleVenta" style="color: var(--white, #fff);">Detalle de venta</h1>
                    <form method="dialog">
                        <button class="producto-dialog-cerrar" type="submit" aria-label="Cerrar">&times;</button>
                    </form>
                </div>
                <div id="contenidoDetalleVenta" style="max-height: 70vh; overflow-y: auto;"></div>
            </dialog>

            <dialog class="inventario-dialog producto-dialog" id="modalFacturaVenta" aria-labelledby="tituloFacturaVenta" style="width: 92vw; max-width: 560px;">
                <div class="producto-dialog-encabezado">
                    <h1 id="tituloFacturaVenta">Generar factura</h1>
                    <form method="dialog">
                        <button class="producto-dialog-cerrar" type="submit" aria-label="Cerrar">&times;</button>
                    </form>
                </div>
                <form class="factura-formulario" id="formFacturaVenta" novalidate>
                    <label class="label-campo" for="facturaRfc">RFC</label>
                    <input class="campo" id="facturaRfc" name="rfc" type="text" maxlength="13" autocomplete="off" style="text-transform: uppercase;" required>
                    <label class="label-campo" for="facturaRazonSocial">Razón social</label>
                    <input class="campo" id="facturaRazonSocial" name="razon_social" type="text" maxlength="100" autocomplete="organization" required>
                    <label class="label-campo" for="facturaCodigoPostal">Código postal fiscal</label>
                    <input class="campo" id="facturaCodigoPostal" name="codigo_postal" type="text" maxlength="5" inputmode="numeric" autocomplete="postal-code" required>
                    <label class="label-campo" for="facturaCorreo">Correo electrónico</label>
                    <input class="campo" id="facturaCorreo" name="correo" type="email" autocomplete="email" required>
                    <p id="errorFactura" role="alert" style="color: #c0392b; min-height: 1.2em; margin: 8px 0;"></p>
                    <div class="pago-acciones">
                        <button class="btn btn-secundario btn-chico" id="btnCancelarFactura" type="button">Cancelar</button>
                        <button class="btn btn-chico" id="btnGuardarFactura" type="submit">Guardar factura</button>
                    </div>
                </form>
            </dialog>
        </main>
    </div>
    <script src="js/historial_ventas.js"></script>
</body>
</html>