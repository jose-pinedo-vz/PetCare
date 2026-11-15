<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veterinaria PetCare - Corte de caja</title>
    <link rel="stylesheet" href="css/estilos_base_v2.css">
    <link rel="stylesheet" href="css/corte_caja.css">
</head>
<body>
    <div class="layout">
        <?php $paginaActiva = 'ventas'; include __DIR__ . '/layout/sidebar.php'; ?>

        <main class="corte-pagina">
            <div class="migaja"><a href="Ventas.php">Punto de Venta</a> / Corte de caja</div>

            <header class="encabezado-pagina corte-encabezado">
                <div>
                    <span class="corte-kicker">Administración de caja</span>
                    <h1>Corte de caja</h1>
                </div>
                <div class="corte-encabezado-derecha">
                    <div class="corte-sesion" aria-label="Sesión de caja">
                        <span class="corte-avatar" aria-hidden="true">ML</span>
                        <span><strong>María López</strong><small>Matutino · <time id="fechaActual">14/11/2026</time></small></span>
                    </div>
                    <a class="btn btn-secundario btn-chico" href="Ventas.php">Regresar a venta</a>
                </div>
            </header>

            <nav class="corte-tabs" role="tablist" aria-label="Secciones de corte de caja">
                <button class="corte-tab activa" id="tab-diario" type="button" role="tab" aria-selected="true" aria-controls="panel-diario" tabindex="0" data-tab="diario">1. Corte diario (Turno)</button>
                <button class="corte-tab" id="tab-historial" type="button" role="tab" aria-selected="false" aria-controls="panel-historial" tabindex="-1" data-tab="historial">2. Historial y cierres mensuales</button>
                <button class="corte-tab" id="tab-estadisticas" type="button" role="tab" aria-selected="false" aria-controls="panel-estadisticas" tabindex="-1" data-tab="estadisticas">3. Estadísticas de caja</button>
            </nav>

            <section class="corte-panel" id="panel-diario" role="tabpanel" aria-labelledby="tab-diario" data-panel="diario">
                <div class="corte-panel-encabezado">
                    <div>
                        <h2>Corte diario y turno en curso</h2>
                        <p>Revisa los movimientos, realiza el arqueo y prepara el cierre del turno.</p>
                    </div>
                    <span class="corte-demo">Vista de demostración</span>
                </div>

                <div class="corte-apertura tarjeta">
                    <div class="corte-apertura-campos">
                        <div class="corte-campo">
                            <label class="label-campo" for="turnoCorte">Turno</label>
                            <select class="campo" id="turnoCorte">
                                <option value="matutino" selected>Matutino</option>
                                <option value="vespertino">Vespertino</option>
                                <option value="completo">Completo</option>
                            </select>
                        </div>
                        <div class="corte-campo">
                            <label class="label-campo" for="fondoInicial">Fondo inicial</label>
                            <div class="corte-input-monto"><span aria-hidden="true">$</span><input class="campo" id="fondoInicial" type="number" min="0" step="0.01" value="1500"></div>
                        </div>
                    </div>
                    <div class="corte-apertura-estado">
                        <span class="corte-estado abierto" id="estadoTurno">Abierto</span>
                        <span><strong>Turno iniciado</strong><small>Hoy, 08:00 a. m.</small></span>
                    </div>
                </div>

                <section class="corte-seccion" aria-labelledby="tituloResumen">
                    <div class="corte-seccion-titulo">
                        <div><h2 id="tituloResumen">Resumen de caja</h2><p>Montos de ejemplo para ilustrar el corte.</p></div>
                    </div>
                    <div class="corte-kpis">
                        <article class="corte-kpi corte-kpi--esperado">
                            <span class="corte-kpi-icono" aria-hidden="true">↗</span>
                            <span class="corte-kpi-label">Efectivo esperado</span>
                            <strong id="efectivoEsperado">$5,280.00</strong>
                            <small>Fondo + entradas − salidas</small>
                        </article>
                        <article class="corte-kpi corte-kpi--contado">
                            <span class="corte-kpi-icono" aria-hidden="true">▤</span>
                            <label class="corte-kpi-label" for="efectivoContado">Efectivo contado</label>
                            <div class="corte-kpi-contado"><span aria-hidden="true">$</span><input class="campo" id="efectivoContado" type="number" min="0" step="0.01" value="5280" aria-describedby="ayudaEfectivoContado"></div>
                            <button class="btn-secundario btn-chico" id="abrirArqueo" type="button">Desglosar billetes/monedas</button>
                            <small id="ayudaEfectivoContado">Captura el efectivo físico del cajón.</small>
                        </article>
                        <article class="corte-kpi corte-kpi--diferencia" id="tarjetaDiferencia">
                            <span class="corte-kpi-icono" aria-hidden="true">=</span>
                            <span class="corte-kpi-label">Diferencia</span>
                            <strong id="diferenciaCorte">$0.00</strong>
                            <small id="estadoDiferencia">Caja cuadrada</small>
                        </article>
                        <article class="corte-kpi">
                            <span class="corte-kpi-icono" aria-hidden="true">▣</span>
                            <span class="corte-kpi-label">Tarjeta</span>
                            <strong>$3,260.00</strong>
                            <small>Débito y crédito</small>
                        </article>
                        <article class="corte-kpi">
                            <span class="corte-kpi-icono" aria-hidden="true">⇄</span>
                            <span class="corte-kpi-label">Transferencia</span>
                            <strong>$1,000.00</strong>
                            <small>Pagos recibidos</small>
                        </article>
                        <article class="corte-kpi corte-kpi--total">
                            <span class="corte-kpi-icono" aria-hidden="true">＄</span>
                            <span class="corte-kpi-label">Total general vendido</span>
                            <strong>$8,460.00</strong>
                            <small>Productos y servicios</small>
                        </article>
                    </div>
                </section>

                <section class="tarjeta corte-movimientos" aria-labelledby="tituloMovimientos">
                    <div class="corte-seccion-titulo corte-movimientos-titulo">
                        <div><h2 id="tituloMovimientos">Movimientos del turno</h2><p>Entradas y salidas que explican el efectivo esperado.</p></div>
                        <button class="btn btn-chico" id="abrirSalida" type="button">+ Registrar salida/gasto</button>
                    </div>
                    <div class="corte-tabla-envoltura">
                        <table class="corte-tabla">
                            <thead><tr><th>Tipo</th><th>Concepto</th><th>Hora</th><th>Responsable</th><th class="corte-monto">Monto</th></tr></thead>
                            <tbody id="tablaMovimientos">
                                <tr data-impact="1500"><td><span class="corte-tipo-movimiento entrada">Entrada</span></td><td>Fondo inicial</td><td>08:00</td><td>María López</td><td class="corte-monto" data-amount>$1,500.00</td></tr>
                                <tr data-impact="2850"><td><span class="corte-tipo-movimiento entrada">Entrada</span></td><td>Ventas de productos (POS) en efectivo</td><td>08:20–13:40</td><td>María López</td><td class="corte-monto" data-amount>$2,850.00</td></tr>
                                <tr data-impact="1350"><td><span class="corte-tipo-movimiento entrada">Entrada</span></td><td>Cobros y abonos de servicios en efectivo</td><td>09:10–13:55</td><td>María López</td><td class="corte-monto" data-amount>$1,350.00</td></tr>
                                <tr data-impact="200"><td><span class="corte-tipo-movimiento entrada">Entrada</span></td><td>Abonos manuales a caja</td><td>10:30</td><td>María López</td><td class="corte-monto" data-amount>$200.00</td></tr>
                                <tr data-impact="-450"><td><span class="corte-tipo-movimiento salida">Salida</span></td><td>Pagos a empleados en efectivo</td><td>11:00</td><td>María López</td><td class="corte-monto" data-amount>−$450.00</td></tr>
                                <tr data-impact="-170"><td><span class="corte-tipo-movimiento salida">Salida</span></td><td>Gastos menores / caja chica</td><td>12:15</td><td>María López</td><td class="corte-monto" data-amount>−$170.00</td></tr>
                            </tbody>
                            <tfoot><tr><th colspan="4">Efectivo esperado al cierre</th><th class="corte-monto" id="totalMovimientos">$5,280.00</th></tr></tfoot>
                        </table>
                    </div>
                    <p class="corte-tabla-nota">Los importes y movimientos mostrados son ilustrativos; aún no se conectan con la base de datos.</p>
                </section>

                <section class="tarjeta corte-auditoria" aria-labelledby="tituloAuditoria">
                    <div><h2 id="tituloAuditoria">Cierre y auditoría</h2><p>Agrega una nota para dejar contexto sobre el arqueo del turno.</p></div>
                    <label class="label-campo" for="observacionesCorte">Observaciones</label>
                    <textarea class="campo" id="observacionesCorte" rows="3" placeholder="Ej. Se reportó un faltante por..."></textarea>
                    <div class="corte-acciones">
                        <button class="btn btn-peligro" id="abrirConfirmarCierre" type="button">Cerrar corte</button>
                        <button class="btn-secundario" type="button" data-aviso="La descarga de PDF estará disponible al conectar el backend.">Descargar PDF del corte</button>
                    </div>
                </section>
            </section>

            <section class="corte-panel" id="panel-historial" role="tabpanel" aria-labelledby="tab-historial" data-panel="historial" hidden>
                <div class="corte-panel-encabezado">
                    <div><h2>Historial y cierres mensuales</h2><p>Consulta cortes anteriores y revisa el balance del periodo seleccionado.</p></div>
                    <span class="corte-demo">Datos de ejemplo</span>
                </div>

                <section class="tarjeta corte-filtros" aria-label="Filtros del historial">
                    <div class="corte-filtro">
                        <label class="label-campo" for="filtroTipo">Vista / tipo</label>
                        <select class="campo" id="filtroTipo"><option value="todos">Todos</option><option value="diario">Cortes diarios</option><option value="mensual">Cierres mensuales</option></select>
                    </div>
                    <div class="corte-filtro">
                        <label class="label-campo" for="filtroPeriodo">Mes / año</label>
                        <input class="campo" id="filtroPeriodo" type="month" value="2026-11">
                    </div>
                    <div class="corte-filtro corte-filtro--buscar">
                        <label class="label-campo" for="buscarHistorial">Folio o cajero</label>
                        <input class="campo" id="buscarHistorial" type="search" placeholder="Buscar por folio o nombre">
                    </div>
                </section>

                <div class="corte-kpis corte-kpis--periodo">
                    <article class="corte-kpi"><span class="corte-kpi-label">Ingresos brutos del mes</span><strong>$186,420.00</strong><small>Efectivo $72,500 · Tarjeta $86,700 · Transferencia $27,220</small></article>
                    <article class="corte-kpi"><span class="corte-kpi-label">Egresos del mes</span><strong>$48,350.00</strong><small>Nómina y gastos operativos</small></article>
                    <article class="corte-kpi corte-kpi--total"><span class="corte-kpi-label">Balance neto del mes</span><strong>$138,070.00</strong><small>Ingresos menos egresos</small></article>
                    <article class="corte-kpi corte-kpi--diferencia"><span class="corte-kpi-label">Diferencias acumuladas</span><strong>−$125.00</strong><small>Sobrantes y faltantes del periodo</small></article>
                </div>

                <section class="tarjeta corte-historial" aria-labelledby="tituloHistorial">
                    <div class="corte-seccion-titulo">
                        <div><h2 id="tituloHistorial">Cortes registrados</h2><p id="resultadoHistorial">Mostrando registros de ejemplo.</p></div>
                        <button class="btn" id="abrirCierreMensual" type="button">Generar / Cerrar corte mensual</button>
                    </div>
                    <div class="corte-tabla-envoltura">
                        <table class="corte-tabla corte-tabla-historial">
                            <thead><tr><th>Folio</th><th>Tipo</th><th>Fecha / periodo</th><th>Cajero</th><th>Total ventas</th><th>Diferencia</th><th>Estado</th><th>Acciones</th></tr></thead>
                            <tbody id="tablaHistorial">
                                <tr data-kind="diario" data-period="2026-11" data-search="cc-261114-01 maria lopez">
                                    <td><strong>CC-261114-01</strong></td><td>Diario</td><td>14 nov 2026</td><td>María López</td><td>$8,460.00</td><td class="monto-positivo">$0.00</td><td><span class="corte-estado abierto">Abierto</span></td>
                                    <td><div class="corte-fila-acciones"><button class="btn-secundario btn-chico" type="button" data-detalle data-folio="CC-261114-01" data-periodo="14 nov 2026" data-cajero="María López" data-total="$8,460.00" data-diferencia="$0.00" data-estado="Abierto">Ver detalle</button><button class="btn-secundario btn-chico" type="button" data-aviso="La descarga de PDF estará disponible al conectar el backend." aria-label="Descargar PDF del corte CC-261114-01">PDF</button></div></td>
                                </tr>
                                <tr data-kind="diario" data-period="2026-11" data-search="cc-261113-02 carlos reyes">
                                    <td><strong>CC-261113-02</strong></td><td>Diario</td><td>13 nov 2026</td><td>Carlos Reyes</td><td>$7,920.00</td><td class="monto-negativo">−$25.00</td><td><span class="corte-estado cerrado">Cerrado</span></td>
                                    <td><div class="corte-fila-acciones"><button class="btn-secundario btn-chico" type="button" data-detalle data-folio="CC-261113-02" data-periodo="13 nov 2026" data-cajero="Carlos Reyes" data-total="$7,920.00" data-diferencia="−$25.00" data-estado="Cerrado">Ver detalle</button><button class="btn-secundario btn-chico" type="button" data-aviso="La descarga de PDF estará disponible al conectar el backend." aria-label="Descargar PDF del corte CC-261113-02">PDF</button></div></td>
                                </tr>
                                <tr data-kind="mensual" data-period="2026-10" data-search="cm-2610 administracion">
                                    <td><strong>CM-2610</strong></td><td>Mensual</td><td>01–31 oct 2026</td><td>Administración</td><td>$172,650.00</td><td class="monto-positivo">$15.00</td><td><span class="corte-estado cerrado">Cerrado</span></td>
                                    <td><div class="corte-fila-acciones"><button class="btn-secundario btn-chico" type="button" data-detalle data-folio="CM-2610" data-periodo="01–31 oct 2026" data-cajero="Administración" data-total="$172,650.00" data-diferencia="$15.00" data-estado="Cerrado">Ver detalle</button><button class="btn-secundario btn-chico" type="button" data-aviso="La descarga de PDF estará disponible al conectar el backend." aria-label="Descargar PDF del corte CM-2610">PDF</button></div></td>
                                </tr>
                                <tr data-kind="diario" data-period="2026-10" data-search="cc-261031-01 maria lopez">
                                    <td><strong>CC-261031-01</strong></td><td>Diario</td><td>31 oct 2026</td><td>María López</td><td>$6,730.00</td><td class="monto-positivo">$0.00</td><td><span class="corte-estado cerrado">Cerrado</span></td>
                                    <td><div class="corte-fila-acciones"><button class="btn-secundario btn-chico" type="button" data-detalle data-folio="CC-261031-01" data-periodo="31 oct 2026" data-cajero="María López" data-total="$6,730.00" data-diferencia="$0.00" data-estado="Cerrado">Ver detalle</button><button class="btn-secundario btn-chico" type="button" data-aviso="La descarga de PDF estará disponible al conectar el backend." aria-label="Descargar PDF del corte CC-261031-01">PDF</button></div></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p class="corte-tabla-nota">El historial es una muestra visual y no representa registros consultados de la base de datos.</p>
                </section>
            </section>

            <section class="corte-panel" id="panel-estadisticas" role="tabpanel" aria-labelledby="tab-estadisticas" data-panel="estadisticas" hidden>
                <div class="corte-panel-encabezado">
                    <div><h2>Estadísticas de caja</h2><p>Indicadores visuales para analizar los ingresos del periodo.</p></div>
                    <span class="corte-demo">Datos de ejemplo</span>
                </div>
                <section class="tarjeta corte-estadisticas-filtro">
                    <div><strong>Periodo de análisis</strong><small>Selecciona un rango para consultar las métricas.</small></div>
                    <label class="visualmente-oculto" for="periodoEstadisticas">Periodo de análisis</label>
                    <select class="campo" id="periodoEstadisticas"><option value="semana">Esta semana</option><option value="mes" selected>Este mes</option><option value="trimestre">Últimos 3 meses</option></select>
                </section>

                <div class="corte-kpis corte-kpis--estadisticas">
                    <article class="corte-kpi"><span class="corte-kpi-label">Ticket promedio</span><strong>$428.50</strong><small>Gasto promedio por cliente</small></article>
                    <article class="corte-kpi"><span class="corte-kpi-label">Turno más rentable</span><strong>Matutino</strong><small>$68,420.00 en ingresos</small></article>
                    <article class="corte-kpi"><span class="corte-kpi-label">Día de mayor venta</span><strong>Sábado</strong><small>$14,860.00 en promedio</small></article>
                </div>

                <div class="corte-graficas">
                    <section class="tarjeta corte-grafica" aria-labelledby="tituloGraficaIngresos">
                        <div class="corte-grafica-encabezado"><div><h2 id="tituloGraficaIngresos">Ingresos por día</h2><p>Comparativa del periodo seleccionado</p></div><span class="corte-grafica-unidad">MXN</span></div>
                        <div class="corte-barras" role="img" aria-label="Gráfica de barras de ingresos diarios de ejemplo">
                            <div class="corte-barra-columna"><span>$6.2k</span><i style="--barra: 45%"></i><small>01</small></div>
                            <div class="corte-barra-columna"><span>$8.4k</span><i style="--barra: 61%"></i><small>02</small></div>
                            <div class="corte-barra-columna"><span>$7.1k</span><i style="--barra: 52%"></i><small>03</small></div>
                            <div class="corte-barra-columna"><span>$10.8k</span><i style="--barra: 79%"></i><small>04</small></div>
                            <div class="corte-barra-columna"><span>$9.3k</span><i style="--barra: 68%"></i><small>05</small></div>
                            <div class="corte-barra-columna"><span>$13.7k</span><i style="--barra: 100%"></i><small>06</small></div>
                            <div class="corte-barra-columna"><span>$11.2k</span><i style="--barra: 82%"></i><small>07</small></div>
                        </div>
                        <div class="corte-eje"><span>Semana 1</span><span>Ingresos totales · $66,700</span></div>
                    </section>

                    <section class="tarjeta corte-grafica" aria-labelledby="tituloGraficaMetodos">
                        <div class="corte-grafica-encabezado"><div><h2 id="tituloGraficaMetodos">Ingresos por método de pago</h2><p>Distribución porcentual</p></div></div>
                        <div class="corte-dona-contenido">
                            <div class="corte-dona" role="img" aria-label="Efectivo 39 por ciento, tarjeta 46 por ciento, transferencia 15 por ciento"><div class="corte-dona-centro"><strong>$186k</strong><span>ingresos</span></div></div>
                            <ul class="corte-leyenda">
                                <li><span class="corte-punto efectivo"></span><span>Efectivo</span><strong>39%</strong></li>
                                <li><span class="corte-punto tarjeta"></span><span>Tarjeta</span><strong>46%</strong></li>
                                <li><span class="corte-punto transferencia"></span><span>Transferencia</span><strong>15%</strong></li>
                            </ul>
                        </div>
                    </section>

                    <section class="tarjeta corte-grafica corte-grafica-origen" aria-labelledby="tituloGraficaOrigen">
                        <div class="corte-grafica-encabezado"><div><h2 id="tituloGraficaOrigen">Ingresos por origen</h2><p>Productos frente a servicios veterinarios, estética y hotel</p></div></div>
                        <div class="corte-origen">
                            <div class="corte-origen-barra" role="img" aria-label="Productos 65 por ciento y servicios 35 por ciento">
                                <span class="productos" style="width: 65%"></span><span class="servicios" style="width: 35%"></span>
                            </div>
                            <div class="corte-origen-leyenda"><span><i class="corte-punto productos"></i>Productos <strong>65%</strong></span><span><i class="corte-punto servicios"></i>Servicios <strong>35%</strong></span></div>
                        </div>
                    </section>
                </div>
            </section>

            <p class="corte-aviso" id="avisoCorte" role="status" aria-live="polite" hidden></p>
        </main>
    </div>

    <?php include __DIR__ . '/modal_arqueo_efectivo.php'; ?>
    <?php include __DIR__ . '/modal_registrar_salida.php'; ?>
    <?php include __DIR__ . '/modal_confirmar_cierre.php'; ?>
    <?php include __DIR__ . '/modal_cierre_mensual.php'; ?>
    <?php include __DIR__ . '/modal_detalle_corte.php'; ?>
    <script src="js/corte_caja.js"></script>
</body>
</html>
