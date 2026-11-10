<dialog class="inventario-dialog producto-dialog pago-dialog" id="modalPagarVenta" aria-labelledby="tituloPagarVenta">
    <div class="producto-dialog-encabezado pago-dialog-encabezado">
        <h1 id="tituloPagarVenta">Pagar venta</h1>
        <button class="producto-dialog-cerrar" id="cerrarModalPago" type="button" aria-label="Cerrar">&times;</button>
    </div>

    <div class="pago-contenido">
        <section class="pago-resumen" aria-labelledby="tituloResumenPago">
            <h2 id="tituloResumenPago"><span aria-hidden="true"></span> <span id="etiquetaResumenPago">Resumen de la venta</span></h2>
            <div class="pago-tabla-envoltura">
                <table class="pago-tabla" id="tablaResumenPago">
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
                <div id="filaSubtotalPago"><span>Subtotal</span><strong id="subtotalPago">$20.00</strong></div>
                <div id="filaIvaPago"><span>IVA (16%)</span><strong id="ivaPago">$3.20</strong></div>
                <div class="pago-total"><span id="etiquetaTotalPago">Total</span><strong id="totalPago">$23.20 MXN</strong></div>
            </div>
        </section>

        <section class="pago-panel" aria-labelledby="tituloFormaPago">
            <h2 id="tituloFormaPago"><span aria-hidden="true"></span> Forma de pago</h2>
            <div class="pago-metodos" aria-label="Métodos de pago">
                <button class="pago-metodo seleccionado" type="button" data-metodo-pago="Efectivo" aria-pressed="true">
                    <span class="pago-metodo-icono" aria-hidden="true"></span>
                    <strong>Efectivo</strong>
                    <small>Pago en efectivo</small>
                    <span class="pago-metodo-marca" aria-hidden="true">✓</span>
                </button>
                <button class="pago-metodo" type="button" data-metodo-pago="Tarjeta" aria-pressed="false">
                    <span class="pago-metodo-icono" aria-hidden="true"></span>
                    <strong>Tarjeta</strong>
                    <small>Débito / Crédito</small>
                    <span class="pago-metodo-marca" aria-hidden="true"></span>
                </button>
                <button class="pago-metodo" type="button" data-metodo-pago="Transferencia" aria-pressed="false">
                    <span class="pago-metodo-icono" aria-hidden="true"></span>
                    <strong>Transferencia</strong>
                    <small>SPEI / Bancaria</small>
                    <span class="pago-metodo-marca" aria-hidden="true"></span>
                </button>
            </div>

            <div class="pago-calculo">
                <div class="pago-calculo-monto" id="campoEfectivo">
                    <span id="etiquetaMontoTotal">Total a pagar</span>
                    <strong id="montoTotalPago">$23.20 <small>MXN</small></strong>
                    <label for="montoRecibido">Monto recibido</label>
                    <div class="pago-entrada-monto">
                        <input class="campo" id="montoRecibido" type="number" min="0" step="0.01" value="23.20" inputmode="decimal">
                        <span>MXN</span>
                    </div>
                </div>
                <div class="pago-calculo-monto pago-monto-servicio" id="campoAbonoServicio" hidden>
                    <span>Saldo pendiente</span>
                    <strong id="saldoServicioPago">$0.00 <small>MXN</small></strong>
                    <label for="montoAbonoServicio">Monto a abonar</label>
                    <div class="pago-entrada-monto">
                        <input class="campo" id="montoAbonoServicio" type="number" min="0.01" step="0.01" inputmode="decimal">
                        <span>MXN</span>
                    </div>
                </div>
                <div class="pago-cambio">
                    <strong id="etiquetaCambio"><span aria-hidden="true"></span> Cambio</strong>
                    <span id="cambioPago">$0.00 <small>MXN</small></span>
                </div>
                <p class="pago-nota" id="notaMetodoPago"><span aria-hidden="true"></span> El monto recibido debe cubrir el total de la venta.</p>
            </div>

            <section class="pago-detalles-metodo" id="detallesTarjeta" hidden aria-labelledby="tituloDatosTarjeta">
                <h3 id="tituloDatosTarjeta">Datos de tarjeta</h3>
                <p>La pasarela bancaria deberá procesar estos datos de forma segura; esta demostración no los envía ni almacena.</p>
                <div class="pago-tarjeta-grid">
                    <div class="pago-campo-completo">
                        <label for="numeroTarjeta">Número de tarjeta</label>
                        <input class="campo" id="numeroTarjeta" type="text" inputmode="numeric" autocomplete="cc-number" maxlength="19" placeholder="0000 0000 0000 0000" pattern="[0-9 ]{13,19}" aria-describedby="ayudaNumeroTarjeta">
                        <small id="ayudaNumeroTarjeta">Ingresa de 13 a 19 dígitos, sin guiones.</small>
                    </div>
                    <div class="pago-campo-completo">
                        <label for="nombreTarjeta">Titular de la tarjeta</label>
                        <input class="campo" id="nombreTarjeta" type="text" autocomplete="cc-name" maxlength="80" placeholder="Como aparece en la tarjeta">
                    </div>
                    <div>
                        <label for="vencimientoTarjeta">Vencimiento</label>
                        <input class="campo" id="vencimientoTarjeta" type="month" autocomplete="cc-exp">
                    </div>
                    <div>
                        <label for="cvvTarjeta">CVV / CVC</label>
                        <input class="campo" id="cvvTarjeta" type="password" inputmode="numeric" autocomplete="cc-csc" maxlength="4" pattern="[0-9]{3,4}" placeholder="3 o 4 dígitos">
                    </div>
                </div>
            </section>

            <section class="pago-detalles-metodo" id="detallesTransferencia" hidden aria-labelledby="tituloDatosTransferencia">
                <h3 id="tituloDatosTransferencia">Datos de transferencia</h3>
                <label for="referenciaTransferencia">Referencia / folio (opcional)</label>
                <input class="campo" id="referenciaTransferencia" type="text" maxlength="80" autocomplete="off" placeholder="Referencia SPEI">
            </section>

            <div class="pago-acciones">
                <button class="btn btn-secundario btn-chico" id="cancelarPago" type="button">Cancelar</button>
                <button class="btn pago-confirmar btn-chico" id="btnConfirmarPago" type="button">Confirmar pago</button>
            </div>
        </section>
    </div>
</dialog>