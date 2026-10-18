<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veterinaria PetCare - Ventas</title>
    <link rel="stylesheet" href="css/estilos_base_v2.css">
</head>
<body>
    <div class="layout">
        <?php $paginaActiva = 'ventas'; include __DIR__ . '/layout/sidebar.php'; ?>

        <main>
            <div class="migaja">Punto de venta /</div>
            <header class="encabezado-pagina">
                <h1>Punto de venta</h1>
                <div class="venta-cajero">Cajero: <strong>Nombre de empleado</strong> | Turno: Matutino</div>
            </header>

            <div class="venta-contenedor">
                <section class="tarjeta venta-carrito" aria-labelledby="titulo-carrito">
                    <h2 id="titulo-carrito">Productos de la venta</h2>
                    <div class="venta-captura">
                        <div class="input-group venta-codigo">
                            <label for="barcodeInput">Código de barras</label>
                            <div class="venta-busqueda">
                                <input class="campo" type="text" id="barcodeInput" placeholder="Escanea o escribe un código" autocomplete="off">
                                <button class="btn btn-info" id="btnBuscarProducto" type="button">Buscar producto</button>
                            </div>
                            <span class="nota-campo">El producto se agregará a la lista de venta.</span>
                        </div>
                        
                        <div class="input-group venta-cliente">
                            <label for="clienteInput">Cliente</label>
                            <input class="campo" type="tel" id="clienteInput" placeholder="Teléfono (opcional)" inputmode="numeric" autocomplete="off">
                        </div>
                    </div>

                    <div class="tabla-envoltura venta-tabla-envoltura">
                        <table class="venta-tabla">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Código</th>
                                    <th>Precio U.</th>
                                    <th>Cant.</th><!--este campo se puede hacer de ingremento(+,-) si asi se quiere -->
                                    <th>Descuento</th><!--este campo se puede hacer de entrada si se desea agregar algun descuento, o igual de incremento (+,-) -->
                                    <th>Subtotal</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>

                            <tbody id="cartTableBody">
                                <tr>
                                    <td>Pelota</td>
                                    <td>12345</td>
                                    <td>$10.00</td>
                                    <td>2</td>
                                    <td>$0.00</td>
                                    <td>$20.00</td>
                                    <td class="acciones">
                                        <a class="btn btn-peligro" href="enespera.php?id=1">Eliminar</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="venta-carrito-acciones">
                        <button class="btn btn-info" id="btnAgregarServicio" type="button">Agregar servicio</button>
                        <button class="btn btn-peligro" id="btnNuevaVenta" type="button">Nueva venta</button> <!-- este boton deberia limpiar la venta actual y reiniciar todo, tambien se deberia preguntar si se desea cancelar la venta actual, o eso interprete -->
                    </div>
                    <p class="venta-mensaje" id="ventaMensaje" role="status" aria-live="polite"></p>
                </section>

                <div class="venta-lateral">
                    <aside class="tarjeta venta-resumen" aria-label="Resumen de venta">
                        <h2>Resumen</h2>
                        <div class="venta-totales">
                            <div><span>Artículos</span><strong id="lblItems">2</strong></div>
                            <div><span>Subtotal</span><strong id="lblSubtotal">$20.00</strong></div>
                            <div><span>IVA (16%)</span><strong id="lblIva">$3.20</strong></div>
                            <div class="venta-total"><span>Total</span><strong id="lblTotal">$23.20 MXN</strong></div>
                        </div>
                        <div class="venta-acciones">
                            <button class="btn btn-info" id="btnCobrar" type="button">Cobrar</button>
                            <button class="btn btn-peligro" id="btnCancelarVenta" type="button">Cancelar venta</button> <!-- este boton deberia cancelar la venta actual, y se deberia preguntar si se desea cancelar la venta actual y limpiar la ventana actual, o eso entnedi -->
                        </div>
                    </aside>
                    <section class="tarjeta venta-herramientas" aria-labelledby="tituloHerramientasVenta">
                        <h2 id="tituloHerramientasVenta">Accesos</h2>
                        <div class="venta-reportes">
                            <button class="btn-secundario" type="button">Corte de caja</button>
                            <a class="btn-secundario" href="historial_ventas.php">Historial de ventas</a>
                        </div>
                    </section>
                </div>
            </div>
        </main>
    </div>
    <?php include __DIR__ . '/buscar_productos.php'; ?>
    <?php include __DIR__ . '/buscar_servicios.php'; ?>
    <?php include __DIR__ . '/pagar_vents.php'; ?>
    <!-- Backend: tras validar y registrar el pago, abrir este dialgo para que se elija imprimir ticket, generar factura o iniciar una nueva venta. -->
    <dialog class="inventario-dialog producto-dialog resultado-dialog" id="modalPagoConfirmado" aria-labelledby="tituloPagoConfirmado">
        <div class="producto-dialog-encabezado">
            <h1 id="tituloPagoConfirmado">Pago confirmado</h1>
            <form method="dialog">
                <button class="producto-dialog-cerrar" type="submit" aria-label="Cerrar">&times;</button>
            </form>
        </div>
        <div class="resultado-contenido">
            <p class="resultado-mensaje">La venta se marcó como pagada.</p>
            <div class="venta-total">Total cobrado: <strong>$23.20 MXN</strong></div>
            <p class="resultado-factura" id="mensajeFactura" role="status" hidden></p>
            <div class="venta-carrito-acciones">
                <button class="btn-secundario" id="btnImprimirTicket" type="button">Imprimir ticket</button>
                <button class="btn" id="btnGenerarFactura" type="button">Generar factura</button>
                <button class="btn-secundario" id="btnNuevaVentaConfirmada" type="button">Nueva venta</button>
            </div>
        </div>
    </dialog>
    <dialog class="inventario-dialog producto-dialog factura-dialog" id="modalDatosFactura" aria-labelledby="tituloDatosFactura">
        <div class="producto-dialog-encabezado">
            <h1 id="tituloDatosFactura">Datos para facturación</h1>
            <form method="dialog">
                <button class="producto-dialog-cerrar" type="submit" aria-label="Cerrar">&times;</button>
            </form>
        </div>
        <form class="factura-formulario" id="formDatosFactura">
            <label class="label-campo" for="facturaRfc">RFC</label>
            <input class="campo" id="facturaRfc" name="rfc" type="text" autocomplete="off" required>
            <label class="label-campo" for="facturaRazonSocial">Razón social</label>
            <input class="campo" id="facturaRazonSocial" name="razonSocial" type="text" autocomplete="organization" required>
            <label class="label-campo" for="facturaCodigoPostal">Código postal fiscal</label>
            <input class="campo" id="facturaCodigoPostal" name="codigoPostal" type="text" inputmode="numeric" autocomplete="postal-code" required>
            <label class="label-campo" for="facturaCorreo">Correo electrónico</label>
            <input class="campo" id="facturaCorreo" name="correo" type="email" autocomplete="email" required>
            <div class="pago-acciones">
                <button class="btn-secundario" id="btnVolverPagoConfirmado" type="button">Cancelar</button>
                <button class="btn" type="submit">Continuar</button>
            </div>
        </form>
    </dialog>
    <script>
        const botonBuscarProducto = document.getElementById('btnBuscarProducto');
        const modalBuscarProducto = document.getElementById('modalBuscarProducto');
        const botonAgregarServicio = document.getElementById('btnAgregarServicio');
        const modalBuscarServicio = document.getElementById('modalBuscarServicio');
        const botonCobrar = document.getElementById('btnCobrar');
        const modalPagarVenta = document.getElementById('modalPagarVenta');
        const modalPagoConfirmado = document.getElementById('modalPagoConfirmado');
        const modalDatosFactura = document.getElementById('modalDatosFactura');

        botonBuscarProducto.addEventListener('click', () => {
            modalBuscarProducto.showModal();
        });

        botonAgregarServicio.addEventListener('click', () => {
            modalBuscarServicio.showModal();
        });

        botonCobrar.addEventListener('click', () => {
            modalPagarVenta.showModal();
        });

        document.getElementById('btnGenerarFactura').addEventListener('click', () => {
            modalPagoConfirmado.close();
            modalDatosFactura.showModal();
        });

        document.getElementById('btnVolverPagoConfirmado').addEventListener('click', () => {
            modalDatosFactura.close();
            modalPagoConfirmado.showModal();
        });

        document.getElementById('formDatosFactura').addEventListener('submit', (evento) => {
            evento.preventDefault();
            modalDatosFactura.close();
            const mensajeFactura = document.getElementById('mensajeFactura');
            mensajeFactura.textContent = 'Datos fiscales capturados. La factura queda pendiente de emisión.';
            mensajeFactura.hidden = false;
            modalPagoConfirmado.showModal();
        });

        document.getElementById('btnNuevaVentaConfirmada').addEventListener('click', () => {
            window.location.reload();
        });
    </script>
</body>
</html>
