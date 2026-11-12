<?php
// Mostrar nombre del usuario
session_start();
if (!isset($_SESSION['usuario']))
{
  header("Location: ../views/login.php");
  exit();
}
$usuario=$_SESSION['usuario'];
$empleado_asociado = $_SESSION['empleado_asociado'] ?? null;
$horario_empleado = $_SESSION['horario_empleado'] ?? null;
// Mostrar nombre del usuario
?>
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
            <div class="migaja">Ventas y pagos /</div>
            <header class="encabezado-pagina">
                <h1>Ventas y pagos</h1>
                <div class="venta-cajero">Cajero: <strong><?php echo htmlspecialchars($empleado_asociado); ?></strong> | Turno: <strong><?php echo htmlspecialchars($horario_empleado); ?></strong></div>
            </header>

            <nav class="inventario-pestanas" aria-label="Secciones de ventas y pagos">
                <a href="#ventas" class="activa" id="pestanaVentas" aria-controls="panel-ventas" aria-current="page">1. Punto de venta</a>
                <a href="#pagos" id="pestanaPagos" aria-controls="panel-pagos">2. Pagos de servicios</a>
                <a href="#pagos-empleados" id="pestanaPagosEmpleados" aria-controls="panel-pagos-empleados">3. Pagos a empleados</a>
            </nav>

            <section id="panel-ventas" class="ventas-panel" aria-labelledby="pestanaVentas">
            <div class="venta-contenedor">
                <section class="tarjeta venta-carrito" aria-labelledby="titulo-carrito">
                    <h2 id="titulo-carrito">Productos de la venta</h2>
                    <div class="venta-captura">
                        <div class="input-group venta-codigo">
                            <label for="barcodeInput">Código de barras</label>
                            <div class="venta-busqueda">
                                <input class="campo" type="text" id="barcodeInput" placeholder="Escanea o escribe un código" autocomplete="off">
                                <button class="btn btn-info btn-chico" id="btnBuscarProducto" type="button">Buscar producto</button>
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
                                        <a class="btn btn-peligro btn-chico" href="enespera.php?id=1">Eliminar</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="venta-carrito-acciones">
                        <button class="btn btn-info btn-chico" id="btnAgregarServicio" type="button">Agregar servicio</button>
                        <button class="btn btn-chico" id="btnNuevaVenta" type="button">Nueva venta</button> <!-- este boton deberia limpiar la venta actual y reiniciar todo, tambien se deberia preguntar si se desea cancelar la venta actual, o eso interprete -->
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
                            <button class="btn btn-info btn-chico" id="btnCobrar" type="button">Cobrar</button>
                            <button class="btn btn-peligro btn-chico" id="btnCancelarVenta" type="button">Cancelar venta</button> <!-- este boton deberia cancelar la venta actual, y se deberia preguntar si se desea cancelar la venta actual y limpiar la ventana actual, o eso entnedi -->
                        </div>
                    </aside>
                    <section class="tarjeta venta-herramientas" aria-labelledby="tituloHerramientasVenta">
                        <h2 id="tituloHerramientasVenta">Accesos</h2>
                        <div class="venta-reportes">
                            <a class="btn btn-secundario btn-chico" href="corte_caja.php">Corte de caja</a>
                            <a class="btn btn-secundario btn-chico" href="historial_ventas.php">Historial de ventas</a>
                        </div>
                    </section>
                </div>
            </div>
            </section>

            <section id="panel-pagos" class="ventas-panel" aria-labelledby="pestanaPagos" hidden>
                <div class="inventario-resumen pagos-resumen" aria-label="Resumen de pagos pendientes">
                    <article class="inventario-tarjeta">
                        <h3>Servicios por cobrar</h3>
                        <p id="pagosPendientesConteo">4</p>
                    </article>
                    <article class="inventario-tarjeta">
                        <h3>Saldo pendiente</h3>
                        <p id="pagosPendientesTotal">$2,480.00</p>
                    </article>
                    <article class="inventario-tarjeta">
                        <h3>Con abonos</h3>
                        <p id="pagosConAbonoConteo">2</p>
                    </article>
                </div>

                <section class="tarjeta" aria-labelledby="tituloPagosPendientes">
                    <div class="pagos-encabezado">
                        <div>
                            <h2 id="tituloPagosPendientes">Pagos pendientes de servicios</h2>
                            <p>Consulta, hotel y estética en una sola lista de cobranza.</p>
                        </div>
                    </div>
                    <div class="pagos-filtros">
                        <div>
                            <label class="label-campo" for="buscarPagoServicio">Buscar</label>
                            <input class="campo" id="buscarPagoServicio" type="search" placeholder="Folio, cliente, mascota o servicio">
                        </div>
                        <div>
                            <label class="label-campo" for="filtroTipoServicio">Tipo de servicio</label>
                            <select class="campo" id="filtroTipoServicio">
                                <option value="">Todos los servicios</option>
                                <option value="hotel">Hotel</option>
                                <option value="bano">Baño</option>
                            </select>
                        </div>
                        <div>
                            <label class="label-campo" for="filtroEstadoPago">Estado del pago</label>
                            <select class="campo" id="filtroEstadoPago">
                                <option value="">Todos los estados</option>
                                <option value="pendiente">Pendiente</option>
                                <option value="parcial">Abono parcial</option>
                                <option value="pagado">Pagado</option>
                            </select>
                        </div>
                    </div>
                    <div class="tabla-envoltura pagos-tabla-envoltura">
                        <table class="pagos-tabla">
                            <thead>
                                <tr>
                                    <th>Folio</th>
                                    <th>Fecha</th>
                                    <th>Cliente / mascota</th>
                                    <th>Servicio</th>
                                    <th>Total</th>
                                    <th>Abonado</th>
                                    <th>Saldo</th>
                                    <th>Estado</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody id="tablaPagosPendientes">
                                <tr data-tipo="veterinaria" data-fecha="2026-11-04" data-total="650" data-pagado="0" data-saldo="650">
                                    <td><strong>CV-104</strong></td>
                                    <td>04/11/2026</td>
                                    <td>Ana Ruiz<small>Luna · Perro</small></td>
                                    <td>Consulta veterinaria</td>
                                    <td>$650.00</td>
                                    <td class="pago-abonado">$0.00</td>
                                    <td class="pago-saldo"><strong>$650.00</strong></td>
                                    <td class="pago-estado"><span class="historial-estado pendiente">Pendiente</span></td>
                                    <td><button class="btn btn-info btn-chico" type="button" data-registrar-pago>Registrar pago</button></td>
                                </tr>
                                <tr data-tipo="hotel" data-fecha="2026-11-02" data-total="1600" data-pagado="500" data-saldo="1100">
                                    <td><strong>HT-028</strong></td>
                                    <td>02/11/2026</td>
                                    <td>Mario Santos<small>Max · Perro</small></td>
                                    <td>Hotel · 4 noches</td>
                                    <td>$1,600.00</td>
                                    <td class="pago-abonado">$500.00</td>
                                    <td class="pago-saldo"><strong>$1,100.00</strong></td>
                                    <td class="pago-estado"><span class="historial-estado parcial">Abono parcial</span></td>
                                    <td><button class="btn btn-info btn-chico" type="button" data-registrar-pago>Registrar pago</button></td>
                                </tr>
                                <tr data-tipo="bano" data-fecha="2026-11-04" data-total="450" data-pagado="0" data-saldo="450">
                                    <td><strong>ES-091</strong></td>
                                    <td>04/11/2026</td>
                                    <td>Daniela Herrera<small>Nala · Perro</small></td>
                                    <td>Baño medicado</td>
                                    <td>$450.00</td>
                                    <td class="pago-abonado">$0.00</td>
                                    <td class="pago-saldo"><strong>$450.00</strong></td>
                                    <td class="pago-estado"><span class="historial-estado pendiente">Pendiente</span></td>
                                    <td><button class="btn btn-info btn-chico" type="button" data-registrar-pago>Registrar pago</button></td>
                                </tr>
                                <tr data-tipo="corte" data-fecha="2026-11-03" data-total="380" data-pagado="100" data-saldo="280">
                                    <td><strong>ES-092</strong></td>
                                    <td>03/11/2026</td>
                                    <td>Pedro Gómez<small>Toby · Schnauzer</small></td>
                                    <td>Corte de pelo</td>
                                    <td>$380.00</td>
                                    <td class="pago-abonado">$100.00</td>
                                    <td class="pago-saldo"><strong>$280.00</strong></td>
                                    <td class="pago-estado"><span class="historial-estado parcial">Abono parcial</span></td>
                                    <td><button class="btn btn-info btn-chico" type="button" data-registrar-pago>Registrar pago</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p id="mensajePagoServicio" class="venta-mensaje" role="status" aria-live="polite"></p>
                </section>
            </section>

            <section id="panel-pagos-empleados" class="ventas-panel" aria-labelledby="pestanaPagosEmpleados" hidden>
                <div class="inventario-resumen pagos-resumen nomina-resumen" aria-label="Resumen de pagos a empleados">
                    <article class="inventario-tarjeta">
                        <h3>Efectivo disponible en caja</h3>
                        <p id="saldoCajaEmpleados">$800.00</p>
                    </article>
                    <article class="inventario-tarjeta">
                        <h3>Nómina pendiente</h3>
                        <p id="nominaPendienteTotal">$3,500.00</p>
                    </article>
                    <article class="inventario-tarjeta">
                        <h3>Empleados pagados</h3>
                        <p id="empleadosPagadosConteo">0 de 3</p>
                    </article>
                </div>

                <section class="tarjeta" aria-labelledby="tituloPagosEmpleados">
                    <div class="pagos-encabezado nomina-encabezado">
                        <div>
                            <h2 id="tituloPagosEmpleados">Pagos a empleados</h2>
                            <p>Los pagos se cubren completos y exclusivamente en efectivo, descontando el importe de caja.</p>
                        </div>
                        <button class="btn btn-secundario btn-chico" id="btnAbonarCaja" type="button">Abonar a caja</button>
                    </div>
                    <p class="nomina-nota">Demostración frontend: el saldo y los movimientos son de ejemplo. En producción, el permiso para abonar a caja debe limitarse al administrador.</p>
                    <div class="tabla-envoltura pagos-tabla-envoltura">
                        <table class="pagos-tabla nomina-tabla">
                            <thead>
                                <tr>
                                    <th>Empleado</th>
                                    <th>Puesto</th>
                                    <th>Periodo</th>
                                    <th>Pago pendiente</th>
                                    <th>Estado</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody id="tablaPagosEmpleados">
                                <tr data-empleado="María López" data-pago="500" data-estado="pendiente">
                                    <td><strong>María López</strong></td>
                                    <td>Recepcionista</td>
                                    <td>Octubre 2026</td>
                                    <td>$500.00</td>
                                    <td class="nomina-estado"><span class="historial-estado pendiente">Pendiente</span></td>
                                    <td><button class="btn btn-info btn-chico" type="button" data-pagar-empleado>Pagar en efectivo</button></td>
                                </tr>
                                <tr data-empleado="Carlos Méndez" data-pago="1200" data-estado="pendiente">
                                    <td><strong>Carlos Méndez</strong></td>
                                    <td>Estilista</td>
                                    <td>Octubre 2026</td>
                                    <td>$1,200.00</td>
                                    <td class="nomina-estado"><span class="historial-estado pendiente">Pendiente</span></td>
                                    <td><button class="btn btn-info btn-chico" type="button" data-pagar-empleado>Pagar en efectivo</button></td>
                                </tr>
                                <tr data-empleado="Luis Ramírez" data-pago="1800" data-estado="pendiente">
                                    <td><strong>Luis Ramírez</strong></td>
                                    <td>Veterinario</td>
                                    <td>Octubre 2026</td>
                                    <td>$1,800.00</td>
                                    <td class="nomina-estado"><span class="historial-estado pendiente">Pendiente</span></td>
                                    <td><button class="btn btn-info btn-chico" type="button" data-pagar-empleado>Pagar en efectivo</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p id="mensajePagoEmpleado" class="venta-mensaje" role="status" aria-live="polite"></p>
                </section>

                <section class="tarjeta nomina-movimientos" aria-labelledby="tituloMovimientosCaja">
                    <div class="pagos-encabezado">
                        <div>
                            <h2 id="tituloMovimientosCaja">Movimientos de caja</h2>
                            <p>Abonos y pagos registrados en esta demostración.</p>
                        </div>
                    </div>
                    <div class="tabla-envoltura">
                        <table class="pagos-tabla movimientos-caja-tabla">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Concepto</th>
                                    <th>Movimiento</th>
                                    <th>Importe</th>
                                </tr>
                            </thead>
                            <tbody id="tablaMovimientosCaja">
                                <tr id="sinMovimientosCaja">
                                    <td colspan="4">Aún no hay movimientos en esta demostración.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>
            </section>
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
            <p class="resultado-mensaje">Simulación frontend: el cobro no se procesó ni se guardó.</p>
            <div class="venta-total">Total de la venta: <strong id="totalVentaDemostracion">$23.20 MXN</strong></div>
            <p class="resultado-factura" id="mensajeFactura" role="status" hidden></p>
            <div class="venta-carrito-acciones">
                <button class="btn btn-secundario btn-chico" id="btnImprimirTicket" type="button">Imprimir ticket</button>
                <button class="btn btn-chico" id="btnGenerarFactura" type="button">Generar factura</button>
                <button class="btn btn-secundario btn-chico" id="btnNuevaVentaConfirmada" type="button">Nueva venta</button>
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
                <button class="btn btn-secundario btn-chico" id="btnVolverPagoConfirmado" type="button">Cancelar</button>
                <button class="btn btn-chico" type="submit">Continuar</button>
            </div>
        </form>
    </dialog>
    <dialog class="inventario-dialog producto-dialog pago-empleado-dialog" id="modalConfirmarPagoEmpleado" aria-labelledby="tituloConfirmarPagoEmpleado">
        <div class="producto-dialog-encabezado pago-dialog-encabezado">
            <h1 id="tituloConfirmarPagoEmpleado">Confirmar pago a empleado</h1>
            <button class="producto-dialog-cerrar" id="cerrarConfirmarPagoEmpleado" type="button" aria-label="Cerrar">&times;</button>
        </div>
        <div class="pago-empleado-contenido">
            <p id="resumenPagoEmpleado"></p>
            <p class="pago-nota">Este pago se descontará del efectivo disponible en caja. No se aceptan otros métodos de pago.</p>
            <div class="pago-acciones">
                <button class="btn btn-secundario btn-chico" id="cancelarPagoEmpleado" type="button">Cancelar</button>
                <button class="btn btn-chico" id="confirmarPagoEmpleado" type="button">Confirmar pago en efectivo</button>
            </div>
        </div>
    </dialog>
    <dialog class="inventario-dialog producto-dialog pago-empleado-dialog" id="modalAbonoCaja" aria-labelledby="tituloAbonoCaja">
        <div class="producto-dialog-encabezado pago-dialog-encabezado">
            <h1 id="tituloAbonoCaja">Abonar efectivo a caja</h1>
            <button class="producto-dialog-cerrar" id="cerrarAbonoCaja" type="button" aria-label="Cerrar">&times;</button>
        </div>
        <form class="pago-servicio-formulario" id="formAbonoCaja">
            <p id="notaAbonoCaja">Registra un ingreso manual de efectivo a caja.</p>
            <label class="label-campo" for="montoAbonoCaja">Monto del abono</label>
            <input class="campo" id="montoAbonoCaja" name="monto" type="number" min="0.01" step="0.01" inputmode="decimal" required>
            <label class="label-campo" for="conceptoAbonoCaja">Concepto</label>
            <input class="campo" id="conceptoAbonoCaja" name="concepto" type="text" maxlength="100" value="Abono administrativo" required>
            <p class="pago-nota">En esta maqueta, este ingreso solo se simula; no se guarda ni modifica una caja real. En producción, solo el administrador debe tener este permiso.</p>
            <div class="pago-acciones">
                <button class="btn btn-secundario btn-chico" id="cancelarAbonoCaja" type="button">Cancelar</button>
                <button class="btn btn-chico" type="submit">Registrar abono en efectivo</button>
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
        const botonConfirmarPago = document.getElementById('btnConfirmarPago');
        const inputMontoRecibido = document.getElementById('montoRecibido');
        const inputMontoAbono = document.getElementById('montoAbonoServicio');
        const metodosPago = [...document.querySelectorAll('[data-metodo-pago]')];
        const detallesTarjeta = document.getElementById('detallesTarjeta');
        const detallesTransferencia = document.getElementById('detallesTransferencia');
        const filasResumenPago = document.getElementById('tablaResumenPago').querySelector('tbody');
        const resumenVentaOriginal = filasResumenPago.innerHTML;
        const numeroTarjeta = document.getElementById('numeroTarjeta');
        const nombreTarjeta = document.getElementById('nombreTarjeta');
        const vencimientoTarjeta = document.getElementById('vencimientoTarjeta');
        const cvvTarjeta = document.getElementById('cvvTarjeta');
        let metodoPagoActual = 'Efectivo';
        let contextoPago = { tipo: 'venta', total: 23.20, fila: null };

        const pestanasVentas = [
            { enlace: document.getElementById('pestanaVentas'), panel: document.getElementById('panel-ventas') },
            { enlace: document.getElementById('pestanaPagos'), panel: document.getElementById('panel-pagos') },
            { enlace: document.getElementById('pestanaPagosEmpleados'), panel: document.getElementById('panel-pagos-empleados') }
        ];

        function activarPestana() {
            const indiceActivo = window.location.hash === '#pagos-empleados'
                ? 2
                : window.location.hash === '#pagos'
                    ? 1
                    : 0;
            pestanasVentas.forEach(({ enlace, panel }, indice) => {
                const activa = indice === indiceActivo;
                enlace.classList.toggle('activa', activa);
                if (activa) enlace.setAttribute('aria-current', 'page');
                else enlace.removeAttribute('aria-current');
                panel.hidden = !activa;
            });
        }

        window.addEventListener('hashchange', activarPestana);
        activarPestana();

        botonBuscarProducto.addEventListener('click', () => {
            modalBuscarProducto.showModal();
        });

        botonAgregarServicio.addEventListener('click', () => {
            modalBuscarServicio.showModal();
        });

        function obtenerMontoVenta() {
            const texto = document.getElementById('lblTotal').textContent;
            return Number(texto.replace(/[^\d.-]/g, ''));
        }

        function establecerResumenPago({ tipo, total, fila }) {
            const esServicio = tipo === 'servicio';
            const etiquetaTotal = esServicio ? 'Saldo pendiente' : 'Total';
            document.getElementById('tituloPagarVenta').textContent = esServicio ? 'Pagar servicio' : 'Pagar venta';
            document.getElementById('etiquetaResumenPago').textContent = esServicio ? 'Resumen del servicio' : 'Resumen de la venta';
            document.getElementById('etiquetaTotalPago').textContent = etiquetaTotal;
            document.getElementById('totalPago').textContent = moneda.format(total);
            document.getElementById('montoTotalPago').innerHTML = `${moneda.format(total)} <small>MXN</small>`;
            document.getElementById('filaIvaPago').hidden = esServicio;
            document.getElementById('filaSubtotalPago').hidden = esServicio;
            document.getElementById('tablaResumenPago').hidden = esServicio;
            document.getElementById('campoAbonoServicio').hidden = !esServicio;
            document.getElementById('campoEfectivo').hidden = esServicio;
            document.getElementById('etiquetaMontoTotal').textContent = esServicio ? 'Saldo pendiente' : 'Total a pagar';
            document.getElementById('notaMetodoPago').textContent = esServicio
                ? 'Puedes abonar una parte o liquidar el saldo. El registro es solo una demostración frontend.'
                : 'El monto recibido debe cubrir el total de la venta.';
            document.getElementById('etiquetaCambio').hidden = esServicio;
            document.getElementById('cambioPago').hidden = esServicio;

            if (esServicio) {
                const nombreServicio = fila.cells[3].textContent.trim();
                const folio = fila.cells[0].textContent.trim();
                const clienteMascota = fila.cells[2].textContent.trim();
                filasResumenPago.replaceChildren();
                const renglon = document.createElement('tr');
                const detalle = document.createElement('td');
                detalle.textContent = `${folio} · ${nombreServicio} · ${clienteMascota}`;
                const cantidad = document.createElement('td');
                cantidad.textContent = '1';
                const precio = document.createElement('td');
                precio.textContent = moneda.format(total);
                const subtotal = document.createElement('td');
                subtotal.textContent = moneda.format(total);
                renglon.append(detalle, cantidad, precio, subtotal);
                filasResumenPago.append(renglon);
                document.getElementById('saldoServicioPago').innerHTML = `${moneda.format(total)} <small>MXN</small>`;
                inputMontoAbono.max = total.toFixed(2);
                inputMontoAbono.value = total.toFixed(2);
                inputMontoAbono.required = true;
                botonConfirmarPago.textContent = 'Aplicar abono de demostración';
            } else {
                filasResumenPago.innerHTML = resumenVentaOriginal;
                inputMontoRecibido.value = total.toFixed(2);
                document.getElementById('subtotalPago').textContent = document.getElementById('lblSubtotal').textContent;
                document.getElementById('ivaPago').textContent = document.getElementById('lblIva').textContent;
                inputMontoAbono.required = false;
                botonConfirmarPago.textContent = 'Confirmar pago';
            }

            contextoPago = { tipo, total, fila };
            actualizarCambio();
        }

        function actualizarCambio() {
            if (contextoPago.tipo !== 'venta' || metodoPagoActual !== 'Efectivo') {
                document.getElementById('cambioPago').innerHTML = '$0.00 <small>MXN</small>';
                return;
            }
            const recibido = Number(inputMontoRecibido.value);
            const cambio = Number.isFinite(recibido) ? Math.max(0, recibido - contextoPago.total) : 0;
            document.getElementById('cambioPago').innerHTML = `${moneda.format(cambio)} <small>MXN</small>`;
        }

        function seleccionarMetodoPago(metodo) {
            metodoPagoActual = metodo;
            metodosPago.forEach((boton) => {
                const seleccionado = boton.dataset.metodoPago === metodo;
                boton.classList.toggle('seleccionado', seleccionado);
                boton.setAttribute('aria-pressed', String(seleccionado));
            });

            const esEfectivo = metodo === 'Efectivo';
            const esTarjeta = metodo === 'Tarjeta';
            const esTransferencia = metodo === 'Transferencia';
            document.getElementById('campoEfectivo').hidden = contextoPago.tipo === 'servicio' || !esEfectivo;
            document.getElementById('campoAbonoServicio').hidden = contextoPago.tipo !== 'servicio';
            document.getElementById('etiquetaCambio').hidden = contextoPago.tipo === 'servicio' || !esEfectivo;
            document.getElementById('cambioPago').hidden = contextoPago.tipo === 'servicio' || !esEfectivo;
            detallesTarjeta.hidden = !esTarjeta;
            detallesTransferencia.hidden = !esTransferencia;
            [numeroTarjeta, nombreTarjeta, vencimientoTarjeta, cvvTarjeta].forEach((campo) => {
                campo.required = esTarjeta;
            });
            document.getElementById('notaMetodoPago').textContent = contextoPago.tipo === 'servicio'
                ? 'Puedes abonar una parte o liquidar el saldo. El registro es solo una demostración frontend.'
                : esEfectivo
                    ? 'El monto recibido debe cubrir el total de la venta.'
                    : esTarjeta
                        ? 'Captura los datos para preparar la futura integración con la pasarela bancaria.'
                        : 'La referencia de transferencia es opcional; la validación bancaria se conectará después.';
            actualizarCambio();
        }

        function abrirModalPagoVenta() {
            establecerResumenPago({ tipo: 'venta', total: obtenerMontoVenta(), fila: null });
            seleccionarMetodoPago('Efectivo');
            modalPagarVenta.showModal();
        }

        botonCobrar.addEventListener('click', abrirModalPagoVenta);
        inputMontoRecibido.addEventListener('input', actualizarCambio);
        metodosPago.forEach((boton) => {
            boton.addEventListener('click', () => seleccionarMetodoPago(boton.dataset.metodoPago));
        });

        [numeroTarjeta, cvvTarjeta].forEach((campo) => {
            campo.addEventListener('input', () => campo.value = campo.value.replace(/\D/g, ''));
        });

        numeroTarjeta.addEventListener('input', () => {
            const digitos = numeroTarjeta.value.replace(/\D/g, '').slice(0, 19);
            numeroTarjeta.value = digitos.replace(/(\d{4})(?=\d)/g, '$1 ').trim();
            const cantidadDigitos = digitos.length;
            numeroTarjeta.setCustomValidity(cantidadDigitos >= 13 && cantidadDigitos <= 19 ? '' : 'Ingresa entre 13 y 19 dígitos.');
        });

        document.getElementById('vencimientoTarjeta').min = new Date().toISOString().slice(0, 7);

        function limpiarDatosTarjeta() {
            [numeroTarjeta, nombreTarjeta, vencimientoTarjeta, cvvTarjeta].forEach((campo) => {
                campo.value = '';
                campo.required = false;
                campo.setCustomValidity('');
            });
            document.getElementById('referenciaTransferencia').value = '';
            seleccionarMetodoPago('Efectivo');
        }

        document.getElementById('cerrarModalPago').addEventListener('click', () => modalPagarVenta.close());
        document.getElementById('cancelarPago').addEventListener('click', () => modalPagarVenta.close());
        modalPagarVenta.addEventListener('close', limpiarDatosTarjeta);

        botonConfirmarPago.addEventListener('click', () => {
            const esServicio = contextoPago.tipo === 'servicio';
            const importe = esServicio ? Number(inputMontoAbono.value) : contextoPago.total;

            if (!Number.isFinite(importe) || importe <= 0 ||
                (esServicio && importe > contextoPago.total) ||
                (!esServicio && metodoPagoActual === 'Efectivo' && Number(inputMontoRecibido.value) < contextoPago.total)) {
                (esServicio ? inputMontoAbono : inputMontoRecibido).setCustomValidity(
                    esServicio
                        ? 'El abono debe ser mayor a cero y no superar el saldo pendiente.'
                        : 'El monto recibido debe cubrir el total de la venta.'
                );
                (esServicio ? inputMontoAbono : inputMontoRecibido).reportValidity();
                (esServicio ? inputMontoAbono : inputMontoRecibido).setCustomValidity('');
                return;
            }

            if (metodoPagoActual === 'Tarjeta') {
                const tarjetaValida = [numeroTarjeta, nombreTarjeta, vencimientoTarjeta, cvvTarjeta]
                    .every((campo) => campo.reportValidity());
                if (!tarjetaValida) return;
            }

            if (esServicio) {
                const fila = contextoPago.fila;
                const nuevoAbonado = Number(fila.dataset.pagado) + importe;
                const nuevoSaldo = Math.max(0, Number(fila.dataset.saldo) - importe);
                fila.dataset.pagado = nuevoAbonado.toFixed(2);
                fila.dataset.saldo = nuevoSaldo.toFixed(2);
                fila.querySelector('.pago-abonado').textContent = moneda.format(nuevoAbonado);
                fila.querySelector('.pago-saldo').replaceChildren(document.createElement('strong'));
                fila.querySelector('.pago-saldo strong').textContent = moneda.format(nuevoSaldo);
                fila.querySelector('.pago-estado').innerHTML = nuevoSaldo === 0
                    ? '<span class="historial-estado pagada">Pagado</span>'
                    : '<span class="historial-estado parcial">Abono parcial</span>';
                fila.querySelector('[data-registrar-pago]').disabled = nuevoSaldo === 0;
                document.getElementById('mensajePagoServicio').textContent =
                    `Abono de ${moneda.format(importe)} con ${metodoPagoActual} aplicado solo en esta demostración; no se guardó ni se contactó al banco.`;
                actualizarResumenPagos();
                modalPagarVenta.close();
                return;
            }

            document.getElementById('totalVentaDemostracion').textContent = moneda.format(contextoPago.total);
            modalPagarVenta.close();
            modalPagoConfirmado.showModal();
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

        const moneda = new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' });
        const filasPagoServicio = [...document.querySelectorAll('#tablaPagosPendientes tr')];

        function actualizarResumenPagos() {
            const pendientes = filasPagoServicio.filter((fila) => Number(fila.dataset.saldo) > 0);
            const conAbono = pendientes.filter((fila) => Number(fila.dataset.pagado) > 0);
            const saldoTotal = pendientes.reduce((total, fila) => total + Number(fila.dataset.saldo), 0);
            document.getElementById('pagosPendientesConteo').textContent = String(pendientes.length);
            document.getElementById('pagosPendientesTotal').textContent = moneda.format(saldoTotal);
            document.getElementById('pagosConAbonoConteo').textContent = String(conAbono.length);
        }

        filasPagoServicio.forEach((fila) => {
            fila.querySelector('[data-registrar-pago]').addEventListener('click', () => {
                const saldo = Number(fila.dataset.saldo);
                establecerResumenPago({ tipo: 'servicio', total: saldo, fila });
                seleccionarMetodoPago('Efectivo');
                modalPagarVenta.showModal();
            });
        });

        const filasPagoEmpleado = [...document.querySelectorAll('#tablaPagosEmpleados tr')];
        const modalConfirmarPagoEmpleado = document.getElementById('modalConfirmarPagoEmpleado');
        const modalAbonoCaja = document.getElementById('modalAbonoCaja');
        const inputAbonoCaja = document.getElementById('montoAbonoCaja');
        const monedaCaja = new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' });
        let saldoCajaEmpleados = 800;
        let empleadoPagoSeleccionado = null;

        function obtenerPendienteNomina() {
            return filasPagoEmpleado
                .filter((fila) => fila.dataset.estado === 'pendiente')
                .reduce((total, fila) => total + Number(fila.dataset.pago), 0);
        }

        function actualizarResumenEmpleados() {
            const pagados = filasPagoEmpleado.filter((fila) => fila.dataset.estado === 'pagado').length;
            document.getElementById('saldoCajaEmpleados').textContent = monedaCaja.format(saldoCajaEmpleados);
            document.getElementById('nominaPendienteTotal').textContent = monedaCaja.format(obtenerPendienteNomina());
            document.getElementById('empleadosPagadosConteo').textContent = `${pagados} de ${filasPagoEmpleado.length}`;
        }

        function registrarMovimientoCaja(concepto, tipo, importe) {
            const tabla = document.getElementById('tablaMovimientosCaja');
            document.getElementById('sinMovimientosCaja')?.remove();

            const fila = document.createElement('tr');
            const fecha = document.createElement('td');
            fecha.textContent = new Intl.DateTimeFormat('es-MX', { dateStyle: 'short' }).format(new Date());
            const detalle = document.createElement('td');
            detalle.textContent = concepto;
            const movimiento = document.createElement('td');
            movimiento.textContent = tipo;
            const monto = document.createElement('td');
            monto.textContent = `${tipo === 'Salida' ? '-' : '+'}${monedaCaja.format(importe)}`;
            fila.append(fecha, detalle, movimiento, monto);
            tabla.prepend(fila);
        }

        function abrirConfirmacionPagoEmpleado(fila) {
            empleadoPagoSeleccionado = fila;
            const monto = Number(fila.dataset.pago);
            if (monto > saldoCajaEmpleados) {
                const faltante = monto - saldoCajaEmpleados;
                document.getElementById('mensajePagoEmpleado').textContent =
                    `No hay efectivo suficiente para pagar a ${fila.dataset.empleado}. Faltan ${monedaCaja.format(faltante)}; se requiere un abono a caja del administrador.`;
                inputAbonoCaja.value = faltante.toFixed(2);
                document.getElementById('notaAbonoCaja').textContent =
                    `El pago de ${fila.dataset.empleado} requiere ${monedaCaja.format(monto)}; hay ${monedaCaja.format(saldoCajaEmpleados)} en caja. Abona al menos ${monedaCaja.format(faltante)} para continuar.`;
                modalAbonoCaja.showModal();
                return;
            }

            document.getElementById('mensajePagoEmpleado').textContent = '';
            document.getElementById('resumenPagoEmpleado').textContent =
                `¿Confirmas el pago en efectivo de ${monedaCaja.format(monto)} a ${fila.dataset.empleado}?`;
            modalConfirmarPagoEmpleado.showModal();
        }

        filasPagoEmpleado.forEach((fila) => {
            fila.querySelector('[data-pagar-empleado]').addEventListener('click', () => abrirConfirmacionPagoEmpleado(fila));
        });

        document.getElementById('btnAbonarCaja').addEventListener('click', () => {
            empleadoPagoSeleccionado = null;
            inputAbonoCaja.value = '';
            document.getElementById('notaAbonoCaja').textContent = 'Registra un ingreso manual de efectivo a caja.';
            document.getElementById('formAbonoCaja').reset();
            modalAbonoCaja.showModal();
        });

        document.getElementById('formAbonoCaja').addEventListener('submit', (evento) => {
            evento.preventDefault();
            const importe = Number(inputAbonoCaja.value);
            if (!Number.isFinite(importe) || importe <= 0) {
                inputAbonoCaja.setCustomValidity('Ingresa un abono mayor que cero.');
                inputAbonoCaja.reportValidity();
                inputAbonoCaja.setCustomValidity('');
                return;
            }

            const concepto = document.getElementById('conceptoAbonoCaja').value.trim();
            if (!concepto) {
                document.getElementById('conceptoAbonoCaja').reportValidity();
                return;
            }

            saldoCajaEmpleados += importe;
            registrarMovimientoCaja(concepto, 'Entrada', importe);
            actualizarResumenEmpleados();
            document.getElementById('mensajePagoEmpleado').textContent =
                `Abono de ${monedaCaja.format(importe)} registrado en caja solo para esta demostración.`;
            modalAbonoCaja.close();

            if (empleadoPagoSeleccionado) {
                abrirConfirmacionPagoEmpleado(empleadoPagoSeleccionado);
            }
        });

        document.getElementById('confirmarPagoEmpleado').addEventListener('click', () => {
            if (!empleadoPagoSeleccionado) return;
            const importe = Number(empleadoPagoSeleccionado.dataset.pago);
            if (importe > saldoCajaEmpleados) {
                modalConfirmarPagoEmpleado.close();
                abrirConfirmacionPagoEmpleado(empleadoPagoSeleccionado);
                return;
            }

            const nombre = empleadoPagoSeleccionado.dataset.empleado;
            saldoCajaEmpleados -= importe;
            empleadoPagoSeleccionado.dataset.estado = 'pagado';
            empleadoPagoSeleccionado.querySelector('.nomina-estado').innerHTML =
                '<span class="historial-estado pagada">Pagado</span>';
            empleadoPagoSeleccionado.querySelector('[data-pagar-empleado]').disabled = true;
            registrarMovimientoCaja(`Pago a empleado: ${nombre}`, 'Salida', importe);
            actualizarResumenEmpleados();
            document.getElementById('mensajePagoEmpleado').textContent =
                `Pago en efectivo de ${monedaCaja.format(importe)} a ${nombre} aplicado solo en esta demostración; no se guardó.`;
            empleadoPagoSeleccionado = null;
            modalConfirmarPagoEmpleado.close();
        });

        document.getElementById('cerrarConfirmarPagoEmpleado').addEventListener('click', () => modalConfirmarPagoEmpleado.close());
        document.getElementById('cancelarPagoEmpleado').addEventListener('click', () => modalConfirmarPagoEmpleado.close());
        document.getElementById('cerrarAbonoCaja').addEventListener('click', () => modalAbonoCaja.close());
        document.getElementById('cancelarAbonoCaja').addEventListener('click', () => modalAbonoCaja.close());
    </script>
    <script src="js/ventas.js"></script>
</body>
</html>
