<dialog class="inventario-dialog producto-dialog pago-dialog" id="modalPagarVenta" aria-labelledby="tituloPagarVenta">
    <div class="producto-dialog-encabezado pago-dialog-encabezado">
        <h1 id="tituloPagarVenta">Pagar venta</h1>
        <form method="dialog">
            <button class="producto-dialog-cerrar" type="submit" aria-label="Cerrar">&times;</button>
        </form>
    </div>

    <div class="pago-contenido">
        <section class="pago-resumen" aria-labelledby="tituloResumenPago">
            <h2 id="tituloResumenPago"><span aria-hidden="true"></span> Resumen de la venta</h2>
            <div class="pago-tabla-envoltura">
                <table class="pago-tabla">
                    <thead>
                        <tr>
                            <th>Producto / Servicio</th>
                            <th>Cant.</th>
                            <th>P. Unit.</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="pago-producto-icono" aria-hidden="true"></span><span class="pago-producto-detalle"><strong>Pelota</strong><small>Cód. 12345</small></span></td>
                            <td>2</td>
                            <td>$10.00</td>
                            <td>$20.00</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="pago-totales">
                <div><span>Subtotal</span><strong>$20.00</strong></div>
                <div><span>IVA (16%)</span><strong>$3.20</strong></div>
                <div class="pago-total"><span>Total</span><strong>$23.20 MXN</strong></div>
            </div>
        </section>

        <section class="pago-panel" aria-labelledby="tituloFormaPago">
            <h2 id="tituloFormaPago"><span aria-hidden="true"></span> Forma de pago</h2>
            <!-- Aquí puedes agregar los métodos de pago disponibles, como efectivo, tarjeta, transferencia, etc. -->
             <!-- la entrada solo deberia estar activa para el metodo de pago en efectivo -->
            <div class="pago-metodos" aria-label="Métodos de pago">
                <div class="pago-metodo seleccionado">
                    <span class="pago-metodo-icono" aria-hidden="true"></span>
                    <strong>Efectivo</strong>
                    <small>Pago en efectivo</small>
                    <span class="pago-metodo-marca" aria-hidden="true">✓</span>
                </div>
                <div class="pago-metodo">
                    <span class="pago-metodo-icono" aria-hidden="true"></span>
                    <strong>Tarjeta</strong>
                    <small>Débito / Crédito</small>
                    <span class="pago-metodo-marca" aria-hidden="true"></span>
                </div>
                <div class="pago-metodo">
                    <span class="pago-metodo-icono" aria-hidden="true"></span>
                    <strong>Transferencia</strong>
                    <small>SPEI / Bancaria</small>
                    <span class="pago-metodo-marca" aria-hidden="true"></span>
                </div>
            </div>

            <div class="pago-calculo">
                <div class="pago-calculo-monto">
                    <span>Total a pagar</span>
                    <strong>$23.20 <small>MXN</small></strong>
                    <label for="montoRecibido">Monto recibido</label>
                    <div class="pago-entrada-monto">
                        <input class="campo" id="montoRecibido" type="number" min="0" step="0.01" value="0.00">
                        <span>MXN</span>
                    </div>
                </div>
                <div class="pago-cambio">
                    <strong><span aria-hidden="true"></span> Cambio</strong>
                    <span>$0.00 <small>MXN</small></span>
                </div>
                <p class="pago-nota"><span aria-hidden="true"></span> El monto recibido debe cubrir el total de la venta.</p>
            </div>

            <div class="pago-acciones">
                <form method="dialog">
                    <button class="btn btn-secundario btn-chico" type="submit">Volver a la venta</button>
                </form>
                <button class="btn pago-confirmar btn-chico" id="btnConfirmarPago" type="button">Confirmar pago</button>
            </div>
        </section>
    </div>
</dialog>