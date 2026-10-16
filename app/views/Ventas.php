<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Punto de venta - La Trinidad POS</title>
    <link rel="stylesheet" href="css/estilos_base_v2.css">
</head>
<body>
    <div class="layout">
        <?php $paginaActiva = 'ventas'; include __DIR__ . '/layout/sidebar.php'; ?>

        <main>
            <div class="migaja">Punto de Venta /</div>
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
                            <input class="campo" type="text" id="barcodeInput" placeholder="Escanea o escribe un código" autocomplete="off">
                            <span class="nota-campo">El producto se agregará a la lista de venta.</span>
                        </div>
                        <button class="btn btn-info" id="btnAgregarProducto" type="button">Agregar producto</button>
                        <div class="input-group venta-cliente">
                            <label for="clienteInput">Cliente</label>
                            <input class="campo" type="tel" id="clienteInput" placeholder="Teléfono (opcional)" inputmode="numeric" autocomplete="off">
                        </div>
                    </div>

                    <div class="tabla-envoltura venta-tabla-envoltura">
                        <table class="venta-tabla">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Precio U.</th>
                                    <th>Cant.</th>
                                    <th>Subtotal</th>
                                    <th>Quitar</th>
                                </tr>
                            </thead>
                            <tbody id="cartTableBody">
                                <tr id="carritoVacio">
                                    <td colspan="6" class="venta-carrito-vacio">Los productos agregados aparecerán aquí.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p class="venta-mensaje" id="ventaMensaje" role="status" aria-live="polite"></p>
                </section>

                <aside class="tarjeta venta-resumen" aria-label="Resumen de venta">
                    <h2>Resumen</h2>
                    <div class="venta-totales">
                        <div><span>Artículos</span><strong id="lblItems">0</strong></div>
                        <div><span>Subtotal</span><strong id="lblSubtotal">$0.00</strong></div>
                        <div><span>IVA (16%)</span><strong id="lblIva">$0.00</strong></div>
                        <div class="venta-total"><span>Total</span><strong id="lblTotal">$0.00</strong></div>
                    </div>
                    <div class="venta-acciones">
                        <button class="btn btn-info" id="btnCobrar" type="button">Cobrar</button>
                        <button class="btn btn-peligro" id="btnCancelarVenta" type="button">Cancelar venta</button>
                    </div>
                </aside>
            </div>
        </main>
    </div>
</body>
</html>
