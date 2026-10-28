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
                <a class="btn btn-secundario btn-chico" href="Venta.php">&larr; Regresar a ventas</a>
            </div>

            <section class="historial-filtros" aria-label="Filtros del historial de ventas">
                <div class="historial-filtro historial-filtro-busqueda">
                    <label for="buscarVenta">Buscar folio, cliente o empleado</label>
                    <div class="historial-control-icono">
                        <span aria-hidden="true">⌕</span>
                        <input class="campo" id="buscarVenta" type="search" placeholder="Ej. F00123, Juan Pérez, Ana López...">
                    </div>
                </div>
                <div class="historial-filtro">
                    <label for="fechaDesde">Desde</label>
                    <input class="campo" id="fechaDesde" type="date" value="2026-10-01">
                </div>
                <div class="historial-filtro">
                    <label for="fechaHasta">Hasta</label>
                    <input class="campo" id="fechaHasta" type="date" value="2026-10-15">
                </div>
                <div class="historial-filtro">
                    <label for="estadoVenta">Estado</label>
                    <select class="campo" id="estadoVenta">
                        <option>Todos los estados</option>
                        <option>Pagada</option>
                        <option>Pendiente</option>
                        <option>Cancelada</option>
                    </select>
                </div>
                <div class="historial-filtro-acciones">
                    <button class="btn historial-buscar btn-chico" type="button"><span aria-hidden="true"></span> Buscar</button>
                    <button class="btn btn-secundario historial-limpiar btn-chico" type="button"><span aria-hidden="true"></span> Limpiar filtros</button>
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
                            <th>Productos</th>
                            <th>Total</th>
                            <th>Forma de pago</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>FV-00124</strong></td>
                            <td>15/10/2026 11:42</td>
                            <td>Juan Pérez García<small>Tel. 442 123 4567</small></td>
                            <td>Carlos Ramírez</td>
                            <td>3</td>
                            <td>$245.00</td>
                            <td>Tarjeta</td>
                            <td><span class="historial-estado pagada">Pagada</span></td>
                            <td><div class="historial-acciones">                            <button class="btn btn-chico accion-detalles" type="button" aria-label="Ver venta FV-00124">Detalles</button> <!-- este boton deberia abrir una ventana modal con los detalles de la venta, incluyendo productos, cantidades, precios, descuentos, impuestos y total -->
                            <button class="btn btn-chico accion-imprimir" type="button" aria-label="Imprimir ticket de la venta FV-00124">Imprimir ticket</button>
                            <button class="btn btn-chico accion-factura" type="button" aria-label="Generar factura de la venta FV-00124">Generar factura</button>
                            <button class="btn btn-chico accion-cancelar" type="button" aria-label="Cancelar venta FV-00124">Cancelar</button>
                        </div>
                    </td>
                        </tr>
                        <tr>
                            <td><strong>FV-00123</strong></td>
                            <td>14/10/2026 16:28</td>
                            <td>Público en general<small>—</small></td>
                            <td>Ana Torres</td>
                            <td>5</td>
                            <td>$312.50</td>
                            <td>Efectivo</td>
                            <td><span class="historial-estado pagada">Pagada</span></td>
                            <td><div class="historial-acciones">                            <button class="btn btn-chico accion-detalles" type="button" aria-label="Ver venta FV-00123">Detalles</button>
                            <button class="btn btn-chico accion-imprimir" type="button" aria-label="Imprimir ticket de la venta FV-00123">Imprimir ticket</button>
                            <button class="btn btn-chico accion-factura" type="button" aria-label="Generar factura de la venta FV-00123">Generar factura</button>
                            <button class="btn btn-chico accion-cancelar" type="button" aria-label="Cancelar venta FV-00123">Cancelar</button>
                        </div>
                    </td>
                        </tr>
                        <tr>
                            <td><strong>FV-00122</strong></td>
                            <td>14/10/2026 13:15</td>
                            <td>María López Sánchez<small>Tel. 442 987 6543</small></td>
                            <td>Luis Flores</td>
                            <td>2</td>
                            <td>$156.00</td>
                            <td>Transferencia</td>
                            <td><span class="historial-estado pendiente">Pendiente</span></td>
                            <td><div class="historial-acciones">                            <button class="btn btn-chico accion-detalles" type="button" aria-label="Ver venta FV-00122">Detalles</button>
                            <button class="btn btn-chico accion-imprimir" type="button" aria-label="Imprimir ticket de la venta FV-00122" disabled>Imprimir ticket</button>
                            <button class="btn btn-chico accion-factura" type="button" aria-label="Generar factura de la venta FV-00122" disabled>Generar factura</button>
                            <button class="btn btn-chico accion-cancelar" type="button" aria-label="Cancelar venta FV-00122">Cancelar</button>
                        </div>
                    </td>
                        </tr>
                        <tr>
                            <td><strong>FV-00121</strong></td>
                            <td>13/10/2026 10:03</td>
                            <td>Roberto Díaz<small>Tel. 442 555 0199</small></td>
                            <td>Carlos Ramírez</td>
                            <td>1</td>
                            <td>$89.90</td>
                            <td>Efectivo</td>
                            <td><span class="historial-estado cancelada">Cancelada</span></td>
                            <td><div class="historial-acciones">                            <button class="btn btn-chico accion-detalles" type="button" aria-label="Ver venta FV-00121">Detalles</button>
                            <button class="btn btn-chico accion-imprimir" type="button" aria-label="Imprimir ticket de la venta FV-00121" disabled>Imprimir ticket</button>
                            <button class="btn btn-chico accion-factura" type="button" aria-label="Generar factura de la venta FV-00121" disabled>Generar factura</button>
                            <button class="btn btn-chico accion-cancelar" type="button" aria-label="Venta FV-00121 cancelada" disabled>Cancelar</button>
                        </div>
                    </td>
                        </tr>
                    </tbody>
                </table>
                <div class="historial-pie-tabla">
                    <span>Mostrando 1 - 4 de 4 ventas</span>
                    <nav class="historial-paginacion" aria-label="Paginación del historial">
                        <button type="button" aria-label="Página anterior" disabled class="btn btn-chico">‹</button>
                        <button class="btn pagina-activa btn-chico" type="button" aria-current="page">1</button>
                        <button type="button" aria-label="Página siguiente" disabled class="btn btn-chico">›</button>
                    </nav>
                </div>
            </div>
        </main>
    </div>
</body>
</html>