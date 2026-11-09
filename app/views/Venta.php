<?php
require_once __DIR__ . '/../controllers/busca_producto.php';
require_once __DIR__ . '/../controllers/busca_servicio.php';
$iva = isset($subtotalGeneral) ? $subtotalGeneral * 0.16 : 0;
$totalGeneral = isset($subtotalGeneral) ? $subtotalGeneral + $iva : 0;
$totalArticulosMostrar = isset($totalArticulos) ? $totalArticulos : 0;
$subtotalMostrar = isset($subtotalGeneral) ? $subtotalGeneral : 0;
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

                    <form action="Venta.php?accion=actualizar" method="POST">
                        <div class="tabla-envoltura venta-tabla-envoltura">
                            <table class="venta-tabla">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Código</th>
                                        <th>Precio U.</th>
                                        <th>Cant.</th>
                                        <th>Descuento</th>
                                        <th>Subtotal</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="cartTableBody">
                                    <?php if (empty($_SESSION['carrito'])): ?>
                                        <tr>
                                            <td colspan="7" style="text-align: center;">No hay productos en la venta</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php 
                                        $subtotalGeneral = 0;
                                        $totalArticulos = 0;
                                        foreach ($_SESSION['carrito'] as $index => $item): 
                                            $subtotalFila = ( $item['cantidad'])*($item['precio'] - ($item['descuento'] * $item['precio'] / 100));
                                            $subtotalGeneral += $subtotalFila;
                                            $totalArticulos += $item['cantidad'];
                                        ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($item['nombre']); ?></td>
                                                <td><?php echo htmlspecialchars($item['codigo']); ?></td>
                                                <td>$<?php echo number_format($item['precio'], 2); ?></td>
                                                <td>
                                                    <input type="number" name="cantidades[<?php echo $index; ?>]" value="<?php echo $item['cantidad']; ?>" min="1" max="<?php echo $item['existencia']; ?>" class="campo" style="width: 80px;" title="Stock disponible: <?php echo $item['existencia']; ?>">
                                                </td>
                                                <td>
                                                    <input type="number" name="descuentos[<?php echo $index; ?>]" value="<?php echo $item['descuento']; ?>" min="0" max="100" step="0.01" class="campo" style="width: 80px;">
                                                </td>
                                                
                                                <td>$<?php echo number_format($subtotalFila, 2); ?></td>
                                                <td class="acciones">
                                                    <a class="btn btn-chico btn-peligro" href="Venta.php?accion=eliminar&index=<?php echo $index; ?>">Eliminar</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php if (!empty($_SESSION['carrito'])): ?>
                            <div style="margin-top: 10px;">
                                <button type="submit" class="btn btn-info">Actualizar cantidades</button>
                            </div>
                        <?php endif; ?>
                    </form>
                    <div class="venta-carrito-acciones">
                        <button class="btn btn-info" id="btnAgregarServicio" type="button">Agregar servicio</button>
                        <a class="btn btn-peligro" href="Venta.php?accion=cancelar" onclick="return confirm('¿Estás seguro de que deseas cancelar la venta actual?');">Nueva venta</a> <!-- este boton deberia limpiar la venta actual y reiniciar todo, tambien se deberia preguntar si se desea cancelar la venta actual, o eso interprete -->
                    </div>
                    <p class="venta-mensaje" id="ventaMensaje" role="status" aria-live="polite"></p>
                </section>

                <div class="venta-lateral">
                    <aside class="tarjeta venta-resumen" aria-label="Resumen de venta">
                        <h2>Resumen</h2>
                        <?php
                        $iva = isset($subtotalGeneral) ? $subtotalGeneral * 0.16 : 0;
                        $totalGeneral = isset($subtotalGeneral) ? $subtotalGeneral + $iva : 0;
                        $totalArticulosMostrar = isset($totalArticulos) ? $totalArticulos : 0;
                        $subtotalMostrar = isset($subtotalGeneral) ? $subtotalGeneral : 0;
                        ?>
                        <div class="venta-totales">
                            <div><span>Artículos</span><strong id="lblItems"><?php echo $totalArticulosMostrar; ?></strong></div>
                            <div><span>Subtotal</span><strong id="lblSubtotal">$<?php echo number_format($subtotalMostrar, 2); ?></strong></div>
                            <div><span>IVA (16%)</span><strong id="lblIva">$<?php echo number_format($iva, 2); ?></strong></div>
                            <div class="venta-total"><span>Total</span><strong id="lblTotal">$<?php echo number_format($totalGeneral, 2); ?> MXN</strong></div>
                        </div>
                        <div class="venta-acciones">
                            <button class="btn btn-info" id="btnCobrar" type="button">Cobrar</button>
                            <a class="btn btn-peligro" href="Venta.php?accion=cancelar" onclick="return confirm('¿Estás seguro de que deseas cancelar la venta actual?');">Cancelar venta</a>
                            <!-- este boton deberia cancelar la venta actual, y se deberia preguntar si se desea cancelar la venta actual y limpiar la ventana actual, o eso entnedi -->
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
    <script>
        const botonBuscarProducto = document.getElementById('btnBuscarProducto');
        const modalBuscarProducto = document.getElementById('modalBuscarProducto');
        const botonAgregarServicio = document.getElementById('btnAgregarServicio');
        const modalBuscarServicio = document.getElementById('modalBuscarServicio');
        const botonCobrar = document.getElementById('btnCobrar');
        const modalPagarVenta = document.getElementById('modalPagarVenta');

        botonBuscarProducto.addEventListener('click', () => {
            modalBuscarProducto.showModal();
        });

        botonAgregarServicio.addEventListener('click', () => {
            modalBuscarServicio.showModal();
        });

        botonCobrar.addEventListener('click', () => {
            modalPagarVenta.showModal();
        });
    </script>
</body>
</html>
