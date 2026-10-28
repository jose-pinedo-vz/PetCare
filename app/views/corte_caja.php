<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veterinaria PetCare - Corte de caja</title>
    <link rel="stylesheet" href="css/estilos_base_v2.css">
</head>
<body>
    <div class="layout">
        <?php $paginaActiva = 'ventas'; include __DIR__ . '/layout/sidebar.php'; ?>

        <main class="corte-pagina">
            <div class="migaja"><a href="Ventas.php">Punto de Venta</a> /</div>
            <header class="encabezado-pagina corte-encabezado">
                <h1>Corte de caja</h1>
                <p class="corte-cajero">Cajero: <strong>María López</strong> | Turno: Matutino</p>
            </header>

            <div class="corte-layout">
                <aside class="corte-lateral">
                    <section class="tarjeta corte-panel" aria-labelledby="tituloNuevoCorte">
                        <div class="corte-panel-titulo">
                            <h2 id="tituloNuevoCorte">Nuevo corte de caja</h2>
                            <a class="btn btn-secundario corte-volver btn-chico" href="Ventas.php">Regresar a venta</a>
                        </div>

                        <form class="corte-formulario">
                            <fieldset class="corte-tipo">
                                <legend>Tipo de corte</legend>
                                <label class="corte-opcion">
                                    <input type="radio" name="tipoCorte" value="diario">
                                    <span>Diario</span>
                                </label>
                                <label class="corte-opcion">
                                    <input type="radio" name="tipoCorte" value="mensual">
                                    <span>Mensual</span>
                                </label>
                            </fieldset>

                            <div class="corte-campos-fecha">
                                <div class="corte-campo">
                                    <label class="label-campo" for="fechaInicio">Fecha inicio</label>
                                    <input class="campo" id="fechaInicio" type="datetime-local">
                                </div>
                                <div class="corte-campo">
                                    <label class="label-campo" for="fechaFin">Fecha fin</label>
                                    <input class="campo" id="fechaFin" type="datetime-local">
                                </div>
                            </div>

                            <div class="corte-campos-finales">
                                <div class="corte-campo">
                                    <label class="label-campo" for="turnoCorte">Turno</label>
                                    <select class="campo" id="turnoCorte">
                                        <option value="" selected disabled>Seleccionar turno</option>
                                        <option value="matutino">Matutino</option>
                                        <option value="vespertino">Vespertino</option>
                                    </select>
                                </div>
                                <div class="corte-campo">
                                    <label class="label-campo" for="fondoInicial">Fondo inicial</label>
                                    <input class="campo" id="fondoInicial" type="number" min="0" step="0.01">
                                </div>
                            </div>

                            <button class="btn corte-generar btn-chico" type="button">Generar corte</button>
                        </form>
                    </section>

                    <section class="tarjeta corte-panel corte-anteriores" aria-labelledby="tituloCortesAnteriores">
                        <h2 id="tituloCortesAnteriores">Cortes anteriores</h2>
                        <label class="visualmente-oculto" for="buscarCorte">Buscar corte por fecha o folio</label>
                        <input class="campo corte-busqueda buscador-inteligente" id="buscarCorte" type="search" placeholder="Buscar por fecha o folio...">
                        <div class="corte-tabla-envoltura">
                            <table class="corte-tabla corte-tabla-anteriores">
                                <thead>
                                    <tr>
                                        <th>Tipo</th>
                                        <th>Fecha inicio</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Diario</td>
                                        <td>15/10/2026</td>
                                        <td><span class="corte-estado cerrado">Cerrado</span></td>
                                        <td><div class="corte-fila-acciones"><button class="btn btn-chico corte-accion" type="button" aria-label="Ver corte diario del 15/10/2026">Ver</button><button class="btn btn-chico corte-accion" type="button" aria-label="Descargar corte en PDF del 15/10/2026" title="Descargar corte en PDF">PDF</button></div></td>
                                    </tr>
                                    <tr>
                                        <td>Diario</td>
                                        <td>14/10/2026</td>
                                        <td><span class="corte-estado cerrado">Cerrado</span></td>
                                        <td><div class="corte-fila-acciones"><button class="btn btn-chico corte-accion" type="button" aria-label="Ver corte diario del 14/10/2026">Ver</button><button class="btn btn-chico corte-accion" type="button" aria-label="Descargar corte en PDF del 14/10/2026" title="Descargar corte en PDF">PDF</button></div></td>
                                    </tr>
                                    <tr>
                                        <td>Mensual</td>
                                        <td>01/10/2026</td>
                                        <td><span class="corte-estado cerrado">Cerrado</span></td>
                                        <td><div class="corte-fila-acciones"><button class="btn btn-chico corte-accion" type="button" aria-label="Ver corte mensual del 01/10/2026">Ver</button><button class="btn btn-chico corte-accion" type="button" aria-label="Descargar corte en PDF del 01/10/2026" title="Descargar corte en PDF">PDF</button></div></td>
                                    </tr>
                                    <tr>
                                        <td>Diario</td>
                                        <td>13/10/2026</td>
                                        <td><span class="corte-estado abierto">Abierto</span></td>
                                        <td><div class="corte-fila-acciones"><button class="btn btn-chico corte-accion" type="button" aria-label="Ver corte diario del 13/10/2026">Ver</button><button class="btn btn-chico corte-accion" type="button" aria-label="Descargar corte en PDF del 13/10/2026" title="Descargar corte en PDF">PDF</button></div></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>
                </aside>

                <div class="corte-principal">
                    <section class="corte-resumen" aria-labelledby="tituloResumenCorte">
                        <h2 id="tituloResumenCorte">Resumen del corte</h2>
                        <div class="corte-metricas">
                            <article class="corte-metrica corte-metrica-destacada">
                                <span class="corte-metrica-etiqueta">Efectivo contado</span>
                                <!-- Backend: habilitar el campo y su boton cuando se genere el corte. -->
                                <div class="corte-efectivo-control">
                                    <span aria-hidden="true">$</span>
                                    <label class="visualmente-oculto" for="efectivoContado">Cantidad de efectivo contado</label>
                                    <input class="campo" id="efectivoContado" type="number" min="0" step="0.01" disabled>
                                    <button class="btn btn-secundario btn-chico" type="button" disabled>Confirmar</button>
                                </div>
                            </article>
                            <article class="corte-metrica">
                                <span class="corte-metrica-etiqueta">Efectivo esperado</span>
                                <small>Fondo inicial + ventas en efectivo</small>
                                <strong></strong>
                            </article>
                            <article class="corte-metrica corte-metrica-diferencia">
                                <span class="corte-metrica-etiqueta">Diferencia</span>
                                <small>Contado vs. esperado</small>
                                <strong></strong>
                            </article>
                            <article class="corte-metrica">
                                <span class="corte-metrica-etiqueta">Tarjeta</span>
                                <strong></strong>
                            </article>
                            <article class="corte-metrica">
                                <span class="corte-metrica-etiqueta">Transferencia</span>
                                <strong></strong>
                            </article>
                            <article class="corte-metrica">
                                <span class="corte-metrica-etiqueta">Total de ventas</span>
                                <strong></strong>
                            </article>
                        </div>
                    </section>

                    <section class="corte-detalle" aria-labelledby="tituloDetalleCorte">
                        <h2 id="tituloDetalleCorte">Detalle del corte</h2>
                        <div class="corte-detalle-contenido">
                            <div class="corte-tabla-envoltura">
                                <table class="corte-tabla">
                                    <thead>
                                        <tr>
                                            <th>Concepto</th>
                                            <th class="corte-monto">Monto</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Fondo inicial <small>(solo diario)</small></td>
                                            <td class="corte-monto"></td>
                                        </tr>
                                        <tr>
                                            <td>Ventas en efectivo</td>
                                            <td class="corte-monto"></td>
                                        </tr>
                                        <tr>
                                            <td>Ventas con tarjeta</td>
                                            <td class="corte-monto"></td>
                                        </tr>
                                        <tr>
                                            <td>Ventas por transferencia</td>
                                            <td class="corte-monto"></td>
                                        </tr>
                                        <tr class="corte-fila-total">
                                            <td>Total de ventas</td>
                                            <td class="corte-monto"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <aside class="corte-metadata">
                                <section class="corte-bloque-meta" aria-labelledby="tituloEstadoCorte">
                                    <h3 id="tituloEstadoCorte">Estatus del corte</h3>
                                    <span class="corte-estado abierto">Abierto</span>
                                </section>
                                <section class="corte-bloque-meta" aria-labelledby="tituloObservaciones">
                                    <label for="observacionesCorte" id="tituloObservaciones">Observaciones</label>
                                    <textarea class="campo" id="observacionesCorte" rows="4" placeholder="Agregar observaciones..."></textarea>
                                </section>
                            </aside>
                        </div>

                        <div class="corte-acciones">
                            <button class="btn corte-cerrar btn-chico" type="button" disabled>Cerrar corte</button>
                            <button class="btn btn-secundario btn-chico" type="button" disabled>Descargar corte en PDF</button>
                        </div>
                    </section>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
